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
     * After login, check if the user's IP is allowed
     */
    protected function authenticated(Request $request, $user)
    {
        $allowedIds = [1, 2, 8, 58];
        $allowedIp = '88.30.82.217';

        if (!in_array($user->id, $allowedIds)) {
            if ($request->ip() !== $allowedIp) {
                Auth::logout(); // Cierra la sesión

                return redirect()->route('login')->withErrors([
                    'username' => 'Acceso denegado desde esta IP.',
                ]);
            }
        }

        // Si todo va bien, continúa con el login normal
        return redirect()->intended($this->redirectPath());
    }
}