<?php    $id = 0;
    $orden = 1;
    $idModuloPadre = $icono = $nombre = $url = "";    $msgOk = "registró";    $msgError = "registrar";
    $texto = "Registrar Módulo";    $textoBoton = "Registrar";
    $lstModulo = ModuloData::getAllPrincipal();
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
    
        $objModulo = ModuloData::getById($id);
        $idModuloPadre = $objModulo->id_padre;
        $icono = $objModulo->icono;        if ($icono != "") {            $icono = str_replace("<i class='fa ", "", $icono);            $icono = str_replace("'></i>", "", $icono);        }
        $nombre = $objModulo->nombre;
        $url = $objModulo->url;
        if ($url != "") {
            $url = str_replace("./index.php?view=", "", $url);
        }
        $orden = $objModulo->orden;        $msgOk = "actualizó";        $msgError = "actualizar";        $textoBoton = "Actualizar";
    }?><section class="content">		<div class="row">		<div class="col-md-12">    		<form class="form-horizontal" method="post" id="newmodule" action="index.php?action=addmodule" role="form" autocomplete="off">    		<div class="panel panel-primary">				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>        		<div class="panel-body">        			<div class="form-group">        				<div class="col-md-4 col-sm-12">        					<label for="modulo">Módulo Padre:</label>    						<select id="modulo" name="modulo" class="form-control">        						<option value="">-- SELECCIONE --</option>            					<?php foreach ($lstModulo as $objModulo) { ?>            					<option value="<?php echo $objModulo->id; ?>" <?php if ($objModulo->id == $idModuloPadre) { echo "selected"; } ?>><?php echo $objModulo->nombre; ?></option>            					<?php } ?>        					</select>        				</div>        				<div class="col-md-2 col-sm-12">        					<label for="icono">Icono :*</label>        					<input type="text" id="icono" name="icono" class="form-control" placeholder="Icono" maxlength="100" value="<?php echo $icono; ?>" required/>        				</div>        				<div class="col-md-2 col-sm-12">        					<label for="nombre">Nombre :*</label>        					<input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" maxlength="50" value="<?php echo $nombre; ?>" required/>        				</div>        				<div class="col-md-2 col-sm-12">        					<label for="url">URL :</label>        					<input type="text" id="url" name="url" class="form-control" placeholder="URL" maxlength="100" value="<?php echo $url; ?>"/>        				</div>        				<div class="col-md-2 col-sm-12">        					<label for="orden">Orden :</label>        					<input type="text" id="orden" name="orden" class="form-control" placeholder="0" maxlength="2" value="<?php echo $orden; ?>" onkeypress="return soloNumeros(event)"/>        					<script type="text/javascript">        						$("#orden").blur(function(){        							var orden = $("#orden").val();        							if (orden !== "") {        								if (isNaN(orden)) {        									$("#orden").val("<?php echo $orden; ?>");    										document.getElementById("orden").focus();    										    										Swal.fire({    	    									icon: "warning",    	    									title: "Sólo valores numéricos (orden)"    	    								})    									}    								} else {    									$("#orden").val("<?php echo $orden; ?>");    								}    								    						        						})        					</script>        				</div>        			</div>        			<div class="form-group">        				<div class="col-md-12 col-sm-12">        					<button type="submit" id="btnRegistrar" class="btn btn-success"><?php echo $textoBoton; ?></button>        					<button type="button" id="btnCancelar" class="btn btn-danger">Cancelar</button>        					<input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>        					<input type="hidden" id="accion" name="accion" value="1"/>        				</div>        			</div>        		</div>        	</div>    		</form>    		<script type="text/javascript">    			$("#btnCancelar").click(function(){    				$.blockUI();    				$("button").prop("disabled", true);    				location.href = "./index.php?view=modules";    			});    			$(document).ready(function(){        			document.getElementById("modulo").focus();        			$("#newmodule").validate({    		        	submitHandler: function(){    		    			$.ajax({    		    				type: "POST",    		    				url: "./index.php?action=addmodule",    		    				dataType: "html",    		    				data: $("#newmodule").serialize(),    		    				beforeSend: function() {    		    					$("#btnCancelar").attr("disabled", "disabled");    		    					$("#btnRegistrar").attr("disabled", "disabled");    		    					$.blockUI();    		    				},    		    				success: function(data) {    		    					document.getElementById("modulo").focus();    		        				if (data > 0) {    		        					Swal.fire({    		                                icon: "success",    										title: "Se <?php echo $msgOk; ?> correctamente el módulo",    										showCancelButton: false,    										confirmButtonColor: "#3085d6",    										confirmButtonText: "OK"    		                        	}).then((result) => {    										window.location.href = "./index.php?view=modules";    		                        	})    		        				} else {    		        					Swal.fire({    		    							icon: "warning",    		    							title: "Ocurrio un error al <?php echo $msgError; ?> el módulo"    		    						})    		        				}    		    				},    		    				error: function(data) {    		    					document.getElementById("modulo").focus();    		    					Swal.fire({    									icon: "error",    									title: "Ocurrio un error al <?php echo $msgError; ?> el módulo"    								})    		    				},    		    				complete: function(data) {    		    					$("#btnCancelar").removeAttr("disabled");    		    					$("#btnRegistrar").removeAttr("disabled");    		    					$.unblockUI();    		    				}    		    			});    		    		}    		        });        		});    		</script>    	</div>    </div></section>