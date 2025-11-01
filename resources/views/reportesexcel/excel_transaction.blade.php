<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<table>
    <tr>
      <th colspan="7" style="text-align:center; display: table-cell;vertical-align: middle;border: 1px solid black;border-collapse: collapse; font-family:'Arial', Helvetica, sans-serif;font-weight:2em;"><b>SISTEMA DE GESTIÓN DE PEDIDOS</b><div class="image"><img src="{{public_path('assets/images/bdys-dark.png')}}" alt="logo" width="50px" height="50px"></div></th>
    </tr>
    <tr>
        <th colspan="7" style="text-align:center; display: table-cell;vertical-align: middle;border: 1px solid black;border-collapse: collapse;font-family:'Arial', Helvetica, sans-serif;"><b>LISTADO DE TRANSACCIONES</b></th>
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
            @foreach($transactions  as $i => $transaction)
                <tr>
                    <td style="border:1px solid black; text-align:center;">{{ $i + 1 }}</td>
                    <td style="border:1px solid black; text-align:center;">{{ $transaction['type'] }}</td>
                    <td style="border:1px solid black; text-align:center;">{{ $transaction['user'] }}</td>
                    <td style="border:1px solid black; text-align:center;">{{ $transaction['description'] }}</td>
                    <td style="border:1px solid black; text-align:center;">{{ $transaction['productos'] }}</td>
                    <td style="border:1px solid black; text-align:center;">{{ $transaction['amount'] }}</td>
                    <td style="border:1px solid black; text-align:center;">{{ $transaction['created_at'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
  </div>
</body>
</html>
