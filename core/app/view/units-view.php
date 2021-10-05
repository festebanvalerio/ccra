<?php
    $estado = "1";
    if (count($_POST) > 0) {
        $estado = $_POST["estado"];
    }
    $lstEstado = EstadoData::getAll();
    $lstUnidad = UnidadData::getAll($estado);
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Unidad Medida</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="units" action="" role="form" autocomplete="off">    			
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
    							<label for="estado">Estado :</label>
    							<select id="estado" name="estado" class="form-control">
    								<option value="">TODOS</option>
    								<?php foreach ($lstEstado as $objEstado) { ?>
    								<option value="<?php echo $objEstado->id; ?>" <?php if ($objEstado->id == $estado) { echo "selected"; } ?>><?php echo $objEstado->nombre; ?></option>
      								<?php } ?>
    							</select>
    						</div>
    						<div class="col-md-10 col-sm-12" style="padding-top: 25px;">
        						<button type="button" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>
        						<button type="button" id="btnLimpiar" class="btn btn-danger" title="Limpiar"><em class="fa fa-eraser"></em></button>
        						<button type="button" id="btnNuevo" class="btn btn-primary" title="Nuevo" data-toggle="modal" data-target="#exampleModal"><em class="fa fa-pencil-square-o"></em></button>
        						<script type="text/javascript">
        							$("#btnBuscar").click(function(){
            							$.blockUI();
            		    				$("button").prop("disabled", true);
            		    				$("#units").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=units";
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
        								<th scope="col">Abreviatura</th>
        								<th scope="col">Nombre</th>
        								<th scope="col">Estado</th>
        								<th scope="col">Acciones</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						    foreach ($lstUnidad as $objUnidad) {
        						        $objEstado = $objUnidad->getEstado();        						        
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo str_pad($objUnidad->id, 8, "0", STR_PAD_LEFT); ?></td>
            							<td style="text-align: left;"><?php echo $objUnidad->abreviatura; ?></td>
            							<td style="text-align: left;"><?php echo $objUnidad->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>
            							<td style="text-align: left;">
            								<?php if ($objEstado->opcion) { ?>
            								<a href="#" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#exampleModal<?php echo $objUnidad->id; ?>" title="Editar"><em class="fa fa-pencil-square-o"></em></a>
            								<a id="lnkdel<?php echo $objUnidad->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
            								<script type="text/javascript">
                        						$("#lnkdel<?php echo $objUnidad->id; ?>").click(function() {
                        							Swal.fire({
                            							title: "Desea anular la unidad de medida <?php echo $objUnidad->nombre; ?>",
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
                        									    url: "./?action=addunit",
                        									    data: "id=<?php echo $objUnidad->id; ?>&accion=2",                        									    
                        									    dataType: "html",
                        									    beforeSend: function() {
                        									    	$("#lnkdel<?php echo $objUnidad->id; ?>").attr("disabled","disabled");
                        		    		    					$.blockUI();
                        		    		    				},
                        									    success: function(data) {
                        									        if (data > 0) {
                        									        	Swal.fire({
                                    		                                icon: "success",
                                    		                                title: "Se anuló correctamente la unidad de medida <?php echo $objUnidad->nombre; ?>",
                                    										showCancelButton: false,
                                    										confirmButtonColor: "#3085d6",
                                    										confirmButtonText: "OK"
                                    		                        	}).then((result) => {
                                    										window.location.href = "./index.php?view=units";
                                    		                        	})
                        									        } else {
                        									        	Swal.fire({
                        		    		    							icon: "warning",
                        		    		    							title: "Ocurrio un error al anular la unidad de medida <?php echo $objUnidad->nombre; ?>"
                        		    		    						})
                        										    }
                        									    },
                        									    error: function() {
                        									    	Swal.fire({
                        	    		    							icon: "error",
                        	    		    							title: "Ocurrio un error al anular la unidad de medida <?php echo $objUnidad->nombre; ?>"
                        	    		    						})                        									        
                        									    },                        									    
                        		    		    				complete: function(data) {
                        		    		    					$("#lnkdel<?php echo $objUnidad->id; ?>").removeAttr("disabled");
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
	<!-- Modal Registrar -->
	<script type="text/javascript">
    	$(function(){
        	$("#exampleModal").on("shown.bs.modal", function() {
      			$("#abreviatura").focus();
    		})
        });
	</script>
	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
          		<div class="modal-header btn-primary">
            		<h5 class="modal-title" id="exampleModalLabel"><strong>Registrar Unidad Medida</strong></h5>
          		</div>
          		<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form-horizontal" method="post" id="modalunidad" action="" autocomplete="off">
								<div class="form-group">
									<div class="col-md-4 col-sm-12">
                            			<label for="abreviatura">Abreviatura :*</label>
                            			<input type="text" id="abreviatura" name="abreviatura" class="form-control" placeholder="Abreviatura" maxlength="5" value="" required/>
									</div>
        							<div class="col-md-6 col-sm-12">
                            			<label for="nombre">Nombre :*</label>
                            			<input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" maxlength="100" value="" required/>
									</div>
								</div>
							</form>
						</div>
					</div>
          		</div>
          		<div class="modal-footer">
                    <button type="button" id="btnGuardar" class="btn btn-primary">Guardar</button>
                    <button type="button" id="btnCerrar" class="btn btn-danger" data-dismiss="modal">Cerrar</button>                    
                    <script type="text/javascript">
                    	$("#btnCerrar").click(function(){
                    		$("#abreviatura").val("");
                        	$("#nombre").val("");
                    	});
						$("#btnGuardar").click(function(){
							var abreviatura = $("#abreviatura").val();
							var nombre = $("#nombre").val();
							if (abreviatura === "") {
								document.getElementById("abreviatura").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (abreviatura)"
								})
							} else if (nombre === "") {
								document.getElementById("nombre").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (nombre)"
								})
							} else {
								$.ajax({
	    		    				type: "POST",
	    		    				url: "./index.php?action=addunit",
	    		    				dataType: "html",
	    		    				data: "id=0&accion=1&abreviatura="+abreviatura+"&nombre="+nombre,
	    		    				beforeSend: function() {
	    		    					$("#btnGuardar").attr("disabled", "disabled");
	    		    					$("#btnCerrar").attr("disabled", "disabled");
	    		    					$.blockUI();
	    		    				},
	    		    				success: function(data) {
	    		    					document.getElementById("abreviatura").focus();
	    		    					if (data > 0) {
	    		    						$("#exampleModal").hide();
	    		        					Swal.fire({
	    		                                icon: "success",
	    										title: "Se registró correctamente la unidad de medida",
	    										showCancelButton: false,
	    										confirmButtonColor: "#3085d6",
	    										confirmButtonText: "OK"
	    		                        	}).then((result) => {
	    										window.location.href = "./index.php?view=units";
	    		                        	})
	    		        				} else if (data < 0) {	    		        					
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Actualmente ya existe una unidad de unidad con los mismos datos"																	
	    		    						})
	    		        				} else {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Ocurrio un error al registrar la unidad de medida"																	
	    		    						})
	    		        				}
	    		    				},
	    		    				error: function(data) {
	    		    					document.getElementById("abreviatura").focus();
	    		    					Swal.fire({
	    									icon: "error",
	    									title: "Ocurrio un error al registrar la unidad de medida"
	    								})
	    		    				},
	    		    				complete: function(data) {
	    		    					$("#btnGuardar").removeAttr("disabled");
	    		    					$("#btnCerrar").removeAttr("disabled");
	    		    					$.unblockUI();
	    		    				}
	    		    			});
							}
						});
                    </script>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal Editar -->
	<?php foreach ($lstUnidad as $objUnidad) { ?>
	<script type="text/javascript">
    	$(function(){
        	$("#exampleModal<?php echo $objUnidad->id; ?>").on("shown.bs.modal", function() {
      			$("#abreviatura<?php echo $objUnidad->id; ?>").focus();
    		})
        });
	</script>
	<div class="modal fade" id="exampleModal<?php echo $objUnidad->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
          		<div class="modal-header btn-primary">
            		<h5 class="modal-title" id="exampleModalLabel"><strong>Editar Unidad Medida</strong></h5>
          		</div>
          		<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form-horizontal" method="post" id="modalunidad<?php echo $objUnidad->id; ?>" action="" autocomplete="off">								
								<div class="form-group">
									<div class="col-md-4 col-sm-12">
                            			<label for="abreviatura">Abreviatura :*</label>
                            			<input type="text" id="abreviatura<?php echo $objUnidad->id; ?>" name="abreviatura<?php echo $objUnidad->id; ?>" class="form-control" placeholder="Abreviatura" maxlength="5" value="<?php echo $objUnidad->abreviatura; ?>" required/>
									</div>
        							<div class="col-md-6 col-sm-12">
                            			<label for="nombre">Nombre :*</label>
                            			<input type="text" id="nombre<?php echo $objUnidad->id; ?>" name="nombre<?php echo $objUnidad->id; ?>" class="form-control" placeholder="Nombre" maxlength="100" value="<?php echo $objUnidad->nombre; ?>" required/>
									</div>
								</div>
							</form>
						</div>
					</div>
          		</div>
          		<div class="modal-footer">
                    <button type="button" id="btnGuardar<?php echo $objUnidad->id; ?>" class="btn btn-primary">Guardar</button>
                    <button type="button" id="btnCerrar<?php echo $objUnidad->id; ?>" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <script type="text/javascript">
                        $("#btnCerrar<?php echo $objUnidad->id; ?>").click(function(){
                        	$("#abreviatura<?php echo $objUnidad->id; ?>").val("<?php echo $objUnidad->abreviatura; ?>");
                        	$("#nombre<?php echo $objUnidad->id; ?>").val("<?php echo $objUnidad->nombre; ?>");
                    	});
						$("#btnGuardar<?php echo $objUnidad->id; ?>").click(function(){
							var abreviatura = $("#abreviatura<?php echo $objUnidad->id; ?>").val();
							var nombre = $("#nombre<?php echo $objUnidad->id; ?>").val();							
							if (abreviatura === "") {
								document.getElementById("abreviatura<?php echo $objUnidad->id; ?>").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (abreviatura)"
								})
							} else if (nombre === "") {
								document.getElementById("nombre<?php echo $objUnidad->id; ?>").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (nombre)"
								})
							} else {
								$.ajax({
	    		    				type: "POST",
	    		    				url: "./index.php?action=addunit",
	    		    				dataType: "html",
	    		    				data: "id=<?php echo $objUnidad->id; ?>&accion=1&abreviatura="+abreviatura+"&nombre="+nombre,
	    		    				beforeSend: function() {
	    		    					$("#btnGuardar<?php echo $objUnidad->id; ?>").attr("disabled", "disabled");
	    		    					$("#btnCerrar<?php echo $objUnidad->id; ?>").attr("disabled", "disabled");
	    		    					$.blockUI();
	    		    				},
	    		    				success: function(data) {
	    		    					document.getElementById("abreviatura<?php echo $objUnidad->id; ?>").focus();
	    		        				if (data > 0) {
	    		        					$("#exampleModal<?php echo $objUnidad->id; ?>").hide();
	    		        					Swal.fire({
	    		                                icon: "success",
	    										title: "Se actualizó correctamente la unidad de medida",
	    										showCancelButton: false,
	    										confirmButtonColor: "#3085d6",
	    										confirmButtonText: "OK"
	    		                        	}).then((result) => {
	    										window.location.href = "./index.php?view=units";
	    		                        	})
	    		        				} else if (data < 0) {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Actualmente ya existe una unidad de unidad con los mismos datos"																	
	    		    						})
	    		        				} else {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Ocurrio un error al actualizar la unidad de medida"																	
	    		    						})
	    		        				}
	    		    				},
	    		    				error: function(data) {
	    		    					document.getElementById("abreviatura<?php echo $objUnidad->id; ?>").focus();
	    		    					Swal.fire({
	    									icon: "error",
	    									title: "Ocurrio un error al actualizar la unidad de medida"
	    								})
	    		    				},
	    		    				complete: function(data) {
	    		    					$("#btnGuardar<?php echo $objUnidad->id; ?>").removeAttr("disabled");
	    		    					$("#btnCerrar<?php echo $objUnidad->id; ?>").removeAttr("disabled");
	    		    					$.unblockUI();
	    		    				}
	    		    			});
							}
						});
                    </script>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
</section>