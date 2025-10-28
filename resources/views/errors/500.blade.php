<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">
@section('title', 'Sin conexión')
@include('partials.head')

<style>
@keyframes slideSideways {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(40px); }
}
.slide-sideways {
    animation: slideSideways 3s ease-in-out infinite;
}
</style>
<body class="error-page">
    <div class="auth-page-wrapper pt-5">
        <!-- Fondo y formas -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="auth-page-content">
            <div class="container text-center">
                <div class="row justify-content-center">
                    <div class="col-lg-8 pt-4">

                        <div class="mb-4" style="padding: 40px;">
                            <img src="{{ asset('assets/images/error500.png') }}" alt="Sin conexión"
                                 class="error-basic-img slide-sideways mx-auto d-block" style="max-width: 300px;">
                        </div>

                        <div class="mx-auto bg-white p-4 rounded shadow" style="max-width: 450px;">
                            <h1 class="display-1 fw-medium text-dark">500</h1>
                            <h3 class="text-uppercase text-dark">Sin conexión 😞</h3>
                            <p class="text-muted mb-4">Por favor, verifica tu conexión y vuelve a intentarlo.</p>
                            <button id="retry-btn" class="btn btn-primary">
                                <i class="mdi mdi-reload me-1"></i> Reintentar
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>

    @stack('scripts')

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const retryBtn = document.getElementById('retry-btn');
        if (retryBtn) {
            retryBtn.addEventListener('click', () => {
                if (navigator.onLine) {
                    location.reload();
                }
            });
        }
    });
    </script>

</body>
</html>
