<?php
    $empresa = $_SESSION["empresa"];
    $sede = $_SESSION["sede"];
    $estado = "1";
    $fechaInicio = date("d/m/Y");
    $fechaFin = date("d/m/Y");
    if (count($_POST) > 0) {
        $estado = $_POST["estado"];
        $fechaInicio = $_POST["fechai"];
        $fechaFin = $_POST["fechaf"];
        
        $_SESSION["entrances_estado"] = $estado;
        $_SESSION["entrances_fechai"] = $fechaInicio;
        $_SESSION["entrances_fechaf"] = $fechaFin;
    } else {
        if (isset($_SESSION["entrances_estado"]) && isset($_SESSION["entrances_fechai"]) && isset($_SESSION["entrances_fechaf"])) {
            $estado = $_SESSION["entrances_estado"];
            $fechaInicio = $_SESSION["entrances_fechai"];
            $fechaFin = $_SESSION["entrances_fechaf"];
        }
    }
    $lstAlmacen = AlmacenData::getAll(1, $empresa, $sede);
    $objAlmacen = $lstAlmacen[0];
    
    $lstEstado = EstadoData::getAll();    
    $lstIngreso = IngresoData::getAll($estado, $objAlmacen->id, $fechaInicio, $fechaFin);    
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
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Ingresos</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="entrances" action="" role="form" autocomplete="off">    			
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
        						<button type="button" id="btnNuevo" class="btn btn-primary" title="Nuevo"><em class="fa fa-pencil-square-o"></em></button>
        						<?php if (count($lstIngreso) > 0) { ?>
        						<a class="btn btn-success" id="btnExportar" href="excelIngreso.php?sede=<?php echo $sede; ?>&almacen=<?php echo $objAlmacen->id; ?>&fechainicio=<?php echo $fechaInicio; ?>&fechafin=<?php echo $fechaFin; ?>&estado=<?php echo $estado; ?>" role="button" title="Descargar" target="_blank"><em class="fa fa-download"></em></a>
        						<?php } ?>
        						<script type="text/javascript">
        							$("#btnBuscar").click(function(){
            							$.blockUI();
            		    				$("button").prop("disabled", true);
            		    				$("#entrances").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				$.post("index.php?action=deletesearch", {
                							opcion: "entrances"
                                        }, function (data) {
                                        	location.href = "./index.php?view=entrances";
                                        });
            		    			});
            		    			$("#btnNuevo").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=newentrance";
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
        								<th scope="col">Código</th>
        								<th scope="col">Fecha</th>
        								<th scope="col">Sede</th>
        								<th scope="col">Almacen</th>
        								<th scope="col">Estado</th>
        								<th scope="col">Acciones</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						    foreach ($lstIngreso as $objIngreso) {
        						        $codigo = str_pad($objIngreso->id, 8, "0", STR_PAD_LEFT);
        						        $objEstado = $objIngreso->getEstado();
        						        $objAlmacen = $objIngreso->getAlmacen();
        						        $objSede = $objIngreso->getSede();
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo $codigo; ?></td>
            							<td style="text-align: left;"><?php echo date("d/m/Y", strtotime($objIngreso->fecha)); ?></td>
            							<td style="text-align: left;"><?php echo $objSede->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objAlmacen->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>
            							<td style="text-align: left;">
            								<a href="index.php?view=newentrance&id=<?php echo $objIngreso->id; ?>" title="Detalle" class="btn btn-success btn-xs"><em class="fa fa-search-plus"></em></a>
            								<?php if ($objEstado->opcion == 1) { ?>
            								<a id="lnkdel<?php echo $objIngreso->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
            								<script type="text/javascript">
                								$("#lnkdel<?php echo $objIngreso->id; ?>").click(function() {
                        							Swal.fire({
                            							title: "Desea anular el ingreso <?php echo $codigo; ?>",
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
                        									    url: "./?action=addentrance",
                        									    data: "id=<?php echo $objIngreso->id; ?>&accion=2",
                        									    dataType: "html",
                        									    beforeSend: function() {
                        		    		    					$("#lnkdel<?php echo $objIngreso->id; ?>").attr("disabled","disabled");
                        		    		    					$.blockUI();
                        		    		    				},
                        									    success: function(data) {
                        									        if (data > 0) {
                        									        	Swal.fire({
                                    		                                icon: "success",
                                    		                                title: "Se anuló correctamente el ingreso <?php echo $codigo; ?>",
                                    										showCancelButton: false,
                                    										confirmButtonColor: "#3085d6",
                                    										confirmButtonText: "OK"
                                    		                        	}).then((result) => {
                                    										window.location.href = "./index.php?view=entrances";
                                    		                        	})
                        									        } else {
                        									        	Swal.fire({
                        		    		    							icon: "warning",
                        		    		    							title: "Ocurrio un error al anular el ingreso <?php echo $codigo; ?>"
                        		    		    						})
                        										    }
                        									    },
                        									    error: function() {
                        									    	Swal.fire({
                        	    		    							icon: "error",
                        	    		    							title: "Ocurrio un error al anular el ingreso <?php echo $codigo; ?>"
                        	    		    						})
                        									    },
                        		    		    				complete: function(data) {
                        		    		    					$("#lnkdel<?php echo $objIngreso->id; ?>").removeAttr("disabled");
                        		    		    					$.unblockUI();
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