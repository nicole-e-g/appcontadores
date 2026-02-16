@extends('layouts.admin')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0" role="alert" id="success-alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>¡Error de Validación!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{-- ... (Alertas de éxito igual) ... --}}

    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-white">
            <span class="fw-bold text-primary">Gestión de Capacitaciones</span>
            <a href="{{ route('admin.cursos.create') }}" class="btn btn-primary">
                <i class="cil-plus"></i> Nueva Capacitación
            </a>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="tabCursos" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="vigentes-tab" data-coreui-toggle="tab" data-coreui-target="#vigentes" type="button">Vigentes</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="terminadas-tab" data-coreui-toggle="tab" data-coreui-target="#terminadas" type="button">Terminadas</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="anulados-tab" data-coreui-toggle="tab" data-coreui-target="#anulados" type="button">Anulados</button>
                </li>
            </ul>
            <div class="tab-content border border-top-0 p-3 bg-white">
                {{-- TAB 1: VIGENTES --}}
                <div class="tab-pane fade show active" id="vigentes" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tablaVigentes" class="table table-striped border w-100">
                            <thead class="bg-light">
                            <tr>
                                <th>N°</th>
                                <th>Nombre del Curso</th>
                                <th>Organizador</th>
                                <th class="text-center">Modalidad</th>
                                <th class="text-center">Horas</th>
                                <th class="text-center">Fecha Inicio</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: TERMINADAS --}}
                <div class="tab-pane fade" id="terminadas" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tablaTerminadas" class="table table-striped border w-100">
                            <thead class="bg-light">
                            <tr>
                                <th>N°</th>
                                <th>Nombre del Curso</th>
                                <th>Organizador</th>
                                <th class="text-center">Modalidad</th>
                                <th class="text-center">Horas</th>
                                <th class="text-center">Fecha Fin</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- TAB 3: ANULADOS --}}
                <div class="tab-pane fade" id="anulados" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tablaAnulados" class="table table-striped border w-100">
                            <thead class="bg-light">
                            <tr>
                                <th>N°</th>
                                <th>Nombre del Curso</th>
                                <th>Organizador</th>
                                <th class="text-center">Modalidad</th>
                                <th class="text-center">Horas</th>
                                <th class="text-center">Fecha Fin</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('DataTables/datatables.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            const crearDataTable = (id, estado) => {
                return $(id).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.cursos.index') }}",
                        data: { estado: estado }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'nombre_curso', name: 'nombre_curso' },
                        { data: 'organizador', name: 'organizador' },
                        { data: 'modalidad', name: 'modalidad', className: 'text-center' },
                        { data: 'horas_lectivas', name: 'horas_lectivas', className: 'text-center' },
                        { data: estado === 'vigentes' ? 'fecha_inicio' : 'fecha_fin', name: estado === 'vigentes' ? 'fecha_inicio' : 'fecha_fin', className: 'text-center' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                    ],
                    language: { url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json', }
                });
            };

            const tVigentes = crearDataTable('#tablaVigentes', 'vigentes');
            let tTerminadas = null;
            let tAnulados = null;

            $('#terminadas-tab').on('shown.coreui.tab', () => {
                if (!tTerminadas) tTerminadas = crearDataTable('#tablaTerminadas', 'terminadas');
                else tTerminadas.ajax.reload();
            });

            $('#anulados-tab').on('shown.coreui.tab', () => {
                if (!tAnulados) tAnulados = crearDataTable('#tablaAnulados', 'anulados');
                else tAnulados.ajax.reload();
            });

            // Movemos la función aquí adentro para que tVigentes, tTerminadas y tAnulados sean accesibles
            window.anularCurso = function(id) {
                Swal.fire({
                    title: '¿Deseas anular esta capacitación?',
                    text: "El curso se moverá a la pestaña de anulados.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#fabb05',
                    confirmButtonText: 'Sí, anular',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/cursos/anular/${id}`, // URL ajustada a la ruta admin
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(res) {
                                Swal.fire('Anulado', res.message, 'success');
                                tVigentes.ajax.reload();
                                if(tTerminadas) tTerminadas.ajax.reload();
                                if(tAnulados) tAnulados.ajax.reload();
                            },
                            error: function() {
                                console.error(err);
                                Swal.fire('Error', 'No se pudo procesar la anulación.', 'error');
                            }
                        });
                    }
                });
            }
        });
    </script>
@endpush
