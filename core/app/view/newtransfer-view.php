<?php
    $id = $idSede = 0;
    $nomSede = $almacenOrigen = $nomAlmacenOrigen = $almacenDestino = $observacion = "";
    $fecha = date("d-m-Y");
    $soloLectura = "";
    $texto = "Registrar Transferencia";
    $textoBoton = "Registrar";
    $lstDetalleTransferencia = NULL;
    if (isset($_GET["transferencia"])) {
        $id = $_GET["transferencia"];
        
        $objTransferencia = TransferenciaData::getById($id);
        $fecha = date("d-m-Y", strtotime($objTransferencia->fecha));
        $idSede = $objTransferencia->getAlmacenOrigen()->getSede()->id;
        $nomSede = $objTransferencia->getAlmacenOrigen()->getSede()->nombre;
        $almacenOrigen = $objTransferencia->getAlmacenOrigen()->id;        
        $nomAlmacenOrigen = $objTransferencia->getAlmacenOrigen()->nombre;
        $almacenDestino = $objTransferencia->almacen_destino;
        $observacion = $objTransferencia->observacion;
        
        $soloLectura = "disabled";
        
        $texto = "Detalle Transferencia";
        
        $lstDetalleTransferencia = DetalleTransferenciaData::getAllByTransferencia($id);
    } else {
        $idEmpresa = $_SESSION["empresa"];
        $idSede = $_SESSION["sede"];
        $lstAlmacen = AlmacenData::getAll(1, $idEmpresa, $idSede);
        if (count($lstAlmacen) > 0) {
            $almacenOrigen = $lstAlmacen[0]->id;
            $nomAlmacenOrigen = $lstAlmacen[0]->nombre;
            $idSede = $lstAlmacen[0]->getSede()->id;
            $nomSede = $lstAlmacen[0]->getSede()->nombre;
        }
    }
    $lstInsumo = InsumoData::getAll(1, $idSede);
    $lstAlmacenDestino = AlmacenData::getAll(1, $_SESSION["empresa"], "", $almacenOrigen);
    
    unset($_SESSION["insumos"]);
    unset($_SESSION["tmp_insumos"]);
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newtransfer" action="index.php?action=addtransfer" role="form" autocomplete="off">
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
            				<div class="col-md-2 col-sm-12">
            					<label for="sede">Sede :</label>
        						<input type="text" id="nomsede" name="nomsede" class="form-control" value="<?php echo $nomSede; ?>" disabled/>
            				</div>
            				<div class="col-md-4 col-sm-12">
            					<label for="nomalmacen">Almacen Origen :</label>
        						<input type="text" id="nomalmacen" name="nomalmacen" class="form-control" value="<?php echo $nomAlmacenOrigen; ?>" disabled/>
            				</div>
            				<div class="col-md-4 col-sm-12">
            					<label for="almacendestino">Almacen Destino :*</label>
        						<select id="almacendestino" name="almacendestino" class="form-control" required <?php echo $soloLectura; ?>>
        							<option value="">SELECCIONE</option>
        							<?php foreach ($lstAlmacenDestino as $objAlmacen) { ?>
            						<option value="<?php echo $objAlmacen->id; ?>" <?php if ($objAlmacen->id == $almacenDestino) { echo "selected"; } ?>><?php echo $objAlmacen->nombre; ?></option>
              						<?php } ?>
        						</select>
        						<script type="text/javascript">
									$("#almacendestino").change(function(){
										var almacen = $("#almacendestino").val();
										if (almacen === "") {
											$("#idsededestino").val("");
										} else {
											$.post("./?action=utilitarios", {
            									almacen: almacen
                                            }, function (data) {
                                                $("#idsededestino").val(data);
                                                document.getElementById("observacion").focus();
                                            });
										}
									});
        						</script>
            				</div>
            			</div>
            			<div class="form-group">
            				<div class="col-md-12 col-sm-12">
            					<label for="observacion">Observación :</label>
        						<input type="text" id="observacion" name="observacion" class="form-control" value="<?php echo $observacion; ?>" maxlength="255" <?php echo $soloLectura; ?>/>
            				</div>
            			</div>
            		</fieldset>
                	<fieldset class="scheduler-border">
    					<legend class="scheduler-border">Datos del Insumo</legend>
    					<?php if ($id == 0) { ?>
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
    									var almacen = <?php echo $almacenOrigen; ?>;
    									var insumo = $("#insumo").val();
    									if (insumo !== "") {
        									$.blockUI();
        									$.post("./?action=listtransfer", {
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
                		    							title: "El insumo seleccionado no tiene el stock suficiente para realizar la transferencia"
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
                				<input type="text" id="stockactual" name="stockactual" class="form-control" value="" disabled/>
                			</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="cantidad">Cantidad :</label>
                				<input type="text" id="cantidad" name="cantidad" class="form-control" value="" onkeypress="return filterFloat(event,this);" maxlength="5"/>
                			</div>
                			<div class="col-md-2 col-sm-12" style="padding-top: 25px;">
            					<button type="button" id="btnAgregar" class="btn btn-success" title="Agregar"><em class="fa fa-plus"></em></button>
            					<script type="text/javascript">
    								$("#btnAgregar").click(function(){
        								var almacen = <?php echo $almacenOrigen; ?>;
    									var insumo = $("#idInsumo").val();
    									var cantidad = $("#cantidad").val();
    									var stock = $("#stockactual").val().replace(",","");
    									var validaciones = false;
    									if (insumo === "") {
    										document.getElementById("insumo").focus();
    										Swal.fire({
        		    							icon: "warning",
        		    							title: "Seleccionar el insumo"
        		    						})        		    						
    									} else if (cantidad === "") {
    										document.getElementById("cantidad").focus();
    										Swal.fire({
        		    							icon: "warning",
        		    							title: "Ingresar la cantidad"
        		    						})        		    						
    									} else if (isNaN(cantidad)) {
    										document.getElementById("cantidad").focus();
    										Swal.fire({
    	    									icon: "warning",
    	    									title: "La cantidad ingresada debe contener valores numéricos"
    	    								})
    									} else if ((cantidad * 1) > (stock * 1)) {
    										$("#cantidad").val("");
    										document.getElementById("cantidad").focus();
    										Swal.fire({
    	    									icon: "warning",
    	    									title: "La cantidad ingresada es mayor al stock actual"
    	    								})
    									} else {
    										validaciones = true;
    									}
    									if (validaciones) {
    										$.blockUI();
    										$.post("./?action=listtransfer", {
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
            									$.unblockUI();
                                            });
    									}
    								});
            					</script>
            				</div>
            			</div>
            			<?php } ?>
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
                                                        <th scope="col" style="text-align: right;">Cant. Transferida</th>
                                                    </tr>
                                                </thead>
                                                <?php
                                                    if (count($lstDetalleTransferencia) > 0) {
                                                ?>
                                                <tbody>
                                                <?php
                                                        $item = 1;
                                                        foreach ($lstDetalleTransferencia as $objDetalleTransferencia) {
                                                            $objInsumo = $objDetalleTransferencia->getInsumo();
                                                            $objUnidad = $objInsumo->getUnidad();
                                                ?>
                                                	<tr>
                                                		<td style="text-align: left;"><?php echo $item++; ?></td>
                                                        <td style="text-align: left;"><?php echo $objInsumo->nombre; ?></td>
                                                        <td style="text-align: left;"><?php echo $objUnidad->abreviatura; ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objDetalleTransferencia->cantidad, 2); ?></td>
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
        					<?php if ($id > 0) { ?>
        					<a class="btn btn-primary" id="btnExportar" href="transferencia.php?id=<?php echo $id; ?>" role="button" title="Buscar" target="_blank">Exportar</a>
        					<?php } ?>
        					<input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>
        					<input type="hidden" id="idsedeorigen" name="idsedeorigen" value="<?php echo $idSede; ?>"/>
        					<input type="hidden" id="idalmacenorigen" name="idalmacenorigen" value="<?php echo $almacenOrigen; ?>"/>
        					<input type="hidden" id="idsededestino" name="idsededestino" value=""/>
        					<input type="hidden" id="accion" name="accion" value="1"/>
        				</div>
        			</div>
        		</div>
        	</div>
    		</form>
    		<script type="text/javascript">
    			$("#btnCancelar").click(function(){
    				$("button").prop("disabled", true);
    				location.href = "./index.php?view=transfers";
    			});
    			$(function(){
    				document.getElementById("almacendestino").focus();
        			$("#insumo").select2();
        			$("#panel2Content").on("shown.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel2Content").on("hidden.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});        			
    				$("#newtransfer").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addtransfer",
    		    				async: true,
    		    				dataType: "html",
    		    				data: $("#newtransfer").serialize(),
    		    				beforeSend: function() {
        		    				$("button").prop("disabled", true);
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se registró correctamente la transferencia",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    										window.location.href = "./index.php?view=transfers";
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
    		    							title: "Ocurrio un error al registrar la transferencia"
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al registrar la transferencia"
    								})
    		    				},
    		    				complete: function(data) {
    		    					$("button").prop("disabled", false);
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