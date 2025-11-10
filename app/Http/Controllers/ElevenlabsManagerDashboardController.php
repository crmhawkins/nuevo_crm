<?php

namespace App\Http\Controllers;

use App\Models\ElevenlabsCampaign;
use App\Models\ElevenlabsCampaignCall;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ElevenlabsManagerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            $allowedUserIds = [81, 83, 160];

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
        $perPageOptions = [15, 50, 100, 150];
        $perPage = $request->integer('per_page', 50);
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 50;
        }

        $page = max($request->integer('page', 1), 1);
        $search = trim((string) $request->get('search', ''));
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $billingMin = $request->get('billing_min');
        $billingMax = $request->get('billing_max');
        $sort = $request->get('sort', 'recent');

        $invoiceTotals = DB::table('invoices')
            ->selectRaw('client_id, SUM(total) as total_facturacion')
            ->whereNull('deleted_at')
            ->groupBy('client_id');

        $principalPhones = DB::table('clients')
            ->selectRaw(
                "clients.id as client_id, NULL as phone_id, clients.phone as phone, clients.name, clients.company, clients.created_at, COALESCE(invoice_totals.total_facturacion, 0) as billing, 'Principal' as label"
            )
            ->leftJoinSub($invoiceTotals, 'invoice_totals', 'invoice_totals.client_id', '=', 'clients.id')
            ->whereNotNull('clients.phone')
            ->where('clients.phone', '!=', '')
            ->whereRaw("LOWER(TRIM(clients.phone)) <> 'x'");

        $secondaryPhones = DB::table('clients_phones')
            ->selectRaw(
                "clients.id as client_id, clients_phones.id as phone_id, clients_phones.number as phone, clients.name, clients.company, clients.created_at, COALESCE(invoice_totals.total_facturacion, 0) as billing, COALESCE(clients_phones.label, 'Alternativo') as label"
            )
            ->join('clients', 'clients.id', '=', 'clients_phones.client_id')
            ->leftJoinSub($invoiceTotals, 'invoice_totals', 'invoice_totals.client_id', '=', 'clients.id')
            ->whereNull('clients_phones.deleted_at')
            ->whereNotNull('clients_phones.number')
            ->where('clients_phones.number', '!=', '')
            ->whereRaw("LOWER(TRIM(clients_phones.number)) <> 'x'");

        $union = $principalPhones->unionAll($secondaryPhones);
        $query = DB::query()->fromSub($union, 'phones');

        if ($search !== '') {
            $like = "%{$search}%";
            $query->where(function ($q) use ($like) {
                $q->where('phones.name', 'like', $like)
                    ->orWhere('phones.company', 'like', $like)
                    ->orWhere('phones.phone', 'like', $like);
            });
        }

        if ($dateFrom) {
            $query->whereDate('phones.created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('phones.created_at', '<=', $dateTo);
        }

        if ($billingMin !== null && $billingMin !== '') {
            $query->where('phones.billing', '>=', (float) $billingMin);
        }

        if ($billingMax !== null && $billingMax !== '') {
            $query->where('phones.billing', '<=', (float) $billingMax);
        }

        $countQuery = clone $query;
        $total = $countQuery->count();

        switch ($sort) {
            case 'billing_desc':
                $query->orderByDesc('phones.billing')->orderBy('phones.name');
                break;
            case 'billing_asc':
                $query->orderBy('phones.billing')->orderBy('phones.name');
                break;
            case 'name':
                $query->orderBy('phones.name');
                break;
            case 'oldest':
                $query->orderBy('phones.created_at');
                break;
            case 'recent':
            default:
                $query->orderByDesc('phones.created_at');
                break;
        }

        $results = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($row) {
                return [
                    'client_id' => (int) $row->client_id,
                    'phone_id' => $row->phone_id ? (int) $row->phone_id : null,
                    'phone' => $row->phone,
                    'name' => $row->name,
                    'company' => $row->company,
                    'created_at' => $row->created_at ? Carbon::parse($row->created_at)->toDateString() : null,
                    'billing' => (float) $row->billing,
                    'label' => $row->label,
                ];
            });

        $lastPage = max(1, (int) ceil($total / $perPage));
        $from = $total === 0 ? 0 : (($page - 1) * $perPage) + 1;
        $to = $total === 0 ? 0 : min($total, $from + $perPage - 1);

        return response()->json([
            'data' => $results,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
                'from' => $from,
                'to' => $to,
            ],
        ]);
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

