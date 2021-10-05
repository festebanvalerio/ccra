<?php
    $fecha = date("d/m/Y");
    if (count($_POST) > 0) {
        $fecha = $_POST["fecha"];        
    }
    $lstInsumoAlmacen = NULL;
    $lstAlmacen = AlmacenData::getAll(1, $_SESSION["empresa"], $_SESSION["sede"]);
    if (count($lstAlmacen) > 0) {
        $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, "", $lstAlmacen[0]->id);
    }
?>
<script type="text/javascript">
	$(function() {
		$("#fecha").datepicker({
    		dateFormat: "dd/mm/yy",
    		changeMonth: true,
    		changeYear: true,
    		yearRange: '1900:<?php echo date('Y'); ?>'
    	});		
	});
</script>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Reporte Cierre de Inventario</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="adjustments" action="" role="form" autocomplete="off">    			
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
                				<label for="fecha">Fecha:</label>
        						<input type="text" id="fecha" name="fecha" value="<?php echo $fecha; ?>" class="form-control"/>
        					</div>
        					<div class="col-md-4 col-sm-12" style="padding-top: 25px;">
        						<button type="submit" id="btnBuscar" class="btn btn-success">Buscar</button>
        						<button type="button" id="btnExportar" class="btn btn-info">Exportar</button>
        						<button type="button" id="btnLimpiar" class="btn btn-danger">Limpiar</button>        						
        						<script type="text/javascript">
            						$("#btnLimpiar").click(function(){
            		    				location.href = "./index.php?view=report1";
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
        								<th scope="col">Almacen</th>
        								<th scope="col">Insumo</th>
        								<!--<th scope="col" style="text-align: right;">Stock Actual</th>-->
        								<th scope="col" style="text-align: right;">Ingresos</th>
        								<th scope="col" style="text-align: right;">Salidas</th>
        								<th scope="col" style="text-align: right;">Saldo</th>
        								<th scope="col">Acciones</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						    foreach ($lstInsumoAlmacen as $objInsumoAlmacen) {
        						        $objInsumo = $objInsumoAlmacen->getInsumo();
        						        
        						        // Ingresos
        						        $lstMovimientos = MovimientoData::getTotal(1, $_SESSION["sede"], $objInsumo->id, 1, $fecha);
        						        $totalIngresos = $lstMovimientos->total;
        						        
        						        // Salidas
        						        $lstMovimientos = MovimientoData::getTotal(1, $_SESSION["sede"], $objInsumo->id, 0, $fecha);
        						        $totalSalidas = $lstMovimientos->total;
                                ?>
        							<tr>
        								<td style="text-align: left;"><?php echo $objInsumo->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objInsumo->nombre; ?></td>
            							<!--<td style="text-align: right;"></td>-->
            							<td style="text-align: right;"><?php echo number_format($totalIngresos, 2); ?></td>
            							<td style="text-align: right;"><?php echo number_format($totalSalidas, 2); ?></td>
            							<td style="text-align: right;"><?php echo number_format($totalIngresos - $totalSalidas, 2); ?></td>
            							<td style="text-align: left;">
            								<a href="index.php?view=newadjustment&id=<?php echo $objInsumoAlmacen->id; ?>" title="Detalle" class="btn btn-success btn-xs">D</a>
            							</td>
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