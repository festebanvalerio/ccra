<?php
    $estado = "1";
    if (count($_POST) > 0) {
        $estado = $_POST["estado"];
    }
    $empresa = $_SESSION["empresa"];
    $sede = $_SESSION["sede"];
    $lstEstado = EstadoData::getAll();
    $lstAlmacen = AlmacenData::getAll($estado, $empresa, $sede);
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Almacen</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="warehouses" action="" role="form" autocomplete="off">    			
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
            		    				$("#warehouses").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=warehouses";
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
        						    foreach ($lstAlmacen as $objAlmacen) {
        						        $objEstado = $objAlmacen->getEstado();        						        
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo str_pad($objAlmacen->id, 8, "0", STR_PAD_LEFT); ?></td>
            							<td style="text-align: left;"><?php echo $objAlmacen->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>
            							<td style="text-align: left;">
            								<?php if ($objEstado->opcion) { ?>
            								<a href="#" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#exampleModal<?php echo $objAlmacen->id; ?>" title="Editar"><em class="fa fa-pencil-square-o"></em></a>
            								<a id="lnkdel<?php echo $objAlmacen->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
            								<script type="text/javascript">
                        						$("#lnkdel<?php echo $objAlmacen->id; ?>").click(function() {
                        							Swal.fire({
                            							title: "Desea anular el almacen <?php echo $objAlmacen->nombre; ?>",
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
                        									    url: "./?action=addwarehouse",
                        									    dataType: "html",
                        									    data: "id=<?php echo $objAlmacen->id; ?>&accion=2",                        									    
                        									    beforeSend: function() {
                        									    	$("#lnkdel<?php echo $objAlmacen->id; ?>").attr("disabled","disabled");
                        		    		    					$.blockUI();
                        		    		    				},
                        									    success: function(data) {
                        									        if (data > 0) {
                        									        	Swal.fire({
                                    		                                icon: "success",
                                    		                                title: "Se anuló correctamente el almacen <?php echo $objAlmacen->nombre; ?>",
                                    										showCancelButton: false,
                                    										confirmButtonColor: "#3085d6",
                                    										confirmButtonText: "OK"
                                    		                        	}).then((result) => {
                                    										window.location.href = "./index.php?view=warehouses";
                                    		                        	})
                        									        } else {
                        									        	Swal.fire({
                        		    		    							icon: "warning",
                        		    		    							title: "Ocurrio un error al anular el almacen <?php echo $objAlmacen->nombre; ?>"
                        		    		    						})
                        										    }
                        									    },
                        									    error: function() {
                        									    	Swal.fire({
                        	    		    							icon: "error",
                        	    		    							title: "Ocurrio un error al anular el almacen <?php echo $objAlmacen->nombre; ?>"
                        	    		    						})
                        									    },
                        									    complete: function(data) {
                        									    	$("#lnkdel<?php echo $objAlmacen->id; ?>").removeAttr("disabled");
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
            		<h5 class="modal-title" id="exampleModalLabel"><strong>Registrar Almacen</strong></h5>
          		</div>
          		<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form-horizontal" method="post" id="modalalmacen" action="" autocomplete="off">
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
	    		    				url: "./index.php?action=addwarehouse",
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
	    										title: "Se registró correctamente el almacen",
	    										showCancelButton: false,
	    										confirmButtonColor: "#3085d6",
	    										confirmButtonText: "OK"
	    		                        	}).then((result) => {
	    										window.location.href = "./index.php?view=warehouses";
	    		                        	})
	    		        				} else if (data < 0) {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Actualmente ya existe un almacen con el mismo nombre"																	
	    		    						})
	    		        				} else {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Ocurrio un error al registrar el almacen"
	    		    						})
	    		        				}
	    		    				},
	    		    				error: function(data) {
	    		    					document.getElementById("nombre").focus();
	    		    					Swal.fire({
	    									icon: "error",
	    									title: "Ocurrio un error al registrar el almacen"
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
	<?php foreach ($lstAlmacen as $objAlmacen) { ?>
	<script type="text/javascript">
    	$(function(){
        	$("#exampleModal<?php echo $objAlmacen->id; ?>").on("shown.bs.modal", function() {
      			$("#nombre<?php echo $objAlmacen->id; ?>").focus();
    		})
        });
	</script>
	<div class="modal fade" id="exampleModal<?php echo $objAlmacen->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
          		<div class="modal-header btn-primary">
            		<h5 class="modal-title" id="exampleModalLabel"><strong>Editar Almacen</strong></h5>
          		</div>
          		<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form-horizontal" method="post" id="modalalmacen<?php echo $objAlmacen->id; ?>" action="" autocomplete="off">								
								<div class="form-group">
									<div class="col-md-6 col-sm-12">
                            			<label for="nombre">Nombre :*</label>
                            			<input type="text" id="nombre<?php echo $objAlmacen->id; ?>" name="nombre<?php echo $objAlmacen->id; ?>" class="form-control" placeholder="Nombre" maxlength="100" value="<?php echo $objAlmacen->nombre; ?>"/>
									</div>
								</div>
							</form>
						</div>
					</div>
          		</div>
          		<div class="modal-footer">
                    <button type="button" id="btnGuardar<?php echo $objAlmacen->id; ?>" class="btn btn-primary">Guardar</button>
                    <button type="button" id="btnCerrar<?php echo $objAlmacen->id; ?>" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <input type="hidden" id="sede<?php echo $objAlmacen->id; ?>" name="sede<?php echo $objAlmacen->id; ?>" value="<?php echo $sede; ?>"/>
                    <script type="text/javascript">
                        $("#btnCerrar<?php echo $objAlmacen->id; ?>").click(function(){
                        	$("#nombre<?php echo $objAlmacen->id; ?>").val("<?php echo $objAlmacen->nombre; ?>");
                    	});
						$("#btnGuardar<?php echo $objAlmacen->id; ?>").click(function(){
							var sede = $("#sede<?php echo $objAlmacen->id; ?>").val();
							var nombre = $("#nombre<?php echo $objAlmacen->id; ?>").val();
							if (nombre === "") {
								document.getElementById("nombre<?php echo $objAlmacen->id; ?>").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (nombre)"
								})
							} else {
								$.ajax({
	    		    				type: "POST",
	    		    				url: "./index.php?action=addwarehouse",
	    		    				dataType: "html",
	    		    				data: "id=<?php echo $objAlmacen->id; ?>&accion=1&sede="+sede+"&nombre="+nombre,
	    		    				beforeSend: function() {
	    		    					$("#btnGuardar<?php echo $objAlmacen->id; ?>").attr("disabled", "disabled");
	    		    					$("#btnCerrar<?php echo $objAlmacen->id; ?>").attr("disabled", "disabled");
	    		    					$.blockUI();
	    		    				},
	    		    				success: function(data) {
	    		    					document.getElementById("nombre<?php echo $objAlmacen->id; ?>").focus();
	    		        				if (data > 0) {
	    		        					$("#exampleModal<?php echo $objAlmacen->id; ?>").hide();
	    		        					Swal.fire({
	    		                                icon: "success",
	    										title: "Se actualizó correctamente el almacen",
	    										showCancelButton: false,
	    										confirmButtonColor: "#3085d6",
	    										confirmButtonText: "OK"
	    		                        	}).then((result) => {
	    										window.location.href = "./index.php?view=warehouses";
	    		                        	})
	    		        				} else if (data < 0) {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Actualmente ya existe un almacen con el mismo nombre"
	    		    						})
	    		        				} else {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Ocurrio un error al actualizar el almacen"																	
	    		    						})
	    		        				}
	    		    				},
	    		    				error: function(data) {
	    		    					document.getElementById("nombre<?php echo $objAlmacen->id; ?>").focus();
	    		    					Swal.fire({
	    									icon: "error",
	    									title: "Ocurrio un error al actualizar el almacen"
	    								})
	    		    				},
	    		    				complete: function(data) {
	    		    					$("#btnGuardar<?php echo $objAlmacen->id; ?>").removeAttr("disabled");
	    		    					$("#btnCerrar<?php echo $objAlmacen->id; ?>").removeAttr("disabled");
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