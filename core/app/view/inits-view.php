<?php
    $caja = "";
    $estado = "1";
    $fechaInicio = $fechaFin = date("d/m/Y");
    if (count($_POST) > 0) {
        $caja = $_POST["caja"];
        $estado = $_POST["estado"];
        $fechaInicio = $_POST["fechai"];
        $fechaFin = $_POST["fechaf"];
        
        $_SESSION["inits_caja"] = $caja;
        $_SESSION["inits_estado"] = $estado;
        $_SESSION["inits_fechai"] = $fechaInicio;
        $_SESSION["inits_fechaf"] = $fechaFin;
    } else {
        if (isset($_SESSION["inits_caja"]) && isset($_SESSION["inits_estado"]) && isset($_SESSION["inits_fechai"]) &&
            isset($_SESSION["inits_fechaf"])) {
            $caja = $_SESSION["inits_caja"];
            $estado = $_SESSION["inits_estado"];
            $fechaInicio = $_SESSION["inits_fechai"];
            $fechaFin = $_SESSION["inits_fechaf"];
        }
    }
    $lstEstado = EstadoCajaData::getAll();    
    $lstHistorialCaja = HistorialCajaData::getAllHistorial($estado, $_SESSION["sede"], "", $fechaInicio, $fechaFin);
    $lstCaja = CajaData::getAll(1, $_SESSION["sede"]); 
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
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Historial Apertura/Cierre Caja</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="inits" action="" role="form" autocomplete="off">    			
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
                				<label for="fechai">Fecha Inicio:</label>
        						<input type="text" id="fechai" name="fechai" value="<?php echo $fechaInicio; ?>" class="form-control" readonly/>
        					</div>
            				<div class="col-md-2 col-sm-12">
                				<label for="fechaf">Fecha Fin:</label>
        						<input type="text" id="fechaf" name="fechaf" value="<?php echo $fechaFin; ?>" class="form-control" readonly/>
        					</div>        					
        					<div class="col-md-2 col-sm-12">
    							<label for="caja">Caja :</label>
    							<select id="caja" name="caja" class="form-control">
    								<option value="">TODOS</option>
    								<?php foreach ($lstCaja as $objCaja) { ?>
    								<option value="<?php echo $objCaja->id; ?>" <?php if ($objCaja->id == $caja) { echo "selected"; } ?>><?php echo $objCaja->nombre; ?></option>
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
            		    				$("#inits").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
                						$("button").prop("disabled", true);
            							$.post("index.php?action=deletesearch", {
                							opcion: "inits"                							
                                        }, function (data) {
                                        	location.href = "./index.php?view=inits";
                                        });
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
        								<th scope="col">Item</th>
        								<th scope="col">Sede</th>
        								<th scope="col">Piso</th>
        								<th scope="col">Caja</th>
        								<th scope="col">Fecha Apertura</th>        								
        								<th scope="col" style="text-align: right;">Monto Apertura</th>
        								<th scope="col">Fecha Cierre</th>
        								<th scope="col" style="text-align: right;">Monto Cierre</th>
        								<th scope="col">Estado</th>
        								<th scope="col">Acciones</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php        						    
        						    foreach ($lstHistorialCaja as $objHistorialCaja) {
        						        $objSede = $objHistorialCaja->getSede();
        						        $objCaja = $objHistorialCaja->getCaja();
        						        $objPiso = $objCaja->getPiso();
        						        $objEstado = $objHistorialCaja->getEstado();
        						        
        						        $fechaInicial = date("d/m/Y", strtotime($objHistorialCaja->fecha_apertura));
        						        $fechaApertura = date("d/m/Y H:i", strtotime($objHistorialCaja->fecha_apertura));
        						        $opcionCierre = $fechaCierre = "";
        						        if ($objEstado->id == 1) {
        						            $fechaCierre = "";
        						            if (date("d/m/Y", strtotime($objHistorialCaja->fecha_apertura)) != date("d/m/Y")) {
        						                $opcionCierre = 1;
                                            }
        						        } else if ($objEstado->id == 2) {
        						            $fechaCierre = date("d/m/Y H:i", strtotime($objHistorialCaja->fecha_cierre));
        						        }        						        
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo str_pad($objHistorialCaja->id, 8, "0", STR_PAD_LEFT); ?></td>
            							<td style="text-align: left;"><?php echo $objSede->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objPiso->nombre; ?></td>            							
            							<td style="text-align: left;"><?php echo $objCaja->nombre; ?></td>            							
            							<td style="text-align: left;"><?php echo $fechaApertura; ?></td>
            							<td style="text-align: right;"><?php echo number_format($objHistorialCaja->monto_apertura, 2); ?>
            							<td style="text-align: left;"><?php echo $fechaCierre; ?></td>
            							<td style="text-align: right;"><?php echo number_format($objHistorialCaja->monto_cierre, 2); ?>
            							<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>
            							<td style="text-align: left;">
            								<?php if ($objEstado->opcion == 2) { ?>
            								<a href="ticketCierre.php?id=<?php echo $objHistorialCaja->id; ?>&caja=<?php echo $objCaja->id; ?>&fecha=<?php echo date("Y-m-d", strtotime($objHistorialCaja->fecha_apertura)); ?>" title="Imprimir" class="btn btn-success btn-xs" target="_blank"><em class="fa fa-print"></em></a>
            								<?php } else if ($objEstado->opcion == 1) { ?>
            								<a href="ticketPreCierre.php?id=<?php echo $objHistorialCaja->id; ?>&caja=<?php echo $objCaja->id; ?>&fecha=<?php echo date("Y-m-d", strtotime($objHistorialCaja->fecha_apertura)); ?>" title="Imprimir" class="btn btn-success btn-xs" target="_blank"><em class="fa fa-print"></em></a>
            								<?php } ?>
            								<?php if ($objEstado->opcion == 1) { ?>
            								<?php if ($opcionCierre == 1) { ?>
            								<a href="index.php?view=initbox&id=<?php echo $objCaja->id; ?>&fecha=<?php echo $fechaInicial; ?>&opcion=1" title="Cierre" class="btn btn-success btn-xs"><em class="fa fa-times"></em></a>
            								<?php } ?>            								
        									<a id="lnkdel<?php echo $objCaja->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
            								<script type="text/javascript">
                								$("#lnkdel<?php echo $objCaja->id; ?>").click(function() {
                        							Swal.fire({
                            							title: "Desea anular la apertura del día <?php echo date("d/m/Y", strtotime($objHistorialCaja->fecha_apertura)); ?> (Caja : <?php echo $objCaja->nombre; ?> - Sede : <?php echo $objSede->nombre; ?>)",
                        								icon: "warning",
                        								showCancelButton: true,
                        								confirmButtonColor: "#3085d6",
                        								cancelButtonColor: "#d33",
                        								confirmButtonText: "Anular",
                            							cancelButtonText: "Cancelar"	
                        							}).then((result) => {
                        								if (result.isConfirmed) {
                        									$.ajax({
                        									    type: "post",
                        									    url: "./?action=addbox",
                        									    data: "id="+<?php echo $objHistorialCaja->id; ?>+"&accion=4",                        									    
                        									    dataType: "html",
                        									    success: function(data) {
                        									        if (data > 0) {
                        									        	Swal.fire({
                                    		                                icon: "success",
                                    		                                title: "Se anuló correctamente la apertura del día <?php echo date("d/m/Y", strtotime($objHistorialCaja->fecha_apertura)); ?> (Caja : <?php echo $objCaja->nombre; ?> - Sede : <?php echo $objSede->nombre; ?>)",
                                    										showCancelButton: false,
                                    										confirmButtonColor: "#3085d6",
                                    										confirmButtonText: "OK"
                                    		                        	}).then((result) => {
                                    		                        		window.location.href = "./index.php?view=inits";                                    										
                                    		                        	})
                        									        } else {
                        									        	Swal.fire({
                        		    		    							icon: "warning",
                        		    		    							title: "Ocurrio un error al anular la apertura del día <?php echo date("d/m/Y", strtotime($objHistorialCaja->fecha_apertura)); ?> (Caja : <?php echo $objCaja->nombre; ?> - Sede : <?php echo $objSede->nombre; ?>)"
                        		    		    						})
                        										    }
                        									    },
                        									    error: function() {
                        									    	Swal.fire({
                        	    		    							icon: "error",
                        	    		    							title: "Ocurrio un error al anular la apertura del día <?php echo date("d/m/Y", strtotime($objHistorialCaja->fecha_apertura)); ?> (Caja : <?php echo $objCaja->nombre; ?> - Sede : <?php echo $objSede->nombre; ?>)"
                        	    		    						})                        									        
                        									    }
                        									});
                        								}
                        							})
                        						});
                        					</script>
                        					<?php } ?>
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