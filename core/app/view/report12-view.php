<?php
    $insumo = $clasificacion = array();
    $lstIdInsumo = $lstIdClasificacion = $almacen = "";
    $empresa = $_SESSION["empresa"];    
    $sede = $_SESSION["sede"];
    $fechaInicio = $fechaFin = date("d/m/Y");
    $lstDetalleHistoricoStock = array();
    if (count($_POST) > 0) {
        if (isset($_POST["insumo"])) {
            $insumo = $_POST["insumo"];
        }
        if (isset($_POST["clasificacion"])) {
            $clasificacion = $_POST["clasificacion"];
        }
        $fechaInicio = $_POST["fechai"];
        $fechaFin = $_POST["fechaf"];
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
        $lstDetalleHistoricoStock = DetalleHistoricoStockData::getDetalle($sede, $almacen, $fechaInicio, $fechaFin, $lstIdInsumo, $lstIdClasificacion);
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
    
    $lstAlmacen = AlmacenData::getAll(1, $empresa, $sede);
    $lstInsumo = InsumoData::getAll(1, $sede);
    $lstClasificacion = ClasificacionData::getAll(1, $sede);
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
		$("#insumo").multiselect();
		$("#clasificacion").multiselect();
	});	
</script>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Reporte Stock Detallado</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="report12" action="" role="form" autocomplete="off">
            			<div class="form-group">
            				<div class="col-md-4 col-sm-12">
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
    						<div class="col-md-4 col-sm-12">
    							<label for="clasificacion">Clasificación :</label>
    							<select id="clasificacion" name="clasificacion[]" class="form-control" multiple>
                                  	<?php foreach ($lstClasificacion as $objClasificacion) { ?>
    								<option value="<?php echo $objClasificacion->id; ?>" <?php if (in_array($objClasificacion->id, $clasificacion)) { echo "selected"; } ?>><?php echo $objClasificacion->nombre; ?></option>
      								<?php } ?>
                                </select>
    						</div>
    						<div class="col-md-4 col-sm-12">
    							<label for="insumo">Insumo :</label>
    							<select id="insumo" name="insumo[]" class="form-control" multiple>
                                  	<?php foreach ($lstInsumo as $objInsumo) { ?>
    								<option value="<?php echo $objInsumo->id; ?>" <?php if (in_array($objInsumo->id, $insumo)) { echo "selected"; } ?>><?php echo $objInsumo->nombre; ?></option>
      								<?php } ?>
                                </select>
    						</div>
    					</div>
    					<div class="form-group">
            				<div class="col-md-2 col-sm-12">
                				<label for="fechai">Fecha Inicio:</label>
        						<input type="text" id="fechai" name="fechai" value="<?php echo $fechaInicio; ?>" class="form-control" readonly/>
        					</div>
        					<div class="col-md-2 col-sm-12">
                				<label for="fechaf">Fecha Fin:</label>
        						<input type="text" id="fechaf" name="fechaf" value="<?php echo $fechaFin; ?>" class="form-control" readonly/>
        					</div>        					
    						<div class="col-md-8 col-sm-12" style="padding-top: 25px;">
        						<button type="button" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>
        						<button type="button" id="btnLimpiar" class="btn btn-danger" title="Limpiar"><em class="fa fa-eraser"></em></button>
        						<?php if (count($_POST) > 0 && count($lstDetalleHistoricoStock) > 0) { ?>
        						<a class="btn btn-primary" id="btnExportar" href="excelHistoricoStockDetalle.php?sede=<?php echo $sede; ?>&almacen=<?php echo $almacen; ?>&insumo=<?php echo $lstIdInsumo; ?>&clasificacion=<?php echo $lstIdClasificacion; ?>&fechaInicio=<?php echo $fechaInicio; ?>&fechaFin=<?php echo $fechaFin; ?>" role="button" title="Descargar" target="_blank"><em class="fa fa-download"></em></a>
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
        									$("#report12").submit();
        								}
        							});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=report12";
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
        								<th scope="col" rowspan="2">Item</th>
        								<th scope="col" rowspan="2">Insumo</th>
        								<th scope="col" rowspan="2">Clasificación</th>
        								<th scope="col" rowspan="2">Unidad Medida</th>
        								<?php foreach($lstFecha as $dataFecha) { ?>
        								<th scope="col" colspan="3" style="text-align: center;"><?php echo $dataFecha; ?></th>
        								<?php } ?>
        							</tr>
        							<tr>
        								<?php foreach($lstFecha as $dataFecha) { ?>
        								<th scope="col" style="text-align: right;">Ingresa</th>
        								<th scope="col" style="text-align: right;">Queda</th>
        								<th scope="col" style="text-align: right;">Stock</th>
        								<?php } ?>        								
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						      $item = 1;
        						      foreach ($lstDetalleHistoricoStock as $objDetalleHistoricoStock) {        						          
                                ?>
        							<tr>
        								<td style="text-align: left;"><?php echo $item++; ?></td>
            							<td style="text-align: left;"><?php echo $objDetalleHistoricoStock->nom_insumo; ?></td>
            							<td style="text-align: left;"><?php echo $objDetalleHistoricoStock->clasificacion; ?></td>
            							<td style="text-align: left;"><?php echo $objDetalleHistoricoStock->unidad_medida; ?></td>
            							<?php
            							     foreach($lstFecha as $dataFecha) {
            							         $stockInicial = $ingresos = $stockActual = 0;
            							         $lstDetalleHistoricoStockTmp = DetalleHistoricoStockData::getDetalleXInsumo($sede, $almacen, $dataFecha, $dataFecha, $objDetalleHistoricoStock->insumo);
            							         if (count($lstDetalleHistoricoStockTmp) == 1) {
            							             $stockInicial = $lstDetalleHistoricoStockTmp[0]->stock;
            							             
            							             $totalEntradas = $objDetalleHistoricoStock->getTotal(1, $sede, $objDetalleHistoricoStock->insumo, 1, $dataFecha, 1);
            							             $ingresos = $totalEntradas->total;
            							             
            							             $arrFecha = explode("/", $dataFecha);
            							             $fechaTmp = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            							             
            							             $fecha = date_create($fechaTmp);
            							             date_add($fecha, date_interval_create_from_date_string("1 days"));
            							             $dataFechaTmp = date_format($fecha, "d/m/Y");
            							             
            							             $lstDetalleHistoricoStockTmp = DetalleHistoricoStockData::getDetalleXInsumo($sede, $almacen, $dataFechaTmp, $dataFechaTmp, $objDetalleHistoricoStock->insumo);
            							             if (count($lstDetalleHistoricoStockTmp) == 1) {
            							                 $stockActual = $lstDetalleHistoricoStockTmp[0]->stock;
            							             } else {
            							                 $lstInsumoXAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objDetalleHistoricoStock->insumo, $almacen);
            							                 if (count($lstInsumoXAlmacen) == 1) {
            							                     $stockActual = $lstInsumoXAlmacen[0]->stock;
            							                 }
            							             }
            							         }
            							?>
            							<td style="text-align: right;"><?php echo number_format($ingresos, 2); ?>
            							<td style="text-align: right;"><?php echo number_format($stockInicial + $ingresos, 2); ?>
            							<td style="text-align: right;"><?php echo number_format($stockActual, 2); ?>
            							<?php 
            							     }
                                        ?>
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