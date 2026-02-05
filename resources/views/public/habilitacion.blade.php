<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Habilitación - Colegio de Contadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <img src="{{ asset('assets/img/Logotipo_Colegio.png') }}" height="100" class="mb-4">
                <h2 class="mb-4">Consulta de Habilitacion Profesional</h2>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form action="{{ route('public.habilidad.buscar') }}" method="POST">
                            @csrf
                            <div class="input-group mb-3">
                                <input type="text" name="criterio" class="form-control" placeholder="Ingrese DNI o Matrícula" required>
                                <button class="btn btn-primary" type="submit">Consultar</button>
                            </div>
                        </form>
                    </div>
                </div>

                @if(isset($agremiado))
                    <div class="card shadow border-0">
                        <div class="card-body p-4">
                            <h4 class="card-title fw-bold text-uppercase">{{ $agremiado->nombres }} {{ $agremiado->apellidos }}</h4>
                            <p class="text-muted">DNI: {{ $agremiado->dni }}</p>
                            <p class="text-muted">Matrícula: {{ $agremiado->matricula }}</p>
                            <hr>

                            {{-- 1. CASO VITALICIO: Prioridad absoluta --}}
                            @if($agremiado->es_vitalicio)
                                <div class="alert alert-info py-4">
                                    <h3 class="fw-bold mb-0">VITALICIO</h3>
                                    <p class="mb-0">
                                        <i class="bi bi-star-fill"></i> Habilitación Profesional Permanente
                                    </p>
                                </div>
                                <p class="text-success fw-bold">Este miembro se encuentra apto para el ejercicio profesional.</p>
                            {{-- 2. CASO HABILITADO: Por pagos vigentes --}}
                            @elseif($agremiado->estado == 'Habilitado')
                                <div class="alert alert-success">
                                    <h3 class="mb-0">HABILITADO</h3>
                                    <p>Habilitación vigente hasta:
                                        <strong>{{ \Carbon\Carbon::parse($agremiado->fin_habilitacion)->translatedFormat('d F Y') }}</strong>
                                    </p>
                                </div>
                            @else
                                {{-- 3. CASO INHABILITADO --}}
                                <div class="alert alert-danger">
                                    <h3 class="mb-0">INHABILITADO</h3>
                                    @if($agremiado->fin_habilitacion)
                                        Habilitado hasta: {{ \Carbon\Carbon::parse($agremiado->fin_habilitacion)->format('d/m/Y') }}
                                    @else
                                        <span class="text-danger">SIN FECHA DE VENCIMIENTO REGISTRADA</span>
                                    @endif
                                </div>
                                <p>Por favor, acérquese al Colegio para regularizar su situación.</p>
                            @endif
                        </div>
                    </div>
                @elseif(request()->isMethod('post'))
                    <div class="alert alert-warning">No se encontró ningún agremiado con los datos proporcionados.</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
