@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Dashboard</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Inicio</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cols-xxl-5 row-cols-lg-3 row-cols-md-2 row-cols-1 g-3 mb-1">
                <div class="col">
                    <div class="card card-animate summary-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted text-uppercase fw-medium mb-0">Total Categorías</p>
                                    <h2 class="mt-1 ff-secondary fw-semibold mb-0">
                                        <span class="counter-value" id="totalCategorias">0</span>
                                    </h2>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-2">
                                        <i class="ri-database-2-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card card-animate summary-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted text-uppercase fw-medium mb-0">Total Productos</p>
                                    <h2 class="mt-1 ff-secondary fw-semibold mb-0">
                                        <span class="counter-value" id="totalProductos">0</span>
                                    </h2>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-success text-success rounded-circle fs-2">
                                        <i class="ri-shopping-cart-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card card-animate summary-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted text-uppercase fw-medium mb-0">Total Compras</p>
                                    <h2 class="mt-1 ff-secondary fw-semibold mb-0">
                                        S/. <span class="counter-value" id="totalComprasMonto">0</span>
                                    </h2>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-warning text-warning rounded-circle fs-2">
                                        <i class="ri-wallet-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="summary-meta text-muted">
                                <span class="badge bg-soft-warning text-warning">
                                    <i class="ri-shopping-basket-2-line align-middle me-1"></i> Órdenes
                                <span id="totalComprasTransacciones" class="ms-2">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card card-animate summary-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted text-uppercase fw-medium mb-0">Total Ventas</p>
                                    <h2 class="mt-1 ff-secondary fw-semibold mb-0">
                                        S/. <span class="counter-value" id="totalVentasMonto">0</span>
                                    </h2>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-danger text-danger rounded-circle fs-2">
                                        <i class="ri-money-dollar-circle-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="summary-meta text-muted">
                                <span class="badge bg-soft-danger text-danger">
                                    <i class="ri-arrow-right-up-line align-middle me-1"></i> Facturas
                                <span id="totalVentasTransacciones" class="ms-2">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card card-animate summary-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted text-uppercase fw-medium mb-0">Total Usuarios</p>
                                    <h2 class="mt-1 ff-secondary fw-semibold mb-0">
                                        <span class="counter-value" id="totalUsuarios">0</span>
                                    </h2>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-info text-info rounded-circle fs-2">
                                        <i class="ri-user-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Comparación de Ventas y Compras (Últimos 6 meses)</h4>
                            <div id="ventasComprasChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Grafico de Ventas por Producto -->
                <div class="col-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Distribución de Ventas por Producto</h4>
                            <div id="ventasProductosChart"></div>
                        </div>
                    </div>
                </div>
                <!-- Gráfico de Compras por Producto -->
                <div class="col-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Distribución de Compras por Producto</h4>
                            <div id="comprasProductosChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- GrÃ¡fico de Top Clientes -->
                <div class="col-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Top 5 Tiendas con Mayor Monto de Ventas ($)</h4>
                            <div id="topClientesChart"></div>
                        </div>
                    </div>
                </div>
                <!-- GrÃ¡fico de Top Proveedores -->
                <div class="col-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Top 5 Proveedores con Mayor Monto de Compras ($)</h4>
                            <div id="topProveedoresChart"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    @vite('resources/js/dashboard.js')
@endpush
@push('styles')
    <style>
        h2 {
            transition: opacity 0.3s ease-in-out;

        }
    </style>
@endpush
