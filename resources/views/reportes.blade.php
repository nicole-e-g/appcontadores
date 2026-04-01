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
    <div class="container-lg">
        <div class="card mb-4">
            <div class="card-header"><strong>Módulo de Reportes</strong></div>
            <div class="card-body">
                <p>Seleccione el tipo de reporte que desea generar:</p>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-primary mb-3">
                            <div class="card-body text-center">
                                <h5 class="card-title">Padrón General</h5>
                                <p class="card-text text-muted">Lista completa de agremiados con estados y sedes.</p>
                                <a href="{{ route('admin.reportes.exportar') }}" class="btn btn-success text-white">
                                    <i class="cil-cloud-download"></i> Descargar Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
