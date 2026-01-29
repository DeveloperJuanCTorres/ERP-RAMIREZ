<style>
    .factura-container {
        font-family: 'Helvetica', Arial, sans-serif;
        font-size: 11px;
        color: #000;
        padding: 10px;
        background: #fff;
    }
    .factura-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    .factura-empresa {
        width: 65%;
    }
    .factura-empresa h3 {
        margin: 0;
        font-weight: bold;
    }
    .factura-datos {
        width: 30%;
        border: 2px solid #000;
        border-radius: 10px;
        text-align: center;
        padding: 10px;
    }
    .factura-datos h4 {
        font-weight: bold;
        margin-bottom: 5px;
    }
    .factura-info {
        margin-bottom: 15px;
    }
    .factura-info p {
        margin: 2px 0;
    }
    .factura-cliente {
        border: 1px solid #000;
        padding: 10px;
        margin-bottom: 15px;
    }
    .tabla-detalle th, .tabla-detalle td {
        border: 1px solid #000;
        padding: 4px;
        text-align: center;
        font-size: 12px;
    }
    .tabla-detalle {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    .factura-totales {
        text-align: right;
        margin-top: 10px;
    }
    .factura-totales p {
        margin: 2px 0;
        font-size: 14px;
    }
    .factura-footer {
        margin-top: 20px;
        font-size: 11px;
        text-align: center;
        border-top: 1px solid #000;
        padding-top: 10px;
    }

    .factura-datos-cliente {
        display: flex;
        gap: 10px;
        width: 100%;
    }

    .factura-box {
        border: 1px solid #000;
        padding: 10px 15px;
        border-radius: 4px;
        background-color: #fff;
    }

    .factura-box p {
        margin: 2px 0;
        font-size: 13px;
    }
</style>

<div class="factura-container">

    {{-- ENCABEZADO --}}
    <div class="factura-header">
        <div class="factura-empresa">
            @php
                if($business_id = 1){
                    $imgPath = public_path('img/importaciones.jpeg');
                    $alt = "imp-ramirez";
                }else
                {
                    $imgPath = public_path('img/a1ramirez.jpeg');
                    $alt = "a1-ramirez";
                }

                $imgData = base64_encode(file_get_contents($imgPath));
                $imgType = pathinfo($imgPath, PATHINFO_EXTENSION);
            @endphp
            @if($business_id = 1)
                <img src="data:image/{{ $imgType }};base64,{{ $imgData }}" width="250" alt="{{$alt}}">
            @else
                <img src="data:image/{{ $imgType }};base64,{{ $imgData }}" width="150" alt="{{$alt}}">
            @endif
            @if($business_id = 1)
                <h3 style="padding-top: 20px;">IMPORTACIONES RAMIREZ E.I.R.L.</h3>
                <p>7 de Enero 1850 - Chiclayo</p>
                <p>Jr. Industrial 109  Mcdo. Nuevo Oriente - Cutervo</p>
                <p>Jr. Coronel Secada 104 - Moyobamba</p>
                <p>Carretera Pomalca KM 2.5</p>
                <p>Calle Próceres 117 - Chiclayo</p>
                <p>E-mail: informes@importacionesramirez.pe</p>
            @else
                <h3 style="padding-top: 20px;">A1 RAMIREZ SAC</h3>
                <p>AV. SALOMON VILCHEZ M. NRO. 740 - CUTERVO - CUTERVO - CAJAMARCA</p>
                <p>E-mail: {{ $comprobante->empresa_email ?? 'cutervo@importacionesramirez.com' }}</p>
            @endif
        </div>

        <div class="factura-datos">
            @if($business_id = 1)
                <h3>RUC 20495764398</h3>
            @else
                <h3>RUC 20603437331</h3>
            @endif
            @if(Str::startsWith($comprobante->invoice_no, 'F'))
                @php
                $tipo_doc = 'RUC';
                @endphp
                <h3>{{$comprobante->type}}</h3>
            @elseif(Str::startsWith($comprobante->invoice_no, 'B'))
                @php
                $tipo_doc = 'DNI';
                @endphp
                <h3>{{$comprobante->type}}</h3>
            @else
                <h3>COMPROBANTE ELECTRÓNICO</h3>
            @endif
            <h3>{{ $comprobante->invoice_no}}</h3>
        </div>
    </div>

    {{-- DATOS CLIENTE --}}
    <div class="row factura-datos-cliente d-flex justify-content-between mb-3" style="padding-bottom: 20px;">

        <!-- Columna izquierda -->
        <div class="factura-box w-50 mr-2" style="width: 70%;">
            <p><strong>CLIENTE:</strong> {{ $comprobante->name}}</p>
            <p><strong>{{$tipo_doc}}:</strong> {{ $comprobante->numero_doc }}</p>
            <p><strong>DIRECCIÓN:</strong> {{ $comprobante->address }}</p>
        </div>

        <!-- Columna derecha -->
        <div class="factura-box w-50 ml-2" style="width: 30%;">
            <p><strong>FECHA EMISIÓN:</strong> {{ \Carbon\Carbon::parse($comprobante->fecha_emision)->format('d/m/Y') }}</p>
            @if($comprobante->tipo_pago == 'credito')
            <p><strong>FECHA VENC.:</strong> {{ \Carbon\Carbon::parse($comprobante->fecha_vencimiento)->format('d/m/Y') }}</p>
            @endif
            @if($comprobante->moneda == 1)
            @php
            $simbolo = 'S/. ';
            @endphp
            <p><strong>MONEDA:</strong> SOLES</p>
            @elseif($comprobante->moneda == 2)
            @php
            $simbolo = '$. ';
            @endphp
            <p><strong>MONEDA:</strong> DÓLARES</p>
            @endif
            <p><strong>COND. PAGO:</strong> {{$comprobante->tipo_pago}}</p>
        </div>

    </div>

    {{-- DETALLE PRODUCTOS --}}
    <table class="tabla-detalle">
        <thead>
            <tr>
                <th>CANT.</th>
                <th>UM</th>
                <th>CÓD.</th>
                <th>DESCRIPCIÓN</th>
                <th>V/U</th>
                <th>P/U</th>
                <th>IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productos as $item)
                <tr>
                    <td>{{ $item->cantidad }}</td>
                    <td>{{ $item->unidad_de_medida}}</td>
                    <td>{{ $item->codigo}}</td>
                    <td style="text-align:left;">{{ $item->descripcion }}</td>
                    <td>{{ number_format($item->valor_unitario, 3) }}</td>
                    <td>{{ number_format($item->precio_unitario, 3) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTALES --}}
    <div class="factura-totales" style="padding-top: 20px;">
        <p><strong>GRAVADA:</strong> {{$simbolo}} {{ number_format($comprobante->total/1.18, 2) }}</p>
        <p><strong>IGV (18%):</strong> {{$simbolo}} {{ number_format($comprobante->total - ($comprobante->total/1.18), 2) }}</p>
        <p><strong>TOTAL:</strong> {{$simbolo}} {{ number_format($comprobante->total, 2) }}</p>
        <p><strong>IMPORTE EN LETRAS:</strong> {{$totalEnLetras}}</p>
    </div>

    {{-- PIE DE PÁGINA --}}
    @if($comprobante->detraccion == 1)
    <div class="factura-box w-50 mr-2">
        <h2>Información de la detracción</h2>
        <p><strong>Leyenda:</strong> Operación sujeta al Sistema de Pago de Obligaciones Tributarias con el Gobierno Central</p>
        <p><strong>Bien o Servicio:</strong> 019 Arrendamiento de bienes muebles</p>
        <p><strong>Medio de pago:</strong> 001  Depósito en cuenta</p>
        <p><strong>Nro. Cta. Banco de la Nación:</strong> 00274019956</p>
        <p><strong>Porcentaje de detracción:</strong> 10.00</p>
        <p><strong>Monto de detracción:</strong> {{$simbolo}} {{ number_format($comprobante->total*0.10, 2) }}</p>
    </div>
    @endif

    <div class="factura-footer">
            <p>Representación impresa de la FACTURA ELECTRÓNICA, visita www.nubefact.com/10481130145</p>
            <p>Autorizado mediante Resolución de Intendencia No.034-005-0005315</p>
        </div>
    </div>

<script>
    window.onload = () => {
        window.print();
        setTimeout(() => window.close(), 800); // ✅ Se cierra sola luego de imprimir
    };
</script>