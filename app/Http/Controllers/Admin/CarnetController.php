<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carnet;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class CarnetController extends Controller
{
    public function index(Request $request)
    {
        return view('listadocarnets');
    }

    public function entregar(Carnet $carnet)
    {
        // Actualizamos el estado y registramos el momento exacto de la entrega
        $carnet->update([
            'estado_entrega' => 'Entregado',
            'fecha_entrega'  => now(),
            'entregado_por'  => Auth::user()->nombre . ' ' . Auth::user()->apellido
        ]);

        return redirect()->back()->with('success', 'El carnet ha sido marcado como entregado con éxito.');
    }

    public function getCarnetsData(Request $request)
    {
        // Filtrado dinámico según la pestaña (Pendiente o Entregado)
        $query = Carnet::where('estado_entrega', $request->estado)
            ->whereHas('pago', function($q) {
                $q->where('estado', 'Pagado'); // Filtro de seguridad
            })
            ->with(['agremiado', 'pago']);

        return DataTables::of($query)
            ->addColumn('accion', function($row){
                if($row->estado_entrega == 'Pendiente'){
                    // Botón para procesar la entrega física en la sede de Huánuco
                    return '<form action="'.route('admin.carnets.entregar', $row).'" method="POST" style="display:inline;">
                                '.csrf_field().' '.method_field('PUT').'
                                <button type="submit" class="btn btn-sm btn-success">Entregar</button>
                            </form>';
                }
                return '<span class="text-muted small">Entregado</span>';
            })
            ->rawColumns(['accion'])
            ->make(true);
    }
}
