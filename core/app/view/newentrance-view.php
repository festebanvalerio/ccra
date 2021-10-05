<?php
    $id = $idSede = $idAlmacen = 0;
    $nomSede = $nomAlmacen = $comentario = ""; 
    $fecha = date("d-m-Y");
    $soloLectura = "";
    $texto = "Registrar Ingreso";
    $textoBoton = "Registrar";
    $lstDetalleIngreso = array();
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
        $objIngreso = IngresoData::getById($id);
        $fecha = date("d-m-Y", strtotime($objIngreso->fecha));
        $idSede = $objIngreso->getSede()->id;
        $nomSede = $objIngreso->getSede()->nombre;
        $idAlmacen = $objIngreso->getAlmacen()->id;
        $nomAlmacen = $objIngreso->getAlmacen()->nombre;
        $comentario = $objIngreso->comentario;
        
        $soloLectura = "disabled";
        $texto = "Detalle Ingreso";
        
        $lstDetalleIngreso = DetalleIngresoData::getAllByIngreso($id);
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
    
    unset($_SESSION["insumos"]);
    unset($_SESSION["tmp_insumos"]);
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newentrance" action="index.php?action=addentrance" role="form" autocomplete="off">
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
        						<input type="text" id="sede" name="sede" class="form-control" value="<?php echo $nomSede; ?>" disabled/>
            				</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="nomalmacen">Almacen :</label>
        						<input type="text" id="nomalmacen" name="nomalmacen" class="form-control" value="<?php echo $nomAlmacen; ?>" disabled/>
            				</div>            				
							<div class="col-md-6 col-sm-12">
            					<label for="comentario">Comentario :</label>
        						<input type="text" id="comentario" name="comentario" class="form-control" value="<?php echo $comentario; ?>" maxlength="100" <?php echo $soloLectura; ?>/>
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
        									$.post("./?action=listentrance", {
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
              				<div class="col-md-4 col-sm-12" style="padding-top: 25px;">
            					<button type="button" id="btnAgregar" class="btn btn-success" <?php echo $soloLectura; ?> title="Agregar"><em class="fa fa-plus"></em></button>
            					<script type="text/javascript">
    								$("#btnAgregar").click(function(){
        								var almacen = <?php echo $idAlmacen; ?>;
    									var insumo = $("#idInsumo").val();
    									var unidadalmacen = $("#unidadalmacen").val();
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
    										$.post("./?action=listentrance", {
    											opcion: 1,
    											almacen: almacen,
                                                insumo: insumo,
                                                unidad: unidadalmacen,
                                                cantidad: cantidad,
                                                indicador: 1
                                            }, function (data) {
                                            	$("#tabla").html(data);
                                            	$("#insumo").val(null).trigger("change");
                                            	$("#unidad").val("");
        										$("#precio").val("");
        										$("#idInsumo").val("");
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
                                                        <th scope="col" style="text-align: right;">Stock</th>
                        								<th scope="col" style="text-align: right;">Costo</th>
                        								<th scope="col" style="text-align: right; width: 10%;">Cantidad</th>
                                                    </tr>
                                                </thead>
                                                <?php
                                                    if (count($lstDetalleIngreso) > 0) {
                                                ?>
                                                <tbody>
                                                <?php
                                                        $item = 1;
                                                        foreach ($lstDetalleIngreso as $objDetalleIngreso) {
                                                            $objInsumo = $objDetalleIngreso->getInsumo();
                                                            $objUnidad = $objDetalleIngreso->getUnidad();
                                                            
                                                            $stock = 0;
                                                            $lstInsumoxAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objInsumo->id, $idAlmacen);
                                                            if (count($lstInsumoxAlmacen) > 0) {
                                                                $stock = $lstInsumoxAlmacen[0]->stock;
                                                            }
                                                ?>
                                                	<tr>
                                                		<td style="text-align: left;"><?php echo $item++; ?></td>
                                                        <td style="text-align: left;"><?php echo $objInsumo->nombre; ?></td>
                                                        <td style="text-align: left;"><?php echo $objUnidad->nombre . " (" . $objUnidad->abreviatura . ")"; ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($stock, 2); ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objInsumo->costo, 2); ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objDetalleIngreso->cantidad, 2); ?></td>
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
    				location.href = "./index.php?view=entrances";
    			});
    			$(function(){
    				document.getElementById("comentario").focus();
        			$("#insumo").select2();
        			$("#panel2Content").on("shown.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel2Content").on("hidden.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});
    				$("#newentrance").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addentrance",
    		    				dataType: "html",
    		    				data: $("#newentrance").serialize(),
    		    				beforeSend: function() {
        		    				$("#btnRegistrar").attr("disabled", "disabled");
    		    					$("#btnCancelar").attr("disabled", "disabled");
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se registró correctamente el ingreso",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    										window.location.href = "./index.php?view=entrances";
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
    		    							title: "Ocurrio un error al registrar el ingreso"
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al registrar el ingreso"
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