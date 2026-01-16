<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Muestra el formulario de login
    public function showLoginForm()
    {
        return view('login'); // Debes crear esta vista
    }

    // Procesa el intento de login
    public function login(Request $request)
    {
        // 1. Valida el campo 'identificador' (que viene del HTML)
        $request->validate([
            'identificador' => 'required|string', 
            'password' => 'required|string',
        ]);

        // 2. Prepara las credenciales para la base de datos
        //    Mapea 'identificador' (del form) a la columna 'user' (de la BD)
        $credentials = [
            'user' => $request->identificador, // <-- ¡ESTA ES LA LÍNEA CLAVE!
            'password' => $request->password,
        ];

        // 3. Intenta autenticar con el guardia 'admin'
        //    Ahora Auth::attempt() recibe ['user' => '...'] y SÍ funcionará
        if (Auth::guard('admin')->attempt($credentials)) {
            
            // 4. Si tiene éxito, regenera la sesión y redirige
            $request->session()->regenerate();
            
            // Redirige al dashboard
            return redirect()->route('admin.dashboard'); 
        }

        // 5. Si falla, regresa con el error correcto
        return back()->withErrors([
            'identificador' => 'Las credenciales proporcionadas no coinciden.',
        ])->onlyInput('identificador');
    }

    // Cierra la sesión del admin
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form');
    }
}