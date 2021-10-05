<?php
    $sede = $_SESSION["sede"];
    $estado = "1";
    $fechaInicio = $fechaFin = date("d/m/Y");
    if (count($_POST) > 0) {
        $estado = $_POST["estado"];
        $fechaInicio = $_POST["fechai"];
        $fechaFin = $_POST["fechaf"];
        
        $_SESSION["payments_estado"] = $estado;
        $_SESSION["payments_fechai"] = $fechaInicio;
        $_SESSION["payments_fechaf"] = $fechaFin;
    } else {
        if (isset($_SESSION["payments_estado"]) && isset($_SESSION["payments_fechai"]) && isset($_SESSION["payments_fechaf"])) {
            $estado = $_SESSION["payments_estado"];
            $fechaInicio = $_SESSION["payments_fechai"];
            $fechaFin = $_SESSION["payments_fechaf"];
        }
    }
    $lstEstado = EstadoData::getAll();    
    $lstPago = PagoData::getAll($estado, $sede, $fechaInicio, $fechaFin);
?>
<script type="text/javascript">
	$(function() {
		$("#fechai").datepicker({
			dateFormat: "dd/mm/yy",
    		changeMonth: true,
    		changeYear: true,
    		maxDate: "0",
    		yearRange: "1900:<?php echo date("Y"); ?>"
    	});
		$("#fechaf").datepicker({
			dateFormat: "dd/mm/yy",
    		changeMonth: true,
    		changeYear: true,
    		maxDate: "0",
    		yearRange: "1900:<?php echo date("Y"); ?>"
    	});
	});	
</script>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Pagos</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="payments" action="" role="form" autocomplete="off">
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
                				<label for="fecha">Fecha Inicio:</label>
        						<input type="text" id="fechai" name="fechai" value="<?php echo $fechaInicio; ?>" class="form-control" readonly/>
        					</div>
        					<div class="col-md-2 col-sm-12">
                				<label for="fecha">Fecha Fin:</label>
        						<input type="text" id="fechaf" name="fechaf" value="<?php echo $fechaFin; ?>" class="form-control" readonly/>
        					</div>
            				<div class="col-md-2 col-sm-12">
    							<label for="estado">Estado :</label>
    							<select id="estado" name="estado" class="form-control">
    								<option value="">TODOS</option>
    								<?php foreach ($lstEstado as $objEstado) { ?>
    								<option value="<?php echo $objEstado->id; ?>" <?php if ($objEstado->id == $estado) { echo "selected"; } ?>><?php echo $objEstado->nombre; ?></option>
      								<?php } ?>
    							</select>
    						</div>
    						<div class="col-md-6 col-sm-12" style="padding-top: 25px;">
        						<button type="button" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>
        						<button type="button" id="btnLimpiar" class="btn btn-danger" title="Limpiar"><em class="fa fa-eraser"></em></button>
        						<?php if (count($lstPago) > 0) { ?>
        						<a class="btn btn-primary" id="btnExportar" href="excelPago.php?sede=<?php echo $sede; ?>&fechainicio=<?php echo $fechaInicio; ?>&fechafin=<?php echo $fechaFin; ?>&estado=<?php echo $estado; ?>" role="button" title="Descargar" target="_blank"><em class="fa fa-download"></em></a>
        						<?php } ?>
        						<script type="text/javascript">
        							$("#btnBuscar").click(function(){
            							$.blockUI();
            		    				$("button").prop("disabled", true);
            		    				$("#payments").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
                						$.post("index.php?action=deletesearch", {
                							opcion: "payments"
                                        }, function (data) {
                                        	location.href = "./index.php?view=payments";
                                        });
            		    			});
        						</script>
            				</div>
    					</div>
            		</form>
					<div class="table-responsive">
        				<div class="box-body">
        					<table id="example" class="table table-bordered table-hover datatable table-nowrap">
        						<thead>
        							<tr>
        								<th scope="col">Num. Pedido</th>
        								<th scope="col">Código Pago</th>
        								<th scope="col">Fecha y Hora</th>
        								<th scope="col">Tipo Pedido</th>
        								<th scope="col">Tipo Pago</th>
        								<th scope="col" style="text-align: right;">Monto Total</th>
        								<th scope="col" style="text-align: right;">Monto Descuento</th>
        								<th scope="col" style="text-align: right;">Monto Pagado</th>
        								<th scope="col" style="text-align: right;">Monto Efectivo</th>
        								<th scope="col" style="text-align: right;">Monto Tarjeta</th>
        								<th scope="col">Comprobante</th>
        								<th scope="col">Estado</th>
        								<th scope="col">Acciones</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						    $totalMontoEfectivo = $totalMontoTarjeta = $totalMontoTotal = 0; 
        						    $totalMontoDescuento = $totalMontoPagado = 0;
        						    foreach ($lstPago as $objPago) {
        						        $objEstado = $objPago->getEstado();
        						        $objPedido = $objPago->getPedido();
        						        $objTipoPago = $objPago->getTipoPago();
        						        
        						        $montoEfectivo = $objPago->monto_pagado_efectivo;
        						        $montoTarjeta = $objPago->monto_pagado_tarjeta;
        						        $montoTotal = $objPago->monto_total;
        						        $montoDescuento = $objPago->monto_descuento;
        						        $montoPagado = $montoEfectivo + $montoTarjeta;
        						        
        						        $totalMontoEfectivo += $montoEfectivo;
        						        $totalMontoTarjeta += $montoTarjeta;
        						        $totalMontoTotal += $montoTotal;
        						        $totalMontoDescuento += $montoDescuento;
        						        $totalMontoPagado += $montoPagado;
        						        
        						        $comprobantes = "";
        						        $lstHistorialPago = $objPago->getHistorialPago();
        						        foreach ($lstHistorialPago as $objHistorialPago) {
        						            $objComprobante = $objHistorialPago->getComprobante();
        						            if ($objComprobante) {
        						                $comprobantes = $objComprobante->fe_comprobante_ser . "-" . $objComprobante->fe_comprobante_cor . "<br/>";
        						            }
        						        }
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo str_pad($objPedido->id, 8, "0", STR_PAD_LEFT); ?></td>
            							<td style="text-align: left;"><?php echo str_pad($objPago->id, 8, "0", STR_PAD_LEFT); ?></td>
            							<td style="text-align: left;"><?php echo date("d/m/Y H:i", strtotime($objPago->fecha_creacion)); ?></td>
            							<td style="text-align: left;"><?php echo $objPedido->getTipo()->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objTipoPago->nombre; ?></td>
            							<td style="text-align: right;"><?php echo number_format($montoTotal, 2); ?></td>
            							<td style="text-align: right;"><?php echo number_format($montoDescuento, 2); ?></td>
            							<td style="text-align: right;"><?php echo number_format($montoPagado, 2); ?></td>
            							<td style="text-align: right;"><?php echo number_format($montoEfectivo, 2); ?></td>
            							<td style="text-align: right;"><?php echo number_format($montoTarjeta, 2); ?></td>
            							<td style="text-align: left;"><?php echo $comprobantes; ?></td>
            							<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>
            							<td style="text-align: left;">
            								<?php if ($objEstado->opcion) { ?>
            								<a href="index.php?view=detailpayment&id=<?php echo $objPedido->id; ?>" title="Detalle" class="btn btn-success btn-xs"><em class="fa fa-search-plus"></em></a>
											<?php } ?>
            							</td>
        							</tr>
        						<?php
                                    }
                                ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="5" style="font-weight: bold;">TOTAL</td>
										<td style="text-align: right; font-weight: bold;"><?php echo number_format($totalMontoTotal, 2); ?></td>
										<td style="text-align: right; font-weight: bold;"><?php echo number_format($totalMontoDescuento, 2); ?></td>
										<td style="text-align: right; font-weight: bold;"><?php echo number_format($totalMontoPagado, 2); ?></td>
										<td style="text-align: right; font-weight: bold;"><?php echo number_format($totalMontoEfectivo, 2); ?></td>
										<td style="text-align: right; font-weight: bold;"><?php echo number_format($totalMontoTarjeta, 2); ?></td>
										<td colspan="2"></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>