<?php
    $estado = "1";
    if (count($_POST) > 0) {
        $estado = $_POST["estado"];
    }
    $sede = $_SESSION["sede"];
    $lstEstado = EstadoData::getAll();
    $lstMesa = MesaData::getAll($estado, $sede);
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Mesa</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="tables" action="" role="form" autocomplete="off">    			
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
            		    				$("#tables").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=tables";
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
        						    foreach ($lstMesa as $objMesa) {
        						        $objEstado = $objMesa->getEstado();        						        
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo str_pad($objMesa->id, 8, "0", STR_PAD_LEFT); ?></td>
            							<td style="text-align: left;"><?php echo $objMesa->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>
            							<td style="text-align: left;">
            								<?php if ($objEstado->opcion) { ?>
            								<a href="#" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#exampleModal<?php echo $objMesa->id; ?>" title="Editar"><em class="fa fa-pencil-square-o"></em></a>
            								<a id="lnkdel<?php echo $objMesa->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
            								<script type="text/javascript">
                        						$("#lnkdel<?php echo $objMesa->id; ?>").click(function() {
                        							Swal.fire({
                            							title: "Desea anular la mesa <?php echo $objMesa->nombre; ?>",
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
                        									    url: "./?action=addtable",
                        									    dataType: "html",
                        									    data: "id=<?php echo $objMesa->id; ?>&accion=2",                        									    
                        									    beforeSend: function() {
                        									    	$("#lnkdel<?php echo $objMesa->id; ?>").attr("disabled","disabled");
                        		    		    					$.blockUI();
                        		    		    				},
                        									    success: function(data) {
                        									        if (data > 0) {
                        									        	Swal.fire({
                                    		                                icon: "success",
                                    		                                title: "Se anuló correctamente la mesa <?php echo $objMesa->nombre; ?>",
                                    										showCancelButton: false,
                                    										confirmButtonColor: "#3085d6",
                                    										confirmButtonText: "OK"
                                    		                        	}).then((result) => {
                                    										window.location.href = "./index.php?view=tables";
                                    		                        	})
                        									        } else {
                        									        	Swal.fire({
                        		    		    							icon: "warning",
                        		    		    							title: "Ocurrio un error al anular la mesa <?php echo $objMesa->nombre; ?>"
                        		    		    						})
                        										    }
                        									    },
                        									    error: function() {
                        									    	Swal.fire({
                        	    		    							icon: "error",
                        	    		    							title: "Ocurrio un error al anular la mesa <?php echo $objMesa->nombre; ?>"
                        	    		    						})                        									        
                        									    },                        									    
                        		    		    				complete: function(data) {
                        		    		    					$("#lnkdel<?php echo $objMesa->id; ?>").removeAttr("disabled");
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
            		<h5 class="modal-title" id="exampleModalLabel"><strong>Registrar Mesa</strong></h5>
          		</div>
          		<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form-horizontal" method="post" id="modalmesa" action="" autocomplete="off">
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
	    		    				url: "./index.php?action=addtable",
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
	    		    						$("#exampleModal").hide();
	    		        					Swal.fire({
	    		                                icon: "success",
	    										title: "Se registró correctamente la mesa",
	    										showCancelButton: false,
	    										confirmButtonColor: "#3085d6",
	    										confirmButtonText: "OK"
	    		                        	}).then((result) => {
	    										window.location.href = "./index.php?view=tables";
	    		                        	})
	    		        				} else if (data < 0) {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Actualmente ya existe una mesa con el mismo nombre"
	    		    						})
	    		        				} else {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Ocurrio un error al registrar la mesa"																	
	    		    						})
	    		        				}
	    		    				},
	    		    				error: function(data) {
	    		    					document.getElementById("nombre").focus();
	    		    					Swal.fire({
	    									icon: "error",
	    									title: "Ocurrio un error al registrar la mesa"
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
	<?php foreach ($lstMesa as $objMesa) { ?>
	<script type="text/javascript">
    	$(function(){
        	$("#exampleModal<?php echo $objMesa->id; ?>").on("shown.bs.modal", function() {
      			$("#nombre<?php echo $objMesa->id; ?>").focus();
    		})
        });
	</script>
	<div class="modal fade" id="exampleModal<?php echo $objMesa->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
          		<div class="modal-header btn-primary">
            		<h5 class="modal-title" id="exampleModalLabel"><strong>Editar Mesa</strong></h5>
          		</div>
          		<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form-horizontal" method="post" id="modalmesa<?php echo $objMesa->id; ?>" action="" autocomplete="off">
								<div class="form-group">
        							<div class="col-md-6 col-sm-12">
                            			<label for="nombre">Nombre :*</label>
                            			<input type="text" id="nombre<?php echo $objMesa->id; ?>" name="nombre<?php echo $objMesa->id; ?>" class="form-control" placeholder="Nombre" maxlength="100" value="<?php echo $objMesa->nombre; ?>" required/>
									</div>
								</div>
							</form>
						</div>
					</div>
          		</div>
          		<div class="modal-footer">
                    <button type="button" id="btnGuardar<?php echo $objMesa->id; ?>" class="btn btn-primary">Guardar</button>
                    <button type="button" id="btnCerrar<?php echo $objMesa->id; ?>" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <input type="hidden" id="sede<?php echo $objMesa->id; ?>" name="sede<?php echo $objMesa->id; ?>" value="<?php echo $sede; ?>"/>
                    <script type="text/javascript">
                    	$("#btnCerrar<?php echo $objMesa->id; ?>").click(function(){
                        	$("#nombre<?php echo $objMesa->id; ?>").val("<?php echo $objMesa->nombre; ?>");                        	
                    	});
						$("#btnGuardar<?php echo $objMesa->id; ?>").click(function(){
							var sede = $("#sede<?php echo $objMesa->id; ?>").val();
							var nombre = $("#nombre<?php echo $objMesa->id; ?>").val();
							if (nombre === "") {
								document.getElementById("nombre<?php echo $objMesa->id; ?>").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (nombre)"
								})
							} else {
								$.ajax({
	    		    				type: "POST",
	    		    				url: "./index.php?action=addtable",
	    		    				dataType: "html",
	    		    				data: "id=<?php echo $objMesa->id; ?>&accion=1&sede="+sede+"&nombre="+nombre,
	    		    				beforeSend: function() {
	    		    					$("#btnGuardar<?php echo $objMesa->id; ?>").attr("disabled", "disabled");
	    		    					$("#btnCerrar<?php echo $objMesa->id; ?>").attr("disabled", "disabled");
	    		    					$.blockUI();
	    		    				},
	    		    				success: function(data) {
	    		    					document.getElementById("nombre<?php echo $objMesa->id; ?>").focus();
	    		        				if (data > 0) {
	    		        					$("#exampleModal<?php echo $objMesa->id; ?>").hide();
	    		        					Swal.fire({
	    		                                icon: "success",
	    										title: "Se actualizó correctamente la mesa",
	    										showCancelButton: false,
	    										confirmButtonColor: "#3085d6",
	    										confirmButtonText: "OK"
	    		                        	}).then((result) => {
	    										window.location.href = "./index.php?view=tables";
	    		                        	})
	    		        				} else if (data < 0) {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Actualmente ya existe una mesa con el mismo nombre"
	    		    						})
	    		        				} else {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Ocurrio un error al actualizar la mesa"																	
	    		    						})
	    		        				}
	    		    				},
	    		    				error: function(data) {
	    		    					document.getElementById("nombre<?php echo $objMesa->id; ?>").focus();
	    		    					Swal.fire({
	    									icon: "error",
	    									title: "Ocurrio un error al actualizar la mesa"
	    								})
	    		    				},
	    		    				complete: function(data) {
	    		    					$("#btnGuardar<?php echo $objMesa->id; ?>").removeAttr("disabled");
	    		    					$("#btnCerrar<?php echo $objMesa->id; ?>").removeAttr("disabled");
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