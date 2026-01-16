<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agremiado;
use Carbon\Carbon;

class PublicAgremiadoController extends Controller
{
    public function index()
    {
        return view('public.habilitacion');
    }

    public function buscar(Request $request)
    {
        $request->validate([
            'criterio' => 'required|string|min:4',
        ]);

        // Buscamos por DNI o por Matrícula
        $agremiado = Agremiado::where('dni', $request->criterio)
                              ->orWhere('matricula', $request->criterio)
                              ->first();

        return view('public.habilitacion', compact('agremiado'));
    }
}
