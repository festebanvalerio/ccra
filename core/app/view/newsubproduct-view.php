<?php
    $id = 0;
    $nombre = "";
    $soloLectura = "";
    $texto = "Registrar SubProducto";
    $textoBoton = "Registrar";
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
        $objSubProducto = SubProductoData::getById($id);
        $nombre = $objSubProducto->nombre;
        
        if ($objSubProducto->estado != 1) {
            $soloLectura = "disabled";
        }
        
        $texto = "Editar SubProducto";
        $textoBoton = "Actualizar";
    }
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newsubproduct" action="index.php?action=addsubproduct" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>
        		<div class="panel-body">
        			<div class="form-group">
        				<div class="col-md-4 col-sm-12">
        					<label for="nombre">Nombre :*</label>
        					<input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" maxlength="100" value="<?php echo $nombre; ?>" required <?php echo $soloLectura; ?>/>
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
    				location.href = "./index.php?view=subproducts";
    			});
    			$(document).ready(function(){
        			setTimeout(function(){$("#nombre").trigger("focus")},1);
        		});
    			$(function(){
    				$("#newsubproduct").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addsubproduct",
    		    				async: true,
    		    				dataType: "html",
    		    				data: $("#newsubproduct").serialize(),
    		    				beforeSend: function() {
    		    					$("#btnRegistrar").attr("disabled", "disabled");
    		    					$("#btnCancelar").attr("disabled", "disabled");
    		    				},
    		    				success: function(data) {
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: 'success',
    										title: 'Se registró correctamente el subproducto',
    										showCancelButton: false,
    										confirmButtonColor: '#3085d6',
    										confirmButtonText: 'OK'
    		                        	}).then((result) => {
    										if (result.value) {
    											window.location.href = "./index.php?view=subproducts";
    										}
    		                        	})        					
    		        				} else {
    		        					Swal.fire({
    		    							icon: 'error',
    		    							title: 'Ocurrio un error al registrar el subproducto'																	
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: 'error',
    									title: 'Ocurrio un error al registrar el subproducto'
    								})
    		    				},
    		    				complete: function(data) {
    		    					$("#btnRegistrar").removeAttr("disabled");
    		    					$("#btnCancelar").removeAttr("disabled");    									
    		    				}
    		    			});			
    		    		},
    		            messages: {
    		            	nombre: {
    		                	required: "Ingrese nombre"
    		                }
    		            }
    		        });
    			});    		
    		</script>
    	</div>
    </div>
</section>