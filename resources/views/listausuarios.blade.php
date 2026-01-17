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
                    <span>Lista de Usuarios</span>
                    <button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#nuevoUsuario">Agregar</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaUsuarios" class="table border mb-0">
                            <thead class="fw-semibold text-nowrap">
                                <tr class="align-middle">
                                    <th class="bg-body-secondary text-center">N°</th>
                                    <th class="bg-body-secondary">Nombre</th>
                                    <th class="bg-body-secondary text-center">Apellido</th>
                                    <th class="bg-body-secondary">User</th>
                                    <th class="bg-body-secondary text-center">Rol</th>
                                    <th class="bg-body-secondary">Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($usuarios as $usuario)
                                    <tr class="align-middle">
                                        <td class="text-center">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            <div class="text-nowrap">{{ $usuario->nombre }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-nowrap">{{ $usuario->apellido }}</div>
                                        </td>
                                        <td>
                                            <div class="text-nowrap">{{ $usuario->user }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-nowrap">{{ $usuario->rol }}</div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-transparent p-0" type="button" data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <svg class="icon"><use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-options')}}"></use></svg>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" data-coreui-i18n="edit" data-coreui-toggle="modal" data-coreui-target="#editModal-{{ $usuario->id_administrador }}">Editar</a>
                                                    <a class="dropdown-item text-danger" data-coreui-i18n="delete" data-coreui-toggle="modal" data-coreui-target="#deleteModal-{{ $usuario->id_administrador }}">Eliminar</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row-->
    <!-- Modal para crear usuario-->
    <div class="modal fade" id="nuevoUsuario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.usuarios.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Ingresa los datos para el nuevo usuario.</p>

                        <div class="mb-3">
                            <label for="nombre_crear" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre_crear" name="nombre" placeholder="Ej: Juan" required>
                        </div>

                        <div class="mb-3">
                            <label for="apellido_crear" class="form-label">Apellido:</label>
                            <input type="text" class="form-control" id="apellido_crear" name="apellido" placeholder="Ej: Pérez" required>
                        </div>

                        <div class="mb-3">
                            <label for="user_crear" class="form-label">User (para login):</label>
                            <input type="text" class="form-control" id="user_crear" name="user" placeholder="Ej: jperez" required>
                        </div>

                        <div class="mb-3">
                            <label for="rol_crear" class="form-label">Rol:</label>
                            <select class="form-select" id="rol_crear" name="rol" required>
                                <option value="" selected disabled>-- Seleccione un Rol --</option>
                                <option value="superadmin">Superadmin</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="password_crear" class="form-label">Contraseña:</label>
                            <input type="password" class="form-control" id="password_crear" name="password" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($usuarios as $usuario)
        <!--Modal de actualización de usuario-->
        <div class="modal fade" id="editModal-{{ $usuario->id_administrador }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $usuario->id_administrador }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel-{{ $usuario->id_administrador }}">Editar Usuario: {{ $usuario->nombre }}</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <p>Estás editando los datos de <strong>{{ $usuario->user }}</strong>.</p>

                            <div class="mb-3">
                                <label for="nombre-{{ $usuario->id_administrador }}" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="nombre-{{ $usuario->id_administrador }}" name="nombre" value="{{ $usuario->nombre }}">
                            </div>

                            <div class="mb-3">
                                <label for="apellido-{{ $usuario->id_administrador }}" class="form-label">Apellido:</label>
                                <input type="text" class="form-control" id="apellido-{{ $usuario->id_administrador }}" name="apellido" value="{{ $usuario->apellido }}">
                            </div>

                            <div class="mb-3">
                                <label for="rol-{{ $usuario->id_administrador }}" class="form-label">User:</label>
                                <input type="text" class="form-control" id="rol-{{ $usuario->id_administrador }}" name="user" value="{{ $usuario->user }}">
                            </div>

                            <div class="mb-3">
                                <label for="rol-{{ $usuario->id_administrador }}" class="form-label">Rol:</label>
                                <select class="form-select" id="rol-{{ $usuario->id_administrador }}" name="rol" value="{{ $usuario->rol }}">
                                <option value="" selected disabled>-- Seleccione un Rol --</option>
                                <option value="superadmin">Superadmin</option>
                                <option value="admin">Admin</option>
                            </select>
                            </div>

                            <div class="mb-3">
                                <label for="rol-{{ $usuario->id_administrador }}" class="form-label">Contraseña:</label>
                                <input type="text" class="form-control" id="password-{{ $usuario->id_administrador }}" name="password" placeholder="Dejar en blanco para no cambiar">
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

        <!--Modal de eliminacion de usuario-->
        <div class="modal fade" id="deleteModal-{{ $usuario->id_administrador }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $usuario->id_administrador }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel-{{ $usuario->id_administrador }}">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body">
                            <p>¿Estás seguro de que quieres eliminar al usuario <strong>{{ $usuario->user }}</strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script src="{{ asset('vendors/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{ asset('js/datatables.js')}}"></script>
    <script src="{{ asset('DataTables/datatables.min.js')}}"></script>
    <script>
        // Para el DataTable de usuarios
        $(document).ready(function () {

            // Inicializa DataTables en la tabla con el ID 'tablaUsuarios'
            $('#tablaUsuarios').DataTable({
                // Opcional: Poner la tabla en español
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json',
                    emptyTable: "No hay pagos registrados para este agremiado",
                    zeroRecords: "No se encontraron resultados",
                }
            });

        });
    </script>
@endpush
