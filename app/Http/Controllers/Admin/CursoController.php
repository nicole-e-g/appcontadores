<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CursoController extends Controller
{
    public function create()
    {
        return view('crearcurso');
    }

    public function edit(Curso $curso)
    {
        // Retornamos la vista de edición con los datos del curso
        return view('editcursos', compact('curso'));
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $hoy = now()->format('Y-m-d');
            $query = Curso::query();

            // Lógica de filtrado por estados
            if ($request->estado == 'vigentes') {
                $query->where('estado', 'Activo')->where('fecha_fin', '>=', $hoy);
            } elseif ($request->estado == 'terminadas') {
                $query->where('estado', 'Activo')->where('fecha_fin', '<', $hoy);
            } elseif ($request->estado == 'anulados') {
                $query->where('estado', 'Anulado');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    // Rutas para las acciones
                    $editUrl = route('admin.cursos.edit', $row->id);
                    $showUrl = route('admin.cursos.edit', $row->id); // Usamos edit como placeholder

                    $btn = '<div class="dropdown">';

                    // Botón disparador (los tres puntos verticales)
                    // Usamos 'cil-options' y lo rotamos 90 grados para que se vea vertical
                    $btn .= '<button class="btn btn-link text-dark text-decoration-none p-0" type="button" data-coreui-toggle="dropdown" aria-expanded="false">';
                    $btn .= '<i class="cil-options" style="transform: rotate(90deg);"></i>';
                    $btn .= '</button>';

                    // Menú con las opciones
                    $btn .= '<ul class="dropdown-menu dropdown-menu-end">'; // dropdown-menu-end alinea el menú a la derecha

                    // Opción 1: Ver Detalle
                    $btn .= '<li><a class="dropdown-item" href="'.$showUrl.'"><i class="cil-magnifying-glass me-2"></i> Ver Detalle</a></li>';

                    // Opción 2: Editar Datos
                    $btn .= '<li><a class="dropdown-item" href="'.$editUrl.'"><i class="cil-pencil me-2"></i> Editar Datos</a></li>';

                    // Separador
                    $btn .= '<li><hr class="dropdown-divider"></li>';

                    // Opción 3: Anular (Solo si está activo)
                    if ($row->estado == 'Activo') {
                        // Usamos la función anularCurso(id) que ya tienes en tu JavaScript
                        $btn .= '<li><button class="dropdown-item text-danger" onclick="anularCurso('.$row->id.')"><i class="cil-ban me-2"></i> Anular</button></li>';
                    } else {
                        // Opcional: Si ya está anulado, mostrar una opción deshabilitada o un texto
                        $btn .= '<li><span class="dropdown-item disabled text-muted"><i class="cil-check-circle me-2"></i> Ya Anulado</span></li>';
                    }

                    $btn .= '</ul></div>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('listacursos');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_curso' => 'required|string|max:255',
            'organizador' => 'required|string|max:255',
            'modalidad' => 'required|in:Presencial,Virtual',
            'horas_lectivas' => 'required|integer|min:1',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'ponente_nombres' => 'required|string|max:255',
            'ponente_especialidad' => 'required|string|max:255',
            'imagen_curso' => 'nullable|image|max:2048', // Max 2MB
            'modelo_certificado' => 'nullable|mimes:pdf,jpg,png|max:5120', // Max 5MB
            'firma1' => 'nullable|image|max:1024',
            'firma2' => 'nullable|image|max:1024',
        ]);

        $data = $request->all();

        // Manejo de archivos: Se guardan en el disco 'public' para ser accesibles
        if ($request->hasFile('imagen_curso')) {
            $data['imagen_path'] = $request->file('imagen_curso')->store('cursos/fotos', 'public');
        }
        if ($request->hasFile('modelo_certificado')) {
            $data['certificado_path'] = $request->file('modelo_certificado')->store('cursos/modelos', 'public');
        }
        if ($request->hasFile('firma1')) {
            $data['firma1_path'] = $request->file('firma1')->store('cursos/firmas', 'public');
        }
        if ($request->hasFile('firma2')) {
            $data['firma2_path'] = $request->file('firma2')->store('cursos/firmas', 'public');
        }

        Curso::create($data);

        return redirect()->back()->with('success', 'La capacitación se registró correctamente en Colegiaturas.');
    }

    public function update(Request $request, Curso $curso)
    {
        // 1. Validaciones (mismas que en store, pero archivos opcionales)
        $request->validate([
            'nombre_curso' => 'required|string|max:255',
            'imagen_curso' => 'nullable|image|max:2048',
            // ... resto de validaciones ...
        ]);

        $data = $request->all();

        // 2. Lógica de reemplazo de archivos
        $archivos = [
            'imagen_curso' => 'imagen_path',
            'modelo_certificado' => 'certificado_path',
            'firma1' => 'firma1_path',
            'firma2' => 'firma2_path'
        ];

        foreach ($archivos as $input => $columna) {
            if ($request->hasFile($input)) {
                // Borramos el archivo viejo si existe en el disco public
                if ($curso->$columna) {
                    \Storage::disk('public')->delete($curso->$columna);
                }
                // Guardamos el nuevo y actualizamos la ruta
                $data[$columna] = $request->file($input)->store('cursos/'.explode('_', $columna)[0], 'public');
            }
        }

        // 3. Actualización (Laravel Auditing registrará el "antes" y "después")
        $curso->update($data);

        return redirect()->route('admin.cursos.index')->with('success', 'Capacitación actualizada correctamente.');
    }

    public function anular(Curso $curso)
    {
        try {
            $curso->update(['estado' => 'Anulado']); // Solo cambiamos el estado

            return response()->json([
                'status' => 'success',
                'message' => 'La capacitación ha sido anulada correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al anular: ' . $e->getMessage()
            ], 500);
        }
    }
}
