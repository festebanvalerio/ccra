<?php
    $id = $tabla = 0;
    $nombre = $valor1 = "";
    $soloLectura = "";
    $texto = "Registrar Parámetro";
    $textoBoton = "Registrar";
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
        $objParametro = ParametroData::getById($id);
        $tabla = $objParametro->tabla;
        $nombre = $objParametro->nombre;
        $valor1 = $objParametro->valor1;
          
        if ($objParametro->estado != 1) {
            $soloLectura = "disabled";
        }
        
        $texto = "Editar Parámetro";
        $textoBoton = "Actualizar";
    }
    $lstTabla = TablaData::getAll(1);
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newparameter" action="index.php?action=addparameter" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>
        		<div class="panel-body">
        			<div class="form-group">
        				<div class="col-md-4 col-sm-12">
        					<label for="tabla">Tabla :*</label>
        					<select id="tabla" name="tabla" class="form-control" required>
    							<option value="">SELECCIONE</option>
    							<?php foreach ($lstTabla as $objTabla) { ?>
								<option value="<?php echo $objTabla->id; ?>" <?php if ($objTabla->id == $tabla) { echo "selected"; } ?>><?php echo $objTabla->nombre; ?></option>
      							<?php } ?>
    						</select>
        				</div>
        				<div class="col-md-4 col-sm-12">
        					<label for="nombre">Nombre :*</label>
        					<input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" maxlength="100" value="<?php echo $nombre; ?>" required <?php echo $soloLectura; ?>/>
        				</div>
        				<div class="col-md-4 col-sm-12">
        					<label for="valor">Valor :</label>
        					<input type="text" id="valor" name="valor" class="form-control" placeholder="Valor" maxlength="100" value="<?php echo $valor1; ?>" <?php echo $soloLectura; ?>/>
        				</div>
        			</div>
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
        					<button type="submit" id="btnRegistrar" class="btn btn-success" <?php echo $soloLectura; ?>><?php echo $textoBoton; ?></button>
        					<button type="button" id="btnCancelar" class="btn btn-danger">Cancelar</button>
        					<input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>
        					<input type="hidden" id="accion" name="accion" value="1"/>
        				</div>
        			</div>
        		</div>
        	</div>
    		</form>
    		<script type="text/javascript">
    			$("#btnCancelar").click(function(){
    				$.blockUI();
    				location.href = "./index.php?view=parameters";
    			});
    			$(document).ready(function(){
        			setTimeout(function(){$("#tabla").trigger("focus")},1);
        		});
    			$(function(){
    				$("#newparameter").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addparameter",
    		    				async: true,
    		    				dataType: "html",
    		    				data: $("#newparameter").serialize(),
    		    				beforeSend: function() {
    		    					$("#btnRegistrar").attr("disabled", "disabled");
    		    					$("#btnCancelar").attr("disabled", "disabled");
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se registró correctamente el parámetro",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    										window.location.href = "./index.php?view=parameters";
    		                        	})        					
    		        				} else {
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Ocurrio un error al registrar el parámetro"
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al registrar el parámetro"
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