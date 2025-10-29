<style>
    .factura-modal {
        height: 90vh; /* casi pantalla completa */
        display: flex;
        flex-direction: column;
        border-radius: 10px;
        overflow: hidden;
    }

    .factura-barra {
        flex-shrink: 0;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }

    .factura-scroll {
        flex-grow: 1;
        overflow-y: auto;
        background-color: #e9ecef;
        padding: 25px;
    }

    /* Simulación de hoja A4 */
    .factura-hoja {
        width: 210mm;           /* ancho A4 */
        min-height: 297mm;      /* alto A4 */
        background: white;
        box-shadow: 0 0 10px rgba(0,0,0,0.25);
        padding: 25mm 20mm;
        border-radius: 4px;
        font-family: 'Helvetica', Arial, sans-serif;
        font-size: 13px;
        color: #000;
    }

    /* Scrollbar elegante */
    .factura-scroll::-webkit-scrollbar {
        width: 8px;
    }
    .factura-scroll::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.3);
        border-radius: 10px;
    }
    .factura-scroll::-webkit-scrollbar-thumb:hover {
        background-color: rgba(0,0,0,0.5);
    }

    /* Vista para impresión */
    @media print {
        body * {
            visibility: hidden;
        }
        #modalFactura, #modalFactura * {
            visibility: visible;
        }
        #modalFactura {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .factura-barra, .btn {
            display: none !important;
        }
        .factura-scroll {
            background: white !important;
            padding: 0 !important;
        }
        .factura-hoja {
            box-shadow: none !important;
            width: 100%;
            min-height: auto;
            padding: 0;
        }
    }
</style>

<!-- Modal de Vista de Factura -->
<div class="modal fade" id="modalComprobante" tabindex="-1" role="dialog" aria-labelledby="modalFacturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content factura-modal">

            <!-- Barra superior -->
            <div class="factura-barra bg-primary text-white d-flex justify-content-between align-items-center" style="padding: 10px;">
                <h5 class="mb-0 text-white px-4 py-2">
                    <i class="fa fa-file-invoice"></i> Factura generada
                </h5>
                <div class="px-4 r-0">
                    <button class="btn btn-primary btn-sm mr-2" id="btnImprimirFactura">
                        <i class="fa fa-print"></i> Imprimir
                    </button>
                    <button class="btn btn-danger btn-sm" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cerrar
                    </button>
                </div>
            </div>

            <!-- Contenido con scroll -->
            <div class="modal-body factura-scroll d-flex justify-content-center align-items-start">
                <div class="factura-hoja" id="contenidoComprobante">
                    <div class="text-center py-5">
                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                        <p>Cargando factura...</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

