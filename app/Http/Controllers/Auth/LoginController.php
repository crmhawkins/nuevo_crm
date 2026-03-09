<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        Log::info('🔐 Intento de inicio de sesión iniciado', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'username' => $request->input('username'),
            'remember' => $request->filled('remember'),
            'timestamp' => now()->toDateTimeString()
        ]);

        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            Log::warning('⚠️ Demasiados intentos de login', [
                'ip' => $request->ip(),
                'username' => $request->input('username')
            ]);
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            Log::info('✅ Login exitoso - método attemptLogin retornó true', [
                'ip' => $request->ip(),
                'username' => $request->input('username')
            ]);
            
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        Log::warning('❌ Login fallido - método attemptLogin retornó false', [
            'ip' => $request->ip(),
            'username' => $request->input('username')
        ]);

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login attempt
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        
        Log::info('🔍 Validando credenciales de login', [
            'username' => $credentials['username'] ?? 'no proporcionado',
            'ip' => $request->ip(),
            'has_password' => !empty($credentials['password'])
        ]);
        
        // Verificar si el usuario está inactivo antes de intentar el login
        $user = \App\Models\Users\User::where('username', $credentials['username'])->first();
        
        if (!$user) {
            Log::warning('⚠️ Usuario no encontrado en la base de datos', [
                'username' => $credentials['username'] ?? 'no proporcionado',
                'ip' => $request->ip()
            ]);
        } else {
            Log::info('👤 Usuario encontrado', [
                'user_id' => $user->id,
                'username' => $user->username,
                'inactive' => $user->inactive,
                'ip' => $request->ip()
            ]);
        }
        
        if ($user && $user->inactive) {
            Log::error('🚫 Login bloqueado: Usuario inactivo', [
                'user_id' => $user->id,
                'username' => $user->username,
                'ip' => $request->ip()
            ]);
            return false; // No permitir login si está inactivo
        }
        
        Log::info('🔐 Intentando autenticar con guard', [
            'username' => $credentials['username'] ?? 'no proporcionado',
            'remember' => $request->filled('remember'),
            'ip' => $request->ip()
        ]);
        
        $attemptResult = $this->guard()->attempt(
            $credentials, $request->filled('remember')
        );
        
        Log::info($attemptResult ? '✅ Autenticación exitosa' : '❌ Autenticación fallida', [
            'username' => $credentials['username'] ?? 'no proporcionado',
            'ip' => $request->ip(),
            'result' => $attemptResult
        ]);
        
        return $attemptResult;
    }

    /**
     * After login, check if the user's IP is allowed
     */
    protected function authenticated(Request $request, $user)
    {
        Log::info('✅ Usuario autenticado exitosamente', [
            'user_id' => $user->id,
            'username' => $user->username,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $allowedIds = [1, 2, 8, 58, 52, 124];
        $allowedsIp = ['88.30.82.217', '127.0.0.1'];
        
        Log::info('🔍 Verificando permisos de IP', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'is_allowed_id' => in_array($user->id, $allowedIds),
            'is_allowed_ip' => in_array($request->ip(), $allowedsIp)
        ]);
        
        if (!in_array($user->id, $allowedIds)) {
            if (!in_array($request->ip(), $allowedsIp)) {
                Log::error('🚫 Acceso denegado: IP no permitida', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'ip' => $request->ip(),
                    'allowed_ips' => $allowedsIp,
                    'allowed_ids' => $allowedIds
                ]);
                
                Auth::logout();

                return redirect()->route('login')->withErrors([
                    'username' => 'Acceso denegado desde esta IP.',
                ]);
            }
        }

        Log::info('✅ Login completado exitosamente - Redirigiendo', [
            'user_id' => $user->id,
            'username' => $user->username,
            'redirect_path' => $this->redirectPath(),
            'intended_url' => $request->session()->get('url.intended')
        ]);

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
            Log::error('🚫 Login fallido: Usuario inactivo', [
                'user_id' => $user->id,
                'username' => $user->username,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'reason' => 'usuario_inactivo'
            ]);
            
            return redirect()->back()->withErrors([
                'username' => 'Tu cuenta está inactiva. Contacta al administrador.',
            ]);
        }
        
        if ($user) {
            Log::warning('❌ Login fallido: Credenciales incorrectas', [
                'user_id' => $user->id,
                'username' => $user->username,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'reason' => 'credenciales_incorrectas'
            ]);
        } else {
            Log::warning('❌ Login fallido: Usuario no existe', [
                'username' => $credentials['username'] ?? 'no proporcionado',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'reason' => 'usuario_no_existe'
            ]);
        }
        
        return redirect()->back()->withErrors([
            'username' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }
}