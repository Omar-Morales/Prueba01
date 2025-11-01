
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Arial', Helvetica, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
        }
        th, td {
            padding: 6px;
        }
        .center {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .qr {
            position: absolute;
            top: 3px;
            right: 10px;
        }
        .total-row td {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header-title {
            font-size: 16px;
        }
    </style>
</head>
<body>

    {{-- QR en la esquina superior derecha --}}
    <div class="qr">
        <img src="{{ $data['qr_code'] }}" width="80px" height="80px" alt="Código QR">
    </div>

    {{-- Encabezado con logo --}}
    <table border="1" style="border-collapse: collapse; margin-bottom: 5px;">
        <tr>
            <th colspan="4" class="center header-title">
                SISTEMA DE GESTIÓN DE PEDIDOS
                <div style="margin-top: 4px;">
                    <img src="{{ public_path('assets/images/bdys-dark.png') }}" alt="logo" width="50px" height="50px">
                </div>
            </th>
        </tr>
        <tr>
            <th colspan="4" class="center">
                <strong>TRANSACCION DE PRODUCTOS {{ $data['reference_id'] }}</strong>
            </th>
        </tr>
    </table>

    {{-- Datos principales --}}
    <table border="1" style="border-collapse: collapse; margin-bottom: 10px;">
        <tr>
            <td class="bold">Usuario:</td>
            <td>{{ $data['user'] }}</td>
            <td class="bold">Tipo:</td>
            <td>{{ $data['type'] }}</td>
        </tr>
        <tr>
            <td class="bold">Motivo:</td>
            <td>{{ $data['description'] }}</td>
            <td class="bold">Fecha:</td>
            <td>{{ $data['created_at'] }}</td>
        </tr>
    </table>

    {{-- Tabla de productos --}}
    <table border="1" style="border-collapse: collapse;">
        <thead>
            <tr style="background-color:red; color:white;">
                <th style="border:1px solid black; width: 200px;">Producto</th>
                <th style="border:1px solid black; width: 100px;">Cantidad</th>
                <th style="border:1px solid black; width: 120px;">Precio unitario</th>
                <th style="border:1px solid black; width: 120px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['productos'] as $producto)
                <tr>
                    <td style="border:1px solid black;">{{ $producto['Producto'] }}</td>
                    <td style="border:1px solid black;">{{ $producto['Cantidad'] }}</td>
                    <td style="border:1px solid black;">S/ {{ number_format($producto['Precio unitario'], 2) }}</td>
                    <td style="border:1px solid black;">S/ {{ number_format($producto['Subtotal'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" style="border:1px solid black;">Total</td>
                <td colspan="2" style="border:1px solid black;">S/ {{ number_format($data['total'], 2) }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
