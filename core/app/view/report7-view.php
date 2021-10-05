<?php
    $sede = $_SESSION["sede"];
    $fechaInicio = date("d/m/Y");
    $fechaFin = date("d/m/Y");
    if (count($_POST) > 0) {
        $fechaInicio = $_POST["fechai"];
        $fechaFin = $_POST["fechaf"];
    }
    $lstDetallePedido = DetallePedidoData::getAllProductoEliminado($sede, $fechaInicio, $fechaFin);
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
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Reporte Productos Eliminados</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="report7" action="" role="form" autocomplete="off">    			
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
        						<a class="btn btn-primary" id="btnExportar" href="excelProductosEliminados.php?sede=<?php echo $sede; ?>&fechaInicio=<?php echo $fechaInicio; ?>&fechaFin=<?php echo $fechaFin; ?>" role="button" title="Descargar" target="_blank"><em class="fa fa-download"></em></a>
        						<script type="text/javascript">
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=report7";
            		    			});
            		    			$("#btnBuscar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            							$("#report7").submit();
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
        								<th scope="col">Fecha Registro</th>
        								<th scope="col">Fecha Eliminación</th>
        								<th scope="col">Num. Pedido</th>
        								<th scope="col">Producto</th>
        								<th scope="col" style="text-align: right;">Cantidad</th>
        								<th scope="col" style="text-align: right;">Precio</th>
        								<th scope="col" style="text-align: right;">Total</th>
        								<th scope="col">Estado</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						      $item = 1;
        						      foreach ($lstDetallePedido as $objDetallePedido) {
                                ?>
        							<tr>
        								<td style="text-align: left;"><?php echo $item++; ?></td>
            							<td style="text-align: left;"><?php echo date("d/m/Y H:i", strtotime($objDetallePedido->fecha_creacion)); ?></td>
            							<td style="text-align: left;"><?php echo date("d/m/Y H:i", strtotime($objDetallePedido->fecha_actualizacion)); ?></td>
            							<td style="text-align: left;"><?php echo str_pad($objDetallePedido->id, 8, "0", STR_PAD_LEFT); ?></td>
            							<td style="text-align: left;"><?php echo $objDetallePedido->producto; ?></td>
            							<td style="text-align: right;"><?php echo number_format($objDetallePedido->cantidad, 2); ?>
            							<td style="text-align: right;"><?php echo number_format($objDetallePedido->precio_venta, 2); ?>
            							<td style="text-align: right;"><?php echo number_format($objDetallePedido->total, 2); ?>
            							<td style="text-align: left;"><?php echo $objDetallePedido->estado; ?></td>
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