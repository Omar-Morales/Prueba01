<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table>
        <tr>
            <th colspan="4" style="text-align:center; border: 1px solid black; font-family:'Arial', Helvetica, sans-serif;">
                <b>SISTEMA DE GESTIÓN DE PEDIDOS</b>
                <div><img src="{{ public_path('assets/images/bdys-dark.png') }}" alt="logo" width="50px" height="50px"></div>
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align:center; border: 1px solid black; font-family:'Arial', Helvetica, sans-serif;">
                <b>TRANSACCION DE PRODUCTOS {{ $data['reference_id'] }}</b>
            </th>
        </tr>
    </table>

    <br>

    <table>
    <tr>
        <td style="text-align:start;font-weight:bold;"><strong>Usuario:</strong></td>
        <td>{{ $data['user'] }}</td>
        <td style="text-align:start;font-weight:bold;"><strong>Tipo:</strong></td>
        <td>{{ $data['type'] }}</td>
    </tr>

    <tr>
        <td style="text-align:start;font-weight:bold;"><strong>Motivo:</strong></td>
        <td>{{ $data['description'] }}</td>
        <td style="text-align:start;font-weight:bold;"><strong>Fecha:</strong></td>
        <td>{{ $data['created_at'] }}</td>
    </tr>
    </table>

    <br>

    <table border="1" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th style="background-color:red; color:white; border:1px solid black;width : 200px;">Producto</th>
                <th style="background-color:red; color:white; border:1px solid black;width : 200px;">Cantidad</th>
                <th style="background-color:red; color:white; border:1px solid black;width : 200px;">Precio unitario</th>
                <th style="background-color:red; color:white; border:1px solid black;width : 200px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['productos'] as $producto)
                <tr>
                    <td style="border:1px solid black; text-align:center;">{{ $producto['Producto'] }}</td>
                    <td style="border:1px solid black; text-align:center;">{{ $producto['Cantidad'] }}</td>
                    <td style="border:1px solid black; text-align:center;">{{ number_format($producto['Precio unitario'], 2) }}</td>
                    <td style="border:1px solid black; text-align:center;">{{ number_format($producto['Subtotal'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <tr>
        <td colspan="2" style="background-color:red; color:white; border:1px solid black;font-weight:bold;"><strong>Total:</strong></td>
        <td colspan="2" style="border:1px solid black;font-weight:bold;">{{ $data['total'] }}</td>
    </tr>
</body>
</html>
