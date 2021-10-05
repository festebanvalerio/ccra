<?php
    $id = $sede = 0; 
    $producto = $descripcion = ""; 
    $costo = 0.00;
    $soloLectura = "";
    $texto = "Registrar Receta";
    $textoBoton = "Registrar";
    $msgOk = "registró";
    $msgError = "registrar";
    $lstDetalleReceta = array();
    $sede = $_SESSION["sede"];
    unset($_SESSION["insumos"]);
    unset($_SESSION["tmp_insumos"]);    
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
        $objReceta = RecetaData::getById($id);
        $sede = $objReceta->sede;        
        $producto = $objReceta->getProducto()->nombre;        
        $costo = $objReceta->costo;
        $descripcion = $objReceta->descripcion;
        
        if ($objReceta->estado != 1) {
            $soloLectura = "disabled";
        }
        
        $texto = "Editar Receta";
        $textoBoton = "Actualizar";
        $msgOk = "actualizó";
        $msgError = "actualizar";
        
        $lstDetalleReceta = DetalleRecetaData::getAllByReceta($id);
        $_SESSION["insumos"] = array();
        foreach ($lstDetalleReceta as $objDetalleReceta) {
            $_SESSION["insumos"][] = $objDetalleReceta->insumo."|".$objDetalleReceta->cantidad."|".$objDetalleReceta->precio."|".$objDetalleReceta->id;
        }
    }
    $lstProducto = ProductoData::getAllSinReceta($sede);
    $lstInsumo = InsumoData::getAll(1, $sede);
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newrecipe" action="index.php?action=addrecipe" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>				
        		<div class="panel-body">
        			<fieldset class="scheduler-border">
    					<legend class="scheduler-border">Datos Generales</legend>
            			<div class="form-group">
            				<div class="col-md-4 col-sm-12">
            					<?php if (!isset($_GET["id"])) { ?>
            					<label for="producto">Producto :*</label>
            					<select id="producto" name="producto" class="form-control" required>
            						<option value="">SELECCIONE</option>
                					<?php foreach ($lstProducto as $objProducto) { ?>
            						<option value="<?php echo $objProducto->id; ?>" <?php if ($objProducto->id == $producto) { echo "selected"; } ?>><?php echo $objProducto->nombre; ?></option>
              						<?php } ?>
              					</select>
              					<script type="text/javascript">
                					$("#producto").change(function(){
                						var producto = $("#producto").val();
                						if (producto !== "") {
                							document.getElementById("costo").focus();
                							$("#costo").val("");
                						}
                					});
                				</script>
              					<?php } else { ?>
              					<label for="producto">Producto :</label>
              					<input type="text" id="nomproducto" name="nomproducto" class="form-control" value="<?php echo $producto; ?>" readonly/>
              					<?php } ?>
            				</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="costo">Costo :*</label>
                				<input type="text" id="costo" name="costo" class="form-control" placeholder="0.00" value="<?php echo number_format($costo, 2); ?>" required onkeypress="return filterFloat(event,this);" maxlength="8"/>
                			</div>
                			<div class="col-md-6 col-sm-12">
            					<label for="descripcion">Descripción :</label>
                				<input type="text" id="descripcion" name="descripcion" class="form-control" placeholder="Descripción" value="<?php echo $descripcion; ?>" maxlength="255"/>
                			</div>
                		</div>
                	</fieldset>
                	<fieldset class="scheduler-border">
    					<legend class="scheduler-border">Datos del Insumo</legend>
                		<div class="form-group">
            				<div class="col-md-4 col-sm-12">
            					<label for="insumo">Insumo :</label>
            					<select id="insumo" name="insumo" class="form-control">
            						<option value="">SELECCIONE</option>
            						<?php foreach ($lstInsumo as $objInsumo) { ?>
            						<option value="<?php echo $objInsumo->id; ?>"><?php echo $objInsumo->nombre; ?></option>
              						<?php } ?>
              					</select>
              					<script type="text/javascript">
    								$("#insumo").change(function(){
    									var insumo = $("#insumo").val();
    									if (insumo !== "") {									
        									$.blockUI();
        									$.post("./?action=listinput", {
            									opcion: 0,
                                                insumo: insumo,
                                            }, function (data) {
                                                var resultado = data.split("|");
                                                $("#idInsumo").val(resultado[0]);
        										$("#unidad").val(resultado[1]);
        										$("#precio").val(resultado[2]);
        										document.getElementById("cantidad").focus();
                                            	$.unblockUI();
                                            });
    									} else {
    										$("#unidad").val("");
    										$("#precio").val("");
    										$("#idInsumo").val("");
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
            					<label for="cantidad">Cantidad :</label>
                				<input type="text" id="cantidad" name="cantidad" class="form-control" value="" onkeypress="return filterFloat(event,this);" maxlength="5"/>
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
            					<button type="button" id="btnAgregar" class="btn btn-success" title="Agregar"><em class="fa fa-plus"></em></button>
            					<script type="text/javascript">
    								$("#btnAgregar").click(function(){
    									var insumo = $("#idInsumo").val();
    									var cantidad = $("#cantidad").val();
    									var precio = $("#precio").val();
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
    										$("#btnAgregar").attr("disabled","disabled");
    										$.blockUI();
    										$.post("./?action=listinput", {
    											opcion: 1,
                                                insumo: insumo,
                                                cantidad: cantidad,
                                                precio: precio,
                                                indicador: 1,
                                                receta: <?php echo $id; ?>
                                            }, function (data) {
                                            	$("#tabla").html(data);
                                            	$("#insumo").val(null).trigger("change");
                                            	$("#unidad").val("");
            									$("#cantidad").val("");
            									$("#idInsumo").val("");
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
                                                        <th scope="col" style="text-align: right;">Cantidad</th>
                                                        <th scope="col" style="text-align: right;">Precio</th>                                                    
                                                        <th scope="col" style="text-align: right;">Total</th>
                                                        <th scope="col" style="text-align: center;">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <?php
                                                    $totalGeneral = $totalCantidad = 0;
                                                    if (count($lstDetalleReceta) > 0) {
                                                ?>
                                                <tbody>
                                                <?php
                                                        $item = 1;
                                                        foreach ($lstDetalleReceta as $objDetalleReceta) {
                                                            $objInsumo = $objDetalleReceta->getInsumo();
                                                            $objUnidad = $objInsumo->getUnidad();
                                                            $total = $objDetalleReceta->cantidad * $objDetalleReceta->precio;
                                                            $totalCantidad += $objDetalleReceta->cantidad;
                                                            $totalGeneral += $total;
                                                ?>
                                                	<tr>
                                                		<td style="text-align: left;"><?php echo $item++; ?></td>
                                                        <td style="text-align: left;"><?php echo $objInsumo->nombre; ?></td>
                                                        <td style="text-align: left;"><?php echo $objUnidad->abreviatura; ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objDetalleReceta->cantidad, 2); ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objDetalleReceta->precio, 2); ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($total, 2); ?></td>
                                                        <td style="text-align: center;">
                                                        	<a id="lnkdel<?php echo $objDetalleReceta->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
                            								<script type="text/javascript">
                                								$("#lnkdel<?php echo $objDetalleReceta->id; ?>").click(function() {
                                        							Swal.fire({
                                            							title: "Desea anular el insumo <?php echo $objInsumo->nombre; ?> (Receta: <?php echo $producto; ?>)",
                                        								icon: "warning",
                                        								showCancelButton: true,
                                        								confirmButtonColor: "#3085d6",
                                        								cancelButtonColor: "#d33",
                                        								confirmButtonText: "Anular",
                                        								cancelButtonText: "Cancelar"
                                        							}).then((result) => {
                                        								if (result.isConfirmed) {
                                        									$.ajax({
                                        		    		    				type: "POST",
                                        		    		    				url: "./index.php?action=addrecipe",
                                        		    		    				dataType: "html",
                                        		    		    				data: "id=<?php echo $objDetalleReceta->id; ?>&accion=3",
                                        		    		    				beforeSend: function() {
                                        		    		    					$("#lnkdel<?php echo $objDetalleReceta->id; ?>").attr("disabled","disabled");
                                        		    		    					$.blockUI();
                                        		    		    				},
                                        		    		    				success: function(data) {
                                        		    		        				if (data > 0) {
                                        		    		        					Swal.fire({
                                        		    		                                icon: "success",
                                        		    		                                title: "Se anuló correctamente el insumo <?php echo $objInsumo->nombre; ?> (Receta: <?php echo $producto; ?>)",
                                        		    										showCancelButton: false,
                                        		    										confirmButtonColor: "#3085d6",
                                        		    										confirmButtonText: "OK"
                                        		    		                        	})
                                        		    		        				} else {
                                        		    		        					Swal.fire({
                                        		    		    							icon: "warning",
                                        		    		    							title: "Ocurrio un error al anular el insumo <?php echo $objInsumo->nombre; ?> (Receta: <?php echo $producto; ?>)"
                                        		    		    						})
                                        		    		        				}
                                        		    		    				},
                                        		    		    				error: function(data) {
                                        		    		    					Swal.fire({
                                        		    									icon: "error",
                                        		    									title: "Ocurrio un error al anular el insumo <?php echo $objInsumo->nombre; ?> (Receta: <?php echo $producto; ?>)"
                                        		    								})
                                        		    		    				},
                                        		    		    				complete: function(data) {
                                        		    		    					$("#lnkdel<?php echo $objDetalleReceta->id; ?>").removeAttr("disabled");
                                        		    		    					$.post("./?action=listinput", {
                                            											opcion: 1,
                                                                                        insumo: 0,
                                                                                    }, function (data) {
                                                                                    	$("#tabla").html(data);                                                                                    	
                                                                                    });
                                        		    		    					$.unblockUI();
                                        		    		    				}
                                        		    		    			});
                                        								}
                                        							})
                                        						});
                                        					</script>
                                        				</td>
                                                	</tr>
                                                <?php 
                                                        }
                                                ?>
                                                </tbody>
                                                <tfoot>
                                                	<tr>
                                                        <td colspan="3" style="text-align: left;"><strong>TOTAL</strong></td>
                                                        <td style="text-align: right;"><strong><?php echo number_format($totalCantidad,2); ?></strong></td>
                                                        <td></td>
                                                        <td id="totalReceta" style="text-align: right;"><strong><?php echo number_format($totalGeneral,2); ?></strong></td>
                                                        <td></td>
                                                    </tr>
                                             	</tfoot>
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
    				location.href = "./index.php?view=recipes";
    			});
    			$(function(){
        			$("#producto").select2();
        			$("#insumo").select2();
        			<?php if (!isset($_GET["id"])) { ?>
        			document.getElementById("producto").focus();
        			<?php } else { ?>
        			document.getElementById("insumo").focus();
        			<?php } ?>
        			$("#panel2Content").on("shown.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel2Content").on("hidden.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});
    				$("#newrecipe").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addrecipe",
    		    				dataType: "html",
    		    				data: $("#newrecipe").serialize(),
    		    				beforeSend: function() {
    		    					$("#btnCancelar").attr("disabled", "disabled");
    		    					$("#btnRegistrar").attr("disabled", "disabled");
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		    					<?php if (!isset($_GET["id"])) { ?>
    		    					document.getElementById("producto").focus();
                                    <?php } else { ?>
        							document.getElementById("insumo").focus();
        			                <?php } ?>
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se <?php echo $msgOk; ?> correctamente la receta",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    										window.location.href = "./index.php?view=recipes";
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
    		    							title: "Ocurrio un error al <?php echo $msgError; ?> la receta"																	
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					<?php if (!isset($_GET["id"])) { ?>
    		    					document.getElementById("producto").focus();
                                    <?php } else { ?>
                                    document.getElementById("insumo").focus();
                                    <?php } ?>
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al <?php echo $msgError; ?> la receta"
    								})
    		    				},
    		    				complete: function(data) {
    		    					$("#btnCancelar").removeAttr("disabled");
    		    					$("#btnRegistrar").removeAttr("disabled");
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