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
            'tipo_pago'    => 'required|in:Habilitacion,Constancia,Carnet',
            'año_inicio'   => 'required_if:tipo_pago,Habilitacion|nullable|integer|between:2000,2999',
            'año_final'    => 'required_if:tipo_pago,Habilitacion|nullable|integer|gte:año_inicio',
            'mes_inicio'   => 'required_if:tipo_pago,Habilitacion|nullable|integer|between:1,12',
            'mes_final'    => 'required_if:tipo_pago,Habilitacion|nullable|integer|between:1,12',
            'comprobante'  => 'required|string',
            'monto'        => 'required|numeric',
            'fecha_pago'   => 'required_if:tipo_pago,Constancia|nullable|date',
        ]);

        // B. VALIDACIÓN DE SOLAPAMIENTO (Solo para Habilitación)
        if ($request->tipo_pago === 'Habilitacion') {
            $solicitadoInicio = ($request->año_inicio * 100) + $request->mes_inicio;
            $solicitadoFinal = ($request->año_final * 100) + $request->mes_final;

            $existeSolapamiento = Pago::where('agremiado_id', $request->agremiado_id)
                ->where('tipo_pago', 'Habilitacion')
                ->where('estado', 'Pagado') // Ignoramos los anulados
                ->where(function($query) use ($request) {
                    // Verificamos si el rango solicitado choca con registros existentes
                    $query->whereBetween('mes_inicio', [$request->mes_inicio, $request->mes_final])
                        ->orWhereBetween('mes_final', [$request->mes_inicio, $request->mes_final]);
                })
                ->where(function($query) use ($solicitadoInicio, $solicitadoFinal) {
                    // Lógica de rangos: (Inicio1 <= Fin2) AND (Fin1 >= Inicio2)
                    $query->whereRaw('(año_inicio * 100 + mes_inicio) <= ?', [$solicitadoFinal])
                        ->whereRaw('(año_final * 100 + mes_final) >= ?', [$solicitadoInicio]);
                })
                ->exists();

            if ($existeSolapamiento) {
                return redirect()->back()
                    ->withInput() // Mantiene los datos en el modal
                    ->with('error', 'Error: El rango seleccionado se cruza con un pago ya existente.');
            }
        }
        // C. Guardamos el Pago (Habilitacion o Constancia)
        $pago = \App\Models\Pago::create($request->all());

        if ($pago->tipo_pago === 'Carnet') {
            \App\Models\Carnet::create([
                'tipo_tramite'   => $request->tipo_tramite,
                'agremiado_id'   => $pago->agremiado_id,
                'pago_id'        => $pago->id,
                'estado_entrega' => 'Pendiente' // Se crea como pendiente por defecto
            ]);
        }

        // D. ACTUALIZACIÓN DE HABILIDAD (Solo si es Habilitacion)
        // Las constancias no afectan la fecha de vencimiento
        if ($pago->tipo_pago === 'Habilitacion') {
            $this->sincronizarHabilidad($pago->agremiado_id);
        }

        return redirect()->back()->with('success', 'Pago de ' . $pago->tipo_pago . ' registrado exitosamente.');
    }

    public function update(Request $request, Pago $pago)
    {
        $request->merge(['tipo_pago' => $pago->tipo_pago]);
        // 1. Validar los datos que llegan del formulario
        $data = $request->validate([
            'tipo_pago'    => 'required|string',
            'año_inicio'   => 'required_if:tipo_pago,Habilitacion|nullable|integer',
            'año_final'    => 'required_if:tipo_pago,Habilitacion|nullable|integer|gte:año_inicio',
            'mes_inicio'   => 'required_if:tipo_pago,Habilitacion|nullable|integer|between:1,12',
            'mes_final'    => 'required_if:tipo_pago,Habilitacion|nullable|integer|between:1,12',
            'comprobante'  => 'required|string',
            'monto'        => 'required|numeric|min:1',
            'fecha_pago'   => 'required_if:tipo_pago,Constancia|nullable|date',
            'tipo_tramite' => 'required_if:tipo_pago,Carnet|nullable|in:Colegiatura,Duplicado',
        ]);

        // 2. Actualizar el pago directamente
        $pago->update($data);

        // 3. Recalculamos usando la función privada
        if ($pago->tipo_pago === 'Carnet') {
            $pago->carnet()->updateOrCreate(
                ['pago_id' => $pago->id],
                [
                    'agremiado_id' => $pago->agremiado_id,
                    'tipo_tramite' => $request->tipo_tramite, // Guardamos la elección
                ]
            );
        }
        if ($pago->tipo_pago === 'Habilitacion') {
            $this->sincronizarHabilidad($pago->agremiado_id);
        }

        // 4. Redirigir de vuelta a la página anterior con un mensaje de éxito
        return redirect()->back()->with('success', '¡Registro actualizado exitosamente!');
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
                        ->orderBy('año_final', 'desc')
                        ->orderBy('mes_final', 'desc')
                        ->first();

        $agremiado = Agremiado::find($agremiadoId);

        if ($ultimoPago) {
            $nuevaFecha = \Carbon\Carbon::create($ultimoPago->año_final, $ultimoPago->mes_final, 1)
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
        // 1. Lógica de Vigencia Dinámica
        if ($agremiado->es_vitalicio) {
            // Si es vitalicio, la vigencia es el 31 de diciembre del año actual
            $fechaVencimiento = \Carbon\Carbon::now()->month(12)->day(31);
        } else {
            // Si no es vitalicio, usamos su fecha de fin de habilitación normal
            $fechaVencimiento = $agremiado->fin_habilitacion
                ? \Carbon\Carbon::parse($agremiado->fin_habilitacion)
                : null;
        }

        $data = [
            'nombres' => $agremiado->nombres . ' ' . $agremiado->apellidos,
            'matricula' => $agremiado->matricula,
            'estado' => strtoupper($agremiado->estado),
            'dia_fin' => $fechaVencimiento ? $fechaVencimiento->endOfMonth()->format('d'): '--', // {{fines de mes}}
            'mes_fin' => $fechaVencimiento ? $fechaVencimiento->translatedFormat('F'): '---',      // {{Mes final}}
            'año_fin' => $fechaVencimiento ? $fechaVencimiento->format('Y'): '----',               // {{Año}}
            'fecha_hoy' => \Carbon\Carbon::parse($pago->fecha_pago)->translatedFormat('d \d\e F \d\e Y'), // {{Fecha de descarga}}
        ];

        $pdf = Pdf::loadView('pdf_constancia', $data);

        return $pdf->download('Constancia_'.$agremiado->matricula.'.pdf');
    }
}
