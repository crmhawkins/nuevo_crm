<?php

namespace App\Http\Controllers\Alert;

use App\Http\Controllers\Controller;
use App\Models\Alerts\Alert;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetSend;
use App\Models\Clients\Client;
use App\Models\CrmActivities\CrmActivitiesMeetings;
use App\Models\Dominios\Dominio;
use App\Models\Holidays\HolidaysPetitions;
use App\Models\HoursMonthly\HoursMonthly;
use App\Models\Invoices\Invoice;
use App\Models\Petitions\Petition;
use App\Models\Tasks\Task;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AlertController extends Controller
{


public function getUserAlerts()
{
    // Obtener alertas del usuario autenticado
    $alerts = Alert::where('status_id',1)
    ->where('admin_user_id', auth()->id())
    ->get();
    $alertas = $this->getAlerts($alerts);
    return response()->json($alertas);
}

public function updateStatusAlert(Request $request)
{
    $alert = Alert::find($request->id);
    $alert->status_id = $request->status;
    $alertSaved = $alert->save();
    if ($alertSaved) {
        $response = json_encode(array(
            "estado" => "200"
        ));
    } else {
        $response = 503;
    }
    return $response;
}


public function getAlerts($alertas)
{
    $now = Carbon::now();
    $contador = 0;
    $alertasActivadas = array();

    foreach ($alertas as $alerta) {
        if ($now->greaterThan($alerta->activation_datetime)) {

            $adminUserGlobal = User::where('id', $alerta->admin_user_id)->get()->first();
            $alertasActivadas[$contador]["id"] = $alerta->id;
            $alertasActivadas[$contador]["admin_user_id"] = $alerta->admin_user_id;
            $alertasActivadas[$contador]["stage_id"] = $alerta->stage_id;
            $alertasActivadas[$contador]["activation_datetime"] = $alerta->activation_datetime;
            $alertasActivadas[$contador]["status_id"] = $alerta->status_id;
            $alertasActivadas[$contador]["reference_id"] = $alerta->reference_id;
            $alertasActivadas[$contador]["cont_postpone"] = $alerta->cont_postpone;
            $alertasActivadas[$contador]["description"] = $alerta->description;
            $alertasActivadas[$contador]["userAdmin"] = $adminUserGlobal->name . ' ' . $adminUserGlobal->surname;

            if ($alerta->reference_id) {
                if ($alerta->status_id != 2) {
                    switch ($alerta->stage_id) {
                        case 1:
                            $client = Client::find($alerta->reference_id);
                            if ($client) {
                                $alertasActivadas[$contador]["client"] = $client->name;
                            }
                            break;
                        case 2:
                            $budgetAlert = Budget::find($alerta->reference_id);
                            if ($budgetAlert) {
                                $alertasActivadas[$contador]["presupuesto"] = $budgetAlert->reference;
                                if (!$budgetAlert->cliente) {
                                    $alertasActivadas[$contador]["cliente"] = "SIN CLIENTE";
                                } else {
                                    if ($budgetAlert->cliente->company) {
                                        $alertasActivadas[$contador]["cliente"] = $budgetAlert->cliente->company;
                                    } else {
                                        $alertasActivadas[$contador]["cliente"] = $budgetAlert->cliente->name;
                                    }
                                }

                                if ($budgetAlert->budget_status_id == 4 || $budgetAlert->budget_status_id == 5 || $budgetAlert->budget_status_id == 6) {
                                    $alerta->status_id = 2;
                                    $alerta->save();
                                    $alerta->delete();
                                }
                            } else {
                                $alerta->status_id = 2;
                                $alerta->save();
                                $alerta->delete();
                            }
                            break;
                        case 3:
                            $budgetAlert = Budget::find($alerta->reference_id);
                            if ($budgetAlert) {
                                $alertasActivadas[$contador]["presupuesto"] = $budgetAlert->reference;
                                if (!$budgetAlert->cliente) {
                                    $alertasActivadas[$contador]["cliente"] = "SIN CLIENTE";
                                } else {
                                    if ($budgetAlert->cliente->company) {
                                        $alertasActivadas[$contador]["cliente"] = $budgetAlert->cliente->company;
                                    } else {
                                       $alertasActivadas[$contador]["cliente"] = $budgetAlert->cliente->name;
                                    }
                                }
                                if ($budgetAlert->budget_status_id == 4 || $budgetAlert->budget_status_id == 5 || $budgetAlert->budget_status_id == 6) {
                                    $alerta->status_id = 2;
                                    $alerta->save();
                                    $alerta->delete();
                                }
                            } else {
                                $alerta->status_id = 2;
                                $alerta->save();
                                $alerta->delete();
                            }
                            break;
                        case 4:
                            $budgetAlert = Budget::find($alerta->reference_id);
                            if ($budgetAlert) {
                                $alertasActivadas[$contador]["presupuesto"] = $budgetAlert->reference;
                                if (!$budgetAlert->cliente) {
                                    $alertasActivadas[$contador]["cliente"] = "SIN CLIENTE";
                                } else {
                                    if ($budgetAlert->cliente->company) {
                                        $alertasActivadas[$contador]["cliente"] = $budgetAlert->cliente->company;
                                    } else {
                                        $alertasActivadas[$contador]["cliente"] = $budgetAlert->cliente->name;
                                    }
                                }
                                if ($budgetAlert->budget_status_id == 4 || $budgetAlert->budget_status_id == 5 || $budgetAlert->budget_status_id == 6) {
                                    $alerta->status_id = 2;
                                    $alerta->save();
                                    $alerta->delete();
                                }
                            } else {
                                $alerta->status_id = 2;
                                $alerta->save();
                                $alerta->delete();
                            }
                            break;
                        case 5:
                            $budgetAlert = Budget::find($alerta->reference_id);
                            if ($budgetAlert) {
                                $alertasActivadas[$contador]["presupuesto"] = $budgetAlert->reference;
                                if (!$budgetAlert->cliente) {
                                    $alertasActivadas[$contador]["cliente"] = "SIN CLIENTE";
                                } else {
                                    if ($budgetAlert->cliente->company) {
                                        $alertasActivadas[$contador]["cliente"] = $budgetAlert->cliente->company;
                                    } else {
                                        $alertasActivadas[$contador]["cliente"] = $budgetAlert->cliente->name;
                                    }
                                }
                                if ($budgetAlert->budget_status_id == 4 || $budgetAlert->budget_status_id == 5 || $budgetAlert->budget_status_id == 6) {
                                    $alerta->status_id = 2;
                                    $alerta->save();
                                    $alerta->delete();
                                }
                            } else {
                                $alerta->status_id = 2;
                                $alerta->save();
                                $alerta->delete();
                            }
                            break;
                        case 6:
                            $budgetAlert = Budget::find($alerta->reference_id);
                            if ($budgetAlert) {
                                $alertasActivadas[$contador]["presupuesto"] = $budgetAlert->reference;
                                if (!$budgetAlert->cliente) {
                                    $alertasActivadas[$contador]["cliente"] = "SIN CLIENTE";
                                } else {
                                    if ($budgetAlert->cliente->company) {
                                        $alertasActivadas[$contador]["cliente"] = $budgetAlert->cliente->company;
                                    } else {
                                        $alertasActivadas[$contador]["cliente"] = $budgetAlert->cliente->name;
                                    }
                                }
                                if ($budgetAlert->budget_status_id == 4 || $budgetAlert->budget_status_id == 5 || $budgetAlert->budget_status_id == 6) {
                                    $alerta->status_id = 2;
                                    $alerta->save();
                                    $alerta->delete();
                                }
                            } else {
                                $alerta->status_id = 2;
                                $alerta->save();
                                $alerta->delete();
                            }
                            break;
                        case 8:
                            $budgetAlert = Budget::find($alerta->reference_id);
                            if ($budgetAlert) {
                                $alertasActivadas[$contador]["presupuesto"] = $budgetAlert->reference;
                                if (!$budgetAlert->cliente) {
                                    $alertasActivadas[$contador]["cliente"] = "SIN CLIENTE";
                                } else {
                                    if ($budgetAlert->cliente->company) {
                                        $alertasActivadas[$contador]["cliente"] = $budgetAlert->cliente->company;
                                    } else {
                                        $alertasActivadas[$contador]["cliente"] = $budgetAlert->cliente->name;
                                    }
                                }
                                if ($budgetAlert->budget_status_id == 4 || $budgetAlert->budget_status_id == 5 || $budgetAlert->budget_status_id == 6) {
                                    $alerta->status_id = 2;
                                    $alerta->save();
                                    $alerta->delete();
                                }
                            } else {
                                $alerta->status_id = 2;
                                $alerta->save();
                                $alerta->delete();
                            }
                            break;
                        case 9:
                            $invoiceAlert =Invoice::find($alerta->reference_id);
                            if ($invoiceAlert) {
                                if ($invoiceAlert->invoice_status_id != 3) {
                                    $alertasActivadas[$contador]["factura"] = $invoiceAlert->reference;
                                } else {
                                    $alerta->status_id = 2;
                                    $alerta->save();
                                    $alerta->delete();
                                }
                            } else {
                                $alerta->status_id = 2;
                                $alerta->save();
                                $alerta->delete();
                            }
                            break;
                        case 11:
                            break;
                        case 14:
                            $taskAlert = Task::find($alerta->reference_id);
                            if ($taskAlert) {
                                $alertasActivadas[$contador]["tarea"] = $taskAlert->title;
                            }
                            break;
                        case 12:
                            $budgetSendAlert = BudgetSend::find($alerta->reference_id);
                            if ($budgetSendAlert) {
                                $alertasActivadas[$contador]["budget_send"] = $budgetSendAlert->budget_reference;
                                $alertasActivadas[$contador]["budget_send_client"] = $budgetSendAlert->cliente->name;
                            }
                            break;
                        case 15:
                            break;
                        case 16:
                            $holiPetiAlert = HolidaysPetitions::where('id', $alerta->reference_id)->first();
                            if ($holiPetiAlert) {
                                $alertasActivadas[$contador]["usuario"] = $holiPetiAlert->adminUser->name . " " . $holiPetiAlert->adminUser->surname;
                                $alertasActivadas[$contador]["fecha"] = "desde " . $holiPetiAlert->from . " hasta " . $holiPetiAlert->to;
                            } else {
                                $alerta->status_id = 2;
                                $alerta->save();
                                $alerta->delete();
                            }
                            break;
                        case 17:
                            $holiPetiAlert = HolidaysPetitions::find($alerta->reference_id);
                            if ($holiPetiAlert) {
                                $alertasActivadas[$contador]["usuario"] = $holiPetiAlert->adminUser->name . " " . $holiPetiAlert->adminUser->surname;
                                $alertasActivadas[$contador]["fecha"] = "desde " . $holiPetiAlert->from . " hasta " . $holiPetiAlert->to;
                            }
                            break;
                        case 18:
                            $holiPetiAlert = HolidaysPetitions::find($alerta->reference_id);
                            if ($holiPetiAlert) {
                                $alertasActivadas[$contador]["usuario"] = $holiPetiAlert->adminUser->name . " " . $holiPetiAlert->adminUser->surname;
                                $alertasActivadas[$contador]["fecha"] = "desde " . $holiPetiAlert->from . " hasta " . $holiPetiAlert->to;
                            }
                            break;
                        case 19:
                            $user = User::find($alerta->reference_id);
                            if ($user) {
                                $alertasActivadas[$contador]["nota"] = $alerta->description;
                                $alertasActivadas[$contador]["remitente"] = "Alerta de: " . $user->name;
                            }
                            break;
                        case 20:
                            $alertPost = Alert::find($alerta->reference_id);
                            $text =  $this->filterStage($alertPost);
                            $user = User::find($alertPost->admin_user_id);
                            $alertasActivadas[$contador]["nota"] = $text;
                            $alertasActivadas[$contador]["remitente"] = $user->name . " ha pospuesto 3 veces";
                            break;
                        case 21:
                            $budgetAlert = Budget::find($alerta->reference_id);
                            if ($budgetAlert && $alerta->stage_id !== 8 && $budgetAlert->reference != null) {
                                $alertasActivadas[$contador]["presupuesto"] = $budgetAlert->reference;
                            } else {
                                $alerta->status_id = 2;
                                $alerta->save();
                                $alerta->delete();
                            }
                            break;
                        case 22:
                            $hoursMonthly = HoursMonthly::find($alerta->reference_id);
                            if ($hoursMonthly) {
                                $hora = floor($hoursMonthly->hours / 60);
                                $minuto = ($hoursMonthly->hours % 60);
                                $horasa = $hora . ' Horas y ' . $minuto . ' minutos';
                                $alertasActivadas[$contador]["horas"] = $horasa;
                            }
                            break;
                        case 23:
                            break;
                        case 24:
                            $alertasActivadas[$contador]["nota"] = $alerta->description;
                            break;
                        case 25:
                            $petition = Petition::where('id', $alerta->reference_id)->get()->first();
                            if (!$petition) {
                                $alerta->delete();
                                break;
                            }
                            $cliente = Client::where('id', $petition->client_id)->get()->first();
                            if ($petition->adminUser) {
                                $alertasActivadas[$contador]["comercial"] = $petition->adminUser->name;
                                $alertasActivadas[$contador]["cliente"] = $cliente->name;
                            }
                            break;
                        case 26:
                            // $invoice = InvoiceCommercial::where('id', $alerta->reference_id)->get()->first();
                            // $adminUser = AdminUser::where('id', $invoice->commercial_id)->get()->first();
                            // if ($invoice) {
                            //     $alertasActivadas[$contador]["comercial"] = $adminUser->name;
                            //     $alertasActivadas[$contador]["factura"] = $invoice->reference;
                            // } else {
                            //     $alerta->status_id = 2;
                            //     $alerta->save();
                            //     $alerta->delete();
                            // }
                            break;
                        case 27:
                            $alertasActivadas[$contador]["mensaje"] = $alerta->description;
                            break;
                        case 28:
                            $alertasActivadas[$contador]["descripcion"] = $alerta->description;
                            break;
                        case 29:
                            $meeting = CrmActivitiesMeetings::find($alerta->reference_id);
                            if ($meeting) {
                                $alertasActivadas[$contador]["remitente"] = "Acta de: " . $meeting->adminUser->name;
                            }
                            break;
                        case 33:
                            $dominio = Dominio::where('id', $alerta->reference_id)->first();
                            $alertasActivadas[$contador]["dominio"] = $dominio;
                            break;

                        case 42:
                            $part = explode('-', $alerta->reference_id);
                            $alertasActivadas[$contador]["stage"] =  $part[0];
                            $alertasActivadas[$contador]["referencia"] =  $part[1];
                           break;
                    }
                }
            }
            $contador++;
        }
    }
    return $alertasActivadas;
}


public function postpone(Request $request)
{
    $alert = Alert::find($request->id);
    $alert->activation_datetime = Carbon::now()->addDay(1);
    $alert->cont_postpone = $alert->cont_postpone + 1;
    $alertSaved = $alert->save();

    if($alert->cont_postpone == 3){
        $alertapost = Alert::create([
            'admin_user_id' => 1,
            'stage_id' => 42,
            'activation_datetime' => Carbon::now(),
            'status_id' => 1,
            'reference_id' =>  $alert->stage_id .'-'. $alert->reference_id,
            'description' => ($alert->adminUser->name ?? 'Usuario no encontrado') . " ha pospuesto 3 veces la alerta de " . $alert->stage->stage,
        ]);
    }

    if ($alertSaved) {
        return 200;
    } else {
        return 503;
    }
}



public function comprobarPospone(Request $request)
{
    $alert = Alert::find($request->id);
    if ($alert->cont_postpone == 3) {
        $alert->cont_postpone = 0;
        $alert->save();
        return 3;
    } else {
        return 0;
    }
}
}
