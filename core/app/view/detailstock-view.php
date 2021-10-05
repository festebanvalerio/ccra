<?php
    $sede = $_SESSION["sede"];
    $insumo = $almacen = $stock = $unidad = "";
    $fechaInicio = $fechaFin = date("d/m/Y");
    $texto = "Detalle Stock";    
    if (isset($_GET["insumo"]) && isset($_GET["almacen"])) {
        $idInsumo = $_GET["insumo"];
        $idAlmacen = $_GET["almacen"];
        
        $objInsumo = InsumoData::getById($idInsumo);
        $insumo = $objInsumo->nombre;
        $unidad = $objInsumo->getUnidad()->abreviatura;
        
        $objAlmacen = AlmacenData::getById($idAlmacen);
        $almacen = $objAlmacen->nombre;
        
        $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $idInsumo, $idAlmacen);
        $objInsumoAlmacen = $lstInsumoAlmacen[0];
        $stock = number_format($objInsumoAlmacen->stock, 2);
    }
    if (count($_POST) > 0) {
        $fechaInicio = $_POST["fechai"];
        $fechaFin = $_POST["fechaf"];
    }
    $lstMovimiento = MovimientoData::getAll(1, $sede, $idInsumo, "", $fechaInicio, $fechaFin);
    
    $objSede = SedeData::getById($sede);
    $nomSede = $objSede->nombre;    
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
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>
    			<div class="panel-body">
            		<form class="form-horizontal" method="post" id="detailstock" action="" role="form" autocomplete="off">
            			<div class="form-group">
							<div class="col-md-4 col-sm-12">
                				<label for="sede">Sede :</label>
                				<input type="text" id="sede" name="sede" class="form-control" placeholder="Sede" value="<?php echo $nomSede; ?>" disabled/>
                			</div>            			
            				<div class="col-md-4 col-sm-12">
                				<label for="almacen">Almacen :</label>
                				<input type="text" id="almacen" name="almacen" class="form-control" placeholder="Almacen" value="<?php echo $almacen; ?>" disabled/>
                			</div>
                			<div class="col-md-2 col-sm-12">
                				<label for="insumo">Insumo :</label>
                				<input type="text" id="insumo" name="insumo" class="form-control" placeholder="Insumo" value="<?php echo $insumo; ?>" disabled/>
                			</div>
                			<div class="col-md-2 col-sm-12">
                				<label for="unidad">Unidad :</label>
                				<input type="text" id="unidad" name="unidad" class="form-control" placeholder="Unidad" value="<?php echo $unidad; ?>" disabled/>
                			</div>
                		</div>
        				<div class="form-group">
                			<div class="col-md-2 col-sm-12">
                				<label for="stock">Stock Actual:</label>
                				<input type="text" id="stock" name="stock" class="form-control" placeholder="Stock" value="<?php echo $stock; ?>" disabled/>
                			</div>
                			<div class="col-md-2 col-sm-12">
                				<label for="fecha">Fecha Inicio:</label>
        						<input type="text" id="fechai" name="fechai" value="<?php echo $fechaInicio; ?>" class="form-control" readonly/>
        					</div>
        					<div class="col-md-2 col-sm-12">
                				<label for="fecha">Fecha Fin:</label>
        						<input type="text" id="fechaf" name="fechaf" value="<?php echo $fechaFin; ?>" class="form-control" readonly/>
        					</div>
        					<div class="col-md-4 col-sm-12" style="padding-top: 25px;">
                				<button type="button" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>
        						<button type="button" id="btnRegresar" class="btn btn-danger" title="Regresar"><em class="fa fa-reply"></em></button>
        						<?php if (count($lstMovimiento) > 0) { ?>
        						<a class="btn btn-primary" id="btnExportar" href="excelMovimientoStock.php?sede=<?php echo $sede; ?>&almacen=<?php echo $objAlmacen->id; ?>&insumo=<?php echo $idInsumo; ?>&fechainicio=<?php echo $fechaInicio; ?>&fechafin=<?php echo $fechaFin; ?>" role="button" title="Descargar" target="_blank"><em class="fa fa-download"></em></a>
        						<?php } ?>
            					<script type="text/javascript">
            						$("#btnBuscar").click(function(){
            							$.blockUI();
            		    				$("button").prop("disabled", true);
            		    				$("#detailstock").submit();
            		    			});
                					$("#btnRegresar").click(function(){
                						$.blockUI();
                						$("button").prop("disabled", true);
                		    			location.href = "./index.php?view=stocks";
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
                						<th scope="col">Movimiento</th>
                						<th scope="col" style="text-align: right;">Ingreso</th>
                						<th scope="col" style="text-align: right;">Salida</th>        						
                					</tr>
                				</thead>
                				<tbody>
                					<?php
                					       foreach ($lstMovimiento as $objMovimiento) {
                					           $cantidadIngreso = $cantidadSalida = 0.00;        					       
                					           // Salida        					           
                					           if ($objMovimiento->tipo == 0) {
                					               $cantidadSalida = $objMovimiento->cantidad;
                					           } else { // Ingreso
                					               $cantidadIngreso = $objMovimiento->cantidad;
                					           }
                					    
                					?>
                					<tr>
                    					<td style="text-align: left;"><?php echo date("d/m/Y H:i:s", strtotime($objMovimiento->fecha)); ?></td>
                    					<td style="text-align: left;"><?php echo $objMovimiento->detalle; ?></td>
                    					<td style="text-align: right;"><?php echo number_format($cantidadIngreso, 2); ?></td>
                    					<td style="text-align: right;"><?php echo number_format($cantidadSalida, 2); ?></td>
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