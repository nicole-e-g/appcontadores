@extends('layouts.admin')

@section('content')
    @if (session('success'))
        <div class="alert alert-success" role="alert" id="success-alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                @forelse ($agremiados as $agremiado)
                                    <tr class="align-middle">
                                        <td class="text-center">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            <div class="text-nowrap">{{ $agremiado->matricula }}</div>
                                        </td>
                                        <td>
                                            <div class="text-nowrap">{{ $agremiado->dni }}</div>
                                        </td>
                                        <td>
                                            <div class="text-nowrap text-center">{{ $agremiado->nombres }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-nowrap">{{ $agremiado->apellidos }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-nowrap">{{ $agremiado->estado }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if($agremiado->fin_habilitacion)
                                                {{ \Carbon\Carbon::parse($agremiado->fin_habilitacion)->translatedFormat('d F Y') }}
                                            @else
                                                <span class="text-muted">No asignado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-transparent p-0" type="button" data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <svg class="icon"><use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-options')}}"></use></svg>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="{{ route('admin.agremiados.show', $agremiado->id) }}">Ver datos</a>
                                                    <a class="dropdown-item" data-coreui-toggle="modal" data-coreui-target="#editAgremiadoModal-{{ $agremiado->id }}">Editar datos</a>
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
    <!-- Modal para crear agremiado-->
    <div class="modal fade" id="nuevoAgremiado" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Agremiado</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.agremiados.store') }}" method="POST"> <!--Verificar el store del controlador -->
                    @csrf
                    <div class="modal-body">
                        <p>Ingresa los datos para el nuevo agremiado.</p>

                        <div class="mb-3">
                            <label for="dni_crear" class="form-label">DNI:</label>
                            <input type="text" class="form-control" id="dni_crear" name="dni" placeholder="Ej: 12345678" required>
                        </div>
                                                                                    
                        <div class="mb-3">
                            <label for="nombre_crear" class="form-label">Nombres:</label>
                            <input type="text" class="form-control" id="nombre_crear" name="nombres" placeholder="Ej: Juan" required>
                        </div>

                        <div class="mb-3">
                            <label for="apellido_crear" class="form-label">Apellidos:</label>
                            <input type="text" class="form-control" id="apellido_crear" name="apellidos" placeholder="Ej: Pérez" required>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_nacimiento_crear" class="form-label">Fecha de Nacimiento:</label>
                            <input type="date" class="form-control" id="fecha_nacimiento_crear" name="fecha_nacimiento" required>
                        </div>

                        <div class="mb-3">
                            <label for="matricula_crear" class="form-label">N° Matricula:</label>
                            <input type="text" class="form-control" id="matricula_crear" name="matricula" required>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_matricula_crear" class="form-label">Fecha de Matricula:</label>
                            <input type="date" class="form-control" id="fecha_matricula_crear" name="fecha_matricula" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="celular1_crear" class="form-label">Celular 1:</label>
                                <input type="text" class="form-control" id="celular1_crear" name="celular1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="celular2_crear" class="form-label">Celular 2:</label>
                                <input type="text" class="form-control" id="celular2_crear" name="celular2" >
                            </div>
                        </div>
                         
                        <div class="mb-3">
                            <label for="correo1_crear" class="form-label">Correo 1:</label>
                            <input type="text" class="form-control" id="correo1_crear" name="correo1" required>
                        </div>

                        <div class="mb-3">
                            <label for="correo2_crear" class="form-label">Correo 2:</label>
                            <input type="text" class="form-control" id="correo2_crear" name="correo2" >
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
    @foreach ($agremiados as $agremiado)
        <!--Modal de actualización de agremiado-->
        <div class="modal fade" id="editAgremiadoModal-{{ $agremiado->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $agremiado->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel-{{ $agremiado->id }}">Editar Agremiado: {{ $agremiado->nombres }}</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.agremiados.update', $agremiado) }}" method="POST">
                        @csrf
                        @method('PUT') 
                        <div class="modal-body">
                            <p>Estás editando los datos de <strong>{{ $agremiado->nombres }}</strong>.</p>

                            <div class="mb-3">
                                <label for="dni-{{ $agremiado->id}}" class="form-label">DNI:</label>
                                <input type="text" class="form-control" id="dni-{{ $agremiado->id }}" name="dni" value="{{ $agremiado->dni }}">
                            </div>
                                                                                    
                            <div class="mb-3">
                                <label for="nombres-{{ $agremiado->id}}" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="nombres-{{ $agremiado->id}}" name="nombres" value="{{ $agremiado->nombres }}">
                            </div>

                            <div class="mb-3">
                                <label for="apellidos-{{ $agremiado->id}}" class="form-label">Apellido:</label>
                                <input type="text" class="form-control" id="apellidos-{{ $agremiado->id}}" name="apellidos" value="{{ $agremiado->apellidos }}">
                            </div>

                            <div class="md-3">
                                <label class="form-label">Fecha Nacimiento:</label>
                                <input type="date" class="form-control" name="fecha_nacimiento" value="{{ $agremiado->fecha_nacimiento }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="matricula-{{ $agremiado->id}}" class="form-label">N° Matricula:</label>
                                <input type="text" class="form-control" id="matricula-{{ $agremiado->id}}" name="matricula" value="{{ $agremiado->matricula }}">
                            </div>

                            <div class="md-3">
                                <label class="form-label">Fecha Matrícula:</label>
                                <input type="date" class="form-control" name="fecha_matricula" value="{{ $agremiado->fecha_matricula }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="celular-{{ $agremiado->id}}" class="form-label">Celular 1:</label>
                                    <input type="text" class="form-control" id="celular-{{ $agremiado->id}}" name="celular1" value="{{ $agremiado->celular[0] ?? '' }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="celular-{{ $agremiado->id}}" class="form-label">Celular 2:</label>
                                    <input type="text" class="form-control" id="celular-{{ $agremiado->id}}" name="celular2" value="{{ $agremiado->celular[1] ?? '' }}" >
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="correo-{{ $agremiado->id}}" class="form-label">Correo 1:</label>
                                <input type="text" class="form-control" id="correo-{{ $agremiado->id}}" name="correo1" value="{{ $agremiado->correo[0] ?? '' }}">
                            </div>

                            <div class="mb-3">
                                <label for="correo-{{ $agremiado->id}}" class="form-label">Correo 2:</label>
                                <input type="text" class="form-control" id="correo-{{ $agremiado->id}}" name="correo2" value="{{ $agremiado->correo[1] ?? '' }}">
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
        
        <!--Modal de eliminacion de agremiado-->
        <div class="modal fade" id="deleteModal-{{ $agremiado->id}}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $agremiado->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel-{{ $agremiado->id }}">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.agremiados.destroy', $agremiado) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body">
                            <p>¿Estás seguro de que quieres eliminar al agremiado <strong>{{ $agremiado->nombres }}</strong>?</p>                                                            
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
        // Para el DataTable de agremiado
        $(document).ready(function () {
            
            // Inicializa DataTables en la tabla con el ID 'tablaUsuarios'
            $('#tablaAgremiados').DataTable({
                // Opcional: Poner la tabla en español
                pageLength: 50,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json',
                    emptyTable: "No hay pagos registrados para este agremiado",
                    zeroRecords: "No se encontraron resultados",
                }
            });

        });
    </script>
@endpush