<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-layout-style="" data-layout-mode="dark" data-layout-width="" data-layout-position="scrolled">

<head>
    @include('partials.head')
    @vite(['resources/js/app.js']) <!-- o @mix si usas Mix -->
</head>

<body>
<!--<div id="preloader"></div>-->
    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('partials.header')

        @include('partials.menu')

        <div class="horizontal-overlay"></div>

    <div class="main-content">

        <!-- Spinner SOLO para el área de contenido
        <div id="main-loader" class="content-preloader">
            <div class="spinner"></div>
        </div>-->

        <!-- Contenido oculto hasta que se cargue -->
        <div id="main-content-wrapper" class="page-content">
            @yield('content')
        </div>

        @include('partials.footer')
    </div>


    </div>

    @include('partials.top')

    @include('partials.js')

    @stack('scripts')

    @include('partials.spinner')
</body>

<div id="fullscreen-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; cursor:pointer;">
  <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); color:white; font-size:1.2rem;">
    Haz clic para volver a pantalla completa
  </div>
</div>

</html>
