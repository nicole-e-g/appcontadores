<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\AgremiadosExport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    //
    public function index() {
        return view('reportes');
    }

    public function exportExcel() {
        return Excel::download(new AgremiadosExport, 'Reporte_Colegiados_' . date('d-m-Y') . '.xlsx');
    }
}
