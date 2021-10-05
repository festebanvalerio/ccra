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
        
        $_SESSION["ocs_estado"] = $estado;
        $_SESSION["ocs_fechai"] = $fechaInicio;
        $_SESSION["ocs_fechaf"] = $fechaFin;
    } else {
        if (isset($_SESSION["ocs_estado"]) && isset($_SESSION["ocs_fechai"]) && isset($_SESSION["ocs_fechaf"])) {
            $estado = $_SESSION["ocs_estado"];
            $fechaInicio = $_SESSION["ocs_fechai"];
            $fechaFin = $_SESSION["ocs_fechaf"];
        }
    }
    $lstAlmacen = AlmacenData::getAll(1, $empresa, $sede);
    $objAlmacen = $lstAlmacen[0];
    
    $lstEstado = EstadoData::getAll();
    $lstOrdenCompra = OrdenCompraData::getAll($estado, $objAlmacen->id, $fechaInicio, $fechaFin);
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
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Ordenes de Compra</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="ocs" action="" role="form" autocomplete="off">
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
        						<?php if (count($lstOrdenCompra) > 0) { ?>
        						<a class="btn btn-success" id="btnExportar" href="excelOrdenCompra.php?sede=<?php echo $sede; ?>&almacen=<?php echo $objAlmacen->id; ?>&fechainicio=<?php echo $fechaInicio; ?>&fechafin=<?php echo $fechaFin; ?>&estado=<?php echo $estado; ?>" role="button" title="Descargar" target="_blank"><em class="fa fa-download"></em></a>
        						<?php } ?>
        						<script type="text/javascript">
        							$("#btnBuscar").click(function(){
            							$.blockUI();
            		    				$("button").prop("disabled", true);
            		    				$("#ocs").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				$.post("index.php?action=deletesearch", {
                							opcion: "ocs"
                                        }, function (data) {
                                        	location.href = "./index.php?view=ocs";
                                        });
            		    			});
            		    			$("#btnNuevo").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=newoc";
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
        								<th scope="col">Tipo Documento</th>
        								<th scope="col">Num. Documento</th>
        								<th scope="col" style="text-align: right;">Monto</th>
        								<th scope="col">Estado</th>
        								<th scope="col">Acciones</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						    foreach ($lstOrdenCompra as $objOrdenCompra) {
        						        $codigo = str_pad($objOrdenCompra->id, 8, "0", STR_PAD_LEFT);
        						        $objEstado = $objOrdenCompra->getEstado();
        						        $objAlmacen = $objOrdenCompra->getAlmacen();
        						        $objSede = $objOrdenCompra->getSede();
        						        $objTipoDocumento = $objOrdenCompra->getTipoDocumento();
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo $codigo; ?></td>
            							<td style="text-align: left;"><?php echo date("d/m/Y", strtotime($objOrdenCompra->fecha)); ?></td>
            							<td style="text-align: left;"><?php echo $objSede->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objAlmacen->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objTipoDocumento->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objOrdenCompra->num_documento; ?></td>
            							<td style="text-align: right;"><?php echo number_format($objOrdenCompra->monto, 2); ?></td>
            							<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>
            							<td style="text-align: left;">
            								<a href="index.php?view=newoc&id=<?php echo $objOrdenCompra->id; ?>" title="Detalle" class="btn btn-success btn-xs"><em class="fa fa-search-plus"></em></a>
            								<?php if ($objEstado->opcion == 1) { ?>        									
            								<a id="lnkdel<?php echo $objOrdenCompra->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
            								<script type="text/javascript">
                								$("#lnkdel<?php echo $objOrdenCompra->id; ?>").click(function() {
                        							Swal.fire({
                            							title: "Desea anular la orden de compra <?php echo $codigo; ?>",
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
                        									    url: "./?action=addoc",
                        									    data: "id=<?php echo $objOrdenCompra->id; ?>&accion=2",
                        									    dataType: "html",
                        									    beforeSend: function() {
                        		    		    					$("#lnkdel<?php echo $objOrdenCompra->id; ?>").attr("disabled","disabled");
                        		    		    					$.blockUI();
                        		    		    				},
                        									    success: function(data) {
                        									        if (data > 0) {
                        									        	Swal.fire({
                                    		                                icon: "success",
                                    		                                title: "Se anuló correctamente la orden de compra <?php echo $codigo; ?>",
                                    										showCancelButton: false,
                                    										confirmButtonColor: "#3085d6",
                                    										confirmButtonText: "OK"
                                    		                        	}).then((result) => {
                                    										window.location.href = "./index.php?view=ocs";
                                    		                        	})
                        									        } else {
                        									        	Swal.fire({
                        		    		    							icon: "warning",
                        		    		    							title: "Ocurrio un error al anular la orden de compra <?php echo $codigo; ?>"
                        		    		    						})
                        										    }
                        									    },
                        									    error: function() {
                        									    	Swal.fire({
                        	    		    							icon: "error",
                        	    		    							title: "Ocurrio un error al anular la orden de compra <?php echo $codigo; ?>"
                        	    		    						})
                        									    },
                        		    		    				complete: function(data) {
                        		    		    					$("#lnkdel<?php echo $objOrdenCompra->id; ?>").removeAttr("disabled");
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