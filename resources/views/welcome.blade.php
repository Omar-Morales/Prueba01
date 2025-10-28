<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">

<head>

    <meta charset="utf-8" />
    <title>Bienvenido | Sistema de Gestión de Pedidos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/shop-light.png') }}">

    <!--Swiper slider css-->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />
<style>
    @media (max-width: 991.98px) {
    .navbar-light .navbar-nav .nav-item .nav-link {
        color: unset !important;
    }
    }

    /*.nft-hero .bg-overlay {
        background: linear-gradient(to bottom, #05175f, #4c0d2a) !important; /* 🌈 Tus colores */
        opacity: 0.9 !important;
    }*/
</style>
</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <nav class="navbar navbar-expand-lg navbar-landing navbar-light fixed-top" id="navbar">
            <div class="container-fluid px-5">
                <a class="navbar-brand" href="index.html">
                    <img src="{{ asset('assets/images/shop-dark.png') }}" class="card-logo card-logo-dark" alt="logo dark" width="25">
                    <img src="{{ asset('assets/images/shop-light.png') }}" class="card-logo card-logo-light" alt="logo light" width="25">
                </a>
                <button class="navbar-toggler py-0 fs-20 text-body" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="mdi mdi-menu"></i>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto mt-2 mt-lg-0" id="navbar-example">
                        <li class="nav-item">
                            <a class="nav-link active" href="#hero">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#wallet">Gestión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#marketplace">Productos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#categories">Categorias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#proveedores">Proveedores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#creators">Destacados</a>
                        </li>
                    </ul>

                    <div class="">
                        @auth
                            <!-- Si el usuario ya ha iniciado sesión, redirigir al dashboard -->
                            <a href="{{ route('dashboard') }}" class="btn btn-danger">Ir al Dashboard</a>
                        @else
                            <!-- Si el usuario no ha iniciado sesión, mostrar opciones -->
                            <a href="{{ route('login') }}" class="btn btn-danger">Iniciar Sesión</a>
                        @endauth
                    </div>
                </div>

            </div>
        </nav>
            <div class="bg-overlay bg-overlay-pattern"></div>
        <!-- end navbar -->

        <!-- start hero section -->
        <section class="section nft-hero" id="hero">
            <div class="bg-overlay"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-sm-10">
                        <div class="text-center">
                            <h1 class="display-4 fw-medium mb-4 lh-base text-white">Sistema de Gestión de Pedidos<span class="text-success">SHOP</span></h1>
                            <p class="lead text-white-50 lh-base mb-4 pb-2">Compra y vende productos de forma rápida, segura y sin complicaciones. Únete a SHOP y descubre una nueva forma de conectar con compradores y vendedores en todo el país.</p>

                            <div class="hstack gap-2 justify-content-center">
                                <a href="apps-nft-create.html" class="btn btn-primary">Inscribete Ahora <i class="ri-arrow-right-line align-middle ms-1"></i></a>
                                <a href="apps-nft-explore.html" class="btn btn-danger">Explora Productos <i class="ri-arrow-right-line align-middle ms-1"></i></a>
                            </div>
                        </div>
                    </div><!--end col-->
                </div><!-- end row -->
            </div><!-- end container -->
        </section><!-- end hero section -->

        <!-- start wallet -->
        <section class="section" id="wallet">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold lh-base">Gestión de Pagos y Transacciones</h2>
                            <p class="text-muted">Desde tu panel podrás gestionar tus ingresos por ventas, realizar compras de productos y llevar un control detallado de tus transacciones en tiempo real.</p>
                        </div>
                    </div><!-- end col -->
                </div><!-- end row -->

                <div class="row g-4 px-5">
                    <div class="col-lg-4">
                        <div class="card text-center border shadow-none">
                            <div class="card-body py-5 px-4">
                                <img src="{{ asset('assets/images/verification-img.png') }}" alt="" height="55" class="mb-3 pb-2">
                                <h5>Consulta de Saldo</h5>
                                <p class="text-muted pb-1">Visualiza tu saldo disponible según tus ventas realizadas en la plataforma.</p>
                                <a href="#!" class="btn btn-soft-info">Ir al Panel</a>
                            </div>
                        </div>
                    </div><!-- end col -->
                    <div class="col-lg-4">
                        <div class="card text-center border shadow-none">
                            <div class="card-body py-5 px-4">
                                <img src="{{ asset('assets/images/auth-offline.gif') }}" alt="" height="55" class="mb-3 pb-2">
                                <h5>Historial de Ventas</h5>
                                <p class="text-muted pb-1">Accede a los detalles de todas tus ventas, productos vendidos y sus movimientos.</p>
                                <a href="#!" class="btn btn-info">Ver Ventas</a>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4">
                        <div class="card text-center border shadow-none">
                            <div class="card-body py-5 px-4">
                                <img src="{{ asset('assets/images/faq-img.png') }}" alt="" height="55" class="mb-3 pb-2">
                                <h5>Transacciones</h5>
                                <p class="text-muted pb-1">Consulta el historial completo de tus transacciones dentro del sistema.</p>
                                <a href="#!" class="btn btn-soft-info">Ver Transacciones</a>
                            </div>
                        </div>
                    </div><!-- end col -->
                </div><!-- end row -->
            </div><!-- end container -->
        </section><!-- end wallet -->

        <!-- start marketplace -->
        <section class="section bg-light" id="marketplace">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold lh-base">Explore Products</h2>
                            <p class="text-muted mb-4">Navega por nuestra exclusiva colección y aprovecha las mejores ofertas del momento.</p>
                            <ul class="nav nav-pills filter-btns justify-content-center" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-medium active" type="button" data-filter="all">All Items</button>
                                </li>
                                @foreach($categories as $category)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link fw-medium" type="button" data-filter="category-{{ $category->id }}">{{ $category->name }}</button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div><!-- end col -->
                </div><!-- end row -->
                <div class="row px-5">

                    @foreach($products as $product)
                        <div class="col-lg-3 product-item category-{{ $product->category_id }}">
                            <div class="card explore-box card-animate">
                                <div class="bookmark-icon position-absolute top-0 end-0 p-2">
                                    <button type="button" class="btn btn-icon active" data-bs-toggle="button" aria-pressed="true">
                                        <i class="mdi mdi-cards-heart fs-16"></i>
                                    </button>
                                </div>
                                <div class="explore-place-bid-img">
                                    <img src="{{ $product->images->first()?->url ?? asset('assets/images/product.png') }}" class="card-img-top explore-img" alt="{{ $product->name }}" />
                                    <div class="bg-overlay"></div>
                                    <div class="place-bid-btn">
                                        <a href="#" class="btn btn-success">
                                            <i class="ri-auction-fill align-bottom me-1"></i> View Details
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="fw-medium mb-0 float-end">
                                        <i class="mdi mdi-heart text-danger align-middle"></i> {{ rand(1000, 10000) }}
                                    </p>
                                    <h5 class="mb-1"><a href="#">{{ $product->name }}</a></h5>
                                    <p class="text-muted mb-0">{{ $product->category->name }}</p>
                                </div>
                                <div class="card-footer border-top border-top-dashed">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 fs-14">
                                            <i class="ri-price-tag-3-fill text-warning align-bottom me-1"></i> Price:
                                            <span class="fw-medium">${{ number_format($product->price, 2) }}</span>
                                        </div>
                                        <h5 class="flex-shrink-0 fs-14 text-primary mb-0">Qty: {{ $product->quantity }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div><!-- end container -->
        </section>
        <!-- end marketplace -->

        <!-- start features -->
        <section class="section">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold lh-base">Compra y Venta Fácil de Equipos Tecnológicos</h2>
                            <p class="text-muted">Encuentra los mejores equipos o vende tus dispositivos con seguridad y rapidez en nuestra plataforma.</p>
                        </div>
                    </div><!-- end col -->
                </div><!-- end row -->

                <div class="row px-5">
                    <div class="col-lg-3">
                        <div class="card shadow-none">
                            <div class="card-body">
                                <img src="assets/images/nft/wallet.png" alt="" class="avatar-sm">
                                <h5 class="mt-4">Crea tu cuenta</h5>
                                <p class="text-muted fs-14">Regístrate gratis y configura tu perfil para empezar a comprar o vender.</p>
                                <a href="#!" class="link-success fs-14">Más info <i class="ri-arrow-right-line align-bottom"></i></a>
                            </div>
                        </div>
                    </div><!--end col-->
                    <div class="col-lg-3">
                        <div class="card shadow-none">
                            <div class="card-body">
                                <img src="assets/images/nft/money.png" alt="" class="avatar-sm">
                                <h5 class="mt-4">Publica tus equip</h5>
                                <p class="text-muted fs-14">Sube fotos y detalla especificaciones para mostrar tus dispositivos.</p>
                                <a href="#!" class="link-success fs-14">Más info <i class="ri-arrow-right-line align-bottom"></i></a>
                            </div>
                        </div>
                    </div><!--end col-->
                    <div class="col-lg-3">
                        <div class="card shadow-none">
                            <div class="card-body">
                                <img src="assets/images/nft/add.png" alt="" class="avatar-sm">
                                <h5 class="mt-4">Compra con confianza</h5>
                                <p class="text-muted fs-14">Busca y adquiere equipos con garantía y contacto directo con el vendedor.</p>
                                <a href="#!" class="link-success fs-14">Más info <i class="ri-arrow-right-line align-bottom"></i></a>
                            </div>
                        </div>
                    </div><!--end col-->
                    <div class="col-lg-3">
                        <div class="card shadow-none">
                            <div class="card-body">
                                <img src="assets/images/nft/sell.png" alt="" class="avatar-sm">
                                <h5 class="mt-4">Vende con confianza</h5>
                                <p class="text-muted fs-14">Brinda productos al alacanze mundial.</p>
                                <a href="#!" class="link-success fs-14">Más info <i class="ri-arrow-right-line align-bottom"></i></a>
                            </div>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->
            </div><!-- end container -->
        </section><!-- end features -->

        <!-- start plan -->
        <section class="section bg-light" id="categories">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-5">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold lh-base">Categorías Destacadas</h2>
                            <p class="text-muted">Explora las categorías más activas con productos destacados.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="swiper mySwiper pb-4">
                            <div class="swiper-wrapper">
                                @foreach($categoriescount as $category)
                                    @php
                                        $categoryProducts = $products->where('category_id', $category->id)->take(4)->values();
                                    @endphp
                                    <div class="swiper-slide">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row g-1 mb-3">
                                                    <div class="col-6">
                                                        @if(isset($categoryProducts[0]))
                                                            <div class="ratio ratio-1x1 mb-1">
                                                                <img src="{{ $categoryProducts[0]->main_image_url }}" class="w-100 rounded object-fit-cover" alt="img">
                                                            </div>
                                                        @endif
                                                        @if(isset($categoryProducts[1]))
                                                            <div class="ratio ratio-1x1">
                                                                <img src="{{ $categoryProducts[1]->main_image_url }}" class="w-100 rounded object-fit-cover" alt="img">
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-6">
                                                        @if(isset($categoryProducts[2]))
                                                            <div class="ratio ratio-1x1 mb-1">
                                                                <img src="{{ $categoryProducts[2]->main_image_url }}" class="w-100 rounded object-fit-cover" alt="img">
                                                            </div>
                                                        @endif
                                                        @if(isset($categoryProducts[3]))
                                                            <div class="ratio ratio-1x1">
                                                                <img src="{{ $categoryProducts[3]->main_image_url }}" class="w-100 rounded object-fit-cover" alt="img">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0 fs-16">
                                                        {{ $category->name }}
                                                        <span class="badge bg-success-subtle text-success">{{ $category->products_count }}</span>
                                                    </h5>
                                                    <span class="text-muted small">Ver todos</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-pagination swiper-pagination-dark"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- end plan -->

        <!-- start Discover Items-->
        <section class="section" id="proveedores">
            <div class="container-fluid px-5">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="d-flex align-items-center mb-5">
                            <h2 class="mb-0 fw-semibold lh-base flex-grow-1">Top 3 Proveedores y sus Productos Estrella</h2>
                            <a href="apps-nft-explore.html" class="btn btn-primary">Ver los Top <i class="ri-arrow-right-line align-bottom"></i></a>
                        </div>
                    </div>
                </div><!-- end row -->
                <div class="row">
                @foreach ($topProveedores as $item)
                        <div class="col-lg-4 mb-4">
                            <div class="card explore-box card-animate border">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="{{ $item->supplier_photo && file_exists(public_path('storage/' . $item->supplier_photo))
                                            ? asset('storage/' . $item->supplier_photo)
                                            : asset('assets/images/customers.png') }}"
                                            alt="{{ $item->supplier_name }}"
                                            class="avatar-xs rounded-circle"
                                        >
                                        <div class="ms-3 flex-grow-1">
                                            <h6 class="mb-0 fs-15">{{ $item->supplier_name }}</h6>
                                            <p class="mb-0 text-muted fs-13">Proveedor destacado</p>
                                        </div>
                                    </div>
                                    <div class="explore-place-bid-img overflow-hidden rounded">
                                        <img src="{{ $item->product_image && file_exists(public_path('storage/' . $item->product_image))
                                            ? asset('storage/' . $item->product_image)
                                            : asset('assets/images/product.png') }}"
                                            alt="{{ $item->product_name }}"
                                            class="explore-img w-100"
                                        >
                                    </div>
                                    <div class="mt-3">
                                        <p class="fw-medium mb-0 float-end">Vendidos: {{ $item->total_cantidad }}</p>
                                        <h6 class="fs-16 mb-0">{{ $item->product_name }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div><!--end row-->
            </div><!--end container-->
        </section>
        <!--end Discover Items-->

        <!-- start Work Process -->
        <section class="section bg-light" id="creators">
            <div class="container-fluid px-5">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold lh-base">Clientes Destacados de la Semana</h2>
                            <p class="text-muted">Estos son los compradores más activos y comprometidos con nuestra comunidad.</p>
                        </div>
                    </div>
                </div><!-- end row -->
                <div class="row">
                @foreach($topClientes as $venta)
                    <div class="col-xl-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="{{ $venta->customer->photo_url }}" class="avatar-sm object-cover rounded" alt="Foto de {{ $venta->customer->name }}">
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <h5 class="mb-1">{{ $venta->customer->name }}</h5>
                                        <p class="text-muted mb-0">Compras: {{ $venta->total_compras }}</p>
                                        <p class="text-muted mb-0">Total gastado: ${{ number_format($venta->total_gastado, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div><!-- end container -->
        </section><!-- end Work Process -->

        <!-- start cta -->
        <section class="py-5 bg-primary position-relative">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container-fluid px-5">
                <div class="row align-items-center gy-4">
                    <div class="col-sm">
                        <div>
                            <h4 class="text-white mb-0 fw-semibold">
                                ¿Listo para vender o comprar tus productos tecnológicos?
                            </h4>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-sm-auto">
                        <div>
                            <a href="{{ route('products.create') }}" class="btn bg-gradient btn-danger">
                                Publicar Producto
                            </a>
                            <a href="{{ route('products.index') }}" class="btn bg-gradient btn-info">
                                Ver Productos
                            </a>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end cta -->

        <!-- Start footer -->
        <footer class="custom-footer bg-dark py-5 position-relative">
            <div class="container-fluid px-5">
                <div class="row">
                    <div class="col-lg-4 mt-4">
                        <div>
                            <div>
                                <img src="{{ asset('assets/images/shop-light.png') }}" alt="logo light" height="37">
                            </div>
                            <div class="mt-4">
                                <p>Tu tienda de tecnología de confianza.</p>
                                <p>Compra laptops, monitores, periféricos y componentes con garantía, seguridad y al mejor precio.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 ms-lg-auto">
                        <div class="row">
                            <div class="col-sm-4 mt-4">
                                <h5 class="text-white mb-0">La Empresa</h5>
                                <div class="text-muted mt-3">
                                    <ul class="list-unstyled ff-secondary footer-list">
                                        <li><a href="pages-profile.html">Sobre Nosotros</a></li>
                                        <li><a href="pages-gallery.html">Blog</a></li>
                                        <li><a href="pages-gallery.html">Ubicación</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-sm-4 mt-4">
                                <h5 class="text-white mb-0">Categorías</h5>
                                <div class="text-muted mt-3">
                                    <ul class="list-unstyled ff-secondary footer-list">
                                        <li><a href="pages-pricing.html">Laptops</a></li>
                                        <li><a href="apps-mailbox.html">Monitores</a></li>
                                        <li><a href="apps-mailbox.html">Accesorios</a></li>
                                        <li><a href="apps-mailbox.html">Componentes</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-sm-4 mt-4">
                                <h5 class="text-white mb-0">Soporte</h5>
                                <div class="text-muted mt-3">
                                    <ul class="list-unstyled ff-secondary footer-list">
                                        <li><a href="pages-faqs.html">Preguntas Frecuentes</a></li>
                                        <li><a href="pages-faqs.html">Términos y Condiciones</a></li>
                                        <li><a href="pages-faqs.html">Contáctanos</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row text-center text-sm-start align-items-center mt-5">
                    <div class="col-sm-6">

                        <div>
                            <p class="copy-rights mb-0">
                                <script> document.write(new Date().getFullYear()) </script> © Shop - Sistema de Gestión de Pedidos
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end mt-3 mt-sm-0">
                            <ul class="list-inline mb-0 footer-social-link">
                                <li class="list-inline-item">
                                    <a href="javascript: void(0);" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-facebook-fill"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="javascript: void(0);" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-github-fill"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="javascript: void(0);" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-linkedin-fill"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="javascript: void(0);" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-google-fill"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="javascript: void(0);" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-dribbble-line"></i>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end footer -->

    </div>
    <!-- end layout wrapper -->

    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

    <script src="assets/js/pages/nft-landing.init.js"></script>
</body>

</html>
