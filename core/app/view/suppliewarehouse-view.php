<?php
    $sede = $_SESSION["sede"];
    $empresa = $_SESSION["empresa"];
    $idInsumo = $insumo = $unidad = $clasificacion = "";
    $tipo = $stock = $stockMinimo = $stockMaximo = 0;
    $texto = "Registrar Stock";
    $textoLabel = "Insumo";
    $textoMensaje = "insumo";
    $lstInsumoAlmacen = array();
    if (isset($_GET["id"])) {
        $idInsumo = $_GET["id"];
        
        $objInsumo = InsumoData::getById($idInsumo);
        $insumo = $objInsumo->nombre;
        $unidad = $objInsumo->getUnidad()->abreviatura;
        $clasificacion = $objInsumo->getClasificacion()->nombre;
        $tipo = $objInsumo->indicador;
        if ($tipo == 2) {
            $textoLabel = "Accesorio";
            $textoMensaje = "accesorio";
        }
        $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $idInsumo);
    }
    $lstAlmacen = AlmacenData::getAll(1, $empresa, $sede);
    
    unset($_SESSION["insumos_almacen"]);
    unset($_SESSION["tmp_insumos_almacen"]);
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="suppliewarehouse" action="index.php?action=addsupplies" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>				
        		<div class="panel-body">
        			<fieldset class="scheduler-border">
    					<legend class="scheduler-border">Datos Generales</legend>
            			<div class="form-group">
            				<div class="col-md-5 col-sm-12">
            					<label for="insumo"><?php echo $textoLabel; ?> :</label>
                				<input type="text" id="insumo" name="insumo" class="form-control" value="<?php echo $insumo; ?>" disabled/>
                			</div>
                			<div class="col-md-5 col-sm-12">
            					<label for="clasificacion">Clasificación :</label>
                				<input type="text" id="clasificacion" name="clasificacion" class="form-control" value="<?php echo $clasificacion; ?>" disabled/>
                			</div>
                			<div class="col-md-2 col-sm-12">
            					<label for="unidad">Unidad :</label>
                				<input type="text" id="unidad" name="unidad" class="form-control" value="<?php echo $unidad; ?>" disabled/>
                			</div>
                		</div>
                		<div class="form-group">
            				<div class="col-md-4 col-sm-12">
            					<label for="almacen">Almacen :*</label>
            					<select id="almacen" name="almacen" class="form-control">
            						<option value="">SELECCIONE</option>
                					<?php foreach ($lstAlmacen as $objAlmacen) { ?>
            						<option value="<?php echo $objAlmacen->id; ?>"><?php echo $objAlmacen->nombre; ?></option>
              						<?php } ?>
              					</select>
              					<script type="text/javascript">
									$("#almacen").change(function(){
										var almacen = $("#almacen").val();
										if (almacen !== "") {
											document.getElementById("stock").focus();
											$("#stock").val("");
										}
									});
              					</script>
            				</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="stock">Stock :*</label>
                				<input type="text" id="stock" name="stock" class="form-control" value="<?php echo number_format($stock, 2); ?>" onkeypress="return filterFloat(event,this);" maxlength="5" required/>
                				<script type="text/javascript">
                					$("#stock").click(function(){
										$("#stock").val("");
									});
									$("#stock").blur(function(){										
										var stock = $("#stock").val();
										if (stock === "") {
											$("#stock").val("0.00");
										} else if (isNaN(stock)) {
											$("#stock").val("0.00");
											document.getElementById("stock").focus();
											Swal.fire({
            	    							icon: "warning",
            	    							title: "Sólo valores numéricos (stock)"
            	    						})            	    						
										}
									});
              					</script>
                			</div>
                			<div class="col-md-2 col-sm-12">
            					<label for="stockmin">Stock Mínimo :*</label>
                				<input type="text" id="stockmin" name="stockmin" class="form-control" value="<?php echo number_format($stockMinimo, 2); ?>" onkeypress="return filterFloat(event,this);" maxlength="5"/>
                				<script type="text/javascript">
                					$("#stockmin").click(function(){
										$("#stockmin").val("");
									});
									$("#stockmin").blur(function(){										
										var stockmin = $("#stockmin").val();
										if (stockmin === "") {
											$("#stockmin").val("0.00");
										} else if (isNaN(stockmin)) {
											$("#stockmin").val("0.00");
											document.getElementById("stockmin").focus();
											Swal.fire({
            	    							icon: "warning",
            	    							title: "Sólo valores numéricos (stock mínimo)"
            	    						})            	    						
										}
									});
              					</script>
                			</div>
                			<div class="col-md-2 col-sm-12">
            					<label for="stockmax">Stock Máximo :*</label>
                				<input type="text" id="stockmax" name="stockmax" class="form-control" value="<?php echo number_format($stockMaximo, 2); ?>" onkeypress="return filterFloat(event,this);" maxlength="5"/>
                				<script type="text/javascript">
                					$("#stockmax").click(function(){
										$("#stockmax").val("");
									});
									$("#stockmax").blur(function(){										
										var stockmax = $("#stockmax").val();
										if (stockmax === "") {
											$("#stockmax").val("0.00");
										} else if (isNaN(stockmax)) {
											$("#stockmax").val("0.00");
											document.getElementById("stockmax").focus();
											Swal.fire({
            	    							icon: "warning",
            	    							title: "Sólo valores numéricos (stock máximo)"
            	    						})            	    						
										}
									});
              					</script>
                			</div>
                			<div class="col-md-2 col-sm-12" style="padding-top: 25px;">
            					<button type="button" id="btnAgregar" class="btn btn-success" title="Agregar"><em class="fa fa-plus"></em></button>
            					<button type="button" id="btnRegresar" class="btn btn-primary" title="Regresar"><em class="fa fa-reply"></em></button>
            					<script type="text/javascript">
            						$("#btnRegresar").click(function(){
            							$.blockUI();
            		    				$("button").prop("disabled", true);
            							<?php if ($tipo == 1) { ?>
                        				location.href = "./index.php?view=supplies";
                        				<?php } else { ?>
                        				location.href = "./index.php?view=enamelwares";
                        				<?php } ?>
            						});
    								$("#btnAgregar").click(function(){
        								var insumo = $("#id").val();
        								var nomAlmacen = $("#almacen option:selected" ).text();
    									var almacen = $("#almacen").val();
    									var stock = $("#stock").val();
    									var stockmin = $("#stockmin").val();
    									var stockmax = $("#stockmax").val();
    									var validaciones = false;
    									if (almacen === "") {
    										document.getElementById("almacen").focus();
    										Swal.fire({
        		    							icon: "warning",
        		    							title: "Campo obligatorio (almacen)"
        		    						})
    									} else if (stock === "") {
    										document.getElementById("stock").focus();
    										Swal.fire({
        		    							icon: "warning",
        		    							title: "Campo obligatorio (stock)"
        		    						})
    									} else if (isNaN(stock)) {
    										document.getElementById("stock").focus();
    										Swal.fire({
    	    									icon: "warning",
    	    									title: "Sólo valores numéricos (stock)"
    	    								})
    									} else if (stockmin === "") {
    										document.getElementById("stockmin").focus();
    										Swal.fire({
        		    							icon: "warning",
        		    							title: "Campo obligatorio (stock mínimo)"
        		    						})
    									} else if (isNaN(stockmin)) {
    										document.getElementById("stockmin").focus();
    										Swal.fire({
    	    									icon: "warning",
    	    									title: "Sólo valores numéricos (stock mínimo)"
    	    								})    	    								
    									} else if (stockmax === "") {
    										document.getElementById("stockmax").focus();
    										Swal.fire({
        		    							icon: "warning",
        		    							title: "Campo obligatorio (stock máximo)"
        		    						})        		    						
    									} else if (isNaN(stockmax)) {
    										document.getElementById("stockmax").focus();
    										Swal.fire({
    	    									icon: "warning",
    	    									title: "Sólo valores numéricos (stock máximo)"
    	    								})
    									} else {
    										validaciones = true;
    									}
    									if (validaciones) {
    										$("#btnAgregar").attr("disabled","disabled");
    										$.blockUI();
    										$.post("./?action=listwarehouse", {
        										insumo: insumo,
    											almacen: almacen,
                                                stock: stock,
                                                stockmin: stockmin,
                                                stockmax: stockmax,
                                                accion: 1
                                            }, function (data) {
                                                if (data === "-1") {
                                                	Swal.fire({
                    									icon: "error",
                    									title: "Ya existe el stock para el insumo <?php echo $insumo; ?> (" + nomAlmacen + ")"
                    								})
                                                } else if (data === "0") {
                                                	Swal.fire({
                    									icon: "error",
                    									title: "Ocurrio un error al registrar el stock del insumo <?php echo $insumo; ?> (" + nomAlmacen + ")"
                    								})    
                                                } else {
                                                	$("#tabla").html(data);
                                                	$("#stock").val("0.00");
                									$("#stockmin").val("0.00");
                									$("#stockmax").val("0.00");
                									$("#almacen").val(null).trigger("change");
                                                }
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
        								<strong>Listado de Stock</strong>
          								<a data-target="#panel2Content" data-parent="#test2Panel" data-toggle="collapse"><span class="pull-right"><i class="panel2Icon fa fa-arrow-up"></i></span></a>
        							</div>
        							<div class="panel-collapse collapse in" id="panel2Content">
          								<div class="panel-body">
                                        	<div class="table-responsive-md table-responsive" id="tabla">
                                        	<table class="table table-hover">
                                                <thead>
                                                    <tr class="btn-primary">
                                                        <th scope="col">Item</th>
                                                        <th scope="col">Almacen</th>
                                                        <th scope="col" style="text-align: right;">Stock</th>
                                                        <th scope="col" style="text-align: right;">Stock Mínimo</th>                                                    
                                                        <th scope="col" style="text-align: right;">Stock Máximo</th>
                                                        <th scope="col" style="text-align: center;">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <?php
                                                    $totalStock = 0;
                                                    if (count($lstInsumoAlmacen) > 0) {
                                                ?>
                                                <tbody>
                                                <?php
                                                        $item = 1;
                                                        foreach ($lstInsumoAlmacen as $objInsumoAlmacen) {
                                                            $objAlmacen = $objInsumoAlmacen->getAlmacen();
                                                            $totalStock += $objInsumoAlmacen->stock;
                                                ?>
                                                	<tr id="row<?php echo $objInsumoAlmacen->id; ?>">
                                                		<td style="text-align: left;"><?php echo $item; ?></td>
                                                        <td style="text-align: left;"><?php echo $objAlmacen->nombre; ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objInsumoAlmacen->stock, 2); ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objInsumoAlmacen->stock_minimo, 2); ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objInsumoAlmacen->stock_maximo, 2); ?></td>
                                                        <td style="text-align: center;">
                                                            <a id="lnkedit<?php echo $objInsumoAlmacen->id; ?>" title="Editar" class="btn btn-info btn-xs"><em class="fa fa-pencil-square-o"></em></a>
                                                            <a id="lnkdel<?php echo $objInsumoAlmacen->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
                            								<script type="text/javascript">
                                        						$("#lnkedit<?php echo $objInsumoAlmacen->id; ?>").click(function() {
                                            						$("#row<?php echo $objInsumoAlmacen->id; ?>").hide();
                                            						$("#rowedit<?php echo $objInsumoAlmacen->id; ?>").show();
                                            						document.getElementById("stockmin<?php echo $objInsumoAlmacen->id; ?>").focus();
                                        						});
                                        						$("#lnkdel<?php echo $objInsumoAlmacen->id; ?>").click(function() {
                                        							Swal.fire({
                                            							title: "Desea anular el stock del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objAlmacen->nombre; ?>)",
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
                                        									    url: "./?action=listwarehouse",
                                        									    data: "id="+<?php echo $objInsumoAlmacen->id; ?>+"&accion=2",
                                        									    dataType: "html",
                                        									    beforeSend: function() {
                                            	    		    					$("#lnkdel<?php echo $objInsumoAlmacen->id; ?>").attr("disabled", "disabled");
                                            	    		    					$.blockUI();
                                            	    		    				},
                                        									    success: function(data) {
                                        									        if (data > 0) {
                                        									        	Swal.fire({
                                                    		                                icon: "success",
                                                    		                                title: "Se anuló correctamente el stock del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objAlmacen->nombre; ?>)",
                                                    										showCancelButton: false,
                                                    										confirmButtonColor: "#3085d6",
                                                    										confirmButtonText: "OK"
                                                    		                        	}).then((result) => {
                                                    										window.location.href = "./index.php?view=suppliewarehouse&id=<?php echo $idInsumo; ?>";
                                                    		                        	})
                                        									        } else if (data < 0) {
                                        									        	Swal.fire({
                                        		    		    							icon: "warning",
                                        		    		    							title: "Existe movimientos asociados al stock del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objAlmacen->nombre; ?>)"
                                        		    		    						})
                                        									        } else {
                                        									        	Swal.fire({
                                        		    		    							icon: "warning",
                                        		    		    							title: "Ocurrio un error al anular el stock del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objAlmacen->nombre; ?>)"
                                        		    		    						})
                                        										    }
                                        									    },
                                        									    error: function() {
                                        									    	Swal.fire({
                                        	    		    							icon: "error",
                                        	    		    							title: "Ocurrio un error al anular el stock del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objAlmacen->nombre; ?>)"
                                        	    		    						})
                                        									    },
                                            	    		    				complete: function(data) {
                                            	    		    					$("#lnkdel<?php echo $objInsumoAlmacen->id; ?>").removeAttr("disabled");
                                            	    		    					$.unblockUI();
                                            	    		    				}
                                        									});
                                        								}
                                        							})
                                    							});
                                    						</script>
                                    					</td>
                                                	</tr>
                                                	<tr id="rowedit<?php echo $objInsumoAlmacen->id; ?>" style="display: none;">
                                                		<td style="text-align: left;"><?php echo $item++; ?></td>
                                                        <td style="text-align: left;"><?php echo $objAlmacen->nombre; ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objInsumoAlmacen->stock, 2); ?></td>
                                                        <td style="text-align: right;">
                                                        	<input type="text" id="stockmin<?php echo $objInsumoAlmacen->id; ?>" name="stockmin<?php echo $objInsumoAlmacen->id; ?>" class="form-control" placeholder="0.00" value="<?php echo number_format($objInsumoAlmacen->stock_minimo, 2); ?>" maxlength="5" onkeypress="return filterFloat(event,this);"/>
                                                        	<script type="text/javascript">
                                                        		$("#stockmin<?php echo $objInsumoAlmacen->id; ?>").focus(function(){
                                                        			var stockmin = $("#stockmin<?php echo $objInsumoAlmacen->id; ?>").val();
                                                        			if (stockmin === "0.00") {
                                                        				$("#stockmin<?php echo $objInsumoAlmacen->id; ?>").val("");
                                                        			}
                                                        		});
                                                        		$("#stockmin<?php echo $objInsumoAlmacen->id; ?>").click(function(){
                                                        			var stockmin = $("#stockmin<?php echo $objInsumoAlmacen->id; ?>").val();
                                                        			if (stockmin === "0.00") {
                                                        				$("#stockmin<?php echo $objInsumoAlmacen->id; ?>").val("");
                                                        			}
                                                        		});
                                                        		$("#stockmin<?php echo $objInsumoAlmacen->id; ?>").blur(function(){
                                                        			var stockmin = $("#stockmin<?php echo $objInsumoAlmacen->id; ?>").val();
                                                        			if (stockmin === "") {
																		$("#stockmin<?php echo $objInsumoAlmacen->id; ?>").val("0.00");
																	} else if (isNaN(stockmin)) {
                                										document.getElementById("stockmin<?php echo $objInsumoAlmacen->id; ?>").focus();
                                										Swal.fire({
                                	    									icon: "warning",
                                	    									title: "Sólo valores numéricos (stock mínimo)"
                                	    								})
                                									}
                                                        		});
                                                        	</script>
														</td>
                                                        <td style="text-align: right;">
                                                        	<input type="text" id="stockmax<?php echo $objInsumoAlmacen->id; ?>" name="stockmax<?php echo $objInsumoAlmacen->id; ?>" class="form-control" placeholder="0.00" value="<?php echo number_format($objInsumoAlmacen->stock_maximo, 2); ?>" maxlength="5" onkeypress="return filterFloat(event,this);"/>
                                                        	<script type="text/javascript">
                                                        		$("#stockmax<?php echo $objInsumoAlmacen->id; ?>").focus(function(){
                                                        			var stockmax = $("#stockmax<?php echo $objInsumoAlmacen->id; ?>").val();
                                                        			if (stockmax === "0.00") {
                                                        				$("#stockmax<?php echo $objInsumoAlmacen->id; ?>").val("");
                                                        			}
                                                        		});
                                                        		$("#stockmax<?php echo $objInsumoAlmacen->id; ?>").click(function(){
                                                        			var stockmax = $("#stockmax<?php echo $objInsumoAlmacen->id; ?>").val();
                                                        			if (stockmax === "0.00") {
                                                        				$("#stockmax<?php echo $objInsumoAlmacen->id; ?>").val("");
                                                        			}
                                                        		});
                                                        		$("#stockmax<?php echo $objInsumoAlmacen->id; ?>").blur(function(){
                                                        			var stockmax = $("#stockmax<?php echo $objInsumoAlmacen->id; ?>").val();
                                                        			if (stockmax === "") {
																		$("#stockmax<?php echo $objInsumoAlmacen->id; ?>").val("0.00");
																	} else if (isNaN(stockmax)) {
                                										document.getElementById("stockmax<?php echo $objInsumoAlmacen->id; ?>").focus();
                                										Swal.fire({
                                	    									icon: "warning",
                                	    									title: "Sólo valores numéricos (stock máximo)"
                                	    								})
                                									}
                                                        		});
                                                        	</script>
                                                        </td>
                                                        <td style="text-align: center;">
                                                        	<a id="lnksave<?php echo $objInsumoAlmacen->id; ?>" title="Grabar" class="btn btn-success btn-xs"><em class="fa fa-floppy-o"></em></a>
                                                        	<a id="lnkcancel<?php echo $objInsumoAlmacen->id; ?>" title="Cancelar" class="btn btn-danger btn-xs"><em class="fa fa-times"></em></a>
                            								<script type="text/javascript">
                            									$("#lnkcancel<?php echo $objInsumoAlmacen->id; ?>").click(function() {
                            										$("#stockmin<?php echo $objInsumoAlmacen->id; ?>").val("<?php echo number_format($objInsumoAlmacen->stock_minimo, 2); ?>");
                                                                    $("#stockmax<?php echo $objInsumoAlmacen->id; ?>").val("<?php echo number_format($objInsumoAlmacen->stock_maximo, 2); ?>");
                                                                    $("#row<?php echo $objInsumoAlmacen->id; ?>").show();
                                            						$("#rowedit<?php echo $objInsumoAlmacen->id; ?>").hide();
                            									});
                                        						$("#lnksave<?php echo $objInsumoAlmacen->id; ?>").click(function() {
                                        							Swal.fire({
                                            							title: "Desea actualizar los datos del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objAlmacen->nombre; ?>)",
                                        								icon: "warning",
                                        								showCancelButton: true,
                                        								confirmButtonColor: "#3085d6",
                                        								cancelButtonColor: "#d33",
                                        								confirmButtonText: "Actualizar",
                                        								cancelButtonText: "Cancelar"
                                        							}).then((result) => {
                                        								if (result.isConfirmed) {
                                        									$.ajax({
                                        									    type: "post",
                                        									    url: "./?action=listwarehouse",
                                        									    data: "id="+<?php echo $objInsumoAlmacen->id; ?>+"&accion=3&stockmin="+$("#stockmin<?php echo $objInsumoAlmacen->id; ?>").val()+"&stockmax="+$("#stockmax<?php echo $objInsumoAlmacen->id; ?>").val(),                        									    
                                        									    dataType: "html",
                                        									    beforeSend: function() {
                                            	    		    					$("#lnksave<?php echo $objInsumoAlmacen->id; ?>").attr("disabled", "disabled");
                                            	    		    					$.blockUI();
                                            	    		    				},
                                        									    success: function(data) {
                                        									        if (data > 0) {
                                        									        	Swal.fire({
                                                    		                                icon: "success",
                                                    		                                title: "Se actualizó los datos del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objAlmacen->nombre; ?>)",
                                                    										showCancelButton: false,
                                                    										confirmButtonColor: "#3085d6",
                                                    										confirmButtonText: "OK"
                                                    		                        	}).then((result) => {
                                                    		                        		window.location.href = "./index.php?view=suppliewarehouse&id=<?php echo $idInsumo; ?>";
                                                    		                        	})
                                        									        } else {
                                        									        	Swal.fire({
                                        		    		    							icon: "warning",
                                        		    		    							title: "Ocurrio un error al actualizar los datos del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objAlmacen->nombre; ?>)"
                                        		    		    						})
                                        										    }
                                        									    },
                                        									    error: function() {
                                        									    	Swal.fire({
                                        	    		    							icon: "error",
                                        	    		    							title: "Ocurrio un error al actualizar los datos del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objAlmacen->nombre; ?>)"
                                        	    		    						})
                                        									    },
                                            	    		    				complete: function(data) {
                                            	    		    					$("#lnksave<?php echo $objInsumoAlmacen->id; ?>").removeAttr("disabled");
                                            	    		    					$.unblockUI();
                                            	    		    				}
                                        									});
                                        								} else {
                                        									$("#stockmin<?php echo $objInsumoAlmacen->id; ?>").val("<?php echo number_format($objInsumoAlmacen->stock_minimo, 2); ?>");
                                                                            $("#stockmax<?php echo $objInsumoAlmacen->id; ?>").val("<?php echo number_format($objInsumoAlmacen->stock_maximo, 2); ?>");
                                                                            $("#row<?php echo $objInsumoAlmacen->id; ?>").show();
                                                    						$("#rowedit<?php echo $objInsumoAlmacen->id; ?>").hide();
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
                                                        <td colspan="2" style="text-align: left;"><strong>TOTAL</strong></td>
                                                        <td style="text-align: right;"><strong><?php echo number_format($totalStock, 2); ?></strong></td>
                                                        <td colspan="3"></td>
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
        					<input type="hidden" id="id" name="id" value="<?php echo $idInsumo; ?>"/>
        					<input type="hidden" id="accion" name="accion" value="1"/>
        				</div>
        			</div>
        		</div>
        	</div>
    		</form>
    		<script type="text/javascript">
    			$(function(){
    				document.getElementById("almacen").focus();
        			$("#panel2Content").on("shown.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel2Content").on("hidden.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});
    			});    		
    		</script>
    	</div>
    </div>
</section>