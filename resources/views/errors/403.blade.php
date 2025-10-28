<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">
@section('title', '403 | Acceso denegado')
@include('partials.head')
<body class="error-page">
    <div class="auth-page-wrapper pt-5">
        <!-- Fondo y decoraciones -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center pt-4">
                            <div>
                                <img src="{{ asset('assets/images/comingsoon.png') }}" alt="Error 403" class="error-basic-img move-animation">
                            </div>
                            <div class="mt-n4">
                                <h1 class="display-1 fw-medium">403</h1>
                                <h3 class="text-uppercase">Acceso denegado 😢</h3>
                                <p class="text-muted mb-4">No tienes permiso para acceder a esta página.</p>
                                @if(Auth::check())
                                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                                        <i class="mdi mdi-arrow-left me-1"></i> Volver al Dashboard
                                    </a>
                                @else
                                    <a href="{{ url('/') }}" class="btn btn-primary">
                                        <i class="mdi mdi-home me-1"></i> Volver al inicio
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
        </div>

        <!-- footer -->
        @include('partials.footer')
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    @stack('scripts')
</body>
</html>
