<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Salida #{{ $salida->id }}</title>
    <style>
        * {
            font-family: Arial, sans-serif;
            font-size: 11px;
            box-sizing: border-box;
        }

        body {
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: 14px;
            margin: 0;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 12px;
            margin: 0;
            text-transform: uppercase;
        }

        .fila-datos {
            width: 100%;
            margin-top: 8px;
            border-collapse: collapse;
        }

        .fila-datos td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
        }

        .tabla-detalle {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .tabla-detalle th,
        .tabla-detalle td {
            border: 1px solid #000;
            padding: 2px 4px;
        }

        .tabla-detalle th {
            text-align: center;
        }

        .tabla-detalle td {
            font-size: 10px;
        }

        .footer-resumen {
            width: 100%;
            margin-top: 12px;
            border-collapse: collapse;
        }

        .footer-resumen th,
        .footer-resumen td {
            border: 1px solid #000;
            padding: 2px 4px;
            font-size: 10px;
        }

        .firmas {
            width: 100%;
            margin-top: 30px;
            text-align: center;
        }

        .firmas td {
            height: 50px;
            vertical-align: bottom;
            font-size: 10px;
        }

        .firmas .linea {
            border-top: 1px solid #000;
            width: 90%;
            margin: 0 auto 2px auto;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>{{ $empresaNombre }}</h1>
        <h2>{{ $tituloDocumento }}</h2>
    </div>

    {{-- DATOS PRINCIPALES --}}
    <table class="fila-datos">
        <tr>
            <td class="label" style="width: 12%;">CLIENTE:</td>
            <td style="width: 38%;">{{ $salida->cliente?->nombre }}</td>

            <td class="label" style="width: 12%;">PEDIDO NO:</td>
            <td style="width: 15%;">{{ $salida->pedido_id ?? 'S/P' }}</td>

            <td class="label" style="width: 8%;">NO.:</td>
            <td style="width: 15%;">{{ $salida->id }}</td>
        </tr>
        <tr>
            <td class="label">ARTÍCULO:</td>
            <td>{{ $articulo }}</td>

            <td class="label">FECHA:</td>
            <td colspan="3">
                {{ optional($salida->fecha_salida)->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    {{-- TABLA DETALLE (ROLLITOS) --}}
    <table class="tabla-detalle">
        <thead>
            <tr>
                <th style="width: 4%;">N°</th>
                <th style="width: 12%;">Tarjeta</th>
                <th style="width: 10%;">Rollo</th>
                <th>Producto</th>
                <th style="width: 15%;">Color</th>
                <th style="width: 15%;">Calidad</th>
                <th style="width: 12%;">Cantidad</th>
            </tr>
        </thead>
        <tbody>
        @php $consecutivo = 1; @endphp
        @foreach($salida->movimientos as $mov)
            <tr>
                <td class="text-center">{{ $consecutivo++ }}</td>
                <td class="text-center">{{ $mov->num_tarjeta }}</td>
                <td class="text-center">{{ $mov->num_rollo }}</td>
                <td>{{ $mov->producto?->nombre }}</td>
                <td>{{ $mov->color?->nombre }}</td>
                <td>{{ $mov->tipoCalidad?->nombre }}</td>
                <td class="text-right">
                    {{ number_format($mov->cantidad, 2) }}
                </td>
            </tr>
        @endforeach

        {{-- si quieres dejar líneas vacías para que se vea llenito, puedes agregar más filas --}}
        @for($i = $consecutivo; $i <= 25; $i++)
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endfor
        </tbody>
    </table>

    {{-- FOOTER RESUMEN (similar al cuadrito de LETRA / MTS-KGS / ROLLOS) --}}
    <table class="footer-resumen">
        <thead>
            <tr>
                <th style="width: 10%;">LETRA</th>
                <th style="width: 45%;">MTS. o KGS.</th>
                <th style="width: 45%;">ROLLOS</th>
            </tr>
        </thead>
        <tbody>
            @php
                // aquí podrías agrupar por tipo, letra, etc.
                // por ahora solo mostramos total en la fila "TOTAL"
            @endphp
            <tr>
                <td class="text-center">TOTAL</td>
                <td class="text-right">{{ number_format($salida->total_kgs, 3) }}</td>
                <td class="text-right">{{ $salida->total_rollos }}</td>
            </tr>
        </tbody>
    </table>

    {{-- CONDICIONES / PRECIO / PLAZO / OBSERVACIONES (simplificado) --}}
    <table class="fila-datos" style="margin-top: 10px;">
        <tr>
            <td class="label" style="width: 10%;">Precio:</td>
            <td style="width: 20%;">&nbsp;</td>

            <td class="label" style="width: 10%;">Plazo:</td>
            <td style="width: 20%;">&nbsp;</td>

            <td class="label" style="width: 10%;">Condiciones:</td>
            <td style="width: 30%;">&nbsp;</td>
        </tr>
        <tr>
            <td class="label">Observaciones:</td>
            <td colspan="5">&nbsp;</td>
        </tr>
    </table>

    {{-- FIRMAS --}}
    <table class="firmas">
        <tr>
            <td>
                <div class="linea"></div>
                ALMACENISTA
            </td>
            <td>
                <div class="linea"></div>
                VIGILANCIA
            </td>
            <td>
                <div class="linea"></div>
                CONDUCTOR
            </td>
            <td>
                <div class="linea"></div>
                CLIENTE
            </td>
        </tr>
    </table>

</body>
</html>
