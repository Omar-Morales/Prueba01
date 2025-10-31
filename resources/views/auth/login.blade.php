<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">
@section('title', 'Inicio de Sesión | Sistema de Gestión de Pedidos')
@include('partials.head')
<body>
    <div class="auth-page-wrapper pt-5">

        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                @include('svgs.shape')
            </div>
        </div>

        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <a href="/" class="d-inline-block auth-logo">
                                    <img src="{{ asset('assets/images/shop-light.png') }}" alt="Shop Logo" height="100">
                                </a>
                            </div>
                            <p class="mt-3 fs-15 fw-medium">Sistema de Gestion de Pedidos</p>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">¡Bienvenido!</h5>
                                    <p class="text-muted">Inicia sesión en BD&S.</p>
                                </div>

                                <div class="p-2 mt-4">
                                    <form action="{{ route('login') }}" method="POST">
                                        @csrf

                                        @if(session('status'))
                                            <div class="alert alert-danger">
                                                {{ session('status') }}
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Correo Electrónico</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Ingresa tu correo electrónico" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="password">Contraseña</label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">

                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" type="button" id="password-addon">
                                                    <i class="ri-eye-off-fill align-middle" id="toggle-password-icon"></i>
                                                </button>
                                                <input type="password" class="form-control pe-5 @error('password') is-invalid @enderror" name="password" id="password" placeholder="Ingresa tu contraseña" required>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="auth-remember-check">
                                            <label class="form-check-label" for="auth-remember-check">Recuérdame</label>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-primary w-100" type="submit">Iniciar Sesión</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- footer -->
        @include('partials.footer')
        <!-- end Footer -->

    </div>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/libs/particles.js/particles.js') }}"></script>
    <script src="{{ asset('assets/js/pages/particles.app.js') }}"></script>
    <style>
    .auth-one-bg .bg-overlay {
        background: linear-gradient(to bottom, #05175f, #4c0d2a) !important; /* 🌈 Tus colores */
        opacity: 0.85 !important;
    }
    </style>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('toggle-password-icon');

    passwordIcon.addEventListener('click', function() {
            if (passwordInput.type === "password") {
                passwordIcon.classList.add('ri-eye-fill');
                passwordIcon.classList.remove('ri-eye-off-fill');
                passwordInput.type = "text";
            }else {
                passwordIcon.classList.add('ri-eye-off-fill');
                passwordIcon.classList.remove('ri-eye-fill');
                passwordInput.type = "password";
            }
    });
    });

    </script>
</body>
</html>
