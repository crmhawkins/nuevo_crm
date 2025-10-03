<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function username()
    {
        return 'username';
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Validate the user login attempt
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        
        // Verificar si el usuario está inactivo antes de intentar el login
        $user = \App\Models\Users\User::where('username', $credentials['username'])->first();
        
        if ($user && $user->inactive) {
            return false; // No permitir login si está inactivo
        }
        
        return $this->guard()->attempt(
            $credentials, $request->filled('remember')
        );
    }

    /**
     * After login, check if the user's IP is allowed
     */
    protected function authenticated(Request $request, $user)
    {
        $allowedIds = [1, 2, 8, 58, 52, 124];
        $allowedsIp = ['88.30.82.217', '127.0.0.1'];
        
        if (!in_array($user->id, $allowedIds)) {
            if (!in_array($request->ip(), $allowedsIp)) {
                Auth::logout();

                return redirect()->route('login')->withErrors([
                    'username' => 'Acceso denegado desde esta IP.',
                ]);
            }
        }

        // Si todo va bien, continúa con el login normal
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Handle a failed login attempt
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $credentials = $this->credentials($request);
        $user = \App\Models\Users\User::where('username', $credentials['username'])->first();
        
        if ($user && $user->inactive) {
            return redirect()->back()->withErrors([
                'username' => 'Tu cuenta está inactiva. Contacta al administrador.',
            ]);
        }
        
        return redirect()->back()->withErrors([
            'username' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }
}