<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParticipanteCongreso;

class CongresoController extends Controller
{
    // Muestra el formulario de acceso
    public function mostrarLogin()
    {
        return view('congreso.logincongreso');
    }

    // Valida el acceso (DNI como usuario y contraseña)
    public function acceder(Request $request)
    {
        $request->validate([
            'usuario' => 'required',
            'password' => 'required'
        ], [
            'usuario.required' => 'El número de documento es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.'
        ]);

        // Verificamos que el usuario y la contraseña sean idénticos y existan
        if ($request->usuario !== $request->password) {
            return back()->withErrors(['error' => 'La contraseña debe ser igual a tu número de documento.']);
        }

        $participante = ParticipanteCongreso::where('dni', $request->usuario)->first();

        if (!$participante) {
            return back()->withErrors(['error' => 'El número de documento no se encuentra registrado en el Congreso.']);
        }

        // Guardamos temporalmente el ID en la sesión para simular la autenticación
        session(['participante_id' => $participante->id]);

        return redirect()->route('congreso.perfil');
    }

    // Muestra los datos del participante
    public function perfil()
    {
        if (!session()->has('participante_id')) {
            return redirect()->route('congreso.login');
        }

        $participante = ParticipanteCongreso::find(session('participante_id'));
        return view('congreso.datos', compact('participante'));
    }

    // Actualiza los datos permitidos
    public function actualizar(Request $request)
    {
        if (!session()->has('participante_id')) {
            return redirect()->route('congreso.login');
        }

        $participante = ParticipanteCongreso::find(session('participante_id'));

        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'celular' => 'nullable|string|max:9',
        ]);

        // Actualizamos solo lo permitido, sin la modalidad
        $participante->update([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'celular' => $request->celular,
        ]);

        return back()->with('success', '¡Tus datos se actualizaron correctamente para la emisión de tu certificado!');
    }

    // Cierra la sesión del participante
    public function salir()
    {
        session()->forget('participante_id');
        return redirect()->route('congreso.login');
    }
}
