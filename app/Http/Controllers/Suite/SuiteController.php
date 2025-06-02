<?php

namespace App\Http\Controllers\Suite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Suite\Suite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use \Carbon\Carbon;

class SuiteController extends Controller
{
    public function index()
    {
        $suites = Suite::all();
        return view('suite.index', compact('suites'));
    }

    public function indexJustificaciones()
    {
        return view('suite.index_gestor');
    }

    public function create()
    {
        return view('suite.create');
    }

    public function edit()
    {
        $suites = Suite::all();
        return view('suite.edit', compact('suites'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'user' => 'required|string|max:255|unique:suite,user',
                'password' => 'required|string|min:6',
            ]);

            Suite::create([
                'user' => $request->user,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado correctamente'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $suite = Suite::find($id);

        if (!$suite) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $request->validate([
            'user' => 'required|string|max:255|unique:suite,user,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        $suite->user = $request->user;

        if (!empty($request->password)) {
            $suite->password = Hash::make($request->password);
        }

        try {
            $suite->save();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $suite = Suite::find($id);

        if (!$suite) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $suite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ], 200);
    }

    public function login(Request $request)
    {
        $suites = Suite::all();

        $data = [
            'user' => $request->user,
            'password' => $request->password
        ];

        foreach ($suites as $suite) {
            if ($suite->user === $data['user'] && Hash::check($data['password'], $suite->password)) {
                $suite->logged_at = Carbon::now()->addHours(2);
                $suite->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Login exitoso'
                ], 200);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Usuario o contraseña incorrectos'
        ], 404);
    }
}

