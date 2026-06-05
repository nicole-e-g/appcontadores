<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Datos - Congreso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span>Validación de Datos del Participante</span>
                    <a href="{{ route('congreso.salir') }}" class="btn btn-sm btn-danger">Salir</a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('congreso.actualizar') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">N° Documento (DNI)</label>
                                <input type="text" class="form-control bg-body-secondary" value="{{ $participante->dni }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Modalidad Registrada</label>
                                <input type="text" class="form-control bg-body-secondary border-warning" value="{{ $participante->modalidad }}" readonly>
                                <div class="form-text text-warning x-small">La modalidad no puede ser cambiada externamente.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombres</label>
                            <input type="text" name="nombres" class="form-control" value="{{ old('nombres', $participante->nombres) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos', $participante->apellidos) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $participante->email) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Celular</label>
                                <input type="text" name="celular" class="form-control" value="{{ old('celular', $participante->celular) }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña del Aula Virtual</label>
                            <input type="text" class="form-control bg-body-secondary" value="E#drX&pA(8X" readonly>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success text-white">Guardar y Confirmar Datos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
