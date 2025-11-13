<?php

namespace App\Http\Controllers;

use App\Models\ElevenlabsCampaign;
use App\Models\ElevenlabsCampaignCall;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ElevenlabsManagerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            $allowedUserIds = [73, 75, 81, 83, 160];

            if (
                !$user
                || (
                    (int) $user->access_level_id !== 4
                    && !in_array((int) $user->id, $allowedUserIds, true)
                )
            ) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $campaigns = ElevenlabsCampaign::ownedBy($user->id)
            ->withCount('calls')
            ->withCount([
                'calls as pendientes_count' => function ($query) {
                    $query->whereIn('status', [
                        ElevenlabsCampaignCall::STATUS_PENDIENTE,
                        ElevenlabsCampaignCall::STATUS_RELLAMANDO,
                    ]);
                },
                'calls as gestionadas_count' => function ($query) use ($user) {
                    $query->where('status', ElevenlabsCampaignCall::STATUS_GESTIONADA)
                        ->where('managed_by', $user->id);
                },
            ])
            ->orderByDesc('created_at')
            ->get();

        $campaignIds = $campaigns->pluck('id');

        $stats = [
            'total_campaigns' => $campaigns->count(),
            'total_calls' => 0,
            'pending_calls' => 0,
            'managed_calls' => 0,
        ];

        if ($campaignIds->isNotEmpty()) {
            $stats['total_calls'] = ElevenlabsCampaignCall::whereIn('campaign_id', $campaignIds)->count();
            $stats['pending_calls'] = ElevenlabsCampaignCall::whereIn('campaign_id', $campaignIds)
                ->whereIn('status', [
                    ElevenlabsCampaignCall::STATUS_PENDIENTE,
                    ElevenlabsCampaignCall::STATUS_RELLAMANDO,
                ])->count();
            $stats['managed_calls'] = ElevenlabsCampaignCall::whereIn('campaign_id', $campaignIds)
                ->where('status', ElevenlabsCampaignCall::STATUS_GESTIONADA)
                ->where('managed_by', $user->id)
                ->count();
        }

        return view('elevenlabs.gestor.dashboard', compact('campaigns', 'stats'));
    }

    public function clientsData(Request $request)
    {
        $paginator = $this->buildClientsPaginator($request);
        $results = collect($paginator->items())->map(fn ($row) => $this->mapClientRow($row));

        return response()->json([
            'data' => $results,
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem() ?? 0,
                'to' => $paginator->lastItem() ?? 0,
            ],
        ]);
    }

    public function exportClients(Request $request)
    {
        $paginator = $this->buildClientsPaginator($request);
        $rows = collect($paginator->items())->map(fn ($row) => $this->mapClientRow($row));

        $export = new class($rows) implements FromCollection, WithHeadings {
            public function __construct(private readonly Collection $rows) {}

            public function collection(): Collection
            {
                return $this->rows->map(function ($row) {
                    return [
                        'Cliente' => $row['name'] ?? '',
                        'Empresa' => $row['company'] ?? '',
                        'Teléfono' => $row['phone'] ?? '',
                        'Etiqueta' => $row['label'] ?? '',
                        'Fecha de alta' => $row['created_at'] ?? '',
                        'Facturación (€)' => $row['billing'],
                    ];
                });
            }

            public function headings(): array
            {
                return [
                    'Cliente',
                    'Empresa',
                    'Teléfono',
                    'Etiqueta',
                    'Fecha de alta',
                    'Facturación (€)',
                ];
            }
        };

        $fileName = sprintf('clientes-elevenlabs-%s.xlsx', now()->format('Ymd_His'));

        return Excel::download($export, $fileName);
    }

    protected function buildClientsPaginator(Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        $perPageOptions = [10, 15, 25, 50, 100, 150];
        $perPage = $request->integer('per_page', 50);
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 50;
        }

        $page = max($request->integer('page', 1), 1);
        $search = trim((string) $request->get('search', ''));
        $billingMin = $request->get('billing_min', 0);
        $billingMax = $request->get('billing_max');
        $sort = $request->get('sort', 'recent');

        $timezone = config('app.timezone');
        $dateFromInput = $request->get('date_from');
        $dateToInput = $request->get('date_to');
        $dateFrom = $dateFromInput
            ? Carbon::parse($dateFromInput, $timezone)->startOfDay()
            : Carbon::now($timezone)->subMonth()->startOfMonth();
        $dateTo = $dateToInput
            ? Carbon::parse($dateToInput, $timezone)->endOfDay()
            : Carbon::now($timezone)->endOfDay();

        $query = DB::table('clients')
            ->select([
                'clients.id as client_id',
                'clients.name',
                'clients.primerApellido',
                'clients.segundoApellido',
                'clients.company',
                'clients.phone',
                'clients.created_at',
            ])
            ->selectRaw('SUM(invoices.total) as total_facturado')
            ->selectRaw('COUNT(invoices.id) as num_facturas')
            ->join('invoices', 'clients.id', '=', 'invoices.client_id')
            ->where('clients.is_client', true)
            ->whereNull('invoices.deleted_at')
            ->whereBetween('invoices.created_at', [$dateFrom, $dateTo])
            ->whereIn('invoices.invoice_status_id', [3, 4])
            ->groupBy('clients.id', 'clients.name', 'clients.primerApellido', 'clients.segundoApellido', 'clients.company', 'clients.phone', 'clients.created_at');

        if ($search !== '') {
            $like = "%{$search}%";
            $query->where(function ($q) use ($like) {
                $q->where('clients.name', 'like', $like)
                    ->orWhere('clients.primerApellido', 'like', $like)
                    ->orWhere('clients.segundoApellido', 'like', $like)
                    ->orWhere('clients.company', 'like', $like)
                    ->orWhere('clients.phone', 'like', $like);
            });
        }

        if ($billingMin !== null && $billingMin !== '') {
            $query->having('total_facturado', '>=', (float) $billingMin);
        } else {
            $query->having('total_facturado', '>=', 0);
        }

        if ($billingMax !== null && $billingMax !== '') {
            $query->having('total_facturado', '<=', (float) $billingMax);
        }

        switch ($sort) {
            case 'billing_desc':
                $query->orderByDesc('total_facturado')->orderBy('clients.name');
                break;
            case 'billing_asc':
                $query->orderBy('total_facturado')->orderBy('clients.name');
                break;
            case 'name':
                $query->orderBy('clients.name');
                break;
            case 'oldest':
                $query->orderBy('clients.created_at');
                break;
            case 'recent':
            default:
                $query->orderByDesc('clients.created_at');
                break;
        }

        return $query->paginate($perPage, ['*'], 'page', $page)->appends($request->query());
    }

    protected function mapClientRow(object $row): array
    {
        $fullName = trim(collect([
            $row->name ?? '',
            $row->primerApellido ?? '',
            $row->segundoApellido ?? '',
        ])->filter()->implode(' '));

        return [
            'client_id' => (int) $row->client_id,
            'phone_id' => null,
            'phone' => $row->phone,
            'name' => $fullName !== '' ? $fullName : ($row->name ?? null),
            'company' => $row->company,
            'created_at' => $row->created_at ? Carbon::parse($row->created_at)->toDateString() : null,
            'billing' => round((float) $row->total_facturado, 2),
            'total_facturado' => round((float) $row->total_facturado, 2),
            'num_facturas' => isset($row->num_facturas) ? (int) $row->num_facturas : null,
            'label' => null,
        ];
    }

    public function calls(Request $request, ElevenlabsCampaign $campaign)
    {
        $this->ensureOwnership($campaign);

        $showResolved = $request->boolean('resolved');
        $userId = $request->user()->id;

        $callsQuery = $campaign->calls()
            ->with('conversation')
            ->orderBy('created_at');

        if ($showResolved) {
            $callsQuery->where('status', ElevenlabsCampaignCall::STATUS_GESTIONADA)
                ->where('managed_by', $userId);
        } else {
            $callsQuery->whereIn('status', [
                ElevenlabsCampaignCall::STATUS_PENDIENTE,
                ElevenlabsCampaignCall::STATUS_RELLAMANDO,
            ]);
        }

        $calls = $callsQuery->get()->map(function (ElevenlabsCampaignCall $call) {
            $conversation = $call->conversation;

            return [
                'id' => $call->id,
                'status' => $call->status,
                'phone_number' => $call->phone_number,
                'sentiment' => $conversation?->sentiment_category ?? $call->sentiment_category,
                'sentiment_label' => $conversation?->sentiment_label ?? null,
                'specific_category' => $conversation?->specific_category ?? $call->specific_category,
                'specific_label' => $conversation?->specific_label ?? null,
                'confidence' => $conversation?->confidence_score ?? $call->confidence_score,
                'summary' => $conversation?->summary_es ?? $call->summary,
                'custom_prompt' => $call->custom_prompt,
                'initial_prompt' => $conversation?->campaign_initial_prompt ?? $call->custom_prompt,
                'managed_by' => $call->managed_by,
                'managed_at' => optional($call->managed_at)->format('d/m/Y H:i'),
                'managed_at_iso' => optional($call->managed_at)->toIso8601String(),
                'is_rellamando' => $call->isRellamando(),
                'created_at' => optional($call->created_at)->format('d/m/Y H:i'),
                'created_at_iso' => optional($call->created_at)->toIso8601String(),
                'updated_at_iso' => optional($call->updated_at)->toIso8601String(),
                'conversation' => $conversation ? [
                    'id' => $conversation->id,
                    'conversation_id' => $conversation->conversation_id,
                    'agent_name' => $conversation->agent_name,
                    'conversation_date' => optional($conversation->conversation_date)->format('d/m/Y H:i'),
                    'conversation_date_iso' => optional($conversation->conversation_date)->toIso8601String(),
                    'duration_seconds' => $conversation->duration_seconds,
                    'sentiment_category' => $conversation->sentiment_category,
                    'specific_category' => $conversation->specific_category,
                    'confidence_score' => $conversation->confidence_score,
                    'scheduled_call_datetime' => optional($conversation->scheduled_call_datetime)->format('d/m/Y H:i'),
                    'scheduled_call_datetime_iso' => optional($conversation->scheduled_call_datetime)->toIso8601String(),
                    'scheduled_call_notes' => $conversation->scheduled_call_notes,
                    'transcript' => $conversation->transcript,
                    'summary_es' => $conversation->summary_es,
                    'metadata' => $conversation->metadata,
                    'initial_prompt' => $conversation->campaign_initial_prompt ?? null,
                    'created_at' => optional($conversation->created_at)->format('d/m/Y H:i'),
                    'updated_at' => optional($conversation->updated_at)->format('d/m/Y H:i'),
                    'processed_at' => optional($conversation->processed_at)->format('d/m/Y H:i'),
                    'created_at_iso' => optional($conversation->created_at)->toIso8601String(),
                    'updated_at_iso' => optional($conversation->updated_at)->toIso8601String(),
                    'processed_at_iso' => optional($conversation->processed_at)->toIso8601String(),
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'resolved' => $showResolved,
            'calls' => $calls,
        ]);
    }

    public function updateCallStatus(Request $request, ElevenlabsCampaignCall $call)
    {
        $campaign = $call->campaign;
        $this->ensureOwnership($campaign);

        $validated = $request->validate([
            'action' => 'required|string|in:rellamar,gestionar',
        ]);

        if ($validated['action'] === 'rellamar') {
            $call->toggleRellamando();
        } else {
            $call->markAsGestionada($request->user()->id);
        }

        if ($campaign) {
            $campaign->refreshCounters();
        }

        return response()->json([
            'success' => true,
            'status' => $call->status,
            'call_id' => $call->id,
        ]);
    }

    protected function ensureOwnership(ElevenlabsCampaign $campaign): void
    {
        $userId = auth()->id();
        $allowedUserIds = [160];

        if ($campaign->created_by !== $userId && !in_array((int) $userId, $allowedUserIds, true)) {
            abort(403);
        }
    }
}

