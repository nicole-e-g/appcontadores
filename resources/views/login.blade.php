<!DOCTYPE html>
<html lang="en">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>AGREMIADOS - Login</title>
    <link rel="stylesheet" href="{{ asset('vendors/simplebar/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendors/simplebar.css') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/examples.css') }}" rel="stylesheet">
    <script src="{{ asset('js/config.js') }}"></script>
    <script src="{{ asset('js/color-modes.js') }}"></script>
    <style>
      /* Estilos para el mensaje de error de Laravel */
      .is-invalid {
        border-color: #e55353 !important;
      }
      .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #e55353;
      }
    </style>
  </head>
  <body>
    <div class="bg-body-tertiary min-vh-100 d-flex flex-row align-items-center">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="card-group d-block d-md-flex row">
              <div class="card col-md-7 p-4 mb-0">
                <div class="card-body">
                  <h1>Login</h1>
                  <p class="text-body-secondary">Ingresa tus credenciales para acceder</p>

                  <form method="POST" action="{{ url('admin/login') }}">
                    @csrf 
                    <div class="input-group mb-3">
                      <span class="input-group-text">
                        <svg class="icon">
                          <use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-user') }}"></use>
                        </svg>
                      </span>
                      <input 
                        class="form-control @error('identificador') is-invalid @enderror" 
                        type="text" 
                        placeholder="Usuario" 
                        name="identificador" 
                        value="{{ old('identificador') }}" 
                        required 
                        autofocus
                      >
                      @error('identificador')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="input-group mb-4">
                      <span class="input-group-text">
                        <svg class="icon">
                          <use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-lock-locked') }}"></use>
                        </svg>
                      </span>
                      <input 
                        class="form-control @error('password') is-invalid @enderror" 
                        type="password" 
                        placeholder="Contraseña" 
                        name="password" 
                        required
                      >
                       @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    
                    <div class="row">
                      <div class="col-6">
                        <button class="btn btn-primary px-4" type="submit">Ingresar</button>
                      </div>
                      <!-- <div class="col-6 text-end">
                        <button class="btn btn-link px-0" type="button">¿Olvidaste la contraseña?</button>
                      </div> -->
                    </div>
                  </form>
                  </div>
              </div>
              
              <div class="card col-md-5 text-white bg-primary py-5">
                <div class="card-body text-center">
                  <div>
                    <h2>Sistema de Agremiados</h2>
                    <p>Acceso exclusivo para personal administrativo. Si no tienes una cuenta, contacta a tu supervisor.</p>
                    <!-- <a class="btn btn-lg btn-outline-light mt-3" href="#">Contactar</a> -->
                  </div>
                </div>
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="{{ asset('vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
    <script src="{{ asset('vendors/simplebar/js/simplebar.min.js') }}"></script>
  </body>
</html>