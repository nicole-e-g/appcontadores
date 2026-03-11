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

  <!-- Empieza el body-->
    <!-- Recorrido-->
        <div class="container-fluid px-4">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0">
              <li class="breadcrumb-item">Home
              </li>
              <li class="breadcrumb-item active"><span>Dashboard</span>
              </li>
            </ol>
          </nav>
        </div>
      </header>
    <!-- cierra recorrido-->
    <div class="body flex-grow-1">
        <div class="container-lg px-4">
          <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
              <div class="card text-white bg-success">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fs-4 fw-semibold">{{ $totalHabilitados }} </div>
                    <div>Habilitados</div>
                  </div>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:87px;">
                  <canvas class="chart" id="card-chart1" height="87"></canvas>
                </div>
              </div>
            </div>
            <!-- /.col-->
            <div class="col-sm-6 col-xl-3">
              <div class="card text-white bg-danger">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fs-4 fw-semibold">{{ $totalInhabilitados }}</div>
                    <div>Inhabilitados</div>
                  </div>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:87px;">
                  <canvas class="chart" id="card-chart2" height="87"></canvas>
                </div>
              </div>
            </div>
            <!-- /.col-->
            <div class="col-sm-6 col-xl-3">
              <div class="card text-white bg-info">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fs-4 fw-semibold">{{ $totalVitalicios }}</div>
                    <div>Vitalicios</div>
                  </div>
                </div>
                <div class="c-chart-wrapper mt-3" style="height:87px;">
                  <canvas class="chart" id="card-chart3" height="87"></canvas>
                </div>
              </div>
            </div>
            <!-- /.col-->
              <div class="col-sm-6 col-xl-3">
                  <div class="card shadow-sm bg-primary border-0">
                      <div class="card-body">
                          <div class="d-flex justify-content-between align-items-start mb-3">
                              <div class="text-body-secondary small text-uppercase fw-semibold">Distribución por Género</div>
                              <i class="cil-people text-primary fs-4"></i>
                          </div>

                          <div class="progress-group mb-3">
                              <div class="progress-group-header align-items-end">
                                  <svg class="icon icon-lg me-2 text-info"><use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-user')}}"></use></svg>
                                  <div>Varones</div>
                                  <div class="ms-auto fw-bold fs-5">{{ $totalVarones }}</div>
                              </div>
                              <div class="progress-group-bars">
                                  <div class="progress progress-thin">
                                      <div class="progress-bar bg-info" role="progressbar"
                                           style="width: {{ ($totalVarones + $totalMujeres > 0) ? ($totalVarones / ($totalVarones + $totalMujeres)) * 100 : 0 }}%"
                                           aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                  </div>
                              </div>
                          </div>

                          <div class="progress-group">
                              <div class="progress-group-header align-items-end">
                                  <svg class="icon icon-lg me-2 text-warning"><use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-user-female')}}"></use></svg>
                                  <div>Mujeres</div>
                                  <div class="ms-auto fw-bold fs-5">{{ $totalMujeres }}</div>
                              </div>
                              <div class="progress-group-bars">
                                  <div class="progress progress-thin">
                                      <div class="progress-bar bg-warning" role="progressbar"
                                           style="width: {{ ($totalVarones + $totalMujeres > 0) ? ($totalMujeres / ($totalVarones + $totalMujeres)) * 100 : 0 }}%"
                                           aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
            <!-- /.col-->
          </div>
          <!-- /.row-->
          <div class="card mb-4">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h4 class="card-title mb-0">Habilitaciones</h4>
                </div>
                <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
                  <div class="btn-group btn-group-toggle mx-3" data-coreui-toggle="buttons">
                    <input class="btn-check" id="option2" type="radio" name="options" autocomplete="off" checked="">
                    <label class="btn btn-outline-secondary active"> Month</label>
                  </div>
                </div>
              </div>
              <div class="c-chart-wrapper" style="height:300px;margin-top:40px;">
                <canvas class="chart" id="grafico-habilitados" height="300"></canvas>
              </div>
            </div>
          </div>
          <!-- /.card-->
            <div class="row g-4 mb-4">
                @foreach($sedesStats as $sede)
                    <div class="col-sm-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header position-relative d-flex justify-content-center align-items-center bg-dark" style="height: 100px;">
                                <svg class="icon icon-3xl text-white my-4">
                                    <use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-location-pin')}}"></use>
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle w-100 text-center">
                                    <h5 class="text-white mt-5 pt-3">{{ $sede->sede}}</h5>
                                </div>
                            </div>
                            <div class="card-body row text-center">
                                <div class="col">
                                    <div class="fs-5 fw-semibold">{{ $sede->total }}</div>
                                    <div class="text-uppercase text-body-secondary small">Total Agremiados</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const datosHabilitaciones = @json($dataGrafico);
            const mesesLabels = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

            // Usamos el NUEVO ID
            const ctx = document.getElementById('grafico-habilitados');

            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: mesesLabels,
                        datasets: [{
                            label: 'Habilitaciones 2026',
                            backgroundColor: 'rgba(50, 31, 219, 0.1)',
                            borderColor: '#321fdb',
                            pointBackgroundColor: '#321fdb',
                            data: datosHabilitaciones, // Datos reales de tu controlador
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1, // Para que no salgan decimales en personas
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
