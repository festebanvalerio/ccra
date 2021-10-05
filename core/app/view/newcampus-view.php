<?php
    $sede = 0;
    $nombre = $direccion = $soloLectura = "";
    $texto = "Registrar Sede";
    $textoBoton = "Registrar";
    $msgOk = "registró";
    $msgError = "registrar";
    $lstPisoXSede = array();
    if (isset($_GET["id"])) {
        $sede = $_GET["id"];
        $objSede = SedeData::getById($sede);
        $nombre = $objSede->nombre;
        $direccion = $objSede->direccion;
        $lstPisoXSede = $objSede->getPisoXSede();
        
        if ($objSede->estado != 1) {
            $soloLectura = "disabled";
        }
        
        $texto = "Editar Sede";
        $textoBoton = "Actualizar";
        $msgOk = "actualizó";
        $msgError = "actualizar";
    }
    $lstPiso = PisoData::getAll(1, $sede);
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newcampus" action="index.php?action=addcampus" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>
        		<div class="panel-body">
        			<div class="form-group">
        				<div class="col-md-4 col-sm-12">
        					<label for="nombre">Nombre :*</label>
        					<input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" maxlength="100" value="<?php echo $nombre; ?>" required <?php echo $soloLectura; ?>/>
        				</div>
        				<div class="col-md-4 col-sm-12">
        					<label for="direccion">Dirección :*</label>
        					<input type="text" id="direccion" name="direccion" class="form-control" placeholder="Dirección" maxlength="100" value="<?php echo $direccion; ?>" required <?php echo $soloLectura; ?>/>
        				</div>
        				<div class="col-md-4 col-sm-12">
    						<label for="piso">Piso :</label>
    						<select id="piso" name="piso[]" class="form-control" multiple required>
    							<?php
    							     $piso = 0;
    							     foreach ($lstPiso as $objPiso) { 
    							         if (count($lstPisoXSede) > 0) {
    							             foreach ($lstPisoXSede as $objPisoXSede) {
    							                 if ($objPisoXSede->piso == $objPiso->id) {
    							                     $piso = $objPiso->id;
    							                 }
    							             }
    							         }    							    
    							?>
    							<option value="<?php echo $objPiso->id; ?>" <?php if ($objPiso->id == $piso) { echo "selected"; } ?>><?php echo $objPiso->nombre; ?></option>
      							<?php } ?>
    						</select>
    					</div>    					
        			</div>
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
        					<button type="submit" id="btnRegistrar" class="btn btn-success" <?php echo $soloLectura; ?>><?php echo $textoBoton; ?></button>
        					<button type="button" id="btnCancelar" class="btn btn-danger">Cancelar</button>
        					<input type="hidden" id="id" name="id" value="<?php echo $sede; ?>"/>
        					<input type="hidden" id="accion" name="accion" value="1"/>
        				</div>
        			</div>
        		</div>
        	</div>
    		</form>
    		<script type="text/javascript">
    			$("#btnCancelar").click(function(){
    				$.blockUI();
    				$("button").prop("disabled", true);
    				location.href = "./index.php?view=campus";
    			});
    			$(function(){
    				document.getElementById("nombre").focus();
    				$("#piso").multiselect({
        			    columns: 1,
        			    placeholder: "Seleccione piso"     			            			   
        			});
    				$("#newcampus").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addcampus",
    		    				dataType: "html",
    		    				data: $("#newcampus").serialize(),
    		    				beforeSend: function() {
    		    					$("#btnRegistrar").attr("disabled", "disabled");
    		    					$("#btnCancelar").attr("disabled", "disabled");
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se <?php echo $msgOk; ?> correctamente la sede",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    										window.location.href = "./index.php?view=campus";
    		                        	})        					
    		        				} else {
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Ocurrio un error al <?php echo $msgError; ?> la sede"																	
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al <?php echo $msgError; ?> la sede"
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