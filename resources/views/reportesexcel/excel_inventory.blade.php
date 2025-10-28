<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<table>
    <tr>
      <th colspan="7" style="text-align:center; display: table-cell;vertical-align: middle;border: 1px solid black;border-collapse: collapse; font-family:'Arial', Helvetica, sans-serif;font-weight:2em;"><b>SISTEMA DE GESTIÓN DE PEDIDOS</b><div class="image"><img src="{{public_path('assets/images/shop-dark.png')}}" alt="logo" width="50px" height="50px"></div></th>
    </tr>
    <tr>
        <th colspan="7" style="text-align:center; display: table-cell;vertical-align: middle;border: 1px solid black;border-collapse: collapse;font-family:'Arial', Helvetica, sans-serif;"><b>INVENTARIO DE PRODUCTOS</b></th>
      </tr>
  </table>

  <div class="contertable">
    <table>
        <thead>
            <tr>
                <th style="background-color:red; color:white; border:1px solid black;">#</th>
                <th style="background-color:red; color:white; border:1px solid black;width : 150px;">Tipo</th>
                <th style="background-color:red; color:white; border:1px solid black;width : 200px;">Usuario</th>
                <th style="background-color:red; color:white; border:1px solid black;width : 200px;">Motivo</th>
                <th style="background-color:red; color:white; border:1px solid black;width : 400px;">Productos (Cantidad)</th>
                <th style="background-color:red; color:white; border:1px solid black;width : 150px;"># Productos Distintos</th>
                <th style="background-color:red; color:white; border:1px solid black;width : 200px;">Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventories as $i => $inv)
            <tr>
                <td style="border:1px solid black; text-align:center;">{{ $i + 1 }}</td>
                <td style="border:1px solid black; text-align:center;">{{ $inv['type'] }}</td>
                <td style="border:1px solid black; text-align:center;">{{ $inv['user'] }}</td>
                <td style="border:1px solid black; text-align:center;">{{ $inv['reason'] }}</td>
                <td style="border:1px solid black; text-align:left;">{{ $inv['productos'] }}</td>
                <td style="border:1px solid black; text-align:left;">{{ $inv['total_products'] }}</td>
                <td style="border:1px solid black; text-align:center;">{{ $inv['created_at'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
  </div>
</body>
</html>
