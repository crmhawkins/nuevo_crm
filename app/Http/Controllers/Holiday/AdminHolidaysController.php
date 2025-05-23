<?php

namespace App\Http\Controllers\Holiday;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Holidays\Holidays;
use App\Models\Holidays\HolidaysAdditions;
use App\Models\Holidays\HolidaysPetitions;
use App\Models\Alerts\Alert;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailHoliday;
use App\Models\Users\User;
use Carbon\Carbon;


class AdminHolidaysController extends Controller
{
    /**
     * Mostrar la lista de usuarios y el número de vacaciones
     *
     */
    public function index()
    {

        return view('holidays.indexAdmin');
    }

     /**
     * Mostrar el formulario de edición
     *
     * @param  Holidays  $holiday
     *
     */
    public function edit(Holidays $holiday, Request $request)
    {
        $holidays = Holidays::where('admin_user_id', 'admin_user_id')->get();
        return view('admin.admin_holidays.edit', compact('holidays', 'request'));
    }

    /**
     * Actualizar registro
     *
     * @param  Request  $request
     * @param  Holidays  $holiday
     *
     */
    public function update(Request $request, Holidays $holiday)
    {
        // Validación
        $request->validate([
            'quantity' => 'required|between:0,99.99',
        ]);

        // Datos del formulario
        $data = $request->all();
        $oldQuantity = $holiday->quantity;
        $daysToAdd = $data['quantity'];
        $holidaysDays =  $oldQuantity  +   $daysToAdd;

        $data['quantity'] = $holidaysDays;

        // Actualizar días de vacaciones
        $holiday->fill($data);
        $holidaySaved = $holiday->save();

        if($holidaySaved){
            DB::table('holidays_additions')->insert([
                [
                    'admin_user_id' => $holiday->admin_user_id,
                    'quantity_before' => $oldQuantity,
                    'quantity_to_add' => $daysToAdd,
                    'quantity_now' => $holidaysDays,
                    'manual' => 1,
                    'holiday_petition' => 0,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ],
            ]);
        }

        // Respuesta
        return redirect()->route('admin_holiday.edit',$holiday->id)->with('toast', [
            'icon' => 'success',
            'mensaje' => 'Nuevo registro actualizado correctamente'
          ]
      );

    }

    /**
     * Mostrar el historial de actualizaciones de vacaciones (días añadidos, quitados)
     *
     */
    public function addedRecord()
    {
        //$holidays = DB::table('holidays')->get();
        $holidaysAdditions = HolidaysAdditions::orderBy('id', 'desc')->get();


        return view('admin.admin_holidays.record', compact('holidaysAdditions'));
    }

    /**
     * Mostrar historial de vacaciones de todo el mundo
     *
     */
    public function allHistory()
    {
        $holidays = HolidaysPetitions::orderBy('id', 'desc')->withTrashed()->get();
        $today = date("Y-m-d");

        return view('admin.admin_holidays.allhistory', compact('holidays','today'));
    }

    /**
     * Gestión de vacaciones
     *
     */
    public function usersPetitions(){

        $holidaysPetitions = HolidaysPetitions::orderBy('created_at', 'asc')->get();
        $numberOfholidaysPetitions = HolidaysPetitions::where('holidays_status_id', 3)->count();

        $holydayEvents = [];
        $data = HolidaysPetitions::orderBy('created_at', 'asc')->get();

        if ($data->count()) {
            foreach ($data as $value) {
                $color = '#FFFFFF'; // Color por defecto
                //dd($value);
                // Asignar color según el estado
                if ($value->holidays_status_id == 1) {
                    $color = '#C3EBC4'; // Color para estado 1
                } elseif ($value->holidays_status_id == 2) {
                    $color = '#FBC4C4'; // Color para estado 2
                } elseif ($value->holidays_status_id == 3) {
                    $color = '#FFDD9E'; // Color para estado 3
                }

                // Verificar si el usuario está asociado con la petición
                if ($value->adminUser) {
                    $holydayEvents[] = [
                        'title' => $value->adminUser->name, // Título del evento
                        'start' => (new \DateTime($value->from))->format('Y-m-d'), // Fecha de inicio
                        'end' => (new \DateTime($value->to . ' +1 day'))->format('Y-m-d'), // Fecha de fin
                        'endTrue' => (new \DateTime($value->to))->format('Y-m-d'), // Fecha de fin
                        'color' => $color, // Color del evento
                        'id' => $value->id,
                        'created_at' => (new \DateTime($value->created_at))->format('Y-m-d')
                    ];

                }
            }
        }
        return view('holidays.gestion',compact('numberOfholidaysPetitions','holydayEvents'));
    }

     /**
     * Gestión de peticiónes de un usuario
     *
     * @param  HolidaysPetitions  $holidayPetition
     *
     */

    public function userPetitions($id){
        $user = User::find($id);
        $holidaysPetitions = HolidaysPetitions::where('admin_user_id',$id)->orderBy('created_at', 'asc')->get();
        $numberOfholidaysPetitions = HolidaysPetitions::where('admin_user_id',$id)->where('holidays_status_id', 3)->count();

        $diasDisponibles = Holidays::where('admin_user_id',$id)->get()->first()->quantity;
        $diasDisfrutados = HolidaysPetitions::where('admin_user_id',$id)->where('holidays_status_id', 1)->sum('total_days');

        $holydayEvents = [];
        $data = HolidaysPetitions::where('admin_user_id',$id)->orderBy('created_at', 'asc')->get();

        if ($data->count()) {
            foreach ($data as $value) {
                $color = '#FFFFFF'; // Color por defecto
                //dd($value);
                // Asignar color según el estado
                if ($value->holidays_status_id == 1) {
                    $color = '#C3EBC4'; // Color para estado 1
                } elseif ($value->holidays_status_id == 2) {
                    $color = '#FBC4C4'; // Color para estado 2
                } elseif ($value->holidays_status_id == 3) {
                    $color = '#FFDD9E'; // Color para estado 3
                }

                // Verificar si el usuario está asociado con la petición
                if ($value->adminUser) {
                    $holydayEvents[] = [
                        'title' => $value->adminUser->name, // Título del evento
                        'start' => (new \DateTime($value->from))->format('Y-m-d'), // Fecha de inicio
                        'end' => (new \DateTime($value->to . ' +1 day'))->format('Y-m-d'), // Fecha de fin
                        'endTrue' => (new \DateTime($value->to))->format('Y-m-d'), // Fecha de fin
                        'color' => $color, // Color del evento
                        'id' => $value->id,
                        'created_at' => (new \DateTime($value->created_at))->format('Y-m-d')
                    ];

                }
            }
        }
        return view('holidays.gestionUser',compact('numberOfholidaysPetitions','holydayEvents','diasDisponibles','diasDisfrutados','user'));
    }

     /**
     * Gestión de una petición
     *
     * @param  HolidaysPetitions  $holidayPetition
     *
     */
    public function managePetition(string $id)
    {
        $holidayPetition = HolidaysPetitions::find($id);
        $userId = Auth::id();
        $usuario = User::find($userId);
        return view('holidays.managePetitions', compact('holidayPetition', 'usuario'));
    }

    /**
     * Aceptar petición
     *
     * @param  Request  $request
     * @param  HolidaysPetitions  $holidayPetition
     *
     */
    public function acceptHolidays(Request $request)
    {
        $holidayPetition = HolidaysPetitions::find($request->id);
        $fechaNow = Carbon::now();

        $data = $request->all();
        $data['holidays_status_id'] = 1;

        try {
            if($holidayPetition->holidays_status_id ==2){
                $user = User::find($holidayPetition->admin_user_id);
                $userHolidaysQuantity = Holidays::where('admin_user_id', $user->id )->get()->first();
                $petitionQuantityDaysNegative = -1 * abs($holidayPetition->total_days);
                $updatedHolidaysQuantity =  $userHolidaysQuantity->quantity - $holidayPetition->total_days;

                if($updatedHolidaysQuantity >= 0){
                    Holidays::where('admin_user_id', $user->id )->update(array('quantity' => $updatedHolidaysQuantity ));
                }else{
                    return response()->json(['error' => 'Sobrepasa el límite de vacaciones']);
                }

                DB::table('holidays_additions')->insert([
                    [
                        'admin_user_id' => $user->id,
                        'quantity_before' => $userHolidaysQuantity->quantity,
                        'quantity_to_add' => $petitionQuantityDaysNegative,
                        'quantity_now' => $updatedHolidaysQuantity,
                        'manual' => 0,
                        'holiday_petition' => 1,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ],
                ]);
            }
            $holidayPetition->fill($data);
            $holidaySaved = $holidayPetition->save();

            if($holidaySaved){
                //Alerta resuelta
                $alertHoliday = Alert::where('stage_id', 16)->where('reference_id', $holidayPetition->id)->get()->first();
                $alertHoliday->status_id = 2;
                $alertHoliday->save();

                // Crear alerta para avisar al usuario
                $data = [
                    'admin_user_id' => $holidayPetition->admin_user_id,
                    'stage_id' => 17,
                    'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
                    'status_id' => 1,
                    'reference_id' => $holidayPetition->id
                ];

                $alert = Alert::create($data);
                $alertSaved = $alert->save();


                // $mailBudget = new \stdClass();
                // $mailBudget->usuario = Auth::user()->name." ".Auth::user()->surname;
                // $mailBudget->usuarioMail = Auth::user()->email;
                // $mailBudget->from = $holidayPetition->from;
                // $mailBudget->to = $holidayPetition->to;


                // $allHolidays = Holidays::all();
                // $mailBudget->usuarios = $allHolidays;

                // Mail::to("ivan@lchawkins.com")
                // ->cc(Auth::user()->email)
                // ->send(new MailHoliday($mailBudget));

                $empleado = User::where("id", $holidayPetition->admin_user_id)->first();

                $this->sendEmail($empleado);

                // Respuesta
                return response()->json(['status' => 'success', 'mensaje' => 'Petición de vacaciones aceptada correctamente']);

            }
        } catch (\Exception $e) {
             // Respuesta
             return response()->json(['status' => 'error', 'mensaje' => 'Error al aceptar la petición']);

        }
    }


    /**
     * Denegar petición
     *
     * @param  Request  $request
     * @param  HolidaysPetitions  $holidayPetition
     *
     */
    public function denyHolidays(Request $request){

        $holidayPetition = HolidaysPetitions::find($request->id);
        if($holidayPetition->holidays_status_id !=2){
            try {
                //Denegar petición
                $holidayPetitionToDeny = holidaysPetitions::where('id', $holidayPetition->id )->update(array('holidays_status_id' => 2 ));

                if($holidayPetitionToDeny){

                    $RecoveryDays = Holidays::where('admin_user_id', $holidayPetition->admin_user_id)->get()->first();

                    $RecoveryDays->quantity += $holidayPetition->total_days;
                    $addrecord = $RecoveryDays->save();
                    if($addrecord){
                        HolidaysAdditions::create([
                            'admin_user_id' => $holidayPetition->admin_user_id,
                            'quantity_before' => $RecoveryDays->quantity - $holidayPetition->total_days,
                            'quantity_to_add' => $holidayPetition->total_days,
                            'quantity_now' => $RecoveryDays->quantity,
                            'manual' => 0,
                            'holiday_petition' => 0,
                            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);
                    }

                    //Alerta resuelta
                    $alertHoliday = Alert::where('stage_id', 16)->where('reference_id', $holidayPetition->id)->get()->first();
                    $alertHoliday->status_id = 2;
                    $alertHoliday->save();
                    $fechaNow = Carbon::now();
                    //Crear alerta para avisar al usuario
                    $data = [
                        'admin_user_id' => $holidayPetition->admin_user_id,
                        'stage_id' => 18,
                        'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
                        'status_id' => 1,
                        'reference_id' => $holidayPetition->id
                    ];

                    $alert = Alert::create($data);
                    $alertSaved = $alert->save();

                    // Respuesta
                    return response()->json(['status' => 'success', 'mensaje' => 'Petición de vacaciones denegada correctamente']);
                }
            } catch (\Exception $e) {
                // Respuesta
                return response()->json(['status' => 'error', 'mensaje' => 'Error al denegar la petición']);

            }
        }else{
            return response()->json(['status' => 'success', 'mensaje' => 'Petición de vacaciones denegada correctamente']);
        }
    }


    // Envía un mensaje al usuario cuando se acepta la petición
    public function sendEmail($empleado){

        // Si el estado es 1, es solicitud de vacaciones, el 2 es aceptada, el 3 es rechazada
        $estado = 2;
        $email = new MailHoliday($estado, $empleado);

        Mail::to($empleado->email)->send($email);

        return 200;

    }
    /**
     *  Mostrar el formulario de creación
     *
     */
    public function create()
    {
        $adminUsers = User::orderBy('name', 'asc')->where('inactive',0)->get();
        return view('admin.admin_holidays.create',  compact('adminUsers'));
    }

     /**
     * Guardar nuevo registro
     *
     * @param  Request  $request
     *
     */
    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'quantity' => 'required|between:0,99.99',
        ]);

        // Formulario datos
        $data = $request->all();

        // Guardar
        $holiday = Holidays::create($data);
        $holidaySaved = $holiday->save();

        // Respuesta
        return redirect()->route('holiday.edit',$holiday->id)->with('toast', [
            'icon' => 'succcess',
            'mensaje' => 'Nuevo registro guardado correctamente'
          ]
      );

    }



    /**
     * Borrar registro
     *
     * @param  Holidays  $password
     *
     */
    public function destroy(Holidays $holiday)
    {
        try {
            //Borrar registro
            $deleted = $holiday->delete();
            // Respuesta
            return redirect()->back()->with('toast', [
                'icon' => 'success',
                'mensaje' => 'El registro se borró correctamente'
              ]
          );
        } catch (\Exception $e) {
             // Respuesta
             return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'El registro no pudo ser eliminada.Pruebe más tarde.'
              ]
          );
        }
    }
    public function getDate(holidaysPetitions $holidaysPetitions){
        return response()->json([ 'fecha_inicio' =>$holidaysPetitions->from]);
    }
}
