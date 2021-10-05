<?php
    $insumo = $clasificacion = array();
    $lstIdInsumo = $lstIdClasificacion = $almacen = "";
    $empresa = $_SESSION["empresa"];    
    $sede = $_SESSION["sede"];
    $fecha = date("d/m/Y");
    $lstDetalleHistoricoStock = array();
    if (count($_POST) > 0) {
        if (isset($_POST["insumo"])) {
            $insumo = $_POST["insumo"];
        }
        if (isset($_POST["clasificacion"])) {
            $clasificacion = $_POST["clasificacion"];
        }
        $fecha = $_POST["fecha"];
        $almacen = $_POST["almacen"];
        if (is_array($insumo)) {
            for($inicio=0;$inicio<count($insumo);$inicio++) {
                $lstIdInsumo .= $insumo[$inicio].",";
            }
            if ($lstIdInsumo != "") {
                $lstIdInsumo = substr($lstIdInsumo, 0, strlen($lstIdInsumo) - 1);
            }
        }
        if (is_array($clasificacion)) {
            for($inicio=0;$inicio<count($clasificacion);$inicio++) {
                $lstIdClasificacion .= $clasificacion[$inicio].",";
            }
            if ($lstIdClasificacion != "") {
                $lstIdClasificacion = substr($lstIdClasificacion, 0, strlen($lstIdClasificacion) - 1);
            }
        }
        $lstDetalleHistoricoStock = DetalleHistoricoStockData::getAll($sede, $almacen, $fecha, $lstIdInsumo, $lstIdClasificacion);
    }    
    $lstAlmacen = AlmacenData::getAll(1, $empresa, $sede);
    $lstInsumo = InsumoData::getAll(1, $sede);
    $lstClasificacion = ClasificacionData::getAll(1, $sede);
?>
<script type="text/javascript">
	$(function() {
		$("#fecha").datepicker({
    		dateFormat: "dd/mm/yy",
    		changeMonth: true,
    		changeYear: true,
    		maxDate: "0",
    		yearRange: "1900:<?php echo date("Y"); ?>"
    	});
		$("#insumo").multiselect();
		$("#clasificacion").multiselect();
	});	
</script>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Reporte Stock</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="report8" action="" role="form" autocomplete="off">
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
    							<label for="almacen">Almacen :</label>
    							<select id="almacen" name="almacen" class="form-control">
    								<?php if (count($lstAlmacen) > 1) { ?>
    								<option value="">SELECCIONE</option>
    								<?php } ?>    								
    								<?php foreach ($lstAlmacen as $objAlmacen) { ?>
    								<option value="<?php echo $objAlmacen->id; ?>" <?php if ($objAlmacen->id == $almacen) { echo "selected"; } ?>><?php echo $objAlmacen->nombre; ?></option>
      								<?php } ?>
    							</select>
    						</div>
    						<div class="col-md-3 col-sm-12">
    							<label for="clasificacion">Clasificación :</label>
    							<select id="clasificacion" name="clasificacion[]" class="form-control" multiple>
                                  	<?php foreach ($lstClasificacion as $objClasificacion) { ?>
    								<option value="<?php echo $objClasificacion->id; ?>" <?php if (in_array($objClasificacion->id, $clasificacion)) { echo "selected"; } ?>><?php echo $objClasificacion->nombre; ?></option>
      								<?php } ?>
                                </select>
    						</div>
    						<div class="col-md-3 col-sm-12">
    							<label for="insumo">Insumo :</label>
    							<select id="insumo" name="insumo[]" class="form-control" multiple>
                                  	<?php foreach ($lstInsumo as $objInsumo) { ?>
    								<option value="<?php echo $objInsumo->id; ?>" <?php if (in_array($objInsumo->id, $insumo)) { echo "selected"; } ?>><?php echo $objInsumo->nombre; ?></option>
      								<?php } ?>
                                </select>
    						</div>
            				<div class="col-md-2 col-sm-12">
                				<label for="fecha">Fecha :</label>
        						<input type="text" id="fecha" name="fecha" value="<?php echo $fecha; ?>" class="form-control" readonly/>
        					</div>        					
    						<div class="col-md-2 col-sm-12" style="padding-top: 25px;">
        						<button type="button" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>
        						<button type="button" id="btnLimpiar" class="btn btn-danger" title="Limpiar"><em class="fa fa-eraser"></em></button>
        						<?php if (count($_POST) > 0 && count($lstDetalleHistoricoStock) > 0) { ?>
        						<a class="btn btn-primary" id="btnExportar" href="excelHistoricoStock.php?sede=<?php echo $sede; ?>&almacen=<?php echo $almacen; ?>&insumo=<?php echo $lstIdInsumo; ?>&clasificacion=<?php echo $lstIdClasificacion; ?>&fecha=<?php echo $fecha; ?>" role="button" title="Descargar" target="_blank"><em class="fa fa-download"></em></a>
        						<?php } ?>
        						<script type="text/javascript">
        							$("#btnBuscar").click(function(){
        								var almacen = $("#almacen").val();
        								if (almacen === "") {
        									Swal.fire({
                    							icon: "warning",
                    							title: "Campo obligatorio (almacen)"
                    						})
        								} else {
        									$.blockUI();
        									$("button").prop("disabled", true);
        									$("#report8").submit();
        								}
        							});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=report8";
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
        								<th scope="col">Item</th>
        								<th scope="col">Insumo</th>
        								<th scope="col">Clasificación</th>
        								<th scope="col">Unidad Medida</th>
        								<th scope="col" style="text-align: right;">Saldo Inicial</th>
        								<th scope="col" style="text-align: right;">Entrada</th>
        								<th scope="col" style="text-align: right;">Salida</th>
        								<th scope="col" style="text-align: right;">Saldo Final</th>
        								<th scope="col" style="text-align: right;">Stock Actual</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						      $item = 1;
        						      foreach ($lstDetalleHistoricoStock as $objDetalleHistoricoStock) {
        						          $totalEntradas = $objDetalleHistoricoStock->getTotal(1, $sede, $objDetalleHistoricoStock->insumo, 1, $fecha, 1);
        						          $totalSalidas = $objDetalleHistoricoStock->getTotal(1, $sede, $objDetalleHistoricoStock->insumo, -1, $fecha, 1);
        						          
        						          $color1 = "blue";
        						          $saldoFinal = $objDetalleHistoricoStock->stock + $totalEntradas->total - $totalSalidas->total;
        						          if ($saldoFinal < 0) {
        						              $color1 = "red";
        						          }
        						          
        						          $color2 = "blue";
        						          $diferencia = $objDetalleHistoricoStock->stock - $saldoFinal;
        						          if ($diferencia < 0) {
        						              $color2 = "red";
        						          }
        						          
        						          $stockActual = 0;
        						          $lstInsumoXAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objDetalleHistoricoStock->insumo, $almacen);
        						          if (count($lstInsumoXAlmacen) == 1) {
        						              $stockActual = $lstInsumoXAlmacen[0]->stock;
        						          }
                                ?>
        							<tr>
        								<td style="text-align: left;"><?php echo $item++; ?></td>
            							<td style="text-align: left;"><?php echo $objDetalleHistoricoStock->nom_insumo; ?></td>
            							<td style="text-align: left;"><?php echo $objDetalleHistoricoStock->clasificacion; ?></td>
            							<td style="text-align: left;"><?php echo $objDetalleHistoricoStock->unidad_medida; ?></td>
            							<td style="text-align: right;"><?php echo number_format($objDetalleHistoricoStock->stock, 2); ?>
            							<td style="text-align: right;"><?php echo number_format($totalEntradas->total, 2); ?>
            							<td style="text-align: right;"><?php echo number_format($totalSalidas->total, 2); ?>
            							<td style="text-align: right; color: <?php echo $color1; ?>"><?php echo number_format($saldoFinal, 2); ?></td>
            							<td style="text-align: right; color: <?php echo $color2; ?>"><?php echo number_format($stockActual, 2); ?></td>
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