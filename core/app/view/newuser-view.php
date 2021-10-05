<?php
    $id = $perfil = $caja = 0;
    $username = $password = $nombres = $apellidos = "";
    $soloLectura = "";
    $texto = "Registrar Usuario";
    $mensajeOk = "registró";
    $mensajeError = "registrar";
    $textoBoton = "Registrar";
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
        $objUsuario = UsuarioData::getById($id);
        $username = $objUsuario->username;
        $password = $objUsuario->password;
        $nombres = $objUsuario->nombres;
        $apellidos = $objUsuario->apellidos;
        $perfil = $objUsuario->perfil;
        $caja = $objUsuario->caja;
        if ($objUsuario->estado != 1) {
            $soloLectura = "disabled";
        }
        $texto = "Editar Usuario";
        $textoBoton = "Actualizar";
        $mensajeOk = "actualizó";
        $mensajeError = "actualizar";
    }
    $idSede = $_SESSION["sede"];
    $lstPerfil = PerfilData::getAll(1);
    $lstCaja = CajaData::getAll(1, $idSede);
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newuser" action="index.php?action=adduser" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>
        		<div class="panel-body">
        			<div class="form-group">
        				<div class="col-md-2 col-sm-12">
        					<label for="username">Username :*</label>
        					<input type="text" id="username" name="username" class="form-control" placeholder="Username" value="<?php echo $username; ?>" disabled/>
        				</div>
        				<!--<div class="col-md-2 col-sm-12">
        					<label for="password">Password :*</label>
        					<input type="text" id="password" name="password" class="form-control" placeholder="Password" value="<?php echo $password; ?>" readonly/>
        				</div>-->
        				<div class="col-md-2 col-sm-12">
        					<label for="perfil">Perfil :*</label>
        					<select id="perfil" name="perfil" class="form-control" required>
    							<option value="">SELECCIONE</option>
    							<?php foreach ($lstPerfil as $objPerfil) { ?>
								<option value="<?php echo $objPerfil->id; ?>" <?php if ($objPerfil->id == $perfil) { echo "selected"; } ?>><?php echo $objPerfil->nombre; ?></option>
      							<?php } ?>
    						</select>
    						<script type="text/javascript">
    							$("#perfil").change(function(){
    								var perfil = $("#perfil").val();
    								if (perfil === "") {
    									$("#divcaja").hide();
    									$("#caja").attr("disabled","disabled");
    								} else {
    									$.blockUI();
    									$.post("./?action=utilitarios", {
    										perfil: perfil,
                                        }, function (data) {
                                        	if (data === "1") {
    											$("#divcaja").show();
    											$("#caja").removeAttr("disabled");
											} else {
												$("#divcaja").hide();
												$("#caja").attr("disabled","disabled");
											}
											$.unblockUI();
    									});
    								}
    							});
    						</script>
        				</div>
        				<div id="divcaja" class="col-md-2 col-sm-12" style="display: none;">
        					<label for="caja">Caja :*</label>
        					<select id="caja" name="caja" class="form-control" disabled>
    							<?php foreach ($lstCaja as $objCaja) { ?>
								<option value="<?php echo $objCaja->id; ?>" <?php if ($objCaja->id == $caja) { echo "selected"; } ?>><?php echo $objCaja->nombre; ?></option>
      							<?php } ?>
    						</select>
        				</div>
        				<div class="col-md-3 col-sm-12">
        					<label for="nombres">Nombres :*</label>
        					<input type="text" id="nombres" name="nombres" class="form-control" placeholder="Nombres" maxlength="100" value="<?php echo $nombres; ?>" required <?php echo $soloLectura; ?>/>
        				</div>
        				<div class="col-md-3 col-sm-12">
        					<label for="apellidos">Apellidos :*</label>
        					<input type="text" id="apellidos" name="apellidos" class="form-control" placeholder="Apellidos" maxlength="100" value="<?php echo $apellidos; ?>" required <?php echo $soloLectura; ?>/>
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
    				location.href = "./index.php?view=users";
    			});
    			$(document).ready(function(){
    				document.getElementById("perfil").focus();
        			<?php if (isset($_GET["id"]) && $caja > 0) { ?>
        			$("#divcaja").show();
					$("#caja").removeAttr("disabled");
        			<?php } ?>
        		});
    			$(function(){
    				$("#newuser").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=adduser",
    		    				dataType: "html",
    		    				data: $("#newuser").serialize(),
    		    				beforeSend: function() {
    		    					$("#btnRegistrar").attr("disabled", "disabled");
    		    					$("#btnCancelar").attr("disabled", "disabled");
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se <?php echo $mensajeOk; ?> correctamente el usuario",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    										window.location.href = "./index.php?view=users";
    		                        	})        					
    		        				} else {
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Ocurrio un error al <?php echo $mensajeError; ?> el usuario"
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al <?php echo $mensajeError; ?> el usuario"
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