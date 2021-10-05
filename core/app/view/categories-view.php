<?php
    $estado = "1";
    if (count($_POST) > 0) {
        $estado = $_POST["estado"];
    }
    $sede = $_SESSION["sede"];
    $lstEstado = EstadoData::getAll();
    $lstCategoria = CategoriaData::getAll($estado, $sede);
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Categoría</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="categories" action="" role="form" autocomplete="off">    			
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
            		    				$("#categories").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=categories";
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
        								<th scope="col">Es Menú</th>
        								<th scope="col">Estado</th>
        								<th scope="col">Acciones</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						    foreach ($lstCategoria as $objCategoria) {
        						        $objEstado = $objCategoria->getEstado();
        						        $indicador = "NO";
        						        if ($objCategoria->indicador == 1) {
        						            $indicador= "SI";
        						        }
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo str_pad($objCategoria->id, 8, "0", STR_PAD_LEFT); ?></td>            							
            							<td style="text-align: left;"><?php echo $objCategoria->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $indicador; ?></td>
            							<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>
            							<td style="text-align: left;">
            								<?php if ($objEstado->opcion) { ?>
            								<a href="#" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#exampleModal<?php echo $objCategoria->id; ?>" title="Editar"><em class="fa fa-pencil-square-o"></em></a>
            								<a id="lnkdel<?php echo $objCategoria->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
            								<script type="text/javascript">
                        						$("#lnkdel<?php echo $objCategoria->id; ?>").click(function() {
                        							Swal.fire({
                            							title: "Desea anular la categoría <?php echo $objCategoria->nombre; ?>",
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
                        									    url: "./?action=addcategory",
                        									    dataType: "html",
                        									    data: "id=<?php echo $objCategoria->id; ?>&accion=2",
                        									    beforeSend: function() {
                        									    	$("#lnkdel<?php echo $objCategoria->id; ?>").attr("disabled","disabled");
                        		    		    					$.blockUI();
                        		    		    				},
                        									    success: function(data) {
                        									        if (data > 0) {
                        									        	Swal.fire({
                                    		                                icon: "success",
                                    		                                title: "Se anuló correctamente la categoría <?php echo $objCategoria->nombre; ?>",
                                    										showCancelButton: false,
                                    										confirmButtonColor: "#3085d6",
                                    										confirmButtonText: "OK"
                                    		                        	}).then((result) => {
                                    										window.location.href = "./index.php?view=categories";
                                    		                        	})
                        									        } else {
                        									        	Swal.fire({
                        		    		    							icon: "warning",
                        		    		    							title: "Ocurrio un error al anular la categoría <?php echo $objCategoria->nombre; ?>"
                        		    		    						})
                        										    }
                        									    },
                        									    error: function() {
                        									    	Swal.fire({
                        	    		    							icon: "error",
                        	    		    							title: "Ocurrio un error al anular la categoría <?php echo $objCategoria->nombre; ?>"
                        	    		    						})
                        									    },
                        									    complete: function(data) {
                        									    	$("#lnkdel<?php echo $objCategoria->id; ?>").removeAttr("disabled");
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
            		<h5 class="modal-title" id="exampleModalLabel"><strong>Registrar Categoría</strong></h5>
          		</div>
          		<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form-horizontal" method="post" id="modalcategoria" action="" autocomplete="off">
								<div class="form-group">
        							<div class="col-md-6 col-sm-12">
                            			<label for="nombre">Nombre :*</label>
                            			<input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" maxlength="100" value="" required/>
									</div>
									<div class="col-md-2 col-sm-12">
                            			<label for="indicador">Es Menú :</label>
            							<select id="indicador" name="indicador" class="form-control">
            								<option value="0">NO</option>
            								<option value="1">SI</option>
            							</select>
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
                    		$("#indicador").val("0");
                    	});
						$("#btnGuardar").click(function(){
							var sede = $("#sede").val();
							var nombre = $("#nombre").val();
							var indicador = $("#indicador").val();							
							if (nombre === "") {
								document.getElementById("nombre").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (nombre)"
								})
							} else {
								$.ajax({
	    		    				type: "POST",
	    		    				url: "./index.php?action=addcategory",
	    		    				dataType: "html",
	    		    				data: "id=0&accion=1&sede="+sede+"&nombre="+nombre+"&indicador="+indicador,
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
	    										title: "Se registró correctamente la categoría",
	    										showCancelButton: false,
	    										confirmButtonColor: "#3085d6",
	    										confirmButtonText: "OK"
	    		                        	}).then((result) => {
	    										window.location.href = "./index.php?view=categories";
	    		                        	})
	    		        				} else if (data < 0) {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Actualmente ya existe una categoría con el mismo nombre"
	    		    						})
	    		        				} else {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Ocurrio un error al registrar la categoría"
	    		    						})
	    		        				}
	    		    				},
	    		    				error: function(data) {
	    		    					document.getElementById("nombre").focus();
	    		    					Swal.fire({
	    									icon: "error",
	    									title: "Ocurrio un error al registrar la categoría"
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
	<?php foreach ($lstCategoria as $objCategoria) { ?>
	<script type="text/javascript">
    	$(function(){
        	$("#exampleModal<?php echo $objCategoria->id; ?>").on("shown.bs.modal", function() {
      			$("#nombre<?php echo $objCategoria->id; ?>").focus();
    		})
        });
	</script>
	<div class="modal fade" id="exampleModal<?php echo $objCategoria->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
          		<div class="modal-header btn-primary">
            		<h5 class="modal-title" id="exampleModalLabel"><strong>Editar Categoría</strong></h5>
          		</div>
          		<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form-horizontal" method="post" id="modalcategoria<?php echo $objCategoria->id; ?>" action="" autocomplete="off">								
								<div class="form-group">
        							<div class="col-md-6 col-sm-12">
                            			<label for="nombre">Nombre :*</label>
                            			<input type="text" id="nombre<?php echo $objCategoria->id; ?>" name="nombre<?php echo $objCategoria->id; ?>" class="form-control" placeholder="Nombre" maxlength="100" value="<?php echo $objCategoria->nombre; ?>" required/>
									</div>
									<div class="col-md-2 col-sm-12">
                    					<label for="indicador">Es Menú :</label>
            							<select id="indicador<?php echo $objCategoria->id; ?>" name="indicador<?php echo $objCategoria->id; ?>" class="form-control">
            								<option value="0" <?php if ($objCategoria->indicador == 0) { echo " selected"; } ?>>NO</option>
            								<option value="1" <?php if ($objCategoria->indicador == 1) { echo " selected"; } ?>>SI</option>
            							</select>
                    				</div>
								</div>
							</form>
						</div>
					</div>
          		</div>
          		<div class="modal-footer">
                    <button type="button" id="btnGuardar<?php echo $objCategoria->id; ?>" class="btn btn-primary">Guardar</button>
                    <button type="button" id="btnCerrar<?php echo $objCategoria->id; ?>" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <input type="hidden" id="sede<?php echo $objCategoria->id; ?>" name="sede<?php echo $objCategoria->id; ?>" value="<?php echo $sede; ?>"/>
                    <script type="text/javascript">
                        $("#btnCerrar<?php echo $objCategoria->id; ?>").click(function(){
                        	$("#nombre<?php echo $objCategoria->id; ?>").val("<?php echo $objCategoria->nombre; ?>");
                        	$("#indicador<?php echo $objCategoria->id; ?>").val("<?php echo $objCategoria->indicador; ?>");
                    	});
						$("#btnGuardar<?php echo $objCategoria->id; ?>").click(function(){
							var sede = $("#sede<?php echo $objCategoria->id; ?>").val();
							var nombre = $("#nombre<?php echo $objCategoria->id; ?>").val();
							var indicador = $("#indicador<?php echo $objCategoria->id; ?>").val();
							if (nombre === "") {
								document.getElementById("nombre<?php echo $objCategoria->id; ?>").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (nombre)"
								})
							} else {
								$.ajax({
	    		    				type: "POST",
	    		    				url: "./index.php?action=addcategory",
	    		    				dataType: "html",
	    		    				data: "id=<?php echo $objCategoria->id; ?>&accion=1&sede="+sede+"&nombre="+nombre+"&indicador="+indicador,
	    		    				beforeSend: function() {
	    		    					$("#btnGuardar<?php echo $objCategoria->id; ?>").attr("disabled", "disabled");
	    		    					$("#btnCerrar<?php echo $objCategoria->id; ?>").attr("disabled", "disabled");
	    		    					$.blockUI();
	    		    				},
	    		    				success: function(data) {
	    		    					document.getElementById("nombre<?php echo $objCategoria->id; ?>").focus();
	    		        				if (data > 0) {
	    		        					$("#exampleModal<?php echo $objCategoria->id; ?>").hide();
	    		        					Swal.fire({
	    		                                icon: "success",
	    										title: "Se actualizó correctamente la categoría",
	    										showCancelButton: false,
	    										confirmButtonColor: "#3085d6",
	    										confirmButtonText: "OK"
	    		                        	}).then((result) => {
	    										window.location.href = "./index.php?view=categories";
	    		                        	})
	    		        				} else if (data < 0) {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Actualmente ya existe una categoría con el mismo nombre"
	    		    						})
	    		        				} else {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Ocurrio un error al actualizar la categoría"																	
	    		    						})
	    		        				}
	    		    				},
	    		    				error: function(data) {
	    		    					document.getElementById("nombre<?php echo $objCategoria->id; ?>").focus();
	    		    					Swal.fire({
	    									icon: "error",
	    									title: "Ocurrio un error al actualizar la categoría"
	    								})
	    		    				},
	    		    				complete: function(data) {
	    		    					$("#btnGuardar<?php echo $objCategoria->id; ?>").removeAttr("disabled");
	    		    					$("#btnCerrar<?php echo $objCategoria->id; ?>").removeAttr("disabled");
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