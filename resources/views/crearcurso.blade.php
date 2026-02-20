@extends('layouts.admin')

@section('content')
    <style>
        /* Ajuste para que no tape el Header/Cerrar Sesión */
        .sticky-preview {
            top: 90px !important; /* Baja la posición para que no choque con el navbar */
            z-index: 5 !important; /* Lo mantiene por debajo de los menús desplegables del sistema */
        }

        #certificado-preview-container {
            background-image: url('{{ asset("assets/img/checkered-pattern.png") }}'); /* Opcional: fondo para notar transparencia */
            background-color: #eee;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            {{-- Formulario --}}
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0 fw-bold text-primary">Creación del Nuevo Curso</h5>
                    </div>
                    <form action="{{ route('admin.cursos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">1. Información General</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Nombre del curso</label>
                                    <input type="text" name="nombre_curso" class="form-control" placeholder="Ej: Especialización en NIIF" required autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Organizador</label>
                                    <input type="text" name="organizador" class="form-control" placeholder="Ej: Colegio de Contadores" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Modalidad</label>
                                    <select name="modalidad" class="form-select" required>
                                        <option value="Presencial">Presencial</option>
                                        <option value="Virtual">Virtual</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Horas Lectivas</label>
                                    <input type="number" name="horas_lectivas" class="form-control" placeholder="0" required>
                                </div>
                            </div>

                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">2. Fechas y Ponente</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small">Fecha de Inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small">Fecha de Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nombres del Ponente</label>
                                    <input type="text" name="ponente_nombres" class="form-control" placeholder="Nombre completo" required autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Especialidad o Profesión</label>
                                    <input type="text" name="ponente_especialidad" class="form-control" placeholder="Ej: Auditor Financiero" required>
                                </div>
                            </div>

                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">3. Archivos y Firmas</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Imagen Publicitaria (Curso)</label>
                                    <input type="file" name="imagen_curso" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Modelo del Certificado (Fondo)</label>
                                    <input type="file" name="modelo_certificado" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-danger">Firma Digital 1</label>
                                    <input type="file" name="firma1" class="form-control" accept="image/*">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-danger">Firma Digital 2</label>
                                    <input type="file" name="firma2" class="form-control" accept="image/*">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Descripción de Participación (lo que irá en el certificado)</label>
                                    <textarea name="descripcion_certificado" class="form-control" rows="3"
                                              placeholder="Ej: como asistente en la SEMANA INFORMÁTICA... o como ponente con los temas..."></textarea>
                                    <small class="text-muted">Este texto aparecerá en el recuadro central del certificado.</small>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-end py-3">
                            <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary">Regresar</a>
                            <button type="submit" class="btn btn-primary px-4">Guardar Capacitación</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Previsualización Fija --}}
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 sticky-top sticky-preview">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Vista Previa del Certificado</h5>
                    </div>
                    <div class="card-body text-center">
                        <div id="certificado-preview-container"
                             style="position: relative; width: 100%; aspect-ratio: 1.41 / 1; border: 1px solid #ddd; background: #fff; overflow: hidden;">

                            <img id="prev-modelo" src="#" style="width: 100%; height: 100%; display: none; object-fit: contain;">
                            {{-- Textos con IDs verificados --}}
                            <div style="position: absolute; top: 24%; width: 100%; text-align: center; font-size: 1.2vw; color: #333;">
                                Certifica a:
                            </div>
                            <div id="prev-agremiado" style="position: absolute; width: 100%; text-align: center; top: 33%; font-weight: bold; font-size: 1.2vw; padding: 0 10%; color: #555; z-index: 10;">JUANITO PEREZ TORRES</div>
                            <div id="prev-contenedor-descripcion"
                                 style="position: absolute; top:40%; width: 86%; left: 7%; padding: 1.5% 2%; text-align: justify; font-size: 1vw; line-height: 1.4; color: #000; z-index: 3;">
                                 <span id="prev-descripcion-dinamica">Por su participación en el curso [CURSO] [DESCRIPCIÓN ADICIONAL]</span>
                            </div>
                            <div id="prev-fecha-huanuco"
                                 style="position: absolute; top: 60%; right: 10%; font-size: 0.7vw; font-weight: bold; color: #333; z-index: 3;">
                                [FECHA DE EMISIÓN]
                            </div>
                            <div id="prev-texto-horas" style="position: absolute; width: 100%; color: #555; text-align: center; bottom: 25%; font-weight: bold; font-size: 0.5vw; z-index: 10;">[HORAS]</div>

                            <img id="prev-firma1" src="#" style="position: absolute; bottom: 10%; left: 25%; width: 15%; display: none; z-index: 11;">
                            <img id="prev-firma2" src="#" style="position: absolute; bottom: 10%; right: 25%; width: 15%; display: none; z-index: 11;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Iniciando previsualización unificada en Huánuco...");

            // --- 1. REFERENCIAS DE ELEMENTOS ---
            const inputNombre = document.querySelector('input[name="nombre_curso"]');
            const inputDesc = document.querySelector('textarea[name="descripcion_certificado"]');
            const displayFrase = document.getElementById('prev-descripcion-dinamica');

            const inputFechaFin = document.querySelector('input[name="fecha_fin"]');
            const displayFechaHuanuco = document.getElementById('prev-fecha-huanuco');

            // --- 2. LÓGICA DE CONCATENACIÓN (El cambio solicitado) ---
            function actualizarFraseParticipacion() {
                if (inputNombre && inputDesc && displayFrase) {
                    const nombre = inputNombre.value.trim().toUpperCase() || '[CURSO]';
                    const adicional = inputDesc.value.trim() || '[DETALLES ADICIONALES]';

                    // Unimos las piezas según el modelo de aniversario
                    displayFrase.innerText = `Por participar en el curso ${nombre} ${adicional}`;
                }
            }

            // --- 3. FUNCIONES DE APOYO (Fechas e Imágenes) ---
            function formatearFecha(fechaStr) {
                if(!fechaStr) return null;
                const date = new Date(fechaStr + 'T00:00:00');
                const meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
                return `${date.getDate()} de ${meses[date.getMonth()]} del ${date.getFullYear()}`;
            }

            // --- 4. ASIGNACIÓN DE EVENTOS ---

            // Eventos para la descripción unificada
            if (inputNombre) inputNombre.addEventListener('input', actualizarFraseParticipacion);
            if (inputDesc) inputDesc.addEventListener('input', actualizarFraseParticipacion);

            // Evento para la fecha de emisión
            if (inputFechaFin && displayFechaHuanuco) {
                inputFechaFin.addEventListener('change', function() {
                    const fechaFmt = formatearFecha(this.value);
                    displayFechaHuanuco.innerText = fechaFmt ? `Huánuco, ${fechaFmt}` : '[FECHA DE EMISIÓN]';
                });
            }

            // Otros campos simples (Ponente, Horas)
            function actualizarTextoSimple(name, id, prefijo = "", defaultText = "") {
                const el = document.querySelector(`input[name="${name}"]`);
                const disp = document.getElementById(id);
                if (el && disp) {
                    el.addEventListener('input', function() {
                        disp.innerText = this.value.trim() ? (prefijo + this.value.trim()).toUpperCase() : defaultText;
                    });
                }
            }
            actualizarTextoSimple('ponente_nombres', 'prev-texto-ponente', 'PONENTE: ', '[PONENTE]');
            actualizarTextoSimple('horas_lectivas', 'prev-texto-horas', 'HORAS LECTIVAS: ', '[HORAS]');

            // Manejo de Imágenes (Fondo y Firmas)
            const inputsImagen = { 'modelo_certificado': 'prev-modelo', 'firma1': 'prev-firma1', 'firma2': 'prev-firma2' };
            Object.keys(inputsImagen).forEach(inputName => {
                const input = document.querySelector(`input[name="${inputName}"]`);
                const imgPrev = document.getElementById(inputsImagen[inputName]);
                if (input && imgPrev) {
                    input.addEventListener('change', function() {
                        if (this.files && this.files[0]) {
                            const reader = new FileReader();
                            reader.onload = e => { imgPrev.src = e.target.result; imgPrev.style.display = 'block'; };
                            reader.readAsDataURL(this.files[0]);
                        }
                    });
                }
            });
        });
    </script>
@endpush
