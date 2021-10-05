<?php
    $sede = $piso = 0;
    $nombre = $direccion = "";
    $soloLectura = "";
    $texto = "";
    $textoBoton = "Registrar";
    $lstPisoXSede = NULL;
    if (isset($_GET["id"])) {
        $sede = $_GET["id"];
        $objSede = SedeData::getById($sede);
        $nombre = $objSede->nombre;
        $direccion = $objSede->direccion;
        $lstPisoXSede = $objSede->getPisoXSede();
        
        $soloLectura = "disabled";        
        $texto = "Registrar Area x Piso x Sede";
        
        if (isset($_GET["piso"])) {
            $piso = $_GET["piso"];
        }
    }    
    $lstArea = AreaData::getAll(1, $sede);
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newcampusarea" action="index.php?action=addcampus" role="form" autocomplete="off">
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
    						<select id="piso" name="piso" class="form-control" required>
    							<option value="">SELECCIONE</option>
    							<?php foreach ($lstPisoXSede as $objPiso) { ?>
    							<option value="<?php echo $objPiso->id; ?>" <?php if ($objPiso->id == $piso) { echo " selected"; } ?>><?php echo $objPiso->getPiso()->nombre; ?></option>
    							<?php } ?>
    						</select>
    						<script type="text/javascript">
								$("#piso").change(function(){
									$.blockUI();
									var piso = $("#piso").val();
									if (piso === "") {
										location.href = "./index.php?view=newcampusarea&id=<?php echo $sede?>";
									} else {
										location.href = "./index.php?view=newcampusarea&id=<?php echo $sede?>&piso="+piso;
									}									
								});
    						</script>
    					</div>
    				</div>
    				<div class="form-group">
        				<div class="col-md-4 col-sm-12">
    						<label for="area">Area :</label>
    						<select id="area" name="area[]" class="form-control" multiple required>
    							<?php
    							     foreach ($lstArea as $objArea) {
    							         $selected = "";
    							         if ($piso > 0) {
    							             $lstAreaXPisoXSede = AreaPisoSedeData::getAreaxPisoxSede($piso, $objArea->id);
    							             if (count($lstAreaXPisoXSede) == 1) {
        							             $selected = " selected";
        							         }
    							         }
    							?>
    							<option value="<?php echo $objArea->id; ?>" <?php echo $selected; ?>><?php echo $objArea->nombre; ?></option>	
      							<?php } ?>
    						</select>
    					</div>    					
        			</div>
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
        					<button type="submit" id="btnRegistrar" class="btn btn-success"><?php echo $textoBoton; ?></button>
        					<button type="button" id="btnCancelar" class="btn btn-danger">Cancelar</button>
        					<input type="hidden" id="id" name="id" value="<?php echo $sede; ?>"/>
        					<input type="hidden" id="accion" name="accion" value="4"/>
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
    				document.getElementById("piso").focus();	
    				$("#area").multiselect({
        			    columns: 1,
        			    placeholder: "Seleccione Area"
        			});
    				$("#newcampusarea").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addcampus",
    		    				dataType: "html",
    		    				data: $("#newcampusarea").serialize(),
    		    				beforeSend: function() {
    		    					$("#btnRegistrar").attr("disabled", "disabled");
    		    					$("#btnCancelar").attr("disabled", "disabled");
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se registró correctamente las áreas asociadas a la sede <?php echo $nombre; ?>",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    										window.location.href = "./index.php?view=campus";
    									})        					
    		        				} else {
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Ocurrio un error al registrar las áreas asociadas a la sede <?php echo $nombre; ?>"																	
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al registrar las áreas asociadas a la sede <?php echo $nombre; ?>"
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