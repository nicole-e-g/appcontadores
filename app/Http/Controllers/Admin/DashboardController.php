<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agremiado;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas por estado
        $totalHabilitados   = Agremiado::where('estado', 'Habilitado')->where('es_vitalicio', 0)
            ->count();;
        $totalInhabilitados = Agremiado::where('estado', 'Inhabilitado')->count();
        $totalVitalicios    = Agremiado::where('es_vitalicio', '1')->count();

        // Estadísticas por género
        // Nota: Asegúrate que los strings coincidan con lo que guardas ('Varón'/'Mujer' o 'Masculino'/'Femenino')
        $totalMujeres = Agremiado::where('sexo', 'F')->count();
        $totalVarones = Agremiado::where('sexo', 'M')->count();

        // Obtenemos las habilitaciones por mes para el año actual
        $habilitacionesPorMes = Pago::where('tipo_pago', 'Habilitacion')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->all();

        // Llenamos los meses que no tienen datos con 0
        $dataGrafico = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataGrafico[] = $habilitacionesPorMes[$i] ?? 0;
        }

        $sedesStats = \App\Models\Agremiado::select('sede', \DB::raw('count(*) as total'))
            ->whereNotNull('sede') // Evitamos campos vacíos
            ->groupBy('sede')
            ->get();

        return view('index', compact(
            'totalHabilitados',
            'totalInhabilitados',
            'totalVitalicios',
            'totalMujeres',
            'totalVarones',
            'dataGrafico',
            'sedesStats',
        ));
    }
}
