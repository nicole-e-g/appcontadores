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

    <div class="card mb-4">
        <div class="card-header">
            <i class="cil-contact"></i> <strong>Gestión de Carnets Profesionales</strong>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="tabCarnets" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="pendientes-tab" data-coreui-toggle="tab" data-coreui-target="#pendientes" type="button">Pendientes por Entregar</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="entregados-tab" data-coreui-toggle="tab" data-coreui-target="#entregados" type="button">Historial de Entregados</button>
                </li>
            </ul>


            <div class="tab-content border p-3">
                <div class="tab-pane fade show active" id="pendientes" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tablaPendientes" class="table table-striped border w-100">
                            <thead class="fw-semibold text-nowrap">
                            <tr>
                                <th>N°</th>
                                <th>Matrícula</th>
                                <th>Agremiado</th>
                                <th>DNI</th>
                                <th>RUC</th>
                                <th>Incorporación</th>
                                <th>Fecha de solicitud</th>
                                <th>Acción</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="entregados" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tablaEntregados" class="table table-striped border w-100">
                            <thead>
                            <tr>
                                <th>N°</th>
                                <th>Matrícula</th>
                                <th>Agremiado</th>
                                <th>DNI</th>
                                <th>RUC</th>
                                <th>Incorporación</th>
                                <th>Fecha de entrega</th>
                                <th>Acción</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendors/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{ asset('js/datatables.js')}}"></script>
    <script src="{{ asset('DataTables/datatables.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            const configBase = {
                processing: true,
                serverSide: true, // Optimización para que no sea pesado
                pageLength: 50,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
                    emptyTable: "No hay solicitudes de carnet registrados",
                    zeroRecords: "No se encontraron resultados",
                }
            };

            // Inicializamos sobre la TABLE con id="tablaPendientes"
            $('#tablaPendientes').DataTable({
                ...configBase,
                ajax: {
                    url: "{{ route('admin.carnets.data') }}",
                    data: { estado: 'Pendiente' }
                },
                columns: [
                    { data: null, render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }, class: 'text-center',
                        searchable: false,
                        orderable: false},
                    { data: 'agremiado.matricula', name: 'agremiado.matricula' },
                    { data: 'agremiado.nombres', name: 'agremiado.nombres',
                        render: function(data, type, row) {
                            return row.agremiado.nombres + ' ' + row.agremiado.apellidos;
                        }
                    },
                    { data: 'agremiado.dni', name: 'agremiado.dni' },
                    { data: 'agremiado.ruc', name: 'agremiado.ruc' },
                    { data: 'agremiado.fecha_matricula', name: 'agremiado.fecha_matricula' },
                    { data: 'pago.fecha_pago', name: 'pago.fecha_pago' },
                    { data: 'accion', name: 'accion', orderable: false, searchable: false }
                ]
            });

            // Carga de la segunda tabla al cambiar de pestaña
            $('#entregados-tab').on('shown.coreui.tab', function () {
                if (!$.fn.DataTable.isDataTable('#tablaEntregados'))
                {
                    $('#tablaEntregados').DataTable({
                        ...configBase,
                        ajax: {
                            url: "{{ route('admin.carnets.data') }}",
                            data: { estado: 'Entregado' }
                        },
                        columns: [
                            { data: null, render: function (data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;},
                                class: 'text-center',
                                searchable: false,
                                orderable: false},
                            { data: 'agremiado.fecha_matricula', name: 'agremiado.fecha_matricula' },
                            { data: 'agremiado.nombres', name: 'agremiado.nombres',
                                render: function(data, type, row) {
                                    return row.agremiado.nombres + ' ' + row.agremiado.apellidos;
                                }
                            },
                            { data: 'agremiado.dni', name: 'agremiado.dni' },
                            { data: 'agremiado.ruc', name: 'agremiado.ruc' },
                            { data: 'agremiado.matricula', name: 'agremiado.matricula' },
                            { data: 'fecha_entrega', name: 'fecha_entrega' },
                            { data: 'accion', name: 'accion', orderable: false, searchable: false }
                        ]
                    });
                }else {
                    // Si ya existe la tabla, solo la refrescamos para ver los nuevos cambios
                    $('#tablaEntregados').DataTable().ajax.reload();
                }
            });
        });
    </script>
@endpush
