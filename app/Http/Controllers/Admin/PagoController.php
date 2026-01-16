<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agremiado;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PagoController extends Controller
{
    public function store(Request $request)
    {
        // A. Validamos que todo esté correcto según tu DB
        $request->validate([
            'agremiado_id' => 'required|exists:agremiados,id',
            'tipo_pago'    => 'required|in:Habilitacion,Constancia',
            'año'          => 'required_if:tipo_pago,Habilitacion|nullable|integer',
            'mes_inicio'   => 'required_if:tipo_pago,Habilitacion|nullable|integer|between:1,12',
            'mes_final'    => 'required_if:tipo_pago,Habilitacion|nullable|integer|between:1,12',
            'comprobante'  => 'required|string',
            'monto'        => 'required|numeric',
            'fecha_pago'   => 'required_if:tipo_pago,Constancia|nullable|date',
        ]);

        // B. Guardamos el Pago (Habilitacion o Constancia)
        $pago = \App\Models\Pago::create($request->all());

        // C. ACTUALIZACIÓN DE HABILIDAD (Solo si es Habilitacion)
        // Las constancias no afectan la fecha de vencimiento
        if ($pago->tipo_pago === 'Habilitacion') {
            $this->sincronizarHabilidad($pago->agremiado_id);
        }

        return redirect()->back()->with('success', 'Pago de ' . $pago->tipo_pago . ' registrado exitosamente.');
    }

    public function update(Request $request, Pago $pago)
    {
        // 1. Validar los datos que llegan del formulario
        $data = $request->validate([
            'mes_inicio'   => 'required|integer|between:1,12',
            'mes_final'    => 'required|integer|between:1,12',
            'comprobante'  => 'required|string',
            'monto'        => 'required|numeric|min:0',
        ]);

        // 2. Actualizar el pago directamente
        $pago->update($data);
    
        // 3. Recalculamos usando la función privada
        $this->sincronizarHabilidad($pago->agremiado_id);

        // 4. Redirigir de vuelta a la página anterior con un mensaje de éxito
        return redirect()->back()->with('success', '¡Pago actualizado y vigencia recalculada!');
    }

    public function anular(Request $request, Pago $pago)
    {
        $request->validate([
            'motivo_anulacion' => 'required|string|min:5'
        ]);

        // 1. Registrar la anulación con el usuario autenticado
        $pago->update([
            'estado' => 'Anulado',
            'anulado_por' => Auth::guard('admin')->user()->nombre . ' ' . Auth::guard('admin')->user()->apellido,
            'motivo_anulacion' => $request->motivo_anulacion,
            'fecha_anulacion' => now(),
        ]);

        // 2. Recalcular la fecha de fin de habilitación
        $this->sincronizarHabilidad($pago->agremiado_id);

        return redirect()->back()->with('success', 'El pago ha sido anulado y la vigencia actualizada.');
    }

    // Función privada para reutilizar en store, update y anular
    private function sincronizarHabilidad($agremiadoId)
    {
        // IMPORTANTE: Solo tomamos en cuenta pagos que NO estén anulados
        $ultimoPago = Pago::where('agremiado_id', $agremiadoId)
                        ->where('tipo_pago', 'Habilitacion')
                        ->where('estado', 'Pagado')
                        ->orderBy('año', 'desc')
                        ->orderBy('mes_final', 'desc')
                        ->first();

        $agremiado = Agremiado::find($agremiadoId);

        if ($ultimoPago) {
            $nuevaFecha = \Carbon\Carbon::create($ultimoPago->año, $ultimoPago->mes_final, 1)
                                ->endOfMonth();

            $agremiado->update([
                'fin_habilitacion' => $nuevaFecha->format('Y-m-d'),
                'estado' => 'Habilitado'
            ]);
        } else {
            $agremiado->update([
                'fin_habilitacion' => null,
                'estado' => 'Inhabilitado'
            ]);
        }
    }

    public function descargarPDF(Pago $pago)
    {
        $agremiado = $pago->agremiado;
        $fechaVencimiento = \Carbon\Carbon::parse($agremiado->fin_habilitacion);

        $data = [
            'nombres' => $agremiado->nombres . ' ' . $agremiado->apellidos,
            'matricula' => $agremiado->matricula,
            'estado' => strtoupper($agremiado->estado),
            'dia_fin' => $fechaVencimiento->endOfMonth()->format('d'), // {{fines de mes}}
            'mes_fin' => $fechaVencimiento->translatedFormat('F'),      // {{Mes final}}
            'año_fin' => $fechaVencimiento->format('Y'),               // {{Año}}
            'fecha_hoy' => now()->translatedFormat('d \d\e F \d\e Y'), // {{Fecha de descarga}}
        ];

        $pdf = Pdf::loadView('pdf_constancia', $data);
        
        return $pdf->download('Constancia_'.$agremiado->matricula.'.pdf');
    }
}
