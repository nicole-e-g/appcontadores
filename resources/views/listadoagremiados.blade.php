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
    <!-- Listado de Usuarios-->
    <!-- /.row-->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Lista de Agremiados</span>
                    <button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#nuevoAgremiado">Agregar</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaAgremiados" class="table border mb-0">
                            <thead class="fw-semibold text-nowrap">
                                <tr class="align-middle">
                                    <th class="bg-body-secondary text-center">N°</th>
                                    <th class="bg-body-secondary text-center">Matricula</th>
                                    <th class="bg-body-secondary text-center">DNI</th>
                                    <th class="bg-body-secondary text-center">Nombres</th>
                                    <th class="bg-body-secondary text-center">Apellidos</th>
                                    <th class="bg-body-secondary text-center">Estado</th>
                                    <th class="bg-body-secondary text-center">Fin Habilitacion</th>
                                    <th class="bg-body-secondary">Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row-->
    <!-- Modal para crear agremiado-->
    <div class="modal fade" id="nuevoAgremiado" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Agremiado</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.agremiados.store') }}" method="POST"> <!--Verificar el store del controlador -->
                    @csrf
                    <div class="modal-body">
                        <p>Ingresa los datos para el nuevo agremiado.</p>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="dni_crear" class="form-label">DNI:</label>
                                <input type="text" class="form-control" id="dni_crear" name="dni" placeholder="Ej: 12345678" required>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_nacimiento_crear" class="form-label">Fecha de Nacimiento:</label>
                                <input type="date" class="form-control" id="fecha_nacimiento_crear" name="fecha_nacimiento" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="nombre_crear" class="form-label">Nombres:</label>
                                <input type="text" class="form-control" id="nombre_crear" name="nombres" placeholder="Ej: Juan" required>
                            </div>
                            <div class="col-md-6">
                                <label for="apellido_crear" class="form-label">Apellidos:</label>
                                <input type="text" class="form-control" id="apellido_crear" name="apellidos" placeholder="Ej: Pérez" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ruc_crear" class="form-label">RUC:</label>
                            <input type="text" class="form-control" maxlength="11" pattern="[0-9]{11}" id="ruc_crear" name="ruc" placeholder="Ej: 10254785041" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="matricula_crear" class="form-label">N° Matricula:</label>
                                <input type="text" class="form-control" id="matricula_crear" name="matricula" required>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_matricula_crear" class="form-label">Fecha de Matricula:</label>
                                <input type="date" class="form-control" id="fecha_matricula_crear" name="fecha_matricula" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="celular1_crear" class="form-label">Celular 1:</label>
                                <input type="text" maxlength="9" pattern="[0-9]{9}" class="form-control" id="celular1_crear" name="celular1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="celular2_crear" class="form-label">Celular 2:</label>
                                <input type="text" maxlength="9" pattern="[0-9]{9}" class="form-control" id="celular2_crear" name="celular2" >
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="correo1_crear" class="form-label">Correo 1:</label>
                                <input type="text" class="form-control" id="correo1_crear" name="correo1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="correo2_crear" class="form-label">Correo 2:</label>
                                <input type="text" class="form-control" id="correo2_crear" name="correo2" >
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Crear Agremiado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para actualizar agremiado-->
    <div class="modal fade" id="modalEditarAgremiado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="formEditarAgremiado" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Agremiado: <span id="span_nombre"></span></h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="edit_dni" class="form-label">DNI:</label>
                                <input type="text" class="form-control" id="edit_dni" name="dni">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                                <input type="date" class="form-control" id="edit_fecha_nacimiento" name="fecha_nacimiento">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="edit_nombres" class="form-label">Nombres:</label>
                                <input type="text" class="form-control" id="edit_nombres" name="nombres">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_apellidos" class="form-label">Apellidos:</label>
                                <input type="text" class="form-control" id="edit_apellidos" name="apellidos">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_ruc" class="form-label">RUC:</label>
                            <input type="text" class="form-control" maxlength="11" pattern="[0-9]{11}" id="edit_ruc" name="ruc">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="edit_matricula" class="form-label">N° Matricula:</label>
                                <input type="text" class="form-control" id="edit_matricula" name="matricula">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_fecha_matricula" class="form-label">Fecha de Matricula:</label>
                                <input type="date" class="form-control" id="edit_fecha_matricula" name="fecha_matricula">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="edit_celular1" class="form-label">Celular 1:</label>
                                <input type="text" maxlength="9" pattern="[0-9]{9}" class="form-control" id="edit_celular1" name="celular1">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_celular2" class="form-label">Celular 2:</label>
                                <input type="text" maxlength="9" pattern="[0-9]{9}" class="form-control" id="edit_celular2" name="celular2">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="edit_correo1" class="form-label">Correo 1:</label>
                                <input type="text" class="form-control" id="edit_correo1" name="correo1">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_correo2" class="form-label">Correo 2:</label>
                                <input type="text" class="form-control" id="edit_correo2" name="correo2">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para eliminar agremiado-->
    <div class="modal fade" id="modalEliminarAgremiado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formEliminarAgremiado" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que quieres eliminar al agremiado <strong id="del_nombre"></strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('DataTables/datatables.min.js')}}"></script>
    <script>
        // Para el DataTable de agremiado
        $(document).ready(function () {

            // Inicializa DataTables en la tabla con el ID 'tablaAgremiados'
            $('#tablaAgremiados').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,               // Arranca mostrando 50 registros por defecto
                lengthMenu: [50, 100, 200],
                ajax: "{{ route('admin.agremiados.index') }}", // La misma ruta del index
                columns: [
                    // El 'data: null' con render ayuda a poner el número correlativo
                    { data: null, render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }, class: 'text-center',
                        searchable: false,
                        orderable: false},
                    { data: 'matricula', name: 'matricula', class: 'text-center' },
                    { data: 'dni', name: 'dni', class: 'text-center' },
                    { data: 'nombres', name: 'nombres' },
                    { data: 'apellidos', name: 'apellidos' },
                    { data: 'estado', name: 'estado', class: 'text-center' },
                    { data: 'fin_habilitacion', name: 'fin_habilitacion', class: 'text-center' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        class: 'text-center'
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json',
                    emptyTable: "No hay agremiados registrados",
                    zeroRecords: "No se encontraron resultados",
                }
            });

        });

        // Función para abrir modal de edición
        function editarAgremiado(id) {
            // Petición AJAX al controlador para obtener los datos
            $.get('/admin/agremiados/' + id + '/edit', function(data) {
                // Datos básicos
                $('#edit_dni').val(data.dni);
                $('#edit_fecha_nacimiento').val(data.fecha_nacimiento);
                $('#edit_nombres').val(data.nombres);
                $('#edit_apellidos').val(data.apellidos);
                $('#edit_ruc').val(data.ruc);
                $('#edit_matricula').val(data.matricula);
                $('#edit_fecha_matricula').val(data.fecha_matricula);
                $('#span_nombre').text(data.nombres);

                // Manejo de Celulares (Arreglos)
                // Usamos la validación 'data.celular ? data.celular[0] : ""' por seguridad
                $('#edit_celular1').val(data.celular && data.celular[0] ? data.celular[0] : '');
                $('#edit_celular2').val(data.celular && data.celular[1] ? data.celular[1] : '');

                // Manejo de Correos (Arreglos)
                $('#edit_correo1').val(data.correo && data.correo[0] ? data.correo[0] : '');
                $('#edit_correo2').val(data.correo && data.correo[1] ? data.correo[1] : '');

                // Actualizamos la ruta del formulario
                $('#formEditarAgremiado').attr('action', '/admin/agremiados/' + id);

                // Abrimos el modal
                $('#modalEditarAgremiado').modal('show');
            });
        }

        // Función para abrir modal de eliminación
        function eliminarAgremiado(id, nombre) {
            $('#del_nombre').text(nombre);
            $('#formEliminarAgremiado').attr('action', '/admin/agremiados/' + id);
            $('#modalEliminarAgremiado').modal('show');
        }
    </script>
@endpush
