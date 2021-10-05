<?php
    $sede = $_SESSION["sede"];
    $fechaInicio = date("d/m/Y");
    $fechaFin = date("d/m/Y");
    if (count($_POST) > 0) {
        $fechaInicio = $_POST["fechai"];
        $fechaFin = $_POST["fechaf"];
    }
    $arrFecha = explode("/", $fechaInicio);
    $fechaInicioBus = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
    
    $arrFecha = explode("/", $fechaFin);
    $fechaFinBus = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
    
    $lstFecha = array();
    $sd = strtotime($fechaInicioBus);
    $ed = strtotime($fechaFinBus);
    for ($i = $sd; $i <= $ed; $i += (60 * 60 * 24)) {
        $lstFecha[] = date("d/m/Y", $i);
    }
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
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Reporte Cuadre</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="report11" action="" role="form" autocomplete="off">
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
                				<label for="fecha">Fecha Inicio:</label>
        						<input type="text" id="fechai" name="fechai" value="<?php echo $fechaInicio; ?>" class="form-control" readonly/>
        					</div>
        					<div class="col-md-2 col-sm-12">
                				<label for="fecha">Fecha Fin:</label>
        						<input type="text" id="fechaf" name="fechaf" value="<?php echo $fechaFin; ?>" class="form-control" readonly/>
        					</div>
    						<div class="col-md-6 col-sm-12" style="padding-top: 25px;">
        						<button type="button" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>
        						<button type="button" id="btnLimpiar" class="btn btn-danger" title="Limpiar"><em class="fa fa-eraser"></em></button>
        						<script type="text/javascript">
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=report11";
            		    			});
            		    			$("#btnBuscar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            							$("#report11").submit();
            		    			})
        						</script>
            				</div>
    					</div>
            		</form>
					<div class="table-responsive">
        				<div class="box-body">
        					<table id="example" class="table table-bordered table-hover datatable table-nowrap">
        						<thead>
        							<tr>
        								<th scope="col">Fecha</th>
        								<th scope="col">Num. Pagos</th>
        								<th scope="col">Num. Comprobantes</th>
        								<th scope="col">Diferencia</th>
        								<th scope="col">Monto Pagado</th>
        								<th scope="col">Monto Comprobante</th>
        								<th scope="col">Diferencia</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						      $item = 1;
        						      foreach ($lstFecha as $dataFecha) {
        						          $arrFecha = explode("/", $dataFecha);
        						          $fechaInicioTmp = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
        						          
        						          $arrFecha = explode("/", $dataFecha);
        						          $fechaFinTmp = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
        						          
        						          $numPagos = $numComprobantes = $totalPagado = $totalComprobante = 0;
        						          $objPago = PagoData::getTotalPagadoXFecha($sede, $fechaInicioTmp, $fechaFinTmp);
        						          if ($objPago) {
        						              $totalPagado = $objPago->total;
        						          }
        						          $objPago = PagoData::getNumTotalPagadoXFecha($sede, $fechaInicioTmp, $fechaFinTmp);
        						          if ($objPago) {
        						              $numPagos = $objPago->total;
        						          }
        						          
        						          $objComprobante = ComprobanteData::getTotalComprobanteXFecha($sede, $fechaInicioTmp, $fechaFinTmp);
        						          if ($objComprobante) {
        						              $totalComprobante = $objComprobante->total;
        						          }
        						          $objComprobante = ComprobanteData::getNumTotalComprobanteXFecha($sede, $fechaInicioTmp, $fechaFinTmp);
        						          if ($objComprobante) {
        						              $numComprobantes = $objComprobante->total;
        						          }
        						          
        						          $detallePago = "";
        						          $diferencia1 = $numPagos - $numComprobantes;
        						          if ($diferencia1 > 0) {
        						              $lstPago = PagoData::getPagosSinComprobante($sede, $fechaInicioTmp, $fechaFinTmp);
        						              if (count($lstPago) > 0) {        						                  
        						                  foreach ($lstPago as $objPago) {
        						                      $detallePago .= date("d/m/Y", strtotime($objPago->fecha)) . " - " . str_pad($objPago->pago, 8, "0", STR_PAD_LEFT) . " - " . $objPago->total . "<br>";
        						                  }
        						              }
        						          }
        						          $diferencia2 = $totalPagado - $totalComprobante;
                                ?>
        							<tr>
        								<td style="text-align: left;"><?php echo $dataFecha; ?></td>
        								<td style="text-align: right;"><?php echo number_format($numPagos, 2); ?></td>
        								<td style="text-align: right;"><?php echo number_format($numComprobantes, 2); ?></td>
        								<?php if ($diferencia1 > 0) { ?>
            							<td style="text-align: right;">
            								<a href="#" id="lnkdetalle<?php echo $item; ?>" style="color: red;"><?php echo number_format($diferencia1, 2); ?></a>
            								<script type="text/javascript">
            									$("#lnkdetalle<?php echo $item++; ?>").click(function() {
            										Swal.fire({
                                    					icon: "success",
                                    					title: "<?php echo $detallePago; ?>"
                                    				})
            									});
            								</script>
            							</td>
            							<?php } else { ?>
            							<td style="text-align: right;"><?php echo number_format($diferencia1, 2); ?></td>
            							<?php } ?>
            							<td style="text-align: right;"><?php echo number_format($totalPagado, 2); ?></td>
            							<td style="text-align: right;"><?php echo number_format($totalComprobante, 2); ?></td>
            							<td style="text-align: right;"><?php echo number_format($diferencia2, 2); ?></td>
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
</section>