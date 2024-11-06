<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\RequestStore;
use Illuminate\Foundation\Auth\RegistersUsers;

use Illuminate\Http\Request;
use App\Models\Users\User;
use App\Models\Users\UserAccessLevel;
use App\Models\Users\UserDepartament;
use App\Models\Users\UserPosition;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;


    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('guest');
    // }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // session()->flash('toast','Esto es una prueba');
        $usuarios = User::where('inactive', 0)->get();
        return view('users.index', compact('usuarios'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admin_user'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $role = UserAccessLevel::all();
        $departamentos = UserDepartament::all();
        $posiciones = UserPosition::all();

        return view('users.create', compact('role','departamentos', 'posiciones'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|max:200',
            'surname' => 'required|max:200',
            'username' => 'required|unique:admin_user',
            'email' => 'required|email:filter',
            'password' => 'required|min:8',
            'access_level_id' => 'required|exists:admin_user_access_level,id',
            'admin_user_department_id' => 'required|exists:admin_user_department,id',
            'admin_user_position_id' => 'required|exists:admin_user_position,id',
        ], [
            'name.required' => 'El nombre es requerido para continuar',
            'surname.required' => 'Los apellidos son requeridos para continuar',
            'username.required' => 'El nombre de usuario es requerido para continuar',
            'username.unique' => 'El nombre de usuario ya existe',
            'email.required' => 'El email es requerido para continuar',
            'email.email' => 'El email debe ser un email valido',
            'password.required' => 'El password es requerido para continuar',
            'password.min' => 'El password debe contener al menos 8 caracteres para continuar',
            'access_level_id.exists' => 'El rol debe ser valido y es requerido para continuar',
            'admin_user_department_id.exists' => 'El departamento debe ser valido y es requerido para continuar',
            'admin_user_position_id.exists' => 'La posicion debe ser valido y es requerido para continuar',
        ]);

          $data = $request->all();

          $data['role'] = 'Admin';
          $data['inactive'] = 0;
          $data['password'] = Hash::make($data['password']);

        //   dd($data);
          $usuarioCreado = User::create($data);

        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El usuario se creo correctamente'
        ]);

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $usuario = User::find($id);
        if(is_null($usuario)){

        }

        return view('users.show', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El usuario no existe'
            ]);
            return redirect()->route('users.index');
        }
        $role = UserAccessLevel::all();
        $departamentos = UserDepartament::all();
        $posiciones = UserPosition::all();
        return view('users.edit', compact('usuario', 'role', 'departamentos', 'posiciones'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required|max:200',
            'surname' => 'required|max:200',
            'username' => 'required|unique:admin_user,username,' . $id, // Permitir el mismo username si pertenece al usuario actual
            'email' => 'required|email:filter',
            'password' => 'nullable|min:8',
            'access_level_id' => 'required|exists:admin_user_access_level,id',
            'admin_user_department_id' => 'required|exists:admin_user_department,id',
            'admin_user_position_id' => 'required|exists:admin_user_position,id',
        ], [
            'name.required' => 'El nombre es requerido para continuar',
            'surname.required' => 'Los apellidos son requeridos para continuar',
            'username.required' => 'El nombre de usuario es requerido para continuar',
            'username.unique' => 'El nombre de usuario ya existe',
            'email.required' => 'El email es requerido para continuar',
            'email.email' => 'El email debe ser un email valido',
            'password.required' => 'El password es requerido para continuar',
            'password.min' => 'El password debe contener al menos 8 caracteres para continuar',
            'access_level_id.exists' => 'El rol debe ser valido y es requerido para continuar',
            'admin_user_department_id.exists' => 'El departamento debe ser valido y es requerido para continuar',
            'admin_user_position_id.exists' => 'La posicion debe ser valido y es requerido para continuar',
        ]);

        $user = User::findOrFail($id); // Buscar el usuario existente

        $data = $request->all();

        // Solo actualizar la contraseña si se proporciona una nueva
        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['role'] = 'Admin';
        $data['inactive'] = 0;

        $user->update($data);

        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El usuario se actualizó correctamente'
        ]);

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json([
                'error' => true,
                'mensaje' => "Error en el servidor, intentelo mas tarde."
            ]);
        }

        $user->inactive = 1;
        $user->save();

        return response()->json([
            'error' => false,
            'mensaje' => 'El usuario fue borrado correctamente'
        ]);
    }

    public function avatar(Request $request, $id)
    {
        $file = $request->file('filepond');
        $new_name = uniqid(rand(), true).'.'.strtolower($file->getClientOriginalExtension());
        $result = Storage::disk('public')->put('avatars/'.$new_name, File::get($file));
        $usuario = User::find($id);
        if (Storage::disk('public')->exists('avatars/'.$usuario->image)) {
            Storage::disk('public')->delete('avatars/'.$usuario->image);
        }
            $usuario['image'] = $new_name;
            // dd($usuario);
            $usuario->save();

            session()->flash('toast', [
                'icon' => 'success',
                'mensaje' => 'La imagen del usuario se actualizo correctamente'
            ]);

        return $new_name;

    }

    public function base64ToImage($base64_string, $output_file) {
        $file = fopen($output_file, "wb");

        $data = explode(',', $base64_string);

        fwrite($file, base64_decode($data[1]));
        fclose($file);

        return $output_file;
    }

    public function saveThemePreference(Request $request)
    {
        $user = auth()->user();
        $user->is_dark = $request->input('is_dark');
        // dd($request->input('is_dark'));
        $user->save();

        return response()->json(['message' => 'Preferencia de tema guardada con éxito.']);
    }

}
