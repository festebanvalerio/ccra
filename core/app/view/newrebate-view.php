<?php
    $idSede = $_SESSION["sede"];
    $id = $categoria = $producto = 0;
    $fechaInicio = $fechaFin = ""; 
    $porcentaje = "0.00";
    $soloLectura = "";
    $texto = "Registrar Descuento Programado";
    $textoBoton = "Registrar";
    $msgOk = "registró";
    $msgError = "registrar";
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
        $objDescuentoProgramado = DescuentoProgramadoData::getById($id);
        $producto = $objDescuentoProgramado->producto;
        $categoria = $objDescuentoProgramado->getProducto()->categoria;
        $fechaInicio = date("d/m/Y", strtotime($objDescuentoProgramado->fecha_inicio));
        $fechaFin = date("d/m/Y", strtotime($objDescuentoProgramado->fecha_fin));
        $porcentaje = number_format($objDescuentoProgramado->porcentaje, 2);
        
        if ($objDescuentoProgramado->estado != 1) {
            $soloLectura = "disabled";
        }
        
        $texto = "Editar Descuento Programado";
        $textoBoton = "Actualizar";
        $msgOk = "actualizó";
        $msgError = "actualizar";
    }
    $lstCategoria = CategoriaData::getAll(1);
    $lstProducto = ProductoData::getAll(1, $idSede);
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newrebate" action="index.php?action=addrebate" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>
        		<div class="panel-body">
        			<div class="form-group">
        				<div class="col-md-4 col-sm-12">
    						<label for="categoria">Categoría :</label>
    						<select id="categoria" name="categoria" class="form-control">
    							<option value="">SELECCIONE</option>
    							<?php foreach ($lstCategoria as $objCategoria) { ?>
    							<option value="<?php echo $objCategoria->id; ?>" <?php if ($objCategoria->id == $categoria) { echo "selected"; } ?>><?php echo $objCategoria->nombre; ?></option>
      							<?php } ?>
    						</select>
    						<script type="text/javascript">
								$("#categoria").change(function(){
									var categoria = $("#categoria").val();
									var sede = <?php echo $idSede; ?>;
									$.blockUI();
									$.post("./?action=utilitarios", {
    									categoria: categoria,
    									sede: sede,
    									vista: 0
    	                           	}, function (data) {
        	                           	$("#producto").html(data);
        	                           	document.getElementById("producto").focus();    									
    									$.unblockUI();
    	                            });
								});
    						</script>
    					</div>
        				<div class="col-md-4 col-sm-12">
    						<label for="producto">Producto :*</label>
    						<select id="producto" name="producto" class="form-control" required>
    							<option value="">SELECCIONE</option>
    							<?php
    							         if ($id > 0) {
    							             foreach ($lstProducto as $objProducto) {
    							?>
    							<option value="<?php echo $objProducto->id; ?>" <?php if ($objProducto->id == $producto) { echo "selected"; } ?>><?php echo $objProducto->nombre; ?></option>
      							<?php
    							             }
    							         }
                                ?>
    						</select>
    						<script type="text/javascript">
								$("#producto").change(function(){
									var producto = $("#producto").val();
									if (producto !== "") {
										document.getElementById("fechainicio").focus();
									}
								});
							</script>
    					</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="fechainicio">Fecha Inicio :*</label>
        					<input type="text" id="fechainicio" name="fechainicio" class="form-control" placeholder="Fecha Inicio" maxlength="10" value="<?php echo $fechaInicio; ?>" required <?php echo $soloLectura; ?> readonly/>
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="fechafin">Fecha Fin :*</label>
        					<input type="text" id="fechafin" name="fechafin" class="form-control" placeholder="Fecha Fin" maxlength="10" value="<?php echo $fechaFin; ?>" required <?php echo $soloLectura; ?> readonly/>
        				</div>
        			</div>
        			<div class="form-group">
        				<div class="col-md-2 col-sm-12">
        					<label for="porcentaje">Porcentaje :*</label>
        					<input type="text" id="porcentaje" name="porcentaje" class="form-control" placeholder="Porcentaje" maxlength="6" value="<?php echo $porcentaje; ?>" required <?php echo $soloLectura; ?> onkeypress="return filterFloat(event, this);"/>
        				</div>
        				<script type="text/javascript">
    						$("#porcentaje").click(function(){
								$("#porcentaje").val("");
							});
    						$("#porcentaje").blur(function(){
    							var porcentaje = $("#porcentaje").val();
    							if (porcentaje === "") {
									$("#porcentaje").val("0.00");
								} else if (isNaN(porcentaje)) {
									$("#porcentaje").val("0.00");
									document.getElementById("porcentaje").focus();										
									Swal.fire({
    	    							icon: "warning",
    	    							title: "Sólo valores numéricos (porcentaje)"
    	    						})
								}
    						});
        				</script>
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
    				$("button").prop("disabled", true);
    				location.href = "./index.php?view=rebates";
    			});    			
    			$(function(){
    				$("#categoria").select2();
    				$("#producto").select2();
    				document.getElementById("categoria").focus();        			    				
					$("#fechainicio").datepicker({
			    		dateFormat: "dd/mm/yy",
			    		changeMonth: true,
			    		changeYear: true,
			    		minDate: "0",
			    		yearRange: "1900:<?php echo date("Y"); ?>"
			    	});
			    	$("#fechafin").datepicker({
			    		dateFormat: "dd/mm/yy",
			    		changeMonth: true,
			    		changeYear: true,
			    		minDate: "0",
			    		yearRange: "1900:<?php echo date("Y"); ?>"
			    	});
    				$("#newrebate").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addrebate",
    		    				dataType: "html",
    		    				data: $("#newrebate").serialize(),
    		    				beforeSend: function() {
    		    					$("#btnCancelar").attr("disabled", "disabled");
    		    					$("#btnRegistrar").attr("disabled", "disabled");
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se <?php echo $msgOk; ?> correctamente el descuento",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    										window.location.href = "./index.php?view=rebates";
    		                        	})
    		        				} else {
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Ocurrio un error al <?php echo $msgError; ?> el descuento"
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al <?php echo $msgError; ?> el descuento"
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