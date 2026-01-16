<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agremiado;
use Carbon\Carbon;

class AgremiadoController extends Controller
{
    public function index()
    {
        Agremiado::where('estado', 'Habilitado')
             ->where('fin_habilitacion', '<=', Carbon::now())
             ->update(['estado' => 'Inhabilitado']);
        // Traemos todos los agremiados que NO estén borrados (SoftDelete automático)
        $agremiados = Agremiado::all(); 
        return view('listadoagremiados', compact('agremiados'));
    }

    public function store(Request $request)
    {
        // 1. Validar los datos
        $data = $request->validate([
            'matricula'        => 'required|string|unique:agremiados',
            'fecha_matricula'  => 'required|date',
            'dni'              => 'required|string|size:8|unique:agremiados',
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
        ]);

        // 2. Juntamos los dos inputs en uno solo
        // array_filter quita los que estén vacíos (si solo puso el celular 1, el 2 se ignora)
        $data['celular'] = array_filter([$request->celular1, $request->celular2]);
        $data['correo']  = array_filter([$request->correo1, $request->correo2]);

        // 3. Guardamos (Laravel lo meterá al único campo de la BD como un texto especial)
        Agremiado::create($data);

        // 3. Redirigir de vuelta a la lista con un mensaje de éxito
        return redirect()->back()->with('success', '¡Agremiado creado exitosamente!');
    }

    public function update(Request $request, Agremiado $agremiado)
    {
        // 1. Validar los datos que llegan del formulario
        $data = $request->validate([
            'matricula'        => 'required|string|unique:agremiados,matricula,' . $agremiado->id,
            'dni'              => 'required|string|size:8|unique:agremiados,dni,' . $agremiado->id,
            'fecha_matricula'  => 'required|date',
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
        ]);

        // 2. Procesar celulares y correos (como hicimos en el store)
        $data['celular'] = array_values(array_filter([$request->celular1, $request->celular2]));
        $data['correo']  = array_values(array_filter([$request->correo1, $request->correo2]));

        // 3. Actualizar la sede en la base de datos
        $agremiado->update($data);

        // 4. Redirigir de vuelta a la página anterior con un mensaje de éxito
       return redirect()->back()->with('success', '¡Agremiado actualizado exitosamente!');
    }

    public function show(Agremiado $agremiado)
    {
        // 1. Buscamos los pagos que pertenecen a este agremiado
        // Usamos el ID del agremiado que ya tenemos en la mano
        $pagos = \App\Models\Pago::where('agremiado_id', $agremiado->id)->get();

        // 2. Enviamos AMBAS cosas a la vista
        // 'agremiado' para los datos de arriba (DNI, Nombre, etc.)
        // 'pagos' para la tabla de abajo (Historial de Cuotas)
        return view('detalleagramiados', compact('agremiado', 'pagos'));
    }

    public function destroy($id)
    {
        // Esto ejecutará el Soft Delete que configuramos en el modelo
        Agremiado::findOrFail($id)->delete();
        return back()->with('success', 'Agremiado eliminado correctamente');
    }
}
