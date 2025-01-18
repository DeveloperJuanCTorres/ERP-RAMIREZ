<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <style>
    body{
      font-family: sans-serif;
    }
    @page {
      margin-top:  0.5cm;
	margin-bottom:  1.5cm;
    }
    /* header { 
      left: -50px;
      top: -150px;
      right: -50px;
      height: 200px;      
    } */
    
    .cabecera {
      background-color: #000000;
    }
    header h1{
      margin: 0;
    }
    header h2{
      margin: 0 0 10px 0;
    }
    footer {
      position: fixed;
      left: 100px;
      bottom: 50px;
      right: 100px;
      height: 50px;
      /* border-bottom: 2px solid #ddd; */
    }
    footer .page:after {
      content: counter(page);
    }
    footer table {
      width: 100%;
    }
    footer p {
      text-align: right;
    }
    footer .izq {
      text-align: left;
    }
    td {
      height: 0px;
      padding:0px;
      margin:0px;
    }
    tr {
      bottom: 50px;
      padding:0px;
      margin:0px;
    }
    p{
        padding:0px;
    }
    span {
        color:#000000;
        font-size:15px;
    }
  </style>
</head>
<body>
  <header>
    <div style="padding-top: 150px;">
        <div style="padding-left: 620px;">
            <p style="font-size: 15px;color: #000000;font-weight: bold;">{{$fecha}} </p>
        </div>
        <table style="padding-left: 100px;padding-top: 2px;">
            <tr>
                <td style="width:500px">
                 <span> <strong>{{$cliente}}</strong></span>
                </td>
                <td>
                  <span><strong>{{$documento}}</strong></span>
                </td>
            </tr>
            <tr style="padding-top:10px;">
                <td style="width:500px;padding-top:4px;">
                 <span> <strong style="size: 10px !important;">{{$direccion}}</strong></span>
                </td>
                <td>
                  <span><strong>{{$telefono}}</strong></span>
                </td>
            </tr>
        </table>
    </div>    
  </header>
  
  <div id="content" style="display: block;padding-top:4px">
    <table style="padding:0px; height:15px;">
        <tr style="height:2px;">
            <td style="width:150px;">
                <span> <strong>PRODUCTO:</strong></span>
            </td>
            <td style="width:250px;">
                <span><strong> MOTOCICLETA </strong></span>
            </td>
            <td style="width:100px;">
                <span><strong> MARCA: </strong></span>
            </td>
            <td>
                <span><strong>{{$marca}}</strong></span>
            </td>
        </tr>  
        <tr>
            <td style="width:150px;">
               <span><strong>FORMA DE PAGO:</strong></span>
            </td>
            <td style="width:250px;">
                <span> <strong> CREDITO </strong></span>
            </td>
            <td style="width:100px;">
                <span><strong> MODELO: </strong> </span>
            </td>
            <td>
                <span><strong>{{$modelo}}</strong> </span>
            </td>
        </tr> 
        <tr>
            <td style="width:150px;">
                <span><strong>CONTR. PLACA:</strong></span>
            </td>
            <td style="width:250px;">
               <span><strong> SIN PLACA </strong></span>
            </td>
            <td style="width:100px;">
                <span><strong> SERIE: </strong></span>
            </td>
            <td>
                <span><strong> {{$chasis}} </strong></span>
            </td>
        </tr> 
        <tr>
            <td style="width:150px;">
                <span><strong>CIUDAD:</strong></span>
            </td>
            <td style="width:250px;">
               <span><strong>CHICLAYO</strong></span>
            </td>
            <td style="width:100px;">
                <span><strong> N° MOTOR:</strong></span>
            </td>
            <td>
                <span><strong>{{$motor}}</strong></span>
            </td>
        </tr> 
        <tr>
            <td style="width:150px;">
                <span><strong style="width:150px;"> POLIZA:</strong></span>
            </td>
            <td style="width:250px;">
                <span><strong> {{$poliza}}</strong></span>
            </td>
            <td style="width:100px;">
                <span><strong>COLOR: </strong></span>
            </td>
            <td>
                <span><strong>{{$color}}</strong></span>
            </td>
        </tr>
    </table> 
    <div>
        <table style="padding-left:150px; padding-top:10px;">
            <tr>
                <td>
                    <span style="font-size:17px;"><strong>{{$acuenta}}</strong></span>
                </td>
                <td style="padding-left:220px;width:220px">
                    <span style="font-size:17px;"><strong>{{$saldo}}</strong></span>
                </td>
                <td>
                    <span style="font-size:17px;"><strong>{{$precio}}</strong></span>
                </td>
            </tr>
        </table>
        <div style="padding-left:645px;padding-top:30px;">
             <span style="font-size:17px;"><strong>{{$precio}}</strong></span>
        </div>
    </div>
  </div>

  <footer>
        
  </footer>

</body>
</html>