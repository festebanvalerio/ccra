<?php
    $sede = $_SESSION["sede"];
    $empresa = $_SESSION["empresa"];
    $id = $almacen = $nomAlmacen = $observacion = "";
    $fecha = date("d-m-Y");
    $soloLectura = "";
    $texto = "Registrar Ajuste de Inventario";
    $textoBoton = "Registrar";
    $lstDetalleAjuste = NULL;
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
        $objAjuste = AjusteData::getById($id);
        $observacion = $objAjuste->observacion;
        
        $soloLectura = "disabled";        
        $texto = "Detalle Ajuste de Inventario";
        
        $lstDetalleAjuste = DetalleAjusteData::getAllByAjuste($id);
    }
    $lstInsumo = InsumoData::getAll(1, $sede, 1);
    $lstAlmacen = AlmacenData::getAll(1, $empresa, $sede);
    if (count($lstAlmacen) > 0) {
        $almacen = $lstAlmacen[0]->id;
        $nomAlmacen = $lstAlmacen[0]->nombre;
    }
    
    unset($_SESSION["insumos"]);
    unset($_SESSION["tmp_insumos"]);
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newadjustment" action="index.php?action=addadjustment" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>				
        		<div class="panel-body">
        			<fieldset class="scheduler-border">
    					<legend class="scheduler-border">Datos Generales</legend>
                		<div class="form-group">
            				<div class="col-md-2 col-sm-12">
            					<label for="fecha">Fecha :</label>
        						<input type="text" id="fecha" name="fecha" class="form-control" value="<?php echo $fecha; ?>" disabled/>
            				</div>
            				<div class="col-md-4 col-sm-12">
            					<label for="nomalmacen">Almacen :</label>
        						<input type="text" id="nomalmacen" name="nomalmacen" class="form-control" value="<?php echo $nomAlmacen; ?>" disabled/>
            				</div>
            				<div class="col-md-6 col-sm-12">
            					<label for="observacion">Observación :</label>
        						<input type="text" id="observacion" name="observacion" class="form-control" value="<?php echo $observacion; ?>" maxlength="255" <?php echo $soloLectura; ?>/>
            				</div>
            			</div>
            		</fieldset>
                	<fieldset class="scheduler-border">
    					<legend class="scheduler-border">Datos del Insumo</legend>
                		<div class="form-group">
            				<div class="col-md-4 col-sm-12">
            					<label for="insumo">Insumo :</label>
            					<select id="insumo" name="insumo" class="form-control" <?php echo $soloLectura; ?>>
            						<option value="">SELECCIONE</option>
            						<?php foreach ($lstInsumo as $objInsumo) { ?>
            						<option value="<?php echo $objInsumo->id; ?>"><?php echo $objInsumo->nombre; ?></option>
              						<?php } ?>
              					</select>
              					<script type="text/javascript">
    								$("#insumo").change(function(){
    									var almacen = <?php echo $almacen; ?>;
    									var insumo = $("#insumo").val();
    									if (insumo !== "") {
        									$.blockUI();
        									$.post("./?action=listadjustment", {
            									opcion: 0,
                                                insumo: insumo,
                                                almacen: almacen
                                            }, function (data) {
                                            	if (data !== "") {
                                                    var resultado = data.split("|");
                                                    $("#idInsumo").val(resultado[0]);
            										$("#unidad").val(resultado[1]);
            										$("#precio").val(resultado[2]);
            										$("#stockactual").val(resultado[3]);
            										document.getElementById("cantidad").focus();
            									} else {
            										$("#insumo").val(null).trigger("change");
            										Swal.fire({
                		    							icon: "warning",
                		    							title: "El insumo seleccionado no tiene stock en el almacen <?php echo $nomAlmacen; ?>"
                		    						})
            									}
                                            	$.unblockUI();
                                            });
    									} else {
    										$("#unidad").val("");
    										$("#precio").val("");
    										$("#idInsumo").val("");
    										$("#stockactual").val("");
    										$("#cantidad").val("");
    									}
    								})
              					</script>
            				</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="unidad">Unidad :</label>
                				<input type="text" id="unidad" name="unidad" class="form-control" value="" disabled/>
                				<input type="hidden" id="precio" name="precio" class="form-control" value=""/>
                				<input type="hidden" id="idInsumo" name="idInsumo" class="form-control" value=""/>
                			</div>
                			<div class="col-md-2 col-sm-12">
                				<label for="stockactual">Stock Actual :</label>
                				<input type="text" id="stockactual" name="stockactual" class="form-control" placeholder="0.00" value="" disabled/>
                			</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="cantidad">Cantidad :</label>
                				<input type="text" id="cantidad" name="cantidad" class="form-control" placeholder="0.00" value="" onkeypress="return filterFloat(event,this);" maxlength="5" <?php echo $soloLectura; ?>/>
                				<script type="text/javascript">
            						$("#cantidad").blur(function(){
            							var cantidad = $("#cantidad").val();
            							if (cantidad !== "") {
            								if (isNaN(cantidad)) {
                								$("#cantidad").val("");
                								document.getElementById("cantidad").focus();
                								Swal.fire({
                		    						icon: "warning",
                		    						title: "Sólo valores numéricos (cantidad)"
                		    					})
                		    				}
            							}
            						});
            					</script>
                			</div>
                			<div class="col-md-2 col-sm-12" style="padding-top: 25px;">
            					<button type="button" id="btnAgregar" class="btn btn-success" <?php echo $soloLectura; ?> title="Agregar"><em class="fa fa-plus"></em></button>
            					<script type="text/javascript">
    								$("#btnAgregar").click(function(){
        								var almacen = <?php echo $almacen; ?>;
    									var insumo = $("#idInsumo").val();
    									var cantidad = $("#cantidad").val();
    									var validaciones = false;
    									if (insumo === "") {
    										document.getElementById("insumo").focus();
    										Swal.fire({
        		    							icon: "warning",
        		    							title: "Campo obligatorio (insumo)"
        		    						})
    									} else if (cantidad === "") {
    										document.getElementById("cantidad").focus();
    										Swal.fire({
        		    							icon: "warning",
        		    							title: "Campo obligatorio (cantidad)"
        		    						})
    									} else if (isNaN(cantidad)) {
    										$("#cantidad").val("");
    										document.getElementById("cantidad").focus();
    										Swal.fire({
    	    									icon: "warning",
    	    									title: "Sólo valores numéricos (cantidad)"
    	    								})
    									} else {
    										validaciones = true;
    									}
    									if (validaciones) {
    										$("#btnAgregar").attr("disabled", "disabled");
    										$.blockUI();
    										$.post("./?action=listadjustment", {
    											opcion: 1,
    											almacen: almacen,
                                                insumo: insumo,
                                                cantidad: cantidad,
                                                indicador: 1
                                            }, function (data) {
                                            	$("#tabla").html(data);
                                            	$("#insumo").val(null).trigger("change");
                                            	$("#unidad").val("");
        										$("#precio").val("");
        										$("#idInsumo").val("");
        										$("#stockactual").val("");
        										$("#cantidad").val("");
        										document.getElementById("insumo").focus();
        										$("#btnAgregar").removeAttr("disabled");
            									$.unblockUI();
                                            });
    									}
    								});
            					</script>
            				</div>
            			</div>
            			<div class="form-group">
            				<div class="col-md-12 col-sm-12">
      							<div class="panel panel-primary" id="test2Pane2">
        							<div class="panel-heading">
        								<strong>Listado de Insumos</strong>
          								<a data-target="#panel2Content" data-parent="#test2Panel" data-toggle="collapse"><span class="pull-right"><i class="panel2Icon fa fa-arrow-up"></i></span></a>
        							</div>
        							<div class="panel-collapse collapse in" id="panel2Content">
          								<div class="panel-body">
                                        	<div class="table-responsive-md table-responsive" id="tabla">
                                        	<table class="table table-hover">
                                                <thead>
                                                    <tr class="btn-primary">
                                                        <th scope="col">Item</th>
                                                        <th scope="col">Insumo</th>
                                                        <th scope="col">Unidad</th>
                                                        <th scope="col">Tipo</th>
                                                        <th scope="col" style="text-align: right;">Stock</th>
                                                        <th scope="col" style="text-align: right;">Ajustar A</th>
                                                        <th scope="col" style="text-align: right;">Diferencia</th>
                                                    </tr>
                                                </thead>
                                                <?php                                                    
                                                    if (count($lstDetalleAjuste) > 0) {
                                                ?>
                                                <tbody>
                                                <?php
                                                        $item = 1;
                                                        foreach ($lstDetalleAjuste as $objDetalleAjuste) {
                                                            $objInsumo = $objDetalleAjuste->getInsumo();
                                                            $objUnidad = $objInsumo->getUnidad();
                                                            
                                                            $diferencia = 0;
                                                            $tipo = "";
                                                            if ($objDetalleAjuste->tipo == 0) {
                                                                $tipo = "SALIDA";
                                                                if ($objDetalleAjuste->cantidad >= $objDetalleAjuste->stock_actual) {
                                                                    $diferencia = $objDetalleAjuste->cantidad - $objDetalleAjuste->stock_actual;
                                                                } else {
                                                                    $diferencia = $objDetalleAjuste->stock_actual - $objDetalleAjuste->cantidad;
                                                                }
                                                            } else if ($objDetalleAjuste->tipo == 1) {
                                                                $tipo = "ENTRADA";
                                                                if ($objDetalleAjuste->stock_actual >= $objDetalleAjuste->cantidad) {
                                                                    $diferencia = $objDetalleAjuste->stock_actual - $objDetalleAjuste->cantidad;
                                                                } else {
                                                                    $diferencia = $objDetalleAjuste->cantidad - $objDetalleAjuste->stock_actual;
                                                                }
                                                            }
                                                ?>
                                                	<tr>
                                                		<td style="text-align: left;"><?php echo $item++; ?></td>
                                                        <td style="text-align: left;"><?php echo $objInsumo->nombre; ?></td>
                                                        <td style="text-align: left;"><?php echo $objUnidad->abreviatura; ?></td>
                                                        <td style="text-align: left;"><?php echo $tipo; ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objDetalleAjuste->stock_actual, 2); ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objDetalleAjuste->cantidad, 2); ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($diferencia, 2); ?></td>
                                                	</tr>
                                                <?php 
                                                        }
                                                ?>
                                                </tbody>
                                                <?php        
                                                    }
                                                ?>
                                           	</table>
                                      		</div>
                                      	</div>
                                  	</div>
                             	</div>
                             </div>
                        </div>
                  	</fieldset>          	
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
        					<button type="submit" id="btnRegistrar" class="btn btn-success" <?php echo $soloLectura; ?>><?php echo $textoBoton; ?></button>
        					<button type="button" id="btnCancelar" class="btn btn-danger">Cancelar</button>
        					<input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>
        					<input type="hidden" id="almacen" name="almacen" value="<?php echo $almacen; ?>"/>
        					<input type="hidden" id="sede" name="sede" value="<?php echo $sede; ?>"/>
        					<input type="hidden" id="accion" name="accion" value="1"/>
        				</div>
        			</div>
        		</div>
        	</div>
    		</form>
    		<script type="text/javascript">
    			$("#btnCancelar").click(function(){
    				$.blockUI();
    				$("button").prop("disabled", true);
    				location.href = "./index.php?view=adjustments";
    			});
    			$(function(){
    				document.getElementById("observacion").focus();
        			$("#insumo").select2();
        			$("#panel2Content").on("shown.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel2Content").on("hidden.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});        			
    				$("#newadjustment").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addadjustment",
    		    				dataType: "html",
    		    				data: $("#newadjustment").serialize(),
    		    				beforeSend: function() {
    		    					$("#btnRegistrar").attr("disabled", "disabled");
    		    					$("#btnCancelar").attr("disabled", "disabled");
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se registró correctamente el ajuste de inventario",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    										window.location.href = "./index.php?view=adjustments";
    		                        	})        					
    		        				} else if (data < 0) {
    		        					document.getElementById("insumo").focus();
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Agregar al menos un insumo"
    		    						})
    		        				} else {
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Ocurrio un error al registrar el ajuste de inventario"
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al registrar el ajuste de inventario"
    								})
    		    				},
    		    				complete: function(data) {
    		    					$("#btnRegistrar").removeAttr("disabled");
    		    					$("#btnCancelar").removeAttr("disabled");
    		    					$.unblockUI();
    		    				}
    		    			});
    		    		}
    		        });
    			});    		
    		</script>
    	</div>
    </div>
</section>