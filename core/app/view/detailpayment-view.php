<?php
    $id = $totalVenta = $montoDescuento = $montoAbonado = 0;
    $sede = $piso = $mesa = $usuario = $fecha = $tipo = "";
    $mostrarMesa = true;
    $texto = "Detalle Pago";
    $textoBoton = "Regresar";
    $lstDetallePedido = array();
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
        $objPedido = PedidoData::getById($id);
        $idPedido = $objPedido->id;
        
        // Sede
        $objSede = $objPedido->getSede();
        $sede = $objSede->nombre;
        
        // Piso
        $objPiso = $objPedido->getPiso();
        $piso = $objPiso->nombre;
        
        // Mesa
        $objMesa = $objPedido->getMesa();
        if ($objMesa) {
            $mesa = $objMesa->nombre;
        }

        // Mesero
        $objUsuario = $objPedido->getUsuario();
        $usuario = $objUsuario->nombres." ".$objUsuario->apellidos;
               
        // Fecha
        $fecha = date("d/m/Y", strtotime($objPedido->fecha));
        
        // Tipo
        $objTipo = $objPedido->getTipo();
        $tipo = $objTipo->nombre;
        if ($objTipo->valor1 == 0) {
            $mostrarMesa = false;
        }        
        $montoDescuento = $objPedido->descuento_pedido;        
        $lstDetallePedido = DetallePedidoData::getProductosXPedido($idPedido);
    }
    
    $lstHistorialPago = $lstHistorialDocumento = array();
    $objPago = PagoData::getByPedido($id);
    if ($objPago) {
        $montoAbonado = $objPago->monto_pagado_efectivo + $objPago->monto_pagado_tarjeta;
        $totalVenta = $objPago->monto_total;
        $lstHistorialPago = HistorialPagoData::getAllByPago($objPago->id);
        $lstHistorialDocumento = HistorialDocumentoData::getAllByPago($objPago->id);
    }
    
    $opcion = 0;
    if (isset($_GET["opcion"])) {
        $opcion = $_GET["opcion"];
    }
    
    $exonerado = 0;
    if ($_SESSION["exonerado"] == 1) {
        $exonerado = 1;
    }
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="detailpayment" action="index.php?action=addsale" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>
        		<div class="panel-body">
        			<div class="form-group">
        				<div class="col-md-2 col-sm-12">
        					<label for="fecha">Fecha :</label>
        					<input type="text" id="fecha" name="fecha" class="form-control" value="<?php echo $fecha; ?>" disabled/>
        				</div>
        				<div class="col-md-4 col-sm-12">
        					<label for="sede">Sede :</label>
        					<input type="text" id="sede" name="sede" class="form-control" value="<?php echo $sede; ?>" disabled/>
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="piso">Piso :</label>
        					<input type="text" id="piso" name="piso" class="form-control" value="<?php echo $piso; ?>" disabled/>
        				</div>
        				<div class="col-md-4 col-sm-12">
        					<label for="usuario">Mesero(a) :</label>
        					<input type="text" id="usuario" name="usuario" class="form-control" value="<?php echo $usuario; ?>" disabled/>
        				</div>
        			</div>
        			<div class="form-group">
        				<?php if ($mostrarMesa) { ?>
        				<div class="col-md-2 col-sm-12">
        					<label for="mesa">Mesa :</label>
        					<input type="text" id="mesa" name="mesa" class="form-control" value="<?php echo $mesa; ?>" disabled/>
        				</div>
        				<?php } ?>
        				<div class="col-md-2 col-sm-12">
        					<label for="tipo">Tipo :</label>
        					<input type="text" id="tipo" name="tipo" class="form-control" value="<?php echo $tipo; ?>" disabled/>
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="montototal">Monto Total :</label>
        					<input type="text" id="montototal" name="montototal" class="form-control" value="<?php echo number_format($totalVenta, 2); ?>" dir="rtl" disabled="disabled"/>
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="montodescuento">Monto Descuento :</label>
        					<input type="text" id="montodescuento" name="montodescuento" class="form-control" value="<?php echo number_format($montoDescuento, 2); ?>" dir="rtl" disabled="disabled"/>
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="montoabonado">Monto Pagado :</label>
        					<input type="text" id="montoabonado" name="montoabonado" class="form-control" value="<?php echo number_format($montoAbonado, 2); ?>" dir="rtl" disabled="disabled"/>
        				</div>
        			</div>
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
  							<div class="panel panel-primary" id="test1Pane1">
    							<div class="panel-heading">
    								<strong>Listado de Productos</strong>
      								<a data-target="#panel1Content" data-parent="#test1Panel" data-toggle="collapse"><span class="pull-right"><i class="panel1Icon fa fa-arrow-up"></i></span> </a>
    							</div>
    							<div class="panel-collapse collapse in" id="panel1Content">
      								<div class="panel-body">
                                    	<div class="table-responsive-md table-responsive" id="tabla">
                                    	<table class="table table-hover">
                                            <thead>
                                                <tr class="btn-primary">
                                                    <th scope="col">Item</th>
                                                    <th scope="col">Categoría</th>
                                                    <th scope="col">Producto</th>
                                                    <th scope="col" style="text-align: right;">Cantidad</th>
                                    				<th scope="col" style="text-align: right;">Precio</th>
                                    				<th scope="col" style="text-align: right;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                $totalListadoProducto = $totalListadoCantidad = 0;
                                                if (count($lstDetallePedido) > 0) {
                                                    $item = 1;
                                                    foreach ($lstDetallePedido as $objDetallePedido) {
                                                        $totalVenta += $objDetallePedido->total;
                                                        $totalListadoProducto += $objDetallePedido->total;
                                                        $totalListadoCantidad += $objDetallePedido->cantidad;
                                            ?>
                                            	<tr>
                                                    <td style="text-align: left;"><?php echo $item++; ?></td>
                                                    <td style="text-align: left;"><?php echo $objDetallePedido->categoria; ?></td>
                                                    <td style="text-align: left;"><?php echo $objDetallePedido->nom_producto; ?></td>
                                                    <td style="text-align: right;"><?php echo number_format($objDetallePedido->cantidad, 2); ?></td>
                                                    <td style="text-align: right;"><?php echo number_format($objDetallePedido->precio_venta, 2); ?></td>
                                                    <td style="text-align: right;"><?php echo number_format($objDetallePedido->total, 2); ?></td>
                                                </tr>
                                            <?php 
                                                    }
                                                }
                                            ?>
                                            </tbody>
                                            <tfoot>
                                            	<tr>
                                            		<td style="font-weight: bold; text-align: left;" colspan="3">TOTAL</td>
                                            		<td style="text-align: right; font-weight: bold;"><?php echo number_format($totalListadoCantidad, 2); ?></td>
                                            		<td></td>
                                            		<td style="text-align: right; font-weight: bold;"><?php echo number_format($totalListadoProducto, 2); ?></td>
                                            	</tr>
                                            </tfoot>
                                      	</table>
                                   		</div>
                                  	</div>
                             	</div>
                        	</div>
                      	</div>
        			</div>        			
        			<?php if (count($lstHistorialPago) > 0) { ?>
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
  							<div class="panel panel-primary" id="test2Pane2">
    							<div class="panel-heading">
    								<strong>Historial Pagos</strong>
      								<a data-target="#panel2Content" data-parent="#test2Panel" data-toggle="collapse"><span class="pull-right"><i class="panel2Icon fa fa-arrow-up"></i></span></a>
    							</div>
    							<div class="panel-collapse collapse in" id="panel2Content">
      								<div class="panel-body">
                                    	<div class="table-responsive-md table-responsive">
                                    	<table class="table table-hover">
                                            <thead>
                                                <tr class="btn-primary">
                                                	<th scope="col">Código</th>
                                                    <th scope="col">Fecha y Hora</th>
                                                    <th scope="col">Producto</th>
                                                    <th scope="col" style="text-align: right;">Cantidad</th>
                                                    <th scope="col">Forma Pago</th>
                                                    <th scope="col">Tipo Tarjeta</th>
                                                    <th scope="col">Num. Operación</th>
                                                    <th scope="col" style="text-align: right;">Monto Pagado</th>
                                                    <th scope="col">Num. Comprobante</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                foreach ($lstHistorialPago as $objHistorialPago) {
                                                    $objPago = $objHistorialPago->getPago();
                                                    
                                                    $detallePago = "";
                                                    if ($objHistorialPago->getFormaPago()->valor1 == 2) {
                                                        $detallePago = "<br><b>(Efectivo: ".number_format($objHistorialPago->monto_efectivo, 2).")<br>".
                                                            "(Tarjeta: ".number_format($objHistorialPago->monto_tarjeta, 2).")</b>";
                                                    }
                                                    
                                                    $totalListado = 0;
                                                    $lstProducto = $lstCantidad = "";
                                                    $lstDetalleHistorialPago = $objHistorialPago->getDetalleHistorialPago();
                                                    foreach ($lstDetalleHistorialPago as $objDetalleHistorialPago) {
                                                        $lstProducto .= $objDetalleHistorialPago->nom_producto."<br>";
                                                        $lstCantidad .= number_format($objDetalleHistorialPago->cantidad, 2)."<br>";
                                                        $totalListado += $objDetalleHistorialPago->total;
                                                    }
                                                    $totalListado = $totalListado - $objPago->monto_descuento;
                                                    
                                                    $nomTipoTarjeta = "";
                                                    if ($objHistorialPago->getTipoTarjeta()) {
                                                        $nomTipoTarjeta = $objHistorialPago->getTipoTarjeta()->nombre;
                                                    }
                                                    $numComprobante = "";
                                                    if ($objHistorialPago->getComprobante()) {
                                                        $numComprobante = $objHistorialPago->getComprobante()->fe_comprobante_ser . "-" . $objHistorialPago->getComprobante()->fe_comprobante_cor;
                                                    }
                                            ?>
                                            	<tr>
                                            		<td style="text-align: left;"><?php echo str_pad($objHistorialPago->id, 8, "0", STR_PAD_LEFT); ?></td>
                                            		<td style="text-align: left;"><?php echo date("d/m/Y H:i", strtotime($objHistorialPago->fecha)); ?></td>
                                            		<td style="text-align: left;"><?php echo $lstProducto; ?></td>
                                            		<td style="text-align: right;"><?php echo $lstCantidad; ?></td>
                                            		<td style="text-align: left;"><?php echo $objHistorialPago->getFormaPago()->nombre; ?></td>
                                            		<td style="text-align: left;"><?php echo $nomTipoTarjeta; ?></td>
                                            		<td style="text-align: left;"><?php echo $objHistorialPago->num_operacion; ?></td>
                                            		<td style="text-align: right;"><?php echo number_format($totalListado, 2).$detallePago; ?></td>
                                            		<td style="text-align: left;"><?php echo $numComprobante; ?></td>
                                            	</tr>
                                            <?php 
                                                }
                                            ?>
                                            </tbody>
                                      	</table>
                                   		</div>
                               		</div>
                          		</div>
                      		</div>
                      	</div>
                   	</div>
                   	<?php } ?>
                   	<?php if (count($lstHistorialDocumento) > 0) { ?>
                   	<div class="form-group">
        				<div class="col-md-12 col-sm-12">
  							<div class="panel panel-primary" id="test3Pane3">
    							<div class="panel-heading">
    								<strong>Historial Comprobantes</strong>
      								<a data-target="#panel3Content" data-parent="#test3Panel" data-toggle="collapse"><span class="pull-right"><i class="panel3Icon fa fa-arrow-up"></i></span></a>
    							</div>
    							<div class="panel-collapse collapse in" id="panel3Content">
      								<div class="panel-body">
                                    	<div class="table-responsive-md table-responsive">
                                    	<table class="table table-hover">
                                            <thead>
                                                <tr class="btn-primary">
                                                    <th scope="col">Fecha y Hora</th>
                                                    <th scope="col">Num. Documento</th>
                                                    <th scope="col">Cliente</th>
                                                    <th scope="col" style="text-align: right;">SubTotal</th>
                                                    <th scope="col" style="text-align: right;">IGV</th>
                                                    <th scope="col" style="text-align: right;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                    foreach ($lstHistorialDocumento as $objHistorialDocumento) {
                                                        $objComprobante = $objHistorialDocumento->getComprobante();
                                                        $montoComprobante = $objComprobante->fe_comprobante_totvengra;
                                                        if ($exonerado == 1) {
                                                            $montoComprobante = $objComprobante->fe_comprobante_totvenexo;
                                                        }
                                            ?>
                                            	<tr>
                                            		<td style="text-align: left;"><?php echo date("d/m/Y H:i", strtotime($objComprobante->fe_comprobante_reg)); ?></td>
                                            		<td style="text-align: left;"><?php echo $objComprobante->fe_comprobante_ser."-".$objComprobante->fe_comprobante_cor; ?></td>
                                            		<td style="text-align: left;"><?php echo $objComprobante->tb_cliente_numdoc."-".$objComprobante->tb_cliente_nom; ?></td>
                                            		<td style="text-align: right;"><?php echo number_format($montoComprobante, 2); ?></td>
                                            		<td style="text-align: right;"><?php echo number_format($objComprobante->fe_comprobante_sumigv, 2); ?></td>
                                            		<td style="text-align: right;"><?php echo number_format($objComprobante->fe_comprobante_imptot, 2); ?></td>
                                            	</tr>
                                            <?php 
                                                    }                                                
                                            ?>
                                            </tbody>
                                      	</table>
                                   		</div>
                               		</div>
                          		</div>
                      		</div>
                      	</div>
                   	</div>
                   	<?php } ?>
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
        					<button type="button" id="btnRegresar" class="btn btn-success"><?php echo $textoBoton; ?></button>
        				</div>
        			</div>
        		</div>
        	</div>
    		</form>
    		<script type="text/javascript">
    			$("#btnRegresar").click(function(){
    				$.blockUI();
    				$("button").prop("disabled", true);
        			<?php if ($opcion == 0) { ?>
    				location.href = "./index.php?view=payments";
    				<?php } else { ?>
    				location.href = "./index.php?view=trays";
    				<?php } ?>
    			});
    			$(document).ready(function(){
    				$("#panel1Content").on("shown.bs.collapse",function(){
        			    $(".panel1Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel1Content").on("hidden.bs.collapse",function(){
        			    $(".panel1Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});
        			$("#panel2Content").on("shown.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel2Content").on("hidden.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});
        			$("#panel3Content").on("shown.bs.collapse",function(){
        			    $(".panel3Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel3Content").on("hidden.bs.collapse",function(){
        			    $(".panel3Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});
        		});
    		</script>
    	</div>
    </div>
</section>