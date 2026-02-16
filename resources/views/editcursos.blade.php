@extends('layouts.admin')

@section('content')
    <style>
        /* Corrección para que la vista previa no tape el menú Account */
        .sticky-preview {
            top: 100px !important;
            z-index: 5 !important;
        }
        #certificado-preview-container {
            background-color: #f0f0f0;
            border: 2px dashed #b2b2b2;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            {{-- Columna Izquierda: Formulario de Edición --}}
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0 fw-bold text-primary">Editar Capacitación: {{ $curso->nombre_curso }}</h5>
                    </div>
                    <form action="{{ route('admin.cursos.update', $curso->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            {{-- 1. Información General --}}
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">1. Información General</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Nombre del curso</label>
                                    <input type="text" name="nombre_curso" class="form-control" value="{{ $curso->nombre_curso }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Organizador</label>
                                    <input type="text" name="organizador" class="form-control" value="{{ $curso->organizador }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Modalidad</label>
                                    <select name="modalidad" class="form-select" required>
                                        <option value="Presencial" {{ $curso->modalidad == 'Presencial' ? 'selected' : '' }}>Presencial</option>
                                        <option value="Virtual" {{ $curso->modalidad == 'Virtual' ? 'selected' : '' }}>Virtual</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Horas Lectivas</label>
                                    <input type="number" name="horas_lectivas" class="form-control" value="{{ $curso->horas_lectivas }}" required>
                                </div>
                            </div>

                            {{-- 2. Fechas y Ponente --}}
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">2. Fechas y Ponente</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small">Fecha de Inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control" value="{{ $curso->fecha_inicio }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small">Fecha de Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control" value="{{ $curso->fecha_fin }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nombres del Ponente</label>
                                    <input type="text" name="ponente_nombres" class="form-control" value="{{ $curso->ponente_nombres }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Especialidad o Profesión</label>
                                    <input type="text" name="ponente_especialidad" class="form-control" value="{{ $curso->ponente_especialidad }}" required>
                                </div>
                            </div>

                            {{-- 3. Archivos (Solo se suben si se desean cambiar) --}}
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">3. Actualizar Archivos (Opcional)</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Imagen del Curso</label>
                                    <input type="file" name="imagen_curso" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-danger">Nuevo Modelo Certificado</label>
                                    <input type="file" name="modelo_certificado" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-danger">Nueva Firma 1</label>
                                    <input type="file" name="firma1" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-danger">Nueva Firma 2</label>
                                    <input type="file" name="firma2" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-end py-3">
                            <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4">Actualizar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Columna Derecha: Previsualización en Tiempo Real --}}
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 sticky-top sticky-preview">
                    <div class="card-header bg-dark text-white py-3">
                        <h5 class="mb-0">Previsualización del Certificado</h5>
                    </div>
                    <div class="card-body text-center">
                        <div id="certificado-preview-container"
                             style="position: relative; width: 100%; aspect-ratio: 1.41 / 1; border: 1px solid #ddd; background: #fff; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">

                            {{-- Carga inicial de imágenes desde el storage --}}
                            <img id="prev-modelo" src="{{ $curso->certificado_path ? asset('storage/'.$curso->certificado_path) : '#' }}"
                                 style="width: 100%; height: 100%; object-fit: contain; {{ $curso->certificado_path ? '' : 'display:none;' }}">

                            <div id="prev-texto-curso" style="position: absolute; width: 100%; text-align: center; top: 35%; font-weight: bold; font-size: 1.2vw; padding: 0 10%; z-index: 10;"></div>
                            <div id="prev-texto-ponente" style="position: absolute; width: 100%; text-align: center; top: 50%; font-size: 0.9vw; color: #555; z-index: 10;"></div>
                            <div id="prev-texto-horas" style="position: absolute; width: 100%; text-align: center; bottom: 25%; font-weight: bold; font-size: 0.8vw; z-index: 10;"></div>

                            <img id="prev-firma1" src="{{ $curso->firma1_path ? asset('storage/'.$curso->firma1_path) : '#' }}"
                                 style="position: absolute; bottom: 12%; left: 15%; width: 15%; z-index: 5; {{ $curso->firma1_path ? '' : 'display:none;' }}">

                            <img id="prev-firma2" src="{{ $curso->firma2_path ? asset('storage/'.$curso->firma2_path) : '#' }}"
                                 style="position: absolute; bottom: 12%; right: 15%; width: 15%; z-index: 5; {{ $curso->firma2_path ? '' : 'display:none;' }}">
                        </div>
                        <p class="small text-muted mt-3">Previsualizando cambios en el sistema Colegiaturas.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Función para actualizar todos los campos al cargar
            function inicializarPreview() {
                $('input[name="nombre_curso"], input[name="ponente_nombres"], input[name="horas_lectivas"]').trigger('input');
            }

            // 1. Sincronización de Textos
            $('input[name="nombre_curso"]').on('input', function() {
                let val = $(this).val().toUpperCase();
                $('#prev-texto-curso').text(val || '[NOMBRE DEL CURSO]');
            });

            $('input[name="ponente_nombres"]').on('input', function() {
                let val = $(this).val();
                $('#prev-texto-ponente').text(val ? 'A cargo de: ' + val : '[PONENTE]');
            });

            $('input[name="horas_lectivas"]').on('input', function() {
                let val = $(this).val();
                $('#prev-texto-horas').text(val ? val + ' HORAS LECTIVAS' : '[HORAS]');
            });

            // 2. Manejo de Nuevos Archivos (FileReader)
            function leerYPrevisualizar(input, targetId) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(targetId).attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('input[name="modelo_certificado"]').change(function() { leerYPrevisualizar(this, '#prev-modelo'); });
            $('input[name="firma1"]').change(function() { leerYPrevisualizar(this, '#prev-firma1'); });
            $('input[name="firma2"]').change(function() { leerYPrevisualizar(this, '#prev-firma2'); });

            // Ejecutar inicialización para cargar datos actuales de Colegiaturas
            inicializarPreview();
        });
    </script>
@endpush
