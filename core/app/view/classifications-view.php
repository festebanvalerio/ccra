<?php
    $estado = "1";
    if (count($_POST) > 0) {
        $estado = $_POST["estado"];
    }
    $sede = $_SESSION["sede"];
    $lstEstado = EstadoData::getAll();
    $lstClasificacion = ClasificacionData::getAll($estado, $sede);
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Clasificación</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="classifications" action="" role="form" autocomplete="off">    			
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
            		    				$("#classifications").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=classifications";
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
        								<th scope="col">Nombre</th>
        								<th scope="col">Estado</th>
        								<th scope="col">Acciones</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						    foreach ($lstClasificacion as $objClasificacion) {
        						        $objEstado = $objClasificacion->getEstado();
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo str_pad($objClasificacion->id, 8, "0", STR_PAD_LEFT); ?></td>            							
            							<td style="text-align: left;"><?php echo $objClasificacion->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>
            							<td style="text-align: left;">
            								<?php if ($objEstado->opcion) { ?>
            								<a href="#" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#exampleModal<?php echo $objClasificacion->id; ?>" title="Editar"><em class="fa fa-pencil-square-o"></em></a>
            								<a id="lnkdel<?php echo $objClasificacion->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
            								<script type="text/javascript">
                        						$("#lnkdel<?php echo $objClasificacion->id; ?>").click(function() {
                        							Swal.fire({
                            							title: "Desea anular la clasificación <?php echo $objClasificacion->nombre; ?>",
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
                        									    url: "./?action=addclassification",
                        									    dataType: "html",
                        									    data: "id=<?php echo $objClasificacion->id; ?>&accion=2",
                        									    beforeSend: function() {
                        									    	$("#lnkdel<?php echo $objClasificacion->id; ?>").attr("disabled","disabled");
                        		    		    					$.blockUI();
                        		    		    				},
                        									    success: function(data) {
                        									        if (data > 0) {
                        									        	Swal.fire({
                                    		                                icon: "success",
                                    		                                title: "Se anuló correctamente la clasificación <?php echo $objClasificacion->nombre; ?>",
                                    										showCancelButton: false,
                                    										confirmButtonColor: "#3085d6",
                                    										confirmButtonText: "OK"
                                    		                        	}).then((result) => {
                                    										window.location.href = "./index.php?view=classifications";
                                    		                        	})
                        									        } else {
                        									        	Swal.fire({
                        		    		    							icon: "warning",
                        		    		    							title: "Ocurrio un error al anular la clasificación <?php echo $objClasificacion->nombre; ?>"
                        		    		    						})
                        										    }
                        									    },
                        									    error: function() {
                        									    	Swal.fire({
                        	    		    							icon: "error",
                        	    		    							title: "Ocurrio un error al anular la clasificación <?php echo $objClasificacion->nombre; ?>"
                        	    		    						})
                        									    },
                        									    complete: function(data) {
                        									    	$("#lnkdel<?php echo $objClasificacion->id; ?>").removeAttr("disabled");
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
      			$("#nombre").focus();
    		})
        });
	</script>
	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
          		<div class="modal-header btn-primary">
            		<h5 class="modal-title" id="exampleModalLabel"><strong>Registrar Clasificación</strong></h5>
          		</div>
          		<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form-horizontal" method="post" id="modalclasificacion" action="" autocomplete="off">
								<div class="form-group">
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
                    <input type="hidden" id="sede" name="sede" value="<?php echo $sede; ?>"/>                    
                    <script type="text/javascript">
                    	$("#btnCerrar").click(function(){
                    		$("#nombre").val("");
                    	});
						$("#btnGuardar").click(function(){
							var sede = $("#sede").val();
							var nombre = $("#nombre").val();
							if (nombre === "") {
								document.getElementById("nombre").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (nombre)"
								})
							} else {
								$.ajax({
	    		    				type: "POST",
	    		    				url: "./index.php?action=addclassification",
	    		    				dataType: "html",
	    		    				data: "id=0&accion=1&sede="+sede+"&nombre="+nombre,
	    		    				beforeSend: function() {
	    		    					$("#btnGuardar").attr("disabled", "disabled");
	    		    					$("#btnCerrar").attr("disabled", "disabled");
	    		    					$.blockUI();
	    		    				},
	    		    				success: function(data) {
	    		    					document.getElementById("nombre").focus();
	    		    					if (data > 0) {
	    		        					Swal.fire({
	    		                                icon: "success",
	    										title: "Se registró correctamente la clasificación",
	    										showCancelButton: false,
	    										confirmButtonColor: "#3085d6",
	    										confirmButtonText: "OK"
	    		                        	}).then((result) => {
	    										window.location.href = "./index.php?view=classifications";
	    		                        	})
	    		        				} else if (data < 0) {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Actualmente ya existe una clasificación con el mismo nombre"
	    		    						})
	    		        				} else {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Ocurrio un error al registrar la clasificación"
	    		    						})
	    		        				}
	    		    				},
	    		    				error: function(data) {
	    		    					document.getElementById("nombre").focus();
	    		    					Swal.fire({
	    									icon: "error",
	    									title: "Ocurrio un error al registrar la clasificación"
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
	<?php foreach ($lstClasificacion as $objClasificacion) { ?>
	<script type="text/javascript">
    	$(function(){
        	$("#exampleModal<?php echo $objClasificacion->id; ?>").on("shown.bs.modal", function() {
      			$("#nombre<?php echo $objClasificacion->id; ?>").focus();
    		})
        });
	</script>
	<div class="modal fade" id="exampleModal<?php echo $objClasificacion->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
          		<div class="modal-header btn-primary">
            		<h5 class="modal-title" id="exampleModalLabel"><strong>Editar clasificación</strong></h5>
          		</div>
          		<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form-horizontal" method="post" id="modalclasificacion<?php echo $objClasificacion->id; ?>" action="" autocomplete="off">								
								<div class="form-group">
        							<div class="col-md-6 col-sm-12">
                            			<label for="nombre">Nombre :*</label>
                            			<input type="text" id="nombre<?php echo $objClasificacion->id; ?>" name="nombre<?php echo $objClasificacion->id; ?>" class="form-control" placeholder="Nombre" maxlength="100" value="<?php echo $objClasificacion->nombre; ?>" required/>
									</div>
								</div>
							</form>
						</div>
					</div>
          		</div>
          		<div class="modal-footer">
                    <button type="button" id="btnGuardar<?php echo $objClasificacion->id; ?>" class="btn btn-primary">Guardar</button>
                    <button type="button" id="btnCerrar<?php echo $objClasificacion->id; ?>" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <input type="hidden" id="sede<?php echo $objClasificacion->id; ?>" name="sede<?php echo $objClasificacion->id; ?>" value="<?php echo $sede; ?>"/>
                    <script type="text/javascript">
                        $("#btnCerrar<?php echo $objClasificacion->id; ?>").click(function(){
                        	$("#nombre<?php echo $objClasificacion->id; ?>").val("<?php echo $objClasificacion->nombre; ?>");
                    	});
						$("#btnGuardar<?php echo $objClasificacion->id; ?>").click(function(){
							var sede = $("#sede<?php echo $objClasificacion->id; ?>").val();
							var nombre = $("#nombre<?php echo $objClasificacion->id; ?>").val();
							if (nombre === "") {
								document.getElementById("nombre<?php echo $objClasificacion->id; ?>").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (nombre)"
								})
							} else {
								$.ajax({
	    		    				type: "POST",
	    		    				url: "./index.php?action=addclassification",
	    		    				dataType: "html",
	    		    				data: "id=<?php echo $objClasificacion->id; ?>&accion=1&sede="+sede+"&nombre="+nombre,
	    		    				beforeSend: function() {
	    		    					$("#btnGuardar<?php echo $objClasificacion->id; ?>").attr("disabled", "disabled");
	    		    					$("#btnCerrar<?php echo $objClasificacion->id; ?>").attr("disabled", "disabled");
	    		    					$.blockUI();
	    		    				},
	    		    				success: function(data) {
	    		    					document.getElementById("nombre<?php echo $objClasificacion->id; ?>").focus();
	    		        				if (data > 0) {
	    		        					$("#exampleModal<?php echo $objClasificacion->id; ?>").hide();
	    		        					Swal.fire({
	    		                                icon: "success",
	    										title: "Se actualizó correctamente la clasificación",
	    										showCancelButton: false,
	    										confirmButtonColor: "#3085d6",
	    										confirmButtonText: "OK"
	    		                        	}).then((result) => {
	    										window.location.href = "./index.php?view=classifications";
	    		                        	})
	    		        				} else if (data < 0) {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Actualmente ya existe una clasificación con el mismo nombre"
	    		    						})
	    		        				} else {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Ocurrio un error al actualizar la clasificación"																	
	    		    						})
	    		        				}
	    		    				},
	    		    				error: function(data) {
	    		    					document.getElementById("nombre<?php echo $objClasificacion->id; ?>").focus();
	    		    					Swal.fire({
	    									icon: "error",
	    									title: "Ocurrio un error al actualizar la clasificación"
	    								})
	    		    				},
	    		    				complete: function(data) {
	    		    					$("#btnGuardar<?php echo $objClasificacion->id; ?>").removeAttr("disabled");
	    		    					$("#btnCerrar<?php echo $objClasificacion->id; ?>").removeAttr("disabled");
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