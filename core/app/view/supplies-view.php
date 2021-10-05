<?php
    $sede = $_SESSION["sede"];
    $estado = "1";
    $clasificacion = "";
    if (count($_POST) > 0) {
        $clasificacion = $_POST["clasificacion"];
        $estado = $_POST["estado"];
        
        $_SESSION["supplies_clasificacion"] = $clasificacion;
        $_SESSION["supplies_estado"] = $estado;
    } else if (isset($_SESSION["supplies_clasificacion"]) && isset($_SESSION["supplies_estado"])) {
        $clasificacion = $_SESSION["supplies_clasificacion"];
        $estado = $_SESSION["supplies_estado"];
    }
    $lstUnidad = UnidadData::getAll(1);
    $lstEstado = EstadoData::getAll();
    $lstClasificacion = ClasificacionData::getAll(1, $sede);
    $lstInsumo = InsumoData::getAll($estado, $sede, 1, $clasificacion);
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Insumo</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="supplies" action="" role="form" autocomplete="off">    			
            			<div class="form-group">
            				<div class="col-md-4 col-sm-12">
            					<label for="clasificacion">Clasificación :</label>
    							<select id="clasificacion" name="clasificacion" class="form-control">
    								<option value="">TODOS</option>
    								<?php foreach ($lstClasificacion as $objClasificacion) { ?>
    								<option value="<?php echo $objClasificacion->id; ?>" <?php if ($objClasificacion->id == $clasificacion) { echo "selected"; } ?>><?php echo $objClasificacion->nombre; ?></option>
      								<?php } ?>
    							</select>
    						</div>
            				<div class="col-md-2 col-sm-12">
    							<label for="estado">Estado :</label>
    							<select id="estado" name="estado" class="form-control">
    								<option value="">TODOS</option>
    								<?php foreach ($lstEstado as $objEstado) { ?>
    								<option value="<?php echo $objEstado->id; ?>" <?php if ($objEstado->id == $estado) { echo "selected"; } ?>><?php echo $objEstado->nombre; ?></option>
      								<?php } ?>
    							</select>
    						</div>
    						<div class="col-md-6 col-sm-12" style="padding-top: 25px;">
        						<button type="button" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>
        						<button type="button" id="btnLimpiar" class="btn btn-danger" title="Limpiar"><em class="fa fa-eraser"></em></button>
        						<button type="button" id="btnNuevo" class="btn btn-primary" title="Nuevo" data-toggle="modal" data-target="#exampleModal"><em class="fa fa-pencil-square-o"></em></button>
        						<script type="text/javascript">
        							$("#btnBuscar").click(function(){
            							$.blockUI();
            		    				$("button").prop("disabled", true);
            		    				$("#supplies").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            							$.post("index.php?action=deletesearch", {
                							opcion: "supplies"
                                        }, function (data) {
                                        	location.href = "./index.php?view=supplies";
                                        });            		    				
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
        								<th scope="col">Unidad Medida</th>
        								<th scope="col">Nombre</th>
        								<th scope="col">Clasificación</th>
        								<th scope="col" style="text-align: right;">Costo</th>
        								<th scope="col">Estado</th>
        								<th scope="col">Acciones</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						    foreach ($lstInsumo as $objInsumo) {
        						        $objUnidad = $objInsumo->getUnidad();
        						        $objEstado = $objInsumo->getEstado();
        						        $objClasificacion = $objInsumo->getClasificacion();
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo str_pad($objInsumo->id, 8, "0", STR_PAD_LEFT); ?></td>
            							<td style="text-align: left;"><?php echo $objUnidad->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objInsumo->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objClasificacion->nombre; ?></td>
            							<td style="text-align: right;"><?php echo number_format($objInsumo->costo, 2); ?></td>
            							<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>
            							<td style="text-align: left;">
            								<?php if ($objEstado->opcion) { ?>
            								<a href="#" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#exampleModal<?php echo $objInsumo->id; ?>" title="Editar"><em class="fa fa-pencil-square-o"></em></a>
            								<a href="index.php?view=suppliewarehouse&id=<?php echo $objInsumo->id; ?>" title="Stock" class="btn btn-success btn-xs"><em class="fa fa-sign-out"></em></a>
            								<a href="index.php?view=equivalence&id=<?php echo $objInsumo->id; ?>" title="Equivalencia" class="btn btn-primary btn-xs"><em class="fa fa-pencil-square-o"></em></a>            								
            								<a id="lnkdel<?php echo $objInsumo->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
            								<script type="text/javascript">
                        						$("#lnkdel<?php echo $objInsumo->id; ?>").click(function() {
                        							Swal.fire({
                            							title: "Desea anular el insumo <?php echo $objInsumo->nombre; ?>",
                        								icon: "warning",
                        								showCancelButton: true,
                        								confirmButtonColor: "#3085d6",
                        								cancelButtonColor: "#d33",
                        								confirmButtonText: "Anular",
                        								cancelButtonText: "Cancelar"
                        							}).then((result) => {
                        								if (result.isConfirmed) {
                        									$.ajax({
                        		    		    				type: "POST",
                        		    		    				url: "./index.php?action=addsupplie",
                        		    		    				dataType: "html",
                        		    		    				data: "id=<?php echo $objInsumo->id; ?>&accion=2",
                        		    		    				beforeSend: function() {
                        		    		    					$("#lnkdel<?php echo $objInsumo->id; ?>").attr("disabled","disabled");
                        		    		    					$.blockUI();
                        		    		    				},
                        		    		    				success: function(data) {
                        		    		        				if (data > 0) {
                        		    		        					Swal.fire({
                        		    		                                icon: "success",
                        		    		                                title: "Se anuló correctamente el insumo <?php echo $objInsumo->nombre; ?>",
                        		    										showCancelButton: false,
                        		    										confirmButtonColor: "#3085d6",
                        		    										confirmButtonText: "OK"
                        		    		                        	}).then((result) => {
                        		    										window.location.href = "./index.php?view=supplies";
                        		    		                        	})
                        		    		        				} else {
                        		    		        					Swal.fire({
                        		    		    							icon: "warning",
                        		    		    							title: "Ocurrio un error al anular el insumo <?php echo $objInsumo->nombre; ?>"
                        		    		    						})
                        		    		        				}
                        		    		    				},
                        		    		    				error: function(data) {
                        		    		    					Swal.fire({
                        		    									icon: "error",
                        		    									title: "Ocurrio un error al anular el insumo <?php echo $objInsumo->nombre; ?>"
                        		    								})
                        		    		    				},
                        		    		    				complete: function(data) {
                        		    		    					$("#lnkdel<?php echo $objInsumo->id; ?>").removeAttr("disabled");
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
      			$("#unidad").focus();
    		})
        });
	</script>
	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
          		<div class="modal-header btn-primary">
            		<h5 class="modal-title" id="exampleModalLabel"><strong>Registrar Insumo</strong></h5>
          		</div>
          		<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form-horizontal" method="post" id="modalinsumo" action="" autocomplete="off">
								<div class="form-group">
									<div class="col-md-4 col-sm-12">
                    					<label for="unidad">Unidad Medida :*</label>
                    					<select id="unidad" name="unidad" class="form-control" required>
                							<option value="">SELECCIONE</option>
                							<?php foreach ($lstUnidad as $objUnidad) { ?>
            								<option value="<?php echo $objUnidad->id; ?>"><?php echo $objUnidad->nombre; ?></option>
                  							<?php } ?>
                						</select>
                					</div>
                    				<div class="col-md-8 col-sm-12">
                    					<label for="nombre">Nombre :*</label>
                    					<input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" maxlength="100" value="" required/>
                    				</div>
								</div>
								<div class="form-group">
                    				<div class="col-md-4 col-sm-12">
                        				<label for="costo">Costo :*</label>
            							<input type="text" id="costo" name="costo" class="form-control" placeholder="0.00" maxlength="6" value="" dir="rtl" required onkeypress="return filterFloat(event,this);"/>
                        			</div>
                        			<div class="col-md-8 col-sm-12">
                    					<label for="clasificacion">Clasificación :*</label>
                    					<select id="clasificacion" name="clasificacion" class="form-control" required>
                							<option value="">SELECCIONE</option>
                							<?php foreach ($lstClasificacion as $objClasificacion) { ?>
            								<option value="<?php echo $objClasificacion->id; ?>"><?php echo $objClasificacion->nombre; ?></option>
                  							<?php } ?>
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
                    		$("#unidad").val("");
                        	$("#nombre").val("");
                        	$("#costo").val("");
                        	$("#clasificacion").val("");
                    	});
						$("#btnGuardar").click(function(){
							var sede = $("#sede").val();
							var unidad = $("#unidad").val();
							var nombre = $("#nombre").val();
							var costo = $("#costo").val();
							var clasificacion = $("#clasificacion").val();
							if (unidad === "") {
								document.getElementById("unidad").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (unidad medida)"
								})
							} else if (nombre === "") {
								document.getElementById("nombre").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (nombre)"
								})
							} else if (costo === "") {
								document.getElementById("costo").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (costo)"
								})
							} else if (isNaN(costo)) {
								document.getElementById("costo").focus();
								$("#costo").val("");
								Swal.fire({
									icon: "warning",
									title: "Sólo valores numéricos (costo)"
								})
							} else if (clasificacion === "") {
								document.getElementById("clasificacion").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (clasificación)"
								})
							} else {
								$.ajax({
	    		    				type: "POST",
	    		    				url: "./index.php?action=addsupplie",
	    		    				dataType: "html",
	    		    				data: "id=0&accion=1&sede="+sede+"&unidad="+unidad+"&nombre="+nombre+"&costo="+costo+"&indicador=1&clasificacion="+clasificacion,
	    		    				beforeSend: function() {
	    		    					$("#btnGuardar").attr("disabled", "disabled");
	    		    					$("#btnCerrar").attr("disabled", "disabled");
	    		    					$.blockUI();
	    		    				},
	    		    				success: function(data) {
	    		    					document.getElementById("unidad").focus();
	    		    					if (data > 0) {
	    		    						$("#exampleModal").hide();
	    		        					Swal.fire({
	    		                                icon: "success",
	    										title: "Se registró correctamente el insumo",
	    										showCancelButton: false,
	    										confirmButtonColor: "#3085d6",
	    										confirmButtonText: "OK"
	    		                        	}).then((result) => {
	    										window.location.href = "./index.php?view=supplies";
	    		                        	})
	    		        				} else if (data < 0) {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Actualmente ya existe un insumo con los mismos datos"																	
	    		    						})
	    		        				} else {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Ocurrio un error al registrar el insumo"
	    		    						})
	    		        				}
	    		    				},
	    		    				error: function(data) {
	    		    					document.getElementById("unidad").focus();
	    		    					Swal.fire({
	    									icon: "error",
	    									title: "Ocurrio un error al registrar el insumo"
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
	<?php foreach ($lstInsumo as $objInsumo) { ?>
	<script type="text/javascript">
    	$(function(){
        	$("#exampleModal<?php echo $objInsumo->id; ?>").on("shown.bs.modal", function() {
      			$("#unidad<?php echo $objInsumo->id; ?>").focus();
    		})
        });
	</script>
	<div class="modal fade" id="exampleModal<?php echo $objInsumo->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
          		<div class="modal-header btn-primary">
            		<h5 class="modal-title" id="exampleModalLabel"><strong>Editar Insumo</strong></h5>
          		</div>
          		<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form-horizontal" method="post" id="modalinsumo<?php echo $objInsumo->id; ?>" action="" autocomplete="off">								
								<div class="form-group">
									<div class="col-md-4 col-sm-12">
                    					<label for="unidad">Unidad Medida :*</label>
                    					<select id="unidad<?php echo $objInsumo->id; ?>" name="unidad<?php echo $objInsumo->id; ?>" class="form-control" required>
                							<option value="">SELECCIONE</option>
                							<?php foreach ($lstUnidad as $objUnidad) { ?>
            								<option value="<?php echo $objUnidad->id; ?>" <?php if ($objUnidad->id == $objInsumo->unidad) { echo "selected"; } ?>><?php echo $objUnidad->nombre; ?></option>
                  							<?php } ?>
                						</select>
                					</div>
                    				<div class="col-md-8 col-sm-12">
                    					<label for="nombre">Nombre :*</label>
                    					<input type="text" id="nombre<?php echo $objInsumo->id; ?>" name="nombre<?php echo $objInsumo->id; ?>" class="form-control" placeholder="Nombre" maxlength="100" value="<?php echo $objInsumo->nombre; ?>" required/>
                    				</div>
                    			</div>
                    			<div class="form-group">
                    				<div class="col-md-4 col-sm-12">
                        				<label for="costo">Costo :*</label>
            							<input type="text" id="costo<?php echo $objInsumo->id; ?>" name="costo<?php echo $objInsumo->id; ?>" class="form-control" placeholder="0.00" maxlength="8" value="<?php echo number_format($objInsumo->costo, 2); ?>" dir="rtl" required onkeypress="return filterFloat(event,this);"/>
                        			</div>
                        			<div class="col-md-8 col-sm-12">
                    					<label for="clasificacion">Clasificación :*</label>
                    					<select id="clasificacion<?php echo $objInsumo->id; ?>" name="clasificacion<?php echo $objInsumo->id; ?>" class="form-control" required>
                							<option value="">SELECCIONE</option>
                							<?php foreach ($lstClasificacion as $objClasificacion) { ?>
            								<option value="<?php echo $objClasificacion->id; ?>" <?php if ($objClasificacion->id == $objInsumo->clasificacion) { echo "selected"; } ?>><?php echo $objClasificacion->nombre; ?></option>
                  							<?php } ?>
                						</select>
                					</div>
								</div>
							</form>
						</div>
					</div>
          		</div>
          		<div class="modal-footer">
                    <button type="button" id="btnGuardar<?php echo $objInsumo->id; ?>" class="btn btn-primary">Guardar</button>
                    <button type="button" id="btnCerrar<?php echo $objInsumo->id; ?>" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <input type="hidden" id="sede<?php echo $objInsumo->id; ?>" name="sede<?php echo $objInsumo->id; ?>" value="<?php echo $sede; ?>"/>
                    <script type="text/javascript">
                        $("#btnCerrar<?php echo $objInsumo->id; ?>").click(function(){
                        	$("#unidad<?php echo $objInsumo->id; ?>").val("<?php echo $objInsumo->unidad; ?>");
                        	$("#nombre<?php echo $objInsumo->id; ?>").val("<?php echo $objInsumo->nombre; ?>");
                        	$("#costo<?php echo $objInsumo->id; ?>").val("<?php echo number_format($objInsumo->costo, 2); ?>");
                        	$("#clasificacion<?php echo $objInsumo->id; ?>").val("<?php echo $objInsumo->clasificacion; ?>");
                    	});
						$("#btnGuardar<?php echo $objInsumo->id; ?>").click(function(){
							var sede = $("#sede<?php echo $objInsumo->id; ?>").val();
							var unidad = $("#unidad<?php echo $objInsumo->id; ?>").val();
							var nombre = $("#nombre<?php echo $objInsumo->id; ?>").val();
							var costo = $("#costo<?php echo $objInsumo->id; ?>").val();
							var clasificacion = $("#clasificacion<?php echo $objInsumo->id; ?>").val();
							if (unidad === "") {
								document.getElementById("unidad<?php echo $objInsumo->id; ?>").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (unidad medida)"
								})
							} else if (nombre === "") {
								document.getElementById("nombre<?php echo $objInsumo->id; ?>").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (nombre)"
								})
							} else if (costo === "") {
								document.getElementById("costo<?php echo $objInsumo->id; ?>").focus();								
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (costo)"
								})
							} else if (isNaN(costo)) {
								document.getElementById("costo<?php echo $objInsumo->id; ?>").focus();
								$("#costo<?php echo $objInsumo->id; ?>").val("");
								Swal.fire({
									icon: "warning",
									title: "Sólo valores numéricos (costo)"
								})
							} else if (clasificacion === "") {
								document.getElementById("clasificacion<?php echo $objInsumo->id; ?>").focus();
								Swal.fire({
									icon: "warning",
									title: "Campo obligatorio (clasificación)"
								})
							} else {
								$.ajax({
	    		    				type: "POST",
	    		    				url: "./index.php?action=addsupplie",
	    		    				dataType: "html",
	    		    				data: "id=<?php echo $objInsumo->id; ?>&accion=1&sede="+sede+"&unidad="+unidad+"&nombre="+nombre+"&costo="+costo+"&indicador=1&clasificacion="+clasificacion,
	    		    				beforeSend: function() {
	    		    					$("#btnGuardar<?php echo $objInsumo->id; ?>").attr("disabled", "disabled");
	    		    					$("#btnCerrar<?php echo $objInsumo->id; ?>").attr("disabled", "disabled");
	    		    					$.blockUI();
	    		    				},
	    		    				success: function(data) {
	    		    					document.getElementById("unidad<?php echo $objInsumo->id; ?>").focus();
	    		        				if (data > 0) {
	    		        					$("#exampleModal<?php echo $objInsumo->id; ?>").hide();
	    		        					Swal.fire({
	    		                                icon: "success",
	    										title: "Se actualizó correctamente el insumo",
	    										showCancelButton: false,
	    										confirmButtonColor: "#3085d6",
	    										confirmButtonText: "OK"
	    		                        	}).then((result) => {
	    										window.location.href = "./index.php?view=supplies";
	    		                        	})
	    		        				} else if (data < 0) {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Actualmente ya existe un insumo con los mismos datos"
	    		    						})
	    		        				} else {
	    		        					Swal.fire({
	    		    							icon: "warning",
	    		    							title: "Ocurrio un error al actualizar el insumo"																	
	    		    						})
	    		        				}
	    		    				},
	    		    				error: function(data) {
	    		    					document.getElementById("unidad<?php echo $objInsumo->id; ?>").focus();
	    		    					Swal.fire({
	    									icon: "error",
	    									title: "Ocurrio un error al actualizar el insumo"
	    								})
	    		    				},
	    		    				complete: function(data) {
	    		    					$("#btnGuardar<?php echo $objInsumo->id; ?>").removeAttr("disabled");
	    		    					$("#btnCerrar<?php echo $objInsumo->id; ?>").removeAttr("disabled");
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