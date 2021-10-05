<?php
    $sede = $_SESSION["sede"];
    $estado = "1";
    $tipo = "";
    $fechaInicio = date("d/m/Y");
    $fechaFin = date("d/m/Y");
    if (count($_POST) > 0) {
        $estado = $_POST["estado"];
        $tipo = $_POST["tipo"];
        $fechaInicio = $_POST["fechai"];
        $fechaFin = $_POST["fechaf"];
    }
        
    $lstTipoComprobante = ParametroData::getAllVouchers(1, "TIPO DOCUMENTO");
    $lstEstado = EstadoData::getAll();    
    $lstHistorialDocumento = HistorialDocumentoData::getAll($estado, $sede, $tipo, $fechaInicio, $fechaFin, 1);
    
    $exonerado = $_SESSION["exonerado"];
    $ruc = $_SESSION["ruc"];
    $indFacturaElectronica = $_SESSION["factel"];
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
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Comprobantes Electrónicos</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="vouchers" action="" role="form" autocomplete="off">    			
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
                				<label for="fecha">Fecha Inicio :</label>
        						<input type="text" id="fechai" name="fechai" value="<?php echo $fechaInicio; ?>" class="form-control" readonly/>
        					</div>
        					<div class="col-md-2 col-sm-12">
                				<label for="fecha">Fecha Fin :</label>
        						<input type="text" id="fechaf" name="fechaf" value="<?php echo $fechaFin; ?>" class="form-control" readonly/>
        					</div>
        					<div class="col-md-2 col-sm-12">
    							<label for="tipo">Tipo Comprobante :</label>
    							<select id="tipo" name="tipo" class="form-control">
    								<option value="">TODOS</option>
    								<?php foreach ($lstTipoComprobante as $objTipoComprobante) { ?>
    								<option value="<?php echo $objTipoComprobante->valor2; ?>" <?php if ($objTipoComprobante->valor2 == $tipo) { echo "selected"; } ?>><?php echo $objTipoComprobante->nombre; ?></option>
      								<?php } ?>
    							</select>
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
    						<div class="col-md-4 col-sm-12" style="padding-top: 25px;">
        						<button type="button" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>
        						<button type="button" id="btnLimpiar" class="btn btn-danger" title="Limpiar"><em class="fa fa-eraser"></em></button>        						
        						<script type="text/javascript">
        							$("#btnBuscar").click(function(){
            							$.blockUI();
            		    				$("button").prop("disabled", true);
            		    				$("#vouchers").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=vouchers";
            		    			});
        						</script>
            				</div>
    					</div>
            		</form>
					<div class="table-responsive">
        				<div class="box-body">
        					<table class="table table-bordered table-hover datatable table-nowrap">
        						<thead>
        							<tr>
        								<th scope="col">Fecha</th>
        								<th scope="col">Num. Comprobante</th>
        								<th scope="col" style="width: 30%;">Cliente</th>
        								<th scope="col" style="text-align: right;">SubTotal</th>
        								<th scope="col" style="text-align: right;">IGV</th>
        								<th scope="col" style="text-align: right;">Total</th>
        								<th scope="col">Fecha Envio Sunat</th>
        								<th scope="col">Estado Envio Sunat</th>
        								<th scope="col">Estado</th>
        								<th scope="col" style="width: 10%;">Acciones</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						      $totalMontoComprobante = $totlaIgv = $totalImporte = 0;
        						      foreach ($lstHistorialDocumento as $objHistorialDocumento) {
        						          $objComprobante = $objHistorialDocumento->getComprobante();
        						          $objEstado = $objHistorialDocumento->getEstado();
        						          $montoComprobante = $objComprobante->fe_comprobante_totvengra;
        						          if ($exonerado == 1) {
        						              $montoComprobante = $objComprobante->fe_comprobante_totvenexo;
        						          }
        						         
        						          $xml = $cdr = "";
        						          if ($objComprobante->cs_tipodocumento_cod > 0) {
            						          $xml = "" . $ruc . "-0" . $objComprobante->cs_tipodocumento_cod . "-" . $objComprobante->fe_comprobante_ser . "-" . $objComprobante->fe_comprobante_cor;
            						          $cdr = "R-" . $ruc . "-0" . $objComprobante->cs_tipodocumento_cod . "-" . $objComprobante->fe_comprobante_ser . "-" . $objComprobante->fe_comprobante_cor;
        						          }
        						          
        						          $totalMontoComprobante += $montoComprobante;
        						          $totlaIgv += $objComprobante->fe_comprobante_sumigv;
        						          $totalImporte += $objComprobante->fe_comprobante_imptot;
        						          
        						          $errorEnvioSunat = false;
        						          $fechaEnvioSunat = $estadoSunat = "";
        						          if ($objHistorialDocumento->cs_tipodocumento_cod == 1) { // Factura
        						              if ($objHistorialDocumento->fe_comprobante_estsun == "1") {
        						                  $fechaEnvioSunat = date("d/m/Y H:i", strtotime($objHistorialDocumento->fe_comprobante_fecenvsun));
        						                  $estadoSunat = "ACEPTADO";
        						              } else {
        						                  $estadoSunat = $objHistorialDocumento->fe_comprobante_faucod;
        						                  if ($objHistorialDocumento->fe_comprobante_fecenvsun != "0000-00-00 00:00:00") {
        						                      $fechaEnvioSunat = date("d/m/Y H:i", strtotime($objHistorialDocumento->fe_comprobante_fecenvsun));
        						                  }
        						                  if ($estadoSunat != "") {
        						                      $errorEnvioSunat = true;
        						                  }
        						              }
        						          } else if ($objHistorialDocumento->cs_tipodocumento_cod == 3) { // Boleta
        						              $numBoleta = $objComprobante->fe_comprobante_ser."-".$objComprobante->fe_comprobante_cor;
        						              $objResumenDetalleBoleta = ResumenBoletaDetalleData::getBoleta($numBoleta);
        						              if ($objResumenDetalleBoleta) {
        						                  if ($objResumenDetalleBoleta->fe_resumenboleta_fecenvsun != "") {
        						                      $fechaEnvioSunat = date("d/m/Y H:i", strtotime($objResumenDetalleBoleta->fe_resumenboleta_fecenvsun));
        						                      if ($objResumenDetalleBoleta->fe_resumenboleta_estsun2 == 1) {
            						                      $estadoSunat = "ACEPTADO";
            						                  }
        						                  }
        						              }
        						          }
                                ?>
        							<tr>
										<td style="text-align: left;"><?php echo date("d/m/Y", strtotime($objComprobante->fe_comprobante_reg)); ?></td>
										<td style="text-align: left;"><?php echo $objComprobante->fe_comprobante_ser."-".$objComprobante->fe_comprobante_cor; ?></td>
										<td style="text-align: left;"><?php echo $objComprobante->tb_cliente_numdoc."-".$objComprobante->tb_cliente_nom; ?></td>
										<td style="text-align: right;"><?php echo number_format($montoComprobante, 2); ?></td>
										<td style="text-align: right;"><?php echo number_format($objComprobante->fe_comprobante_sumigv, 2); ?></td>
										<td style="text-align: right;"><?php echo number_format($objComprobante->fe_comprobante_imptot, 2); ?></td>
										<td style="text-align: left;"><?php echo $fechaEnvioSunat; ?></td>
										<td style="text-align: left;"><?php echo $estadoSunat; ?></td>
										<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>
            							<td style="text-align: left;">
            								<?php if ($objEstado->opcion == 1) { ?>
            								<?php if (!$errorEnvioSunat) { ?>
            								<a href="ticket.php?id=<?php echo $objHistorialDocumento->id; ?>" title="Imprimir" class="btn btn-success btn-xs" target="_blank"><em class="fa fa-print"></em></a>
            								<?php } ?>
            								<?php if ($errorEnvioSunat) { ?>
            								<a id="lnkdelivery<?php echo $objHistorialDocumento->id; ?>" title="Reenviar a Sunat" class="btn btn-info btn-xs"><em class="fa fa-reply"></em></a>
            								<?php } ?>            								
            								<script type="text/javascript">
            									$("#lnkdelivery<?php echo $objHistorialDocumento->id; ?>").click(function() {
            										$.ajax({
                									    type: "post",
                									    url: "./?action=adddocument",
                									    data: "id=<?php echo $objHistorialDocumento->id; ?>&accion=5",
                									    dataType: "html",
                									    beforeSend: function() {
                									    	$("#lnkdelivery<?php echo $objHistorialDocumento->id; ?>").attr("disabled","disabled");
                		    		    					$.blockUI();
                		    		    				},
                									    success: function(data) {
                									        if (data > 0) {
                									        	Swal.fire({
                            		                                icon: "success",
                            		                                title: "Se reenvió correctamente el comprobante <?php echo $objComprobante->fe_comprobante_ser."-".$objComprobante->fe_comprobante_cor; ?>",
                            										showCancelButton: false,
                            										confirmButtonColor: "#3085d6",
                            										confirmButtonText: "OK"
                            		                        	}).then((result) => {
                            										window.location.href = "./index.php?view=vouchers";
                            		                        	})
                									        } else {
                									        	Swal.fire({
                		    		    							icon: "warning",
                		    		    							title: "Ocurrio un error al reenviar el comprobante <?php echo $objComprobante->fe_comprobante_ser."-".$objComprobante->fe_comprobante_cor; ?>"
                		    		    						})
                										    }
                									    },
                									    error: function() {
                									    	Swal.fire({
                	    		    							icon: "error",
                	    		    							title: "Ocurrio un error al reenviar el comprobante <?php echo $objComprobante->fe_comprobante_ser."-".$objComprobante->fe_comprobante_cor; ?>"
                	    		    						})                        									        
                									    },
                									    complete: function(data) {
                		    		    					$("#lnkdelivery<?php echo $objHistorialDocumento->id; ?>").removeAttr("disabled");
                		    		    					$.unblockUI();
                		    		    				}
                									});
            									});
            								</script>            								
            								<?php if ($indFacturaElectronica == 1 && $objComprobante->cs_tipodocumento_cod > 0 && !$errorEnvioSunat && $fechaEnvioSunat != "") { ?>
            								<a href="../../cperepositorio/send/<?php echo $xml; ?>.xml" title="Descargar XML" class="btn btn-primary btn-xs" target="_blank"><em class="fa fa-cloud-download"></em></a>
            								<!--<a href="../../cperepositorio/cdr/<?php echo $cdr; ?>.zip" title="Descargar CDR" class="btn btn-primary btn-xs" target="_blank"><em class="fa fa-cloud-download"></em></a>-->
            								<?php } ?>
            								<?php } ?>
            							</td>
        							</tr>
        						<?php
                                    }
                                ?>
                                </tbody>
                                <tfoot>
                                	<tr>
                                    	<td colspan="3" style="font-weight: bold;">TOTAL</td>
                                    	<td style="text-align: right; font-weight: bold;"><?php echo number_format($totalMontoComprobante, 2); ?></td>
                                    	<td style="text-align: right; font-weight: bold;"><?php echo number_format($totlaIgv, 2); ?></td>
                                    	<td style="text-align: right; font-weight: bold;"><?php echo number_format($totalImporte, 2); ?></td>
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