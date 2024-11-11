<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Clients\ClientEmail;
use App\Models\Clients\ClientPhone;
use App\Models\Clients\ClientWeb;
use App\Models\Contacts\Contact;
use App\Models\Countries\Country;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Client::where('is_client', 1)->get();
        return view('clients.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $gestores = User::where('access_level_id', 4)->get();
        $gestores = User::all();
        $clientes = Client::all();
        $countries = Country::all();

        return view('clients.create', compact('gestores','clientes','countries'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validamos los campos
        $this->validate($request, [
            'name' => 'required|max:200',
            'admin_user_id' => 'required|exists:admin_user,id',
            'email' => 'required|email:filter',
            'company' => 'nullable|max:200',
            'cif' => 'nullable|max:200',
            'identifier' => 'nullable|max:200',
            'activity' => 'nullable|max:200',
            'address' => 'nullable|max:200',
            'country' => 'nullable|max:200',
            'city' => 'nullable|max:200',
            'province' => 'nullable|max:200',
            'zipcode' => 'nullable|max:200',
            'phone' => 'required',
            'pin' => 'nullable',
        ], [
            'name.required' => 'El nombre es requerido para continuar',
            'admin_user_id.required' => 'El gestor es requerido para continuar',
            'admin_user_id.exists' => 'El gestor debe ser valido para continuar',
            'email.required' => 'El email es requerido para continuar',
            'email.email' => 'El email debe ser un email valido',
            'company.required' => 'El nombre de la empresa a es requerido para continuar',
            'cif.required' => 'El cif es requerido para continuar',
            'identifier.required' => 'La marca es requerida para continuar',
            'activity.required' => 'La actividad es requerida para continuar',
            'address.required' => 'La dirección es requerida para continuar',
            'country.required' => 'El pais es requerido para continuar',
            'city.required' => 'La ciudad es requerida para continuar',
            'province.required' => 'La provincia es requerida para continuar',
            'zipcode.required' => 'El codigo postal es requerido para continuar',
            'phone.required' => 'El telefono es requerido para continuar',
        ]);

        $data = $request->all();
        $data['is_client'] = true;
        $data['is_client'] = true;
        $clienteCreado = Client::create($data);
        if($clienteCreado->pin == null){
            $clienteCreado->pin = rand(100000, 999999);
            $clienteCreado->save();
        }
        // dd($clienteCreado);

        // Validamos si hay contacto asociado
        if (isset($data['newAssociatedContact'])) {
            foreach($data['newAssociatedContact'] as $newContacto){
                // dd(Auth::user()->id);
                $newContacto['admin_user_id'] = Auth::user()->id;
                $newContacto['civil_status_id'] = null;
                $newContacto['phone'] = $newContacto['telephone'];
                $newContacto['client_id'] = $clienteCreado->id;
                $newContacto['privacy_policy_accepted'] = false;
                $newContacto['cookies_accepted'] = false;
                $newContacto['newsletters_sending_accepted'] = false;
                // dd($newContacto);
                $contacto = Contact::create($newContacto);
                if (!$contacto) {
                    return session()->flash('toast', [
                        'icon' => 'error',
                        'mensaje' => "Error en el servidor, intentelo mas tarde."
                    ]);
                }
            }
        }

        // Teléfonos
        if($request->input('numbers')){
            foreach($request->input('numbers') as $key => $value) {
                if($value != ''){
                    $clientPhones = ClientPhone::create(['client_id'=> $clienteCreado->id,'number'=>$value]);
                    $clientPhonesSaved = $clientPhones->save();
                    if (!$clientPhonesSaved) {
                        return session()->flash('toast', [
                            'icon' => 'error',
                            'mensaje' => "Error en el servidor, intentelo mas tarde."
                        ]);
                    }
                }
            }
        }
        // Mails
        if($request->input('mails')){
            foreach($request->input('mails') as $key => $value) {
                if($value != ''){
                    $clientMails = ClientEmail::create(['client_id'=> $clienteCreado->id,'email'=>$value]);
                    $clientMailsSaved = $clientMails->save();
                    if (!$clientMailsSaved) {
                        return session()->flash('toast', [
                            'icon' => 'error',
                            'mensaje' => "Error en el servidor, intentelo mas tarde."
                        ]);
                    }
                }
            }
        }

        // Webs
        if($request->input('webs')){
            foreach($request->input('webs') as $key => $value) {
                if($value != ''){
                    // dd($value);
                    $clientWebs = ClientWeb::create(['client_id'=> $clienteCreado->id,'url'=>$value]);
                    $clientWebsSaved = $clientWebs->save();
                    if (!$clientWebsSaved) {
                        return session()->flash('toast', [
                            'icon' => 'error',
                            'mensaje' => "Error en el servidor, intentelo mas tarde."
                        ]);
                    }
                }
            }
        }
        // dd($data);

        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El cliente se creo correctamente'
        ]);

        return redirect()->route('clientes.show', $clienteCreado->id);
    }


    public function storeFromBudget(Request $request)
    {
        // Validamos los campos
        $this->validate($request, [
            'name' => 'required|max:200',
            'primerApellido' => 'nullable',
            'segundoApellido' => 'nullable',
            'tipoCliente' => 'nullable',
            'admin_user_id' => 'required|exists:admin_user,id',
            'email' => 'required|email:filter',
            'company' => 'nullable|max:200',
            'cif' => 'nullable|max:200',
            'identifier' => 'nullable|max:200',
            'industry' => 'nullable|max:200',
            'activity' => 'nullable|max:200',
            'address' => 'nullable|max:200',
            'country' => 'nullable|max:200',
            'city' => 'nullable|max:200',
            'province' => 'nullable|max:200',
            'zipcode' => 'nullable|max:200',
            'phone' => 'required',
            'pin' => 'nullable',
        ], [
            'name.required' => 'El nombre es requerido para continuar',
            'admin_user_id.required' => 'El gestor es requerido para continuar',
            'admin_user_id.exists' => 'El gestor debe ser valido para continuar',
            'email.required' => 'El email es requerido para continuar',
            'email.email' => 'El email debe ser un email valido',
            'company.required' => 'El nombre de la empresa a es requerido para continuar',
            'cif.required' => 'El cif es requerido para continuar',
            'identifier.required' => 'La marca es requerida para continuar',
            'industry.required' => 'La industria es requerida para continuar',
            'activity.required' => 'La actividad es requerida para continuar',
            'address.required' => 'La dirección es requerida para continuar',
            'country.required' => 'El pais es requerido para continuar',
            'city.required' => 'La ciudad es requerida para continuar',
            'province.required' => 'La provincia es requerida para continuar',
            'zipcode.required' => 'El codigo postal es requerido para continuar',
            'phone.required' => 'El telefono es requerido para continuar',
        ]);

        $data = $request->all();
        $data['is_client'] = true;
        $clienteCreado = Client::create($data);
        // dd($clienteCreado);
        if($clienteCreado->pin == null){
            $clienteCreado->pin = rand(100000, 999999);
            $clienteCreado->save();
        }
        // Validamos si hay contacto asociado
        if (isset($data['newAssociatedContact'])) {
            foreach($data['newAssociatedContact'] as $newContacto){
                // dd(Auth::user()->id);
                $newContacto['admin_user_id'] = Auth::user()->id;
                $newContacto['civil_status_id'] = null;
                $newContacto['phone'] = $newContacto['telephone'];
                $newContacto['client_id'] = $clienteCreado->id;
                $newContacto['privacy_policy_accepted'] = false;
                $newContacto['cookies_accepted'] = false;
                $newContacto['newsletters_sending_accepted'] = false;
                // dd($newContacto);
                $contacto = Contact::create($newContacto);
                if (!$contacto) {
                    return session()->flash('toast', [
                        'icon' => 'error',
                        'mensaje' => "Error en el servidor, intentelo mas tarde."
                    ]);
                }
            }
        }

        // Teléfonos
        if($request->input('numbers')){
            foreach($request->input('numbers') as $key => $value) {
                if($value != ''){
                    $clientPhones = ClientPhone::create(['client_id'=> $clienteCreado->id,'number'=>$value]);
                    $clientPhonesSaved = $clientPhones->save();
                    if (!$clientPhonesSaved) {
                        return session()->flash('toast', [
                            'icon' => 'error',
                            'mensaje' => "Error en el servidor, intentelo mas tarde."
                        ]);
                    }
                }
            }
        }
        // Mails
        if($request->input('mails')){
            foreach($request->input('mails') as $key => $value) {
                if($value != ''){
                    $clientMails = ClientEmail::create(['client_id'=> $clienteCreado->id,'email'=>$value]);
                    $clientMailsSaved = $clientMails->save();
                    if (!$clientMailsSaved) {
                        return session()->flash('toast', [
                            'icon' => 'error',
                            'mensaje' => "Error en el servidor, intentelo mas tarde."
                        ]);
                    }
                }
            }
        }

        // Webs
        if($request->input('webs')){
            foreach($request->input('webs') as $key => $value) {
                if($value != ''){
                    // dd($value);
                    $clientWebs = ClientWeb::create(['client_id'=> $clienteCreado->id,'url'=>$value]);
                    $clientWebsSaved = $clientWebs->save();
                    if (!$clientWebsSaved) {
                        return session()->flash('toast', [
                            'icon' => 'error',
                            'mensaje' => "Error en el servidor, intentelo mas tarde."
                        ]);
                    }
                }
            }
        }
        // dd($data);

        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El cliente se creo correctamente'
        ]);
        return redirect(route('presupuesto.create'))->with('clienteId', $clienteCreado->id);
    }
    public function storeFromPetition(Request $request)
    {
        //Validamos los campos
        $this->validate($request, [
            'name' => 'required|max:200',
            'admin_user_id' => 'required|exists:admin_user,id',
            'email' => 'required|email:filter',
            'phone' => 'required',
        ], [
            'name.required' => 'El nombre es requerido para continuar',
            'admin_user_id.required' => 'El gestor es requerido para continuar',
            'admin_user_id.exists' => 'El gestor debe ser valido para continuar',
            'email.required' => 'El email es requerido para continuar',
            'email.email' => 'El email debe ser un email valido',
            'phone.required' => 'El telefono es requerido para continuar',
        ]);


        $data = $request->all();
        $data['is_client'] = false;
        $clienteCreado = Client::create($data);

        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El lead se creo correctamente'
        ]);
        return redirect(route('peticion.create'))->with('clienteId', $clienteCreado->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cliente = Client::find($id);
        return view('clients.show', compact('cliente'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $gestores = User::all();
        $clientes = Client::all();
        $cliente = Client::find($id);
        $contactos = Contact::where('client_id', $id)->get();
        return view('clients.edit', compact('clientes', 'cliente', 'gestores', 'contactos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validamos los campos
        $this->validate($request, [
            'name' => 'required|max:200',
            'admin_user_id' => 'required|exists:admin_user,id',
            'email' => 'required|email:filter',
            'company' => 'required|max:200',
            'cif' => 'required|max:200',
            'identifier' => 'required|max:200',
            'activity' => 'required|max:200',
            'address' => 'required|max:200',
            'country' => 'required|max:200',
            'city' => 'required|max:200',
            'province' => 'required|max:200',
            'zipcode' => 'required|max:200',
            'phone' => 'required',
            'pin' => 'nullable',
        ], [
            'name.required' => 'El nombre es requerido para continuar',
            'admin_user_id.required' => 'El gestor es requerido para continuar',
            'admin_user_id.exists' => 'El gestor debe ser valido para continuar',
            'email.required' => 'El email es requerido para continuar',
            'email.email' => 'El email debe ser un email valido',
            'company.required' => 'El nombre de la empresa es requerido para continuar',
            'cif.required' => 'El cif es requerido para continuar',
            'identifier.required' => 'La marca es requerida para continuar',
            'activity.required' => 'La actividad es requerida para continuar',
            'address.required' => 'La dirección es requerida para continuar',
            'country.required' => 'El pais es requerido para continuar',
            'city.required' => 'La ciudad es requerida para continuar',
            'province.required' => 'La provincia es requerida para continuar',
            'zipcode.required' => 'El codigo postal es requerido para continuar',
            'phone.required' => 'El telefono es requerido para continuar',
        ]);

        $cliente = Client::findOrFail($id);
        $data = $request->all();
        $data['is_client'] = true;
        $data['privacy_policy_accepted'] = $request->input('privacy_policy_accepted', false); // Valor por defecto

        $cliente->update($data);

        if($cliente->pin == null){
            $cliente->pin = rand(100000, 999999);
            $cliente->save();
        }

        // Validamos si hay contacto asociado
        if (isset($data['newAssociatedContact'])) {
            foreach ($data['newAssociatedContact'] as $newContacto) {
                $newContacto['admin_user_id'] = Auth::user()->id;
                $newContacto['civil_status_id'] = null;
                $newContacto['phone'] = $newContacto['telephone'];
                $newContacto['client_id'] = $cliente->id;
                $newContacto['privacy_policy_accepted'] = false;
                $newContacto['cookies_accepted'] = false;
                $newContacto['newsletters_sending_accepted'] = false;
                $contacto = Contact::updateOrCreate(
                    ['id' => $newContacto['id'] ?? null], // Usa 'id' para encontrar el contacto existente o crea uno nuevo
                    $newContacto
                );
                if (!$contacto) {
                    return session()->flash('toast', [
                        'icon' => 'error',
                        'mensaje' => "Error en el servidor, intentelo mas tarde."
                    ]);
                }
            }
        }

        // Teléfonos
        if ($request->input('numbers')) {
            foreach ($request->input('numbers') as $key => $value) {
                if ($value != '') {
                    $clientPhone = ClientPhone::updateOrCreate(
                        ['client_id' => $cliente->id, 'number' => $value]
                    );
                    if (!$clientPhone) {
                        return session()->flash('toast', [
                            'icon' => 'error',
                            'mensaje' => "Error en el servidor, intentelo mas tarde."
                        ]);
                    }
                }
            }
        }

        // Mails
        if ($request->input('mails')) {
            foreach ($request->input('mails') as $key => $value) {
                if ($value != '') {
                    $clientMail = ClientEmail::updateOrCreate(
                        ['client_id' => $cliente->id, 'email' => $value]
                    );
                    if (!$clientMail) {
                        return session()->flash('toast', [
                            'icon' => 'error',
                            'mensaje' => "Error en el servidor, intentelo mas tarde."
                        ]);
                    }
                }
            }
        }

        // Webs
        if ($request->input('webs')) {
            foreach ($request->input('webs') as $key => $value) {
                if ($value != '') {
                    $clientWeb = ClientWeb::updateOrCreate(
                        ['client_id' => $cliente->id, 'url' => $value]
                    );
                    if (!$clientWeb) {
                        return session()->flash('toast', [
                            'icon' => 'error',
                            'mensaje' => "Error en el servidor, intentelo mas tarde."
                        ]);
                    }
                }
            }
        }

        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El cliente se actualizó correctamente'
        ]);

        return redirect()->route('clientes.show', $cliente->id);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $cliente = Client::find($request->id);

        if (!$cliente) {
            return response()->json([
                'error' => true,
                'mensaje' => "Error en el servidor, intentelo mas tarde."
            ]);
        }

        $cliente->delete();
        return response()->json([
            'error' => false,
            'mensaje' => 'El usuario fue borrado correctamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function logo(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function createFromBudget()
    {
        $gestores = User::all();
        $clientes = Client::all();
        return view('clients.createFromBudget', compact('gestores', 'clientes'));
    }
    public function createFromPetition()
    {
        $gestores = User::all();
        $clientes = Client::all();
        return view('clients.createFromPetition', compact('gestores', 'clientes'));
    }

    public function getGestorFromClient(Request $request){
        $client = Client::find($request->input('client_id'));
        $gestor = $client->gestor->id;
        return response($gestor);
    }
    public function getContactsFromClient(Request $request){
        $client = Client::find($request->input('client_id'));
        $contactos = $client->contacto;
        return response($contactos);
    }

    public function verificarClienteExistente(Request $request)
{
    $clienteExistente = Client::where('name', $request->name)
        ->orWhere('company', $request->company)
        ->first();

    return response()->json($clienteExistente);
}
}
