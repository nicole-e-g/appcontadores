@extends('layouts.admin')

@section('content')
    <style>
        /* Ajuste para que no tape el Header/Cerrar Sesión */
        .sticky-preview {
            top: 90px !important;
            z-index: 5 !important;
        }

        #certificado-preview-container {
            background-color: #eee;
            border: 1px solid #ddd;
        }

        /* Estilo del recuadro rosado según el modelo de aniversario */
        .pink-container {
            background-color: rgba(249, 211, 211, 0.8);
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            {{-- Formulario de Edición --}}
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header py-3 border-bottom">
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
                                    <input type="text" name="nombre_curso" class="form-control" value="{{ $curso->nombre_curso }}" required autocomplete="off">
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
                                    <label class="form-label fw-bold text-muted small">Fecha de Fin (Emisión)</label>
                                    <input type="date" name="fecha_fin" class="form-control" value="{{ $curso->fecha_fin }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nombres del Ponente</label>
                                    <input type="text" name="ponente_nombres" class="form-control" value="{{ $curso->ponente_nombres }}" required autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Especialidad o Profesión</label>
                                    <input type="text" name="ponente_especialidad" class="form-control" value="{{ $curso->ponente_especialidad }}" required>
                                </div>
                            </div>

                            {{-- 3. Archivos y Firmas --}}
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">3. Actualizar Archivos (Opcional)</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Imagen del Curso</label>
                                    <input type="file" name="imagen_curso" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-danger">Nuevo Fondo Certificado</label>
                                    <input type="file" name="modelo_certificado" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-danger">Nueva Firma Digital 1</label>
                                    <input type="file" name="firma1" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-danger">Nueva Firma Digital 2</label>
                                    <input type="file" name="firma2" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Descripción de Participación</label>
                                    <textarea name="descripcion_certificado" class="form-control" rows="3"
                                              placeholder="Ej: con los temas: Auditoría y Tributación.">{{ $curso->descripcion_certificado }}</textarea>
                                    <small class="text-muted">Este texto se unirá automáticamente al nombre del curso.</small>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-end py-3">
                            <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary">Regresar</a>
                            <button type="submit" class="btn btn-primary px-4">Actualizar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Columna de Previsualización --}}
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 sticky-top sticky-preview">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Vista Previa del Certificado</h5>
                    </div>
                    <div class="card-body text-center p-0"> {{-- p-0 para que el contenedor ocupe todo el ancho --}}
                        <div id="certificado-preview-container"
                             style="position: relative; width: 100%; aspect-ratio: 1.41 / 1; border: 1px solid #ddd; background: #fff; overflow: hidden;">

                            {{-- Carga de Fondo Existente --}}
                            <img id="prev-modelo" src="{{ $curso->certificado_path ? asset('storage/'.$curso->certificado_path) : '#' }}"
                                 style="width: 100%; height: 100%; object-fit: contain; position: absolute; top: 0; left: 0; z-index: 1; {{ $curso->certificado_path ? '' : 'display:none;' }}">

                            {{-- Textos Estáticos y Dinámicos --}}
                            <div style="position: absolute; top: 24%; width: 100%; text-align: center; font-size: 1.2vw; color: #333; z-index: 2;">
                                Certifica a:
                            </div>

                            {{-- Nombre del Agremiado (Placeholder fijo) --}}
                            <div id="prev-agremiado" style="position: absolute; width: 100%; text-align: center; top: 33%; font-weight: bold; font-size: 1.2vw; padding: 0 10%; color: #555; z-index: 10;">
                                JUANITO PEREZ TORRES
                            </div>

                            {{-- Descripción Unificada (Nombre del Curso + Descripción Adicional) --}}
                            <div id="prev-contenedor-descripcion"
                                 style="position: absolute; top:40%; width: 86%; left: 7%; padding: 1.5% 2%; text-align: justify; font-size: 1vw; line-height: 1.4; color: #000; z-index: 3;">
                                <span id="prev-descripcion-dinamica">Por participar en el curso {{ strtoupper($curso->nombre_curso) }} {{ $curso->descripcion_certificado }}</span>
                            </div>

                            {{-- Fecha estilo Huánuco --}}
                            <div id="prev-fecha-huanuco"
                                 style="position: absolute; top: 60%; right: 10%; font-size: 0.7vw; font-weight: bold; color: #333; z-index: 3;">
                                [FECHA DE EMISIÓN]
                            </div>

                            {{-- Horas Lectivas --}}
                            <div id="prev-texto-horas" style="position: absolute; width: 100%; color: #555; text-align: center; bottom: 25%; font-weight: bold; font-size: 0.5vw; z-index: 10;">
                                {{ $curso->horas_lectivas }} HORAS LECTIVAS
                            </div>

                            {{-- Firmas Existentes --}}
                            <img id="prev-firma1" src="{{ $curso->firma1_path ? asset('storage/'.$curso->firma1_path) : '#' }}"
                                 style="position: absolute; bottom: 10%; left: 25%; width: 15%; z-index: 11; {{ $curso->firma1_path ? '' : 'display: none;' }}">

                            <img id="prev-firma2" src="{{ $curso->firma2_path ? asset('storage/'.$curso->firma2_path) : '#' }}"
                                 style="position: absolute; bottom: 10%; right: 25%; width: 15%; z-index: 11; {{ $curso->firma2_path ? '' : 'display: none;' }}">
                        </div>
                        <p class="small text-muted mt-2 mb-2">Previsualizando datos actuales de la capacitación.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. REFERENCIAS ---
            const inputNombre = document.querySelector('input[name="nombre_curso"]');
            const inputDesc = document.querySelector('textarea[name="descripcion_certificado"]');
            const displayFrase = document.getElementById('prev-descripcion-dinamica');
            const inputFechaFin = document.querySelector('input[name="fecha_fin"]');
            const displayFechaHuanuco = document.getElementById('prev-fecha-huanuco');
            const inputHoras = document.querySelector('input[name="horas_lectivas"]');
            const displayHoras = document.getElementById('prev-texto-horas');

            // --- 2. LÓGICA DE CONCATENACIÓN ---
            function actualizarFraseParticipacion() {
                const nombre = inputNombre.value.trim().toUpperCase() || '[CURSO]';
                const adicional = inputDesc.value.trim() || '';
                displayFrase.innerText = `Por participar en el curso ${nombre} ${adicional}`;
            }

            // --- 3. FORMATEO DE FECHA ESTILO HUÁNUCO ---
            function formatearYActualizarFecha() {
                const fechaStr = inputFechaFin.value;
                if(!fechaStr) {
                    displayFechaHuanuco.innerText = '[FECHA DE EMISIÓN]';
                    return;
                }
                const date = new Date(fechaStr + 'T00:00:00');
                const meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
                const fechaFmt = `${date.getDate()} de ${meses[date.getMonth()]} del ${date.getFullYear()}`;
                displayFechaHuanuco.innerText = `Huánuco, ${fechaFmt}`;
            }

            // --- 4. ACTUALIZACIÓN DE HORAS ---
            function actualizarHoras() {
                const horas = inputHoras.value.trim();
                displayHoras.innerText = horas ? `${horas} HORAS LECTIVAS` : '[HORAS]';
            }

            // --- 5. EVENTOS ---
            inputNombre.addEventListener('input', actualizarFraseParticipacion);
            inputDesc.addEventListener('input', actualizarFraseParticipacion);
            inputFechaFin.addEventListener('change', formatearYActualizarFecha);
            inputHoras.addEventListener('input', actualizarHoras);

            // Manejo de Imágenes (Fondo y Firmas)
            const inputsImagen = { 'modelo_certificado': 'prev-modelo', 'firma1': 'prev-firma1', 'firma2': 'prev-firma2' };
            Object.keys(inputsImagen).forEach(inputName => {
                const input = document.querySelector(`input[name="${inputName}"]`);
                const imgPrev = document.getElementById(inputsImagen[inputName]);
                if (input && imgPrev) {
                    input.addEventListener('change', function() {
                        if (this.files && this.files[0]) {
                            const reader = new FileReader();
                            reader.onload = e => {
                                imgPrev.src = e.target.result;
                                imgPrev.style.display = 'block';
                            };
                            reader.readAsDataURL(this.files[0]);
                        }
                    });
                }
            });

            // --- 6. INICIALIZACIÓN (Carga datos actuales al entrar) ---
            actualizarFraseParticipacion();
            formatearYActualizarFecha();
            actualizarHoras();
        });
    </script>
@endpush
