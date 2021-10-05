<?php
    $id = $idSede = $idAlmacen = 0;
    $nomSede = $nomAlmacen = $ruc = $razonSocial = ""; 
    $monto = 0.00;
    $tipoDocumento = $numDocumento = "";
    $fecha = date("d-m-Y");
    $soloLectura = "";
    $texto = "Registrar Orden de Compra";
    $textoBoton = "Registrar";
    $lstDetalleOrdenCompra = array();
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
        $objOrdenCompra = OrdenCompraData::getById($id);
        $fecha = date("d-m-Y", strtotime($objOrdenCompra->fecha));
        $idSede = $objOrdenCompra->getSede()->id;
        $nomSede = $objOrdenCompra->getSede()->nombre;
        $idAlmacen = $objOrdenCompra->getAlmacen()->id;
        $nomAlmacen = $objOrdenCompra->getAlmacen()->nombre;
        $tipoDocumento = $objOrdenCompra->tipo_documento;
        $numDocumento = $objOrdenCompra->num_documento;
        $ruc= $objOrdenCompra->ruc;
        $razonSocial = $objOrdenCompra->razon_social;
        $monto = $objOrdenCompra->monto;
        
        $soloLectura = "disabled";
        
        $texto = "Detalle Orden de Compra";
        
        $lstDetalleOrdenCompra = DetalleOrdenCompraData::getAllByOrdenCompra($id);
    } else {
        $idEmpresa = $_SESSION["empresa"];
        $idSede = $_SESSION["sede"];        
        $lstAlmacen = AlmacenData::getAll(1, $idEmpresa, $idSede);
        if (count($lstAlmacen) > 0) {
            $idAlmacen = $lstAlmacen[0]->id;
            $nomAlmacen = $lstAlmacen[0]->nombre;
            $idSede = $lstAlmacen[0]->getSede()->id;
            $nomSede = $lstAlmacen[0]->getSede()->nombre;
        }
    }
    $lstInsumo = InsumoData::getAll(1, $idSede);
    $lstUnidad = UnidadData::getAll(1);
    $lstTipoDocumento = ParametroData::getAll(1, "TIPO DOCUMENTO OC");
    
    $lstTipoDocumentoCliente = ParametroData::getAll(1, "TIPO DOCUMENTO", "FACTURA");
    $tipoDocumentoCliente = $lstTipoDocumentoCliente[0]->id;
    
    unset($_SESSION["insumos"]);
    unset($_SESSION["tmp_insumos"]);
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newoc" action="index.php?action=addoc" role="form" autocomplete="off">
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
            					<label for="sede">Sede :</label>
        						<input type="text" id="sede" name="sede" class="form-control" value="<?php echo $nomSede; ?>" disabled/>
            				</div>
            				<div class="col-md-4 col-sm-12">
            					<label for="nomalmacen">Almacen :</label>
        						<input type="text" id="nomalmacen" name="nomalmacen" class="form-control" value="<?php echo $nomAlmacen; ?>" disabled/>
            				</div>
            			</div>
            		</fieldset>
            		<fieldset class="scheduler-border">
    					<legend class="scheduler-border">Datos Proveedor</legend>
                		<div class="form-group">
                			<div class="col-md-3 col-sm-12">
            					<label for="tipodoc">Tipo Documento :*</label>
            					<select id="tipodoc" name="tipodoc" class="form-control" required <?php echo $soloLectura; ?>>
            						<option value="">SELECCIONE</option>
        							<?php foreach ($lstTipoDocumento as $objTipoDocumento) { ?>
        							<option value="<?php echo $objTipoDocumento->id; ?>" <?php if ($objTipoDocumento->id == $tipoDocumento) { echo "selected"; } ?>><?php echo $objTipoDocumento->nombre; ?></option>        								
          							<?php } ?>
          						</select>
          						<script type="text/javascript">
          							$("#tipodoc").change(function(){
          								var tipodoc = $("#tipodoc").val();
          								if (tipodoc !== "") {
          									document.getElementById("numdoc").focus();
          								}
          							});
          						</script>
            				</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="numdoc">Num. Documento :*</label>
        						<input type="text" id="numdoc" name="numdoc" class="form-control" value="<?php echo $numDocumento; ?>" maxlength="25" required <?php echo $soloLectura; ?>/>
            				</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="ruc">RUC :</label>
        						<input type="text" id="ruc" name="ruc" class="form-control" value="<?php echo $ruc; ?>" maxlength="11" onkeypress="return soloNumeros(event)" <?php echo $soloLectura; ?>/>
        						<input type="hidden" id="cliente" name="cliente" value=""/>
        						<script type="text/javascript">
    								$("#ruc").change(function(){
    									var tipodoc = <?php echo $tipoDocumentoCliente; ?>;
    									var numdoc = $("#ruc").val();
    									var validaciones = false; 
    									
										if (numdoc !== "") {
    										if (numdoc.length < 11) {
    											document.getElementById("ruc").focus();
    											Swal.fire({
    		    									icon: "warning",
    		    									title: "RUC deber ser de 11 dígitos"
    		    								})		    								
    										} else if (isNaN(numdoc)) {
    											document.getElementById("ruc").focus();
    											Swal.fire({
    		    									icon: "warning",
    		    									title: "Sólo valores numéricos (RUC)"
    		    								})
    										} else {
    											validaciones = true;
    										}
										}
    									if (!validaciones) {
    										if (numdoc !== "") {
        										$("#ruc").val("");
        										$("#razon").val("");
        										$("#cliente").val("");
        										document.getElementById("ruc").focus();
    										}
    										$("#cliente").val("");
    									} else {
        									$.blockUI();
        									$.post("./?action=utilitarios", {
        										tipodoc: tipodoc,
        										numdoc: numdoc
                                            }, function (data) {
                                            	var resultado = data.split("@");
                                                if (resultado[0] === "") {
                                                	if (resultado[2] === "1") {
                                                		document.getElementById("ruc").focus();
                                                    	Swal.fire({
            		    									icon: "error",
            		    									title: "RUC inválido"
            		    								})
            		    								$("#ruc").val("");
            		    								$("#razon").val("");
            		    								$("#cliente").val("");
                                                	}
                                                } else {
                                                	$("#razon").val(resultado[0]);
                                                    $("#cliente").val(resultado[3]);
                                                    document.getElementById("monto").focus();
                                                    $("#monto").val("");
                                                }
                                                $.unblockUI();
                                        	});
    									}
    								});
        						</script>
            				</div>
            				<div class="col-md-5 col-sm-12">
            					<label for="razon">Razon Social :</label>
        						<input type="text" id="razon" name="razon" class="form-control" value="<?php echo $razonSocial; ?>" readonly="readonly" <?php echo $soloLectura; ?>/>
            				</div>
            			</div>
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
            					<label for="monto">Monto :*</label>
            					<input type="text" id="monto" name="monto" class="form-control" maxlength="10" placeholder="0.00" value="<?php echo number_format($monto, 2); ?>" dir="rtl" required onkeypress="return filterFloat(event,this);" <?php echo $soloLectura; ?>/>
            					<script type="text/javascript">
            						$("#monto").click(function(){
            							$("#monto").val("");
            						});
            						$("#monto").blur(function(){
            							var monto = $("#monto").val();
            							if (monto === "") {
            								$("#monto").val("0.00");
            							} else if (isNaN(monto)) {
            								$("#monto").val("0.00");
            								document.getElementById("monto").focus();
            								Swal.fire({
            		    						icon: "warning",
            		    						title: "Sólo valores numéricos (monto)"
            		    					})
            							}
            						});
            					</script>
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
    									var insumo = $("#insumo").val();
    									if (insumo !== "") {
        									$.blockUI();
        									$.post("./?action=listoc", {
            									opcion: 0,
                                                insumo: insumo,
                                            }, function (data) {
                                                var resultado = data.split("|");
                                                $("#idInsumo").val(resultado[0]);
        										$("#unidad").val(resultado[1]);
        										$("#precio").val(resultado[2]);
        										$("#unidadalmacen").val(resultado[3]);
        										document.getElementById("cantidad").focus();
        							        	$.unblockUI();
                                            });
    									} else {
    										$("#unidad").val("");
    										$("#precio").val("");
    										$("#idInsumo").val("");
    										$("#cantidad").val("");
    										$("#unidadalmacen").val("");
    									}
    								})
              					</script>
            				</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="unidad">Unidad Almacen :</label>
                				<input type="text" id="unidad" name="unidad" class="form-control" value="" disabled/>
                				<input type="hidden" id="unidadalmacen" name="unidadalmacen" class="form-control" value=""/>
                				<input type="hidden" id="precio" name="precio" class="form-control" value=""/>
                				<input type="hidden" id="idInsumo" name="idInsumo" class="form-control" value=""/>
                			</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="cantidad">Cantidad :*</label>
                				<input type="text" id="cantidad" name="cantidad" class="form-control" value="" placeholder="0.00" onkeypress="return filterFloat(event,this);" maxlength="5"/>
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
                			<div class="col-md-4 col-sm-12">
            					<label for="unidad">Unidad Compra :*</label>
            					<select id="unidadcompra" name="unidadcompra" class="form-control">
            						<option value="">SELECCIONE</option>
            						<?php foreach ($lstUnidad as $objUnidad) { ?>
            						<option value="<?php echo $objUnidad->id; ?>"><?php echo $objUnidad->nombre; ?></option>
              						<?php } ?>
              					</select>
              					<script type="text/javascript">
              						$("#unidadcompra").change(function(){
              							var unidad = $("#unidadcompra").val();
              							if (unidad !== "") {
              								document.getElementById("costo").focus();
              								$("#costo").val("");
              							}
              						});
              					</script>
              				</div>
              			</div>
              			<div class="form-group">
              				<div class="col-md-2 col-sm-12">
            					<label for="costo">Costo :*</label>
                				<input type="text" id="costo" name="costo" class="form-control" value="" placeholder="0.00" onkeypress="return filterFloat(event,this);" maxlength="5"/>
                				<script type="text/javascript">
            						$("#costo").blur(function(){
            							var costo = $("#costo").val();
            							if (costo !== "") {
            								if (isNaN(costo)) {
                								$("#costo").val("");
                								document.getElementById("costo").focus();
                								Swal.fire({
                		    						icon: "warning",
                		    						title: "Sólo valores numéricos (costo)"
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
        								var almacen = <?php echo $idAlmacen; ?>;
    									var insumo = $("#idInsumo").val();
    									var unidadalmacen = $("#unidadalmacen").val();
    									var cantidad = $("#cantidad").val();
    									var unidadcompra = $("#unidadcompra").val();
    									var costo = $("#costo").val();
    									
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
    									} else if (unidadcompra === "") {
    										document.getElementById("unidadcompra").focus();
    										Swal.fire({
        		    							icon: "warning",
        		    							title: "Campo obligatorio (unidad compra)"
        		    						})
    									} else if (costo === "") {
    										document.getElementById("costo").focus();
    										Swal.fire({
        		    							icon: "warning",
        		    							title: "Campo obligatorio (costo)"
        		    						})        		    						
    									} else if (isNaN(costo)) {
    										$("#costo").val("");
    										document.getElementById("costo").focus();
    										Swal.fire({
    	    									icon: "warning",
    	    									title: "Sólo valores numéricos (costo)"
    	    								})
    									} else {
    										validaciones = true;
    									}
    									if (validaciones) {
    										$("#btnAgregar").attr("disabled", "disabled");
    										$.blockUI();
    										$.post("./?action=listoc", {
    											opcion: 1,
    											almacen: almacen,
                                                insumo: insumo,
                                                unidad: unidadalmacen,
                                                cantidad: cantidad,
                                                unidadcompra: unidadcompra,
                                                precio: costo,
                                                indicador: 1
                                            }, function (data) {
                                                if (data === "-1") {
                                                	document.getElementById("unidadcompra").focus();
                                                	Swal.fire({
                	    								icon: "warning",
                	    								title: "El insumo no tiene equivalencia para esa unidad de compra"
                	    							})
                                                } else {
                                                	$("#tabla").html(data);
                                                	$("#insumo").val(null).trigger("change");
                                                	$("#unidad").val("");
            										$("#precio").val("");
            										$("#idInsumo").val("");
            										$("#cantidad").val("");
            										$("#unidadcompra").val("");
            										$("#costo").val("");
            										document.getElementById("insumo").focus();
                                                }
                                                $("#btnAgregar").removeAttr("disabled");
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
                                                        <th scope="col">Unidad Almacen</th>
                                                        <th scope="col">Unidad Compra</th>
                                                        <th scope="col" style="text-align: right;">Cantidad</th>
                                                        <th scope="col" style="text-align: right;">Costo</th>
                                                    </tr>
                                                </thead>
                                                <?php
                                                    if (count($lstDetalleOrdenCompra) > 0) {
                                                ?>
                                                <tbody>
                                                <?php
                                                        $item = 1;
                                                        foreach ($lstDetalleOrdenCompra as $objDetalleOrdenCompra) {
                                                            $objInsumo = $objDetalleOrdenCompra->getInsumo();
                                                            $objUnidadAlmacen = $objDetalleOrdenCompra->getUnidadAlmacen();
                                                            $objUnidadCompra = $objDetalleOrdenCompra->getUnidadCompra();
                                                ?>
                                                	<tr>
                                                		<td style="text-align: left;"><?php echo $item++; ?></td>
                                                        <td style="text-align: left;"><?php echo $objInsumo->nombre; ?></td>
                                                        <td style="text-align: left;"><?php echo $objUnidadAlmacen->nombre . " (" . $objUnidadAlmacen->abreviatura . ")"; ?></td>
                                                        <td style="text-align: left;"><?php echo $objUnidadCompra->nombre . " (" . $objUnidadCompra->abreviatura . ")"; ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objDetalleOrdenCompra->cantidad, 2); ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objDetalleOrdenCompra->costo, 2); ?></td>
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
        					<input type="hidden" id="almacen" name="almacen" value="<?php echo $idAlmacen; ?>"/>
        					<input type="hidden" id="idsede" name="idsede" value="<?php echo $idSede; ?>"/>
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
    				location.href = "./index.php?view=ocs";
    			});
    			$(function(){
    				document.getElementById("tipodoc").focus();
        			$("#insumo").select2({
            	    	placeholder: "-- SELECCIONE --",
            	    	minimumInputLength: 3
            	    });
        			$("#panel2Content").on("shown.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel2Content").on("hidden.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});        			
    				$("#newoc").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addoc",
    		    				dataType: "html",
    		    				data: $("#newoc").serialize(),
    		    				beforeSend: function() {
        		    				$("#btnRegistrar").attr("disabled", "disabled");
    		    					$("#btnCancelar").attr("disabled", "disabled");
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		    					document.getElementById("insumo").focus();
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se registró correctamente la orden de compra",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    										window.location.href = "./index.php?view=ocs";
    		                        	})        					
    		        				} else if (data < 0) {    		        					
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Agregar al menos un insumo"
    		    						})
    		        				} else {
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Ocurrio un error al registrar la orden de compra"
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al registrar la orden de compra"
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