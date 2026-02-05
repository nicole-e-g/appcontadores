<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agremiado;
use App\Models\Pago;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class AgremiadoController extends Controller
{
    /**
     * Listado principal optimizado para miles de registros
     */
    public function index(Request $request)
    {
        // Actualización masiva de estados basada en la fecha de fin de habilitación
        Agremiado::where('es_vitalicio', false)
            ->where('estado', 'Habilitado')
            ->where('fin_habilitacion', '<=', Carbon::now())
            ->update(['estado' => 'Inhabilitado']);

        // 1. Procesamiento para DataTable Server-side
        if ($request->ajax()) {
            $query = Agremiado::select(['id', 'matricula', 'dni', 'ruc', 'nombres', 'apellidos', 'estado', 'fin_habilitacion', 'es_vitalicio']);

            return DataTables::of($query)
                // Renderizado de etiquetas de estado con colores
                ->editColumn('estado', function($row) {
                    // Si es vitalicio, badge azul o dorado
                    if ($row->es_vitalicio) {
                        return '<span class="badge bg-info text-dark">VITALICIO</span>';
                    }

                    // Si no, lógica normal de colores
                    if ($row->estado == 'Habilitado') {
                        return '<span class="badge bg-success">HABILITADO</span>';
                    }

                    return '<span class="badge bg-danger">INHABILITADO</span>';
                })
                // Formateo de fecha de habilitación
                ->editColumn('fin_habilitacion', function($row) {
                    return $row->fin_habilitacion
                        ? Carbon::parse($row->fin_habilitacion)->format('d/m/Y')
                        : '<span class="text-muted">No registra</span>';
                })
                // Columna de Acción con Dropdown de CoreUI
                ->addColumn('action', function($row) {
                    $iconPath = asset('vendors/@coreui/icons/svg/free.svg#cil-options');
                    return '
                    <div class="dropdown">
                        <button class="btn btn-transparent p-0" type="button" data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <svg class="icon" style="width:20px; height:20px;">
                                <use xlink:href="'.$iconPath.'"></use>
                            </svg>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="'.route('admin.agremiados.show', $row->id).'">Ver Detalle</a>
                            <button class="dropdown-item" onclick="editarAgremiado('.$row->id.')">Editar Datos</button>
                            <div class="dropdown-divider"></div>
                            <button class="dropdown-item text-danger" onclick="eliminarAgremiado('.$row->id.', \''.$row->nombres.'\')">Eliminar</button>
                        </div>
                    </div>';
                })
                ->rawColumns(['estado', 'fin_habilitacion', 'action'])
                ->make(true);
        }

        // Enviamos la vista vacía; el script se encarga de los datos
        return view('listadoagremiados');
    }

    /**
     * Provee datos JSON para alimentar los modales dinámicos
     */
    public function getDatos($id)
    {
        $agremiado = Agremiado::findOrFail($id);
        return response()->json($agremiado);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'matricula'        => 'required|string|unique:agremiados',
            'sexo'             => 'required|in:F,M', // Valida que solo sea F o M
            'sede'             => 'required|in:Huánuco,Tingo María', // Debe coincidir con tu migración
            'dni'              => 'required|string|size:8|unique:agremiados',
            'ruc'              => 'nullable|string|size:11|unique:agremiados', // Validación RUC
            'fecha_matricula'  => 'required|date',
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
        ]);

        // Mapeo de campos múltiples a arreglos
        $data['celular'] = array_values(array_filter([$request->celular1, $request->celular2]));
        $data['correo']  = array_values(array_filter([$request->correo1, $request->correo2]));

        Agremiado::create($data);

        return redirect()->back()->with('success', '¡Agremiado creado exitosamente!');
    }

    public function update(Request $request, Agremiado $agremiado)
    {
        $data = $request->validate([
            'matricula'        => 'required|string|unique:agremiados,matricula,' . $agremiado->id,
            'dni'              => 'required|string|size:8|unique:agremiados,dni,' . $agremiado->id,
            'ruc'              => 'nullable|string|size:11|unique:agremiados,ruc,' . $agremiado->id,
            'sexo'             => 'required|in:F,M', // Valida que solo sea F o M
            'sede'             => 'required|in:Huánuco,Tingo María',
            'fecha_matricula'  => 'required|date',
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'es_vitalicio'     => 'nullable|boolean',
        ]);

        $data['celular'] = array_values(array_filter([$request->celular1, $request->celular2]));
        $data['correo']  = array_values(array_filter([$request->correo1, $request->correo2]));

        $data['es_vitalicio'] = $request->has('es_vitalicio');

        if ($data['es_vitalicio']) {
            $data['estado'] = 'Vitalicio';
            $data['fin_habilitacion'] = null; // Al ser vitalicio, eliminamos la fecha de fin de habilitación
        } else {
            // Si dejas de ser vitalicio, se recalcula su estado aquí
            // o dejar que el sistema lo ponga como Inhabilitado por defecto
            if ($agremiado->es_vitalicio) {
                $data['estado'] = 'Inhabilitado';
            }
        }

        $agremiado->update($data);

        return redirect()->back()->with('success', '¡Agremiado actualizado exitosamente!');
    }

    public function show(Agremiado $agremiado)
    {
        // Obtención del historial de pagos (Paginación interna de Eloquent)
        $pagos = $agremiado->pagos()->orderBy('id', 'desc')->get();

        // Consulta corregida para evitar el error de Collection::orderBy
        $ultimo = Pago::where('agremiado_id', $agremiado->id)
            ->where('tipo_pago', 'Habilitacion')
            ->where('estado', 'Pagado')
            ->orderBy('año_final', 'desc')
            ->orderBy('mes_final', 'desc')
            ->first();

        // Lógica de cálculo del siguiente periodo de pago
        $siguienteMes = $ultimo ? ($ultimo->mes_final + 1) : 1;
        $siguienteAño = $ultimo ? $ultimo->año_final : date('Y');

        if ($siguienteMes > 12) {
            $siguienteMes = 1;
            $siguienteAño++;
        }

        return view('detalleagramiados', compact('agremiado', 'pagos', 'siguienteMes', 'siguienteAño'));
    }

    public function destroy($id)
    {
        Agremiado::findOrFail($id)->delete();
        return back()->with('success', 'Agremiado eliminado correctamente');
    }
}
