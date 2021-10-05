<?php
    $fechaInicio = $fechaFin = date("d/m/Y");
    if (count($_POST) > 0) {
        $fechaInicio = $_POST["fechai"];
        $fechaFin = $_POST["fechaf"];
        
        $_SESSION["credits_fechai"] = $fechaInicio;
        $_SESSION["credits_fechaf"] = $fechaFin;
    } else {
        if (isset($_SESSION["credits_fechai"]) && isset($_SESSION["credits_fechaf"])) {
            $fechaInicio = $_SESSION["credits_fechai"];
            $fechaFin = $_SESSION["credits_fechaf"];
        }
    }
    $idCredito = 0;
    if (isset($_GET["id"])) {
        $idCredito = $_GET["id"];
    }
    $lstHistorialCredito = HistorialCreditoData::getByCredito($idCredito, $fechaInicio, $fechaFin);
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
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Detalle Pagos - Crédito</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="payments" action="" role="form" autocomplete="off">    			
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
                				<label for="fecha">Fecha Inicio:</label>
        						<input type="text" id="fechai" name="fechai" value="<?php echo $fechaInicio; ?>" class="form-control"/>
        					</div>
        					<div class="col-md-2 col-sm-12">
                				<label for="fecha">Fecha Fin:</label>
        						<input type="text" id="fechaf" name="fechaf" value="<?php echo $fechaFin; ?>" class="form-control"/>
        					</div>
            				<div class="col-md-8 col-sm-12" style="padding-top: 25px;">
        						<button type="submit" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>
        						<button type="button" id="btnLimpiar" class="btn btn-primary" title="Regresar"><em class="fa fa-share"></em></button>
        						<script type="text/javascript">
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
                						location.href = "./index.php?view=credits";                                        
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
        								<th scope="col">Fecha y Hora</th>
        								<th scope="col" style="text-align: right;">Monto Abonado</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
                                    $item = 1;        						  
        						    foreach ($lstHistorialCredito as $objHistorialCredito) {
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo $item++; ?></td>
            							<td style="text-align: left;"><?php echo date("d/m/Y H:i", strtotime($objHistorialCredito->fecha)); ?></td>
            							<td style="text-align: right;"><?php echo number_format($objHistorialCredito->monto, 2); ?>
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