<?php
    $sede = $_SESSION["sede"];
    $empresa = $_SESSION["empresa"];
    $idInsumo = $idUnidad = $insumo = $unidad = "";
    $tipo = 0;
    $texto = "Registrar Equivalencia";
    $textoLabel = "Insumo";
    $textoMensaje = "insumo";
    $lstEquivalencia = array();
    if (isset($_GET["id"])) {
        $idInsumo = $_GET["id"];
        
        $objInsumo = InsumoData::getById($idInsumo);
        $insumo = $objInsumo->nombre;
        $idUnidad = $objInsumo->unidad;
        $unidad = $objInsumo->getUnidad()->abreviatura;        
        $tipo = $objInsumo->indicador;
        if ($tipo == 2) {
            $textoLabel = "Accesorio";
            $textoMensaje = "accesorio";
        }
        $lstEquivalencia = EquivalenciaData::getAllByInsumo(1, $idInsumo);
    }
    $lstUnidad = UnidadData::getAll(1);
    
    unset($_SESSION["insumos_almacen"]);
    unset($_SESSION["tmp_insumos_almacen"]);
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="equivalence" action="index.php?action=addsupplies" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>				
        		<div class="panel-body">
        			<fieldset class="scheduler-border">
    					<legend class="scheduler-border">Datos Generales</legend>
            			<div class="form-group">
            				<div class="col-md-4 col-sm-12">
            					<label for="insumo"><?php echo $textoLabel; ?> :</label>
                				<input type="text" id="insumo" name="insumo" class="form-control" value="<?php echo $insumo; ?>" disabled/>
                			</div>
                			<div class="col-md-2 col-sm-12">
            					<label for="unidad">Unidad Compra :</label>
                				<select id="unidadalt" name="unidadalt" class="form-control">
                					<option value="">SELECCIONE</option>
                					<?php foreach ($lstUnidad as $objUnidad) { ?>
                					<option value="<?php echo $objUnidad->id; ?>"><?php echo "1 ".$objUnidad->nombre; ?></option>
                					<?php } ?>
                				</select>
                			</div>
                			<div class="col-md-1 col-sm-12">
            					<label for="igualdad">&nbsp;</label>
            					<input type="text" id="igualdad" name="igualdad" class="form-control" value="=" disabled/>
                			</div>                			
                			<div class="col-md-1 col-sm-12">
            					<label for="factor">Factor :</label>
                				<input type="text" id="factor" name="factor" class="form-control" value="" maxlength="5" placeholder="0" onkeypress="return filterFloat(event,this);"/>
                			</div>
                			<div class="col-md-2 col-sm-12">
            					<label for="unidad">Unidad Almacen :</label>
                				<input type="text" id="unidad" name="unidad" class="form-control" value="<?php echo $unidad; ?>" disabled/>
                			</div>                			
                			<div class="col-md-2 col-sm-12" style="padding-top: 25px;">
            					<button type="button" id="btnAgregar" class="btn btn-success" title="Agregar"><em class="fa fa-plus"></em></button>
            					<button type="button" id="btnRegresar" class="btn btn-danger" title="Regresar"><em class="fa fa-reply"></em></button>
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
        								var nomunidadalt = $("#unidadalt option:selected" ).text();
										var unidadbase = <?php echo $idUnidad; ?>;
										var unidadalt = $("#unidadalt").val();
										var factor = $("#factor").val();
        								var validaciones = false;
    									if (unidadalt === "") {
    										document.getElementById("unidadalt").focus();
    										Swal.fire({
        		    							icon: "warning",
        		    							title: "Campo obligatorio (unidad compra)"
        		    						})        		    						
    									} else if (factor === "") {
    										document.getElementById("factor").focus();
    										Swal.fire({
        		    							icon: "warning",
        		    							title: "Campo obligatorio (factor)"
        		    						})        		    						
    									} else if (isNaN(factor)) {
    										document.getElementById("factor").focus();
    										Swal.fire({
    	    									icon: "warning",
    	    									title: "Sólo valores numéricos (factor)"
    	    								})    	    								
    									} else if ((unidadbase * 1) === (unidadalt * 1)) {
    										document.getElementById("unidadalt").focus();
    										Swal.fire({
        		    							icon: "warning",
        		    							title: "Unidad de compra y almacen deben ser distintos"
        		    						})        		    						
    									} else {
    										validaciones = true;
    									}
    									if (validaciones) {
    										$("#btnAgregar").attr("disabled","disabled");
    										$.blockUI();
    										$.post("./?action=listequivalence", {
        										insumo: insumo,
    											unidadbase: unidadbase,
                                                unidadalt: unidadalt,
                                                factor: factor,
                                                accion: 1
                                            }, function (data) {
                                                if (data === "-1") {
                                                	Swal.fire({
                    									icon: "error",
                    									title: "Ya existe la equivalencia para el insumo <?php echo $insumo; ?> (" + nomunidadalt + ")"
                    								})
                                                } else if (data === "0") {
                                                	Swal.fire({
                    									icon: "error",
                    									title: "Ocurrio un error al registrar el stock del insumo <?php echo $insumo; ?> (" + nomunidadalt + ")"
                    								})    
                                                } else {
                                                	$("#tabla").html(data);
                                                	$("#nomunidadalt").val("");
                									$("#factor").val("");
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
        								<strong>Listado de Equivalencia</strong>
          								<a data-target="#panel2Content" data-parent="#test2Panel" data-toggle="collapse"><span class="pull-right"><i class="panel2Icon fa fa-arrow-up"></i></span></a>
        							</div>
        							<div class="panel-collapse collapse in" id="panel2Content">
          								<div class="panel-body">
                                        	<div class="table-responsive-md table-responsive" id="tabla">
                                        	<table class="table table-hover">
                                                <thead>
                                                    <tr class="btn-primary">
                                                        <th scope="col">Item</th>
                                                        <th scope="col"></th>
                                                        <th scope="col">Unidad Compra</th>
                                                        <th scope="col"></th>
                                                        <th scope="col" style="text-align: right;">Factor</th>
                                                        <th scope="col">Unidad Almacen</th>                                                    
                                                        <th scope="col" style="text-align: center;">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <?php
                                                    if (count($lstEquivalencia) > 0) {
                                                ?>
                                                <tbody>
                                                <?php
                                                        $item = 1;
                                                        foreach ($lstEquivalencia as $objEquivalencia) {
                                                            $objUnidadBase = $objEquivalencia->getUnidadBase();
                                                            $objUnidadAlternativa = $objEquivalencia->getUnidadAlternativa();
                                                ?>
                                                	<tr id="row<?php echo $objEquivalencia->id; ?>">
                                                		<td style="text-align: left;"><?php echo $item; ?></td>
                                                		<td style="text-align: center;"><?php echo number_format(1, 2); ?></td>
                                                        <td style="text-align: left;"><?php echo $objUnidadAlternativa->nombre; ?></td>
                                                        <td style="text-align: center;">=</td>
                                                        <td style="text-align: right;"><?php echo number_format($objEquivalencia->factor, 2); ?></td>
                                                        <td style="text-align: left;"><?php echo $objUnidadBase->nombre; ?></td>                                                        
                                                        <td style="text-align: center;">
                                                            <a id="lnkedit<?php echo $objEquivalencia->id; ?>" title="Editar" class="btn btn-info btn-xs"><em class="fa fa-pencil-square-o"></em></a>
                                                            <a id="lnkdel<?php echo $objEquivalencia->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
                            								<script type="text/javascript">
                                        						$("#lnkedit<?php echo $objEquivalencia->id; ?>").click(function() {
                                            						$("#row<?php echo $objEquivalencia->id; ?>").hide();
                                            						$("#rowedit<?php echo $objEquivalencia->id; ?>").show();
                                        						});
                                        						$("#lnkdel<?php echo $objEquivalencia->id; ?>").click(function() {
                                        							Swal.fire({
                                            							title: "Desea anular la equivalencia del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objUnidadAlternativa->nombre; ?>)",
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
                                        									    data: "id="+<?php echo $objEquivalencia->id; ?>+"&accion=2",
                                        									    dataType: "html",
                                        									    success: function(data) {
                                        									        if (data > 0) {
                                        									        	Swal.fire({
                                                    		                                icon: "success",
                                                    		                                title: "Se anuló correctamente la equivalencia del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objUnidadAlternativa->nombre; ?>)",
                                                    										showCancelButton: false,
                                                    										confirmButtonColor: "#3085d6",
                                                    										confirmButtonText: "OK"
                                                    		                        	}).then((result) => {
                                                    										window.location.href = "./index.php?view=equivalence&id=<?php echo $idInsumo; ?>";
                                                    		                        	})
                                        									        } else {
                                        									        	Swal.fire({
                                        		    		    							icon: "warning",
                                        		    		    							title: "Ocurrio un error la equivalencia del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objUnidadAlternativa->nombre; ?>)"
                                        		    		    						})
                                        										    }
                                        									    },
                                        									    error: function() {
                                        									    	Swal.fire({
                                        	    		    							icon: "error",
                                        	    		    							title: "Ocurrio un error la equivalencia del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objUnidadAlternativa->nombre; ?>)"
                                        	    		    						})
                                        									    }
                                        									});
                                        								}
                                        							})
                                    							});
                                    						</script>
                                    					</td>
                                                	</tr>
                                                	<tr id="rowedit<?php echo $objEquivalencia->id; ?>" style="display: none;">
                                                		<td style="text-align: left;"><?php echo $item++; ?></td>
                                                        <td style="text-align: center;"><?php echo number_format(1, 2); ?></td>
                                                        <td style="text-align: left;"><?php echo $objUnidadAlternativa->nombre; ?></td>
                                                        <td style="text-align: center;">=</td>
                                                        <td style="text-align: right;">
                                                        	<input type="text" id="factor<?php echo $objEquivalencia->id; ?>" name="factor<?php echo $objEquivalencia->id; ?>" class="form-control" value="<?php echo number_format($objEquivalencia->factor, 2); ?>" maxlength="5" onkeypress="return filterFloat(event,this);"/>
														</td>
                                                        <td style="text-align: left;"><?php echo $objUnidadBase->nombre; ?></td>
                                                        <td style="text-align: center;">
                                                        	<a id="lnksave<?php echo $objEquivalencia->id; ?>" title="Grabar" class="btn btn-success btn-xs"><em class="fa fa-floppy-o"></em></a>
                                                        	<a id="lnkcancel<?php echo $objEquivalencia->id; ?>" title="Cancelar" class="btn btn-danger btn-xs"><em class="fa fa-times"></em></a>
                            								<script type="text/javascript">
                            									$("#lnkcancel<?php echo $objEquivalencia->id; ?>").click(function() {
                            										$("#factor<?php echo $objEquivalencia->id; ?>").val("<?php echo number_format($objEquivalencia->factor, 2); ?>");
                                                                    $("#row<?php echo $objEquivalencia->id; ?>").show();
                                            						$("#rowedit<?php echo $objEquivalencia->id; ?>").hide();
                            									});
                                        						$("#lnksave<?php echo $objEquivalencia->id; ?>").click(function() {
                                        							Swal.fire({
                                            							title: "Desea actualizar el factor de la equivalencia del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objUnidadAlternativa->nombre; ?>)",
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
                                        									    url: "./?action=listequivalence",
                                        									    data: "id="+<?php echo $objEquivalencia->id; ?>+"&accion=3&factor="+$("#factor<?php echo $objEquivalencia->id; ?>").val(),                        									    
                                        									    dataType: "html",
                                        									    beforeSend: function() {
                                            	    		    					$("#lnksave<?php echo $objEquivalencia->id; ?>").attr("disabled", "disabled");
                                            	    		    					$.blockUI();
                                            	    		    				},
                                        									    success: function(data) {
                                        									        if (data > 0) {
                                        									        	Swal.fire({
                                                    		                                icon: "success",
                                                    		                                title: "Se actualizó el factor de la equivalencia del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objUnidadAlternativa->nombre; ?>)",
                                                    										showCancelButton: false,
                                                    										confirmButtonColor: "#3085d6",
                                                    										confirmButtonText: "OK"
                                                    		                        	}).then((result) => {
                                                    		                        		window.location.href = "./index.php?view=equivalence&id=<?php echo $idInsumo; ?>";
                                                    		                        	})
                                        									        } else {
                                        									        	Swal.fire({
                                        		    		    							icon: "warning",
                                        		    		    							title: "Ocurrio un error al actualizar el factor de la equivalencia del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objUnidadAlternativa->nombre; ?>)"
                                        		    		    						})
                                        										    }
                                        									    },
                                        									    error: function() {
                                        									    	Swal.fire({
                                        	    		    							icon: "error",
                                        	    		    							title: "Ocurrio un error al actualizar el factor de la equivalencia del <?php echo $textoMensaje; ?> <?php echo $insumo; ?> (<?php echo $objUnidadAlternativa->nombre; ?>)"
                                        	    		    						})
                                        									    },
                                            	    		    				complete: function(data) {
                                            	    		    					$("#lnkdel<?php echo $objEquivalencia->id; ?>").removeAttr("disabled");
                                            	    		    					$.unblockUI();
                                            	    		    				}
                                        									});
                                        								} else {
                                        									window.location.href = "./index.php?view=equivalence&id=<?php echo $idInsumo; ?>";
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
    				document.getElementById("unidadalt").focus();
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