<?php
    $estado = "1";
    $fechaInicio = $fechaFin = date("d/m/Y");
    if (count($_POST) > 0) {
        $estado = $_POST["estado"];
        $fechaInicio = $_POST["fechai"];
        $fechaFin = $_POST["fechaf"];
        
        $_SESSION["adjustments_estado"] = $estado;
        $_SESSION["adjustments_fechai"] = $fechaInicio;
        $_SESSION["adjustments_fechaf"] = $fechaFin;
    } else {
        if (isset($_SESSION["adjustments_estado"]) && isset($_SESSION["adjustments_fechai"]) && isset($_SESSION["adjustments_fechaf"])) {
            $estado = $_SESSION["adjustments_estado"];
            $fechaInicio = $_SESSION["adjustments_fechai"];
            $fechaFin = $_SESSION["adjustments_fechaf"];
        }
    }
    $lstAlmacen = AlmacenData::getAll(1, $_SESSION["empresa"], $_SESSION["sede"]);
    $objAlmacen = $lstAlmacen[0];
    
    $lstEstado = EstadoData::getAll();
    $lstAjuste = AjusteData::getAll($estado, $objAlmacen->id, $fechaInicio, $fechaFin);
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
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Ajustes de Inventario</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="adjustments" action="" role="form" autocomplete="off">    			
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
        						<script type="text/javascript">
        							$("#btnBuscar").click(function(){
            							$.blockUI();
            		    				$("button").prop("disabled", true);
            		    				$("#adjustments").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            							$.post("index.php?action=deletesearch", {
                							opcion: "adjustments"
                                        }, function (data) {
                                        	location.href = "./index.php?view=adjustments";
                                        });
            		    			});
            		    			$("#btnNuevo").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            							location.href = "./index.php?view=newadjustment";
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
        								<th scope="col">Almacen</th>
        								<th scope="col">Estado</th>
        								<th scope="col">Acciones</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						    foreach ($lstAjuste as $objAjuste) {
        						        $codigo = str_pad($objAjuste->id, 8, "0", STR_PAD_LEFT);
        						        $objEstado = $objAjuste->getEstado();
        						        $objAlmacen = $objAjuste->getAlmacen();
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo $codigo; ?></td>
            							<td style="text-align: left;"><?php echo date("d-m-Y H:i", strtotime($objAjuste->fecha)); ?></td>
            							<td style="text-align: left;"><?php echo $objAlmacen->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>
            							<td style="text-align: left;">
            								<?php if ($objEstado->opcion == 1) { ?>
        									<a href="index.php?view=newadjustment&id=<?php echo $objAjuste->id; ?>" title="Detalle" class="btn btn-success btn-xs"><em class="fa fa-search-plus"></em></a>
            								<a id="lnkdel<?php echo $objAjuste->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
            								<script type="text/javascript">
                								$("#lnkdel<?php echo $objAjuste->id; ?>").click(function() {
                        							Swal.fire({
                            							title: "Desea anular el ajuste de inventario <?php echo $codigo; ?>",
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
                        									    url: "./?action=addadjustment",
                        									    data: "id="+<?php echo $objAjuste->id; ?>+"&accion=2",
                        									    dataType: "html",
                        									    beforeSend: function() {
                        		    		    					$("#lnkdel<?php echo $objAjuste->id; ?>").attr("disabled","disabled");
                        		    		    					$.blockUI();
                        		    		    				},
                        									    success: function(data) {
                        									        if (data > 0) {
                        									        	Swal.fire({
                                    		                                icon: "success",
                                    		                                title: "Se anuló correctamente el ajuste de inventario <?php echo $codigo; ?>",
                                    										showCancelButton: false,
                                    										confirmButtonColor: "#3085d6",
                                    										confirmButtonText: "OK"
                                    		                        	}).then((result) => {
                                    										window.location.href = "./index.php?view=adjustments";
                                    		                        	})
                        									        } else {
                        									        	Swal.fire({
                        		    		    							icon: "warning",
                        		    		    							title: "Ocurrio un error al anular el ajuste de inventario <?php echo $codigo; ?>"
                        		    		    						})
                        										    }
                        									    },
                        									    error: function() {
                        									    	Swal.fire({
                        	    		    							icon: "error",
                        	    		    							title: "Ocurrio un error al anular el ajuste de inventario <?php echo $codigo; ?>"
                        	    		    						})
                        									    },
                        									    complete: function(data) {
                        		    		    					$("#lnkdel<?php echo $objAjuste->id; ?>").removeAttr("disabled");
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