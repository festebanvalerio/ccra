<?php
    $id = $categoria = $tipo = 0;
    $nombre = ""; 
    $costo = $precio1 = $precio2 = "0.00";
    $mensajeOk = $mensajeError = $soloLectura = "";
    $texto = "Registrar Producto";
    $textoBoton = "Registrar";
    $lstProductoXArea = array();
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
        $objProducto = ProductoData::getById($id);
        $categoria = $objProducto->categoria;
        $tipo = $objProducto->tipo;
        $nombre = $objProducto->nombre;
        $costo = number_format($objProducto->costo, 2);
        $precio1 = number_format($objProducto->precio1, 2);
        $precio2 = number_format($objProducto->precio2, 2);
        $lstProductoXArea = ProductoAreaData::getProductoXArea($id);
        
        if ($objProducto->estado != 1) {
            $soloLectura = "disabled";
        }
        
        $texto = "Editar Producto";
        $textoBoton = "Actualizar";
        $mensajeError = "actualizar";
        $mensajeOk = "actualizó";
    } else {
        $mensajeError = "registrar";
        $mensajeOk = "registró";
    }
    $idSede = $_SESSION["sede"];
    $lstCategoria = CategoriaData::getAll(1, $idSede);
    $lstTipo = ParametroData::getAll(1, "TIPO PRODUCTO");
    $lstArea = AreaData::getAll(1, $idSede);
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newproduct" action="index.php?action=addproduct" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>
        		<div class="panel-body">
        			<div class="form-group">
        				<div class="col-md-4 col-sm-12">
        					<label for="categoria">Categoría :*</label>
        					<select id="categoria" name="categoria" class="form-control" required>
    							<option value="">SELECCIONE</option>
    							<?php foreach ($lstCategoria as $objCategoria) { ?>
								<option value="<?php echo $objCategoria->id; ?>" <?php if ($objCategoria->id == $categoria) { echo "selected"; } ?>><?php echo $objCategoria->nombre; ?></option>
      							<?php } ?>
    						</select>
    						<script type="text/javascript">
    							$("#categoria").change(function(){
    								var categoria = $("#categoria").val();
    								if (categoria !== "") {
    									document.getElementById("nombre").focus();
    								} 
    							});
    						</script>
        				</div>
        				<div class="col-md-6 col-sm-12">
        					<label for="nombre">Nombre :*</label>
        					<input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" maxlength="100" value="<?php echo $nombre; ?>" required <?php echo $soloLectura; ?>/>
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="tipo">Tipo :*</label>
        					<select id="tipo" name="tipo" class="form-control" required>
    							<option value="">SELECCIONE</option>
    							<?php foreach ($lstTipo as $objTipo) { ?>
								<option value="<?php echo $objTipo->id; ?>" <?php if ($objTipo->id == $tipo) { echo "selected"; } ?>><?php echo $objTipo->nombre; ?></option>
      							<?php } ?>
    						</select>
    						<script type="text/javascript">
    							$("#tipo").change(function(){
    								var tipo = $("#tipo").val();
    								if (tipo !== "") {
    									document.getElementById("area").focus();
    								} 
    							});
    						</script>
        				</div>
        			</div>
        			<div class="form-group">
        				<div class="col-md-4 col-sm-12">
        					<label for="area">Area :*</label>
        					<select id="area" name="area[]" class="form-control" required>
    							<?php
    							     $area = 0;
    							     foreach ($lstArea as $objArea) {
    							         if (count($lstProductoXArea) > 0) {
    							             foreach ($lstProductoXArea as $objProductoXArea) {
    							                 if ($objProductoXArea->area == $objArea->id) {
    							                     $area = $objArea->id;
    							                 }
    							             }
    							         }
    							?>
								<option value="<?php echo $objArea->id; ?>" <?php if ($objArea->id == $area) { echo "selected"; } ?>><?php echo $objArea->nombre; ?></option>
      							<?php } ?>
    						</select>
    						<script type="text/javascript">
    							$("#area").change(function(){
    								var area = $("#area").val();
    								if (area !== "") {
    									document.getElementById("costo").focus();
    								} 
    							});
    						</script>
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="costo">Precio Costo :*</label>
        					<input type="text" id="costo" name="costo" class="form-control" placeholder="0.00" maxlength="10" value="<?php echo $costo; ?>" required <?php echo $soloLectura; ?> onkeypress="return filterFloat(event, this);"/>
        					<script type="text/javascript">
        						$("#costo").click(function(){
									$("#costo").val("");
								});
        						$("#costo").blur(function(){
        							var costo = $("#costo").val();
        							if (costo === "") {
										$("#costo").val("0.00");
									} else if (isNaN(costo)) {
										$("#costo").val("0.00");
										document.getElementById("costo").focus();										
										Swal.fire({
        	    							icon: "warning",
        	    							title: "Sólo valores numéricos (costo)"
        	    						})
									}
        						});        						
        					</script>
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="precio1">Precio Venta Normal:*</label>
        					<input type="text" id="precio1" name="precio1" class="form-control" placeholder="0.00" maxlength="10" value="<?php echo $precio1; ?>" required <?php echo $soloLectura; ?> onkeypress="return filterFloat(event, this);"/>
        					<script type="text/javascript">
        						$("#precio1").click(function(){
									$("#precio1").val("");
								});
        						$("#precio1").blur(function(){        							
        							var precio1 = $("#precio1").val();
        							if (precio1 === "") {
										$("#precio1").val("0.00");
									} else if (isNaN(precio1)) {
										$("#precio1").val("0.00");
										document.getElementById("precio1").focus();										
										Swal.fire({
        	    							icon: "warning",
        	    							title: "Sólo valores numéricos (precio venta normal)"
        	    						})
									}
        						});
        					</script>
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="precio2">Precio Venta Especial:*</label>
        					<input type="text" id="precio2" name="precio2" class="form-control" placeholder="0.00" maxlength="10" value="<?php echo $precio2; ?>" required <?php echo $soloLectura; ?> onkeypress="return filterFloat(event, this);"/>
        					<script type="text/javascript">
        						$("#precio2").click(function(){
									$("#precio2").val("");
								});
        						$("#precio2").blur(function(){        							
        							var precio2 = $("#precio2").val();
        							if (precio2 === "") {
										$("#precio2").val("0.00");
									} else if (isNaN(precio2)) {
										$("#precio2").val("0.00");
										document.getElementById("precio2").focus();										
										Swal.fire({
        	    							icon: "warning",
        	    							title: "Sólo valores numéricos (precio venta especial)"
        	    						})
									}
        						});
        					</script>
        				</div>
        			</div>
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
        					<button type="submit" id="btnRegistrar" class="btn btn-success" <?php echo $soloLectura; ?>><?php echo $textoBoton; ?></button>
        					<button type="button" id="btnCancelar" class="btn btn-danger">Cancelar</button>
        					<input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>
        					<input type="hidden" id="accion" name="accion" value="1"/>
        					<input type="hidden" id="sede" name="sede" value="<?php echo $idSede; ?>"/>
        				</div>
        			</div>
        		</div>
        	</div>
    		</form>
    		<script type="text/javascript">
    			$("#btnCancelar").click(function(){
    				$.blockUI();
    				$("button").prop("disabled", true);
    				location.href = "./index.php?view=products";
    			});
    			$(function(){
    				document.getElementById("categoria").focus();    				
    				$("#newproduct").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addproduct",
    		    				dataType: "html",
    		    				data: $("#newproduct").serialize(),
    		    				beforeSend: function() {
    		    					$("#btnCancelar").attr("disabled", "disabled");
    		    					$("#btnRegistrar").attr("disabled", "disabled");
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		    					document.getElementById("categoria").focus();
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se <?php echo $mensajeOk; ?> correctamente el producto",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    										window.location.href = "./index.php?view=newproduct";
    		                        	})        					
    		        				} else if (data < 0) {    		        					
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "El producto debe estar asociado a una área"
    		    						})
    		        				} else {
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Ocurrio un error al <?php echo $mensajeError; ?> el producto"
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					document.getElementById("categoria").focus();
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al <?php echo $mensajeError; ?> el producto"
    								})
    		    				},
    		    				complete: function(data) {
    		    					$("#btnCancelar").removeAttr("disabled");
    		    					$("#btnRegistrar").removeAttr("disabled");
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