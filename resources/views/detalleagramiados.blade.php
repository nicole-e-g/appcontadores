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

    <div class="container-fluid">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Información del Agremiado: {{ $agremiado->nombres }}</strong>
                <a href="{{ route('admin.agremiados.index') }}" class="btn btn-sm btn-secondary">Volver</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <h5 class="text-primary mb-3">Datos Personales</h5>
                        <p><strong>Nombres:</strong> {{ $agremiado->nombres }}</p>
                        <p><strong>Apellidos:</strong> {{ $agremiado->apellidos }}</p>
                        <p><strong>DNI:</strong> {{ $agremiado->dni }}</p>
                        <p><strong>RUC:</strong> {{ $agremiado->ruc }}</p>
                        <p><strong>Fecha de Nacimiento:</strong> {{ \Carbon\Carbon::parse($agremiado->fecha_nacimiento)->format('d/m/Y') }}</p>
                        <p><strong>Sexo:</strong> {{ $agremiado->sexo }}</p>
                    </div>

                    <div class="col-md-6 ps-4">
                        <h5 class="text-primary mb-3">Colegiatura</h5>
                        <p><strong>Número de Matrícula:</strong> {{ $agremiado->matricula }}</p>
                        <p><strong>Fecha de Matrícula:</strong> {{ \Carbon\Carbon::parse($agremiado->fecha_matricula)->format('d/m/Y') }}</p>
                        <p><strong>Estado Actual:</strong>
                            <span class="badge {{ $agremiado->estado == 'Habilitado' ? 'bg-success' : 'bg-danger' }}">
                                {{ $agremiado->estado }}
                            </span>
                        </p>
                        <p><strong>Sede:</strong> {{ $agremiado->sede }}</p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6 border-end">
                        <h5 class="text-primary mb-3">Celulares registrados</h5>
                        @foreach($agremiado->celular as $index => $numero)
                            <p><strong>Celular {{ $index + 1 }}:</strong> {{ $numero }}</p>
                        @endforeach
                    </div>

                    <div class="col-md-6 ps-4">
                        <h5 class="text-primary mb-3">Correos electrónicos</h5>
                        @foreach($agremiado->correo as $index => $email)
                            <p><strong>Correo {{ $index + 1 }}:</strong> {{ $email }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <ul class="nav nav-tabs" id="pestañasPagos" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="cuotas-tab" data-coreui-toggle="tab" data-coreui-target="#pane-cuotas" type="button" role="tab">
                        Cuota de Habilidad
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="constancias-tab" data-coreui-toggle="tab" data-coreui-target="#pane-constancias" type="button" role="tab">
                        Pagos de Constancias
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="carnet-tab" data-coreui-toggle="tab" data-coreui-target="#pane-carnets" type="button" role="tab">
                        Pagos de Carnet
                    </button>
                </li>
            </ul>

            <div class="tab-content border-start border-end border-bottom p-3" id="contenidoPestañas">
                <div class="tab-pane fade show active" id="pane-cuotas" role="tabpanel">
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Historial de Cuotas</h5>
                        <button class="btn btn-primary btn-sm" data-coreui-toggle="modal" data-coreui-target="#modalNuevoPago">
                            Nuevo Pago de Cuota
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="tablaCuotas" class="table border mb-0">
                            <thead class="fw-semibold text-nowrap">
                                <tr class="align-middle">
                                    <th class="bg-body-secondary">N°</th>
                                    <th class="bg-body-secondary">Año</th>
                                    <th class="bg-body-secondary">Mes Inicio</th>
                                    <th class="bg-body-secondary">Mes Final</th>
                                    <th class="bg-body-secondary">Comprobante</th>
                                    <th class="bg-body-secondary">Monto</th>
                                    <th class="bg-body-secondary">Estado</th>
                                    <th class="bg-body-secondary">Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pagos->where('tipo_pago', 'Habilitacion') as $pago)
                                    <tr class="align-middle">
                                        <td class="text-center">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            <div class="text-nowrap">{{ $pago->año }}</div>
                                        </td>
                                        <td>
                                            <div class="text-nowrap">{{ $pago->getMesNombre($pago->mes_inicio) }}</div>
                                        </td>
                                        <td>
                                            <div class="text-nowrap">{{ $pago->getMesNombre($pago->mes_final) }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-nowrap">{{ $pago->comprobante }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-nowrap">S/ {{ number_format($pago->monto, 2) }}</div>
                                        </td>
                                        <td>
                                            @if($pago->estado == 'Anulado')
                                                <span class="badge bg-danger" title="Anulado por: {{ $pago->anulado_por }}">Anulado</span>
                                            @else
                                                <span class="badge bg-success">Pagado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-transparent p-0" type="button" data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <svg class="icon"><use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-options')}}"></use></svg>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @if($pago->estado == 'Pagado')
                                                        <a class="dropdown-item" data-coreui-toggle="modal" data-coreui-target="#editModal-{{ $pago->id }}">Editar</a>
                                                        <a class="dropdown-item text-danger" data-coreui-i18n="anular" data-coreui-toggle="modal" data-coreui-target="#anularModal-{{ $pago->id }}">Anular pago</a>
                                                    @else
                                                        <a class="dropdown-item" data-coreui-toggle="modal" data-coreui-target="#modalVerMotivo-{{ $pago->id }}">
                                                            <i class="cil-search"></i> Ver motivo de anulación
                                                        </a>
                                                    @endif
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
                {{-- CONSTANCIAS --}}
                <div class="tab-pane fade" id="pane-constancias" role="tabpanel">
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Trámites de Constancias</h5>
                        <button class="btn btn-primary btn-sm" data-coreui-toggle="modal" data-coreui-target="#modalNuevaConstancia">
                            <i class="cil-file"></i> Nueva Constancia
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="tablaConstancia" class="table border mb-0">
                            <thead class="fw-semibold text-nowrap">
                                <tr class="align-middle">
                                    <th class="bg-body-secondary text-center">N°</th>
                                    <th class="bg-body-secondary text-center">Fecha de Pago</th>
                                    <th class="bg-body-secondary text-center">Comprobante</th>
                                    <th class="bg-body-secondary text-center">Monto</th>
                                    <th class="bg-body-secondary text-center">Estado</th>
                                    <th class="bg-body-secondary text-center">Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pagos->where('tipo_pago', 'Constancia') as $pago)
                                    <tr class="align-middle">
                                        <td class="text-center">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="text-center">
                                            <div class="text-nowrap">{{ \Carbon\Carbon::parse($pago->fecha_pago)->translatedFormat('d \d\e F, Y') }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-nowrap">{{ $pago->comprobante }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-nowrap">S/ {{ number_format($pago->monto, 2) }}</div>
                                        </td>
                                        <td>
                                            @if($pago->estado == 'Anulado')
                                                <span class="badge bg-danger" title="Anulado por: {{ $pago->anulado_por }}">Anulado</span>
                                            @else
                                                <span class="badge bg-success">Pagado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-transparent p-0" type="button" data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <svg class="icon"><use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-options')}}"></use></svg>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @if($pago->estado == 'Pagado')
                                                        <a class="dropdown-item" data-coreui-toggle="modal" data-coreui-target="#editModalConstancia-{{ $pago->id }}">Editar</a>
                                                        <a class="dropdown-item" href="{{ route('admin.pagos.descargar', $pago) }}"> Descargar </a>
                                                        <a class="dropdown-item text-danger" data-coreui-i18n="anular" data-coreui-toggle="modal" data-coreui-target="#anularModal-{{ $pago->id }}">Anular pago</a>
                                                    @else
                                                        <a class="dropdown-item" data-coreui-toggle="modal" data-coreui-target="#modalVerMotivo-{{ $pago->id }}">
                                                            <i class="cil-search"></i> Ver motivo de anulación
                                                        </a>
                                                    @endif
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
                {{-- CARNETS --}}
                <div class="tab-pane fade" id="pane-carnets" role="tabpanel">
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Solicitudes de Carnets</h5>
                        <button class="btn btn-primary btn-sm" data-coreui-toggle="modal" data-coreui-target="#modalNuevoCarnet">
                            <i class="cil-file"></i> Nuevo Pago de Carnet
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="tablaCarnets" class="table border mb-0">
                            <thead class="fw-semibold text-nowrap">
                            <tr class="align-middle">
                                <th class="bg-body-secondary text-center">N°</th>
                                <th class="bg-body-secondary text-center">Fecha de Pago</th>
                                <th class="bg-body-secondary text-center">Comprobante</th>
                                <th class="bg-body-secondary text-center">Monto</th>
                                <th class="bg-body-secondary text-center">Tipo Solicitud</th>
                                <th class="bg-body-secondary text-center">Entrega de Carnet</th>
                                <th class="bg-body-secondary text-center">Estado</th>
                                <th class="bg-body-secondary text-center">Acción</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($pagos->where('tipo_pago', 'Carnet') as $pago)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="text-center">
                                        <div class="text-nowrap">{{ \Carbon\Carbon::parse($pago->fecha_pago)->translatedFormat('d \d\e F, Y') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="text-nowrap">{{ $pago->comprobante }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="text-nowrap">S/ {{ number_format($pago->monto, 2) }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="text-nowrap">
                                            @if($pago->carnet)
                                                {{-- Mostramos el valor guardado en la tabla carnets --}}
                                                <strong>{{ $pago->carnet->tipo_tramite }}</strong>
                                            @else
                                                <span class="text-muted small">No especificado</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($pago->carnet)
                                            <span class="badge {{ $pago->carnet->estado_entrega == 'Pendiente' ? 'bg-warning' : 'bg-info' }}">
                                                {{ $pago->carnet->estado_entrega }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Sin solicitud</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($pago->estado == 'Anulado')
                                            <span class="badge bg-danger">Anulado</span>
                                        @else
                                            <span class="badge bg-success">Pagado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-transparent p-0" type="button" data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <svg class="icon"><use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-options')}}"></use></svg>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                {{-- CASO 1: El carnet ya fue entregado --}}
                                                @if($pago->carnet && $pago->carnet->estado_entrega == 'Entregado')
                                                    <a class="dropdown-item" data-coreui-toggle="modal" data-coreui-target="#modalVerDetalleEntrega-{{ $pago->id }}">
                                                        <i class="cil-search"></i> Ver detalle de entrega
                                                    </a>

                                                    {{-- CASO 2: El pago está realizado pero el carnet sigue pendiente --}}
                                                @elseif($pago->estado == 'Pagado')
                                                    <a class="dropdown-item" data-coreui-toggle="modal" data-coreui-target="#editModalCarnet-{{ $pago->id }}">Editar</a>
                                                    <a class="dropdown-item text-danger" data-coreui-toggle="modal" data-coreui-target="#anularModal-{{ $pago->id }}">Anular solicitud</a>

                                                    {{-- CASO 3: El pago fue anulado --}}
                                                @else
                                                    <a class="dropdown-item" data-coreui-toggle="modal" data-coreui-target="#modalVerMotivo-{{ $pago->id }}">
                                                        <i class="cil-search"></i> Ver motivo de anulación
                                                    </a>
                                                @endif
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

    <!-- Modal para crear pago-->
    <div class="modal fade" id="modalNuevoPago" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ingrese Pago</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.pagos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="agremiado_id" value="{{ $agremiado->id }}">

                        <div class="mb-3">
                            <label for="año_crear" class="form-label">Año:</label>
                            <input type="number" id="input_año_pago" class="form-control" maxlength="4" name="año" value="{{ date('Y') }}" step="1" placeholder="YYYY" min="2000" max="2999"
                                   data-siguiente-mes="{{ $siguienteMes }}"
                                   data-siguiente-año="{{ $siguienteAño }}">
                        </div>

                        <div class="mb-3">
                            <label for="mes_inicio_crear" class="form-label">Mes de Inicio:</label>
                            <select id="select_mes_inicio" name="mes_inicio" class="form-select">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}"
                                        {{ $m == $siguienteMes ? 'selected' : '' }}
                                        {{ $m < $siguienteMes ? 'disabled' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="mes_final_crear" class="form-label">Mes Final:</label>
                            <select name="mes_final" id="mes_final" class="form-select">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $m == $siguienteMes ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Comprobante:</label>
                                <input type="text" class="form-control" name="comprobante" placeholder="Ej: F001-123" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Monto a Pagar (S/):</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" name="monto" class="form-control" placeholder="00.00" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="tipo_pago" value="Habilitacion">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($pagos as $pago)
        <!--Modal de actualización de pago-->
        <div class="modal fade" id="editModal-{{ $pago->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $pago->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel-{{ $pago->id }}">Editar Pago: {{ $pago->comprobante }}</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.pagos.update', $pago) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="año_crear" class="form-label">Año:</label>
                                <input type="number" id="input_año_pago" class="form-control" maxlength="4" name="año" value="{{ $pago->año }}" step="1" placeholder="YYYY" min="2000" max="2999"
                                       data-siguiente-mes="{{ $siguienteMes }}"
                                       data-siguiente-año="{{ $siguienteAño }}">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mes Inicio:</label>
                                    <select name="mes_inicio" class="form-select" required>
                                        @foreach(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $index => $mes)
                                            <option value="{{ $index + 1 }}" {{ $pago->mes_inicio == ($index + 1) ? 'selected' : '' }}>
                                                {{ $mes }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mes Final:</label>
                                    <select name="mes_final" class="form-select" required>
                                        @foreach(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $index => $mes)
                                            <option value="{{ $index + 1 }}" {{ $pago->mes_final == ($index + 1) ? 'selected' : '' }}>
                                                {{ $mes }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="nombre-{{ $pago->id }}" class="form-label">Comprobante:</label>
                                <input type="text" class="form-control" id="comprobante-{{ $pago->id }}" name="comprobante" value="{{ $pago->comprobante }}">
                            </div>

                            <div class="mb-3">
                                <label for="rol-{{ $pago->id }}" class="form-label">Monto:</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" class="form-control" id="monto-{{ $pago->id }}" name="monto" value="{{ $pago->monto }}" step="0.01" min="0" required>
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

        <!--Modal de anulacion de pago-->
        <div class="modal fade" id="anularModal-{{ $pago->id }}" tabindex="-1" aria-labelledby="anularModalLabel-{{ $pago->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.pagos.anular', $pago) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Anular Comprobante: {{ $pago->comprobante }}</h5>
                            <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                Atención: Esta acción es irreversible y recalculará la habilitación del agremiado.
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Motivo de la anulación:</label>
                                <textarea name="motivo_anulacion" class="form-control" rows="3" required placeholder="Ej: Pago ingresado por error o comprobante no validado..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Confirmar Anulación</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--Modal de actualización de pago constancia-->
        <div class="modal fade" id="editModalConstancia-{{ $pago->id }}" tabindex="-1" aria-labelledby="editModalConstanciaLabel-{{ $pago->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel-{{ $pago->id }}">Editar Pago: {{ $pago->comprobante }}</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.pagos.update', $pago) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" name="tipo_pago" value="Constancia">

                            <div class="mb-3">
                                <label for="fecha_pago-{{ $pago->id }}" class="form-label">Fecha de Pago:</label>
                                <input type="date" name="fecha_pago" value="{{ $pago->fecha_pago }}" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="comprobante-{{ $pago->id }}" class="form-label">Comprobante:</label>
                                <input type="text" class="form-control" id="comprobante-{{ $pago->id }}" name="comprobante" value="{{ $pago->comprobante }}">
                            </div>

                            <div class="mb-3">
                                <label for="monto-{{ $pago->id }}" class="form-label">Monto:</label>
                                <input type="number" class="form-control" id="monto-{{ $pago->id }}" name="monto" value="{{ $pago->monto }}" step="0.01" min="0" required>
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

    <!--Modal de motivo de anulacion-->
    <div class="modal fade" id="modalVerMotivo-{{ $pago->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de Anulación - {{ $pago->comprobante }}</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold">Estado del Registro:</label>
                        <div><span class="badge bg-danger">Anulado</span></div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Anulado por:</label>
                        <p class="text-muted">{{ $pago->anulado_por ?? 'Usuario no registrado' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Fecha y Hora de Anulación:</label>
                        <p class="text-muted">
                            {{ $pago->fecha_anulacion ? \Carbon\Carbon::parse($pago->fecha_anulacion)->format('d/m/Y H:i:s') : 'No registrada' }}
                        </p>
                    </div>
                    <div class="mb-0">
                        <label class="fw-bold">Motivo:</label>
                        <div class="p-3 border rounded">
                            {{ $pago->motivo_anulacion }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal de actualización de pagos de carnet-->
        <div class="modal fade" id="editModalCarnet-{{ $pago->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title"><i class="cil-pencil"></i> Editar Solicitud de Carnet</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.pagos.update', $pago) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" name="tipo_pago" value="Carnet">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipo de Trámite:</label>
                                <select name="tipo_tramite" class="form-select" required>
                                    <option value="Colegiatura" {{ $pago->carnet && $pago->carnet->tipo_tramite == 'Colegiatura' ? 'selected' : '' }}>Por Colegiatura</option>
                                    <option value="Duplicado" {{ $pago->carnet && $pago->carnet->tipo_tramite == 'Duplicado' ? 'selected' : '' }}>Duplicado</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Número de Comprobante:</label>
                                <input type="text" name="comprobante" class="form-control" value="{{ $pago->comprobante }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Monto (S/):</label>
                                <input type="number" name="monto" class="form-control" step="0.01" value="{{ $pago->monto }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Fecha de Pago:</label>
                                <input type="date" name="fecha_pago" class="form-control" value="{{ $pago->fecha_pago }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Ver Detalle de Entrega --}}
        <div class="modal fade" id="modalVerDetalleEntrega-{{ $pago->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- 1. Verificamos que el objeto carnet no sea null --}}
                    @if($pago->carnet)
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title"><i class="cil-info"></i> Detalle de Entrega de Carnet</h5>
                            <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="fw-bold">Fecha de Entrega:</label>
                                <p class="text-muted">
                                    {{-- 2. Usamos el helper optional o una verificación de la fecha --}}
                                    {{ $pago->carnet->fecha_entrega ? \Carbon\Carbon::parse($pago->carnet->fecha_entrega)->translatedFormat('d \d\e F, Y ') : 'Sin fecha registrada' }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Entregado por:</label>
                                <p class="text-muted">
                                    <i class="cil-user"></i> {{ $pago->carnet->entregado_por ?? 'Usuario del sistema' }}
                                </p>
                            </div>
                        </div>
                    @else
                        {{-- Contenido alternativo si el carnet es null --}}
                        <div class="modal-body text-center p-4">
                            <p class="text-muted">No hay información de carnet disponible para este pago.</p>
                        </div>
                    @endif
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!--Modal de pago de constancia-->
    <div class="modal fade" id="modalNuevaConstancia" tabindex="-1" aria-labelledby="constanciaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="constanciaModalLabel">Registrar Pago de Constancia / Trámite</h5>
                    <button type="button" class="btn-close btn-close-white" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.pagos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="agremiado_id" value="{{ $agremiado->id }}">
                        <input type="hidden" name="tipo_pago" value="Constancia">

                        <div class="alert alert-info py-2">
                            <small>Este registro no afecta la fecha de habilitación profesional.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Fecha de Pago:</label>
                            <input type="date" name="fecha_pago" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Número de Comprobante:</label>
                            <input type="text" name="comprobante" class="form-control" placeholder="Ej: F001-000123" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Monto (S/):</label>
                            <input type="number" name="monto" class="form-control" step="0.01" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para solicitar carnet-->
    <div class="modal fade" id="modalNuevoCarnet" tabindex="-1" aria-labelledby="carnetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="carnetModalLabel">
                        <i class="cil-contact"></i> Nueva Solicitud de Carnet
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.pagos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="agremiado_id" value="{{ $agremiado->id }}">
                        <input type="hidden" name="tipo_pago" value="Carnet">

                        <div class="alert alert-info py-2">
                            <small><i class="cil-info"></i> Al guardar este pago, se generará una solicitud de carnet con estado <strong>Pendiente</strong>.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de Trámite:</label>
                            <select name="tipo_tramite" class="form-select" required>
                                <option value="" disabled selected>Seleccione el tipo...</option>
                                <option value="Colegiatura">Por Colegiatura</option>
                                <option value="Duplicado">Duplicado</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Fecha de Pago:</label>
                            <input type="date" name="fecha_pago" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Número de Comprobante:</label>
                            <input type="text" name="comprobante" class="form-control" placeholder="Ej: F001-000456" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Monto del Derecho (S/):</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" name="monto" class="form-control" step="0.01" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar Pago y Solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('vendors/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{ asset('js/datatables.js')}}"></script>
    <script src="{{ asset('DataTables/datatables.min.js')}}"></script>
    <script>
        // Para el DataTable de agremiado
        $(document).ready(function () {

            // Inicializa DataTables en la tabla con el ID 'tablaUsuarios'
            $('#tablaCuotas').DataTable({
                // Opcional: Poner la tabla en español
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json',
                    emptyTable: "No hay pagos registrados para este agremiado",
                    zeroRecords: "No se encontraron resultados",
                }
            });

            $('#tablaConstancia').DataTable({
                // Opcional: Poner la tabla en español
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json',
                    emptyTable: "No hay pagos registrados para este agremiado",
                    zeroRecords: "No se encontraron resultados",
                }
            });

            $('#tablaCarnets').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json',
                    emptyTable: "No hay pagos de carnet para este agremiado",
                    zeroRecords: "No se encontraron resultados",
                }
            });

        });

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Recuperar la última pestaña guardada
            const activeTab = localStorage.getItem('activeAgremiadoTab');

            if (activeTab) {
                // 2. Si existe, activar esa pestaña automáticamente
                const tabTrigger = document.querySelector(`[data-coreui-target="${activeTab}"]`);
                if (tabTrigger) {
                    const tab = new coreui.Tab(tabTrigger);
                    tab.show();
                }
            }

            // 3. Escuchar cada vez que el usuario hace clic en una pestaña para guardar el ID
            const tabLinks = document.querySelectorAll('[data-coreui-toggle="tab"]');
            tabLinks.forEach(link => {
                link.addEventListener('shown.coreui.tab', function (event) {
                    const tabId = event.target.getAttribute('data-coreui-target');
                    localStorage.setItem('activeAgremiadoTab', tabId);
                });
            });
        });

        document.getElementById('input_año_pago').addEventListener('input', function() {
            const añoIngresado = parseInt(this.value);
            const sigMes = parseInt(this.dataset.siguienteMes);
            const sigAño = parseInt(this.dataset.siguienteAño);
            const selectMes = document.getElementById('select_mes_inicio');
            const opciones = selectMes.querySelectorAll('option');

            opciones.forEach(option => {
                const valorMes = parseInt(option.value);

                if (añoIngresado > sigAño) {
                    // ESCENARIO A: Año futuro -> Apertura total de meses
                    option.disabled = false;
                } else if (añoIngresado === sigAño) {
                    // ESCENARIO B: Es el año de la deuda -> Bloquear meses ya pagados
                    option.disabled = (valorMes < sigMes);

                    // Si el mes seleccionado quedó bloqueado, saltar al primer mes disponible
                    if (selectMes.value < sigMes) {
                        selectMes.value = sigMes;
                    }
                } else {
                    // ESCENARIO C: Año pasado -> Apertura total (opcional para regularizaciones)
                    option.disabled = false;
                }
            });
        });
    </script>
@endpush
