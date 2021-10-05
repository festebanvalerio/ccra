<?php    $sede = $perfil = "";    $estado = "1";    if (count($_POST) > 0) {        $sede = $_POST["sede"];        $estado = $_POST["estado"];        $perfil = $_POST["perfil"];    } else if ($_SESSION["sede"] > 0) {        $sede = $_SESSION["sede"];    }    $lstSede = SedeData::getAll(1);    $lstEstado = EstadoData::getAll();    $lstPerfil = PerfilData::getAll(1);    $lstUsuario = UsuarioData::getAll($estado, $perfil, $sede);?><section class="content">	<div class="row">		<div class="col-md-12">			<div class="panel panel-primary">    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Usuario</h3></div>    			<div class="panel-body">    				<form class="form-horizontal" method="post" id="users" action="" role="form" autocomplete="off">    			            			<div class="form-group">            				<?php if ($_SESSION["sede"] == 0) { ?>            				<div class="col-md-4 col-sm-12">    							<label for="sede">Sede :</label>    							<select id="sede" name="sede" class="form-control">    								<option value="">TODOS</option>    								<?php foreach ($lstSede as $objSede) { ?>    								<option value="<?php echo $objSede->id; ?>" <?php if ($objSede->id == $sede) { echo "selected"; } ?>><?php echo $objSede->nombre; ?></option>      								<?php } ?>    							</select>    						</div>    						<?php } ?>            				<div class="col-md-2 col-sm-12">    							<label for="perfil">Perfil :</label>    							<select id="perfil" name="perfil" class="form-control">    								<option value="">TODOS</option>    								<?php foreach ($lstPerfil as $objPerfil) { ?>    								<option value="<?php echo $objPerfil->id; ?>" <?php if ($objPerfil->id == $perfil) { echo "selected"; } ?>><?php echo $objPerfil->nombre; ?></option>      								<?php } ?>    							</select>    						</div>            				<div class="col-md-2 col-sm-12">    							<label for="estado">Estado :</label>    							<select id="estado" name="estado" class="form-control">    								<option value="">TODOS</option>    								<?php foreach ($lstEstado as $objEstado) { ?>    								<option value="<?php echo $objEstado->id; ?>" <?php if ($objEstado->id == $estado) { echo "selected"; } ?>><?php echo $objEstado->nombre; ?></option>      								<?php } ?>    							</select>    						</div>    						<div class="col-md-4 col-sm-12" style="padding-top: 25px;">        						<button type="button" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>        						<button type="button" id="btnLimpiar" class="btn btn-danger" title="Limpiar"><em class="fa fa-eraser"></em></button>        						<button type="button" id="btnNuevo" class="btn btn-primary" title="Nuevo"><em class="fa fa-pencil-square-o"></em></button>        						<?php if ($_SESSION["sede"] > 0) { ?>        						<input type="hidden" id="sede" name="sede" value="<?php echo $sede; ?>"/>        						<?php } ?>        						<script type="text/javascript">            						$("#btnLimpiar").click(function(){            							$.blockUI();            							$("button").prop("disabled", true);            		    				location.href = "./index.php?view=users";            		    			});            		    			$("#btnBuscar").click(function(){            							$.blockUI();            		    				$("button").prop("disabled", true);            		    				$("#products").submit();            		    			});            		    			$("#btnNuevo").click(function(){            							$.blockUI();            		    				$("button").prop("disabled", true);            		    				location.href = "./index.php?view=newuser";            		    			});        						</script>            				</div>    					</div>            		</form>					<div class="table-responsive">        				<div class="box-body">        					<table class="table table-bordered table-hover datatable table-nowrap">        						<thead>        							<tr>        								<th scope="col">Código</th>        								<th scope="col">Sede</th>        								<th scope="col">Perfil</th>        								<th scope="col">Username</th>        								<th scope="col">Nombres</th>        								<th scope="col">Apellidos</th>        								<th scope="col">Estado</th>        								<th scope="col">Acciones</th>        							</tr>        						</thead>        						<tbody>
        						<?php        						    foreach ($lstUsuario as $objUsuario) {        						        $objSede = $objUsuario->getLocal();        						                						        $objPerfil = $objUsuario->getPerfil();        						        $objEstado = $objUsuario->getEstado();
                                ?>
        							<tr>            							<td style="text-align: left;"><?php echo str_pad($objUsuario->id, 8, "0", STR_PAD_LEFT); ?></td>            							<td style="text-align: left;"><?php echo $objSede->nombre; ?></td>            							<td style="text-align: left;"><?php echo $objPerfil->nombre; ?></td>            							<td style="text-align: left;"><?php echo $objUsuario->username; ?></td>            							<td style="text-align: left;"><?php echo $objUsuario->nombres; ?></td>            							<td style="text-align: left;"><?php echo $objUsuario->apellidos; ?></td>            							<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>            							<td style="text-align: left;">            								<?php if ($objEstado->opcion) { ?>            								<a href="index.php?view=newuser&id=<?php echo $objUsuario->id; ?>" title="Editar" class="btn btn-warning btn-xs"><em class="fa fa-pencil-square-o"></em></a>            								<a id="lnkdel<?php echo $objUsuario->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>            								<script type="text/javascript">                        						$("#lnkdel<?php echo $objUsuario->id; ?>").click(function() {                        							Swal.fire({                            							title: "Desea anular el usuario <?php echo $objUsuario->username; ?>",                        								icon: "warning",                        								showCancelButton: true,                        								confirmButtonColor: "#3085d6",                        								cancelButtonColor: "#d33",                        								confirmButtonText: "Anular",                        								cancelButtonText: "Cancelar"                        							}).then((result) => {                        								if (result.isConfirmed) {                        									$.ajax({                        									    type: "post",                        									    url: "./?action=adduser",                        									    data: "id="+<?php echo $objUsuario->id; ?>+"&accion=2",                        									                            									    dataType: "html",                        									    beforeSend: function() {                        		    		    					$("#lnkdel<?php echo $objUsuario->id; ?>").attr("disabled","disabled");                        		    		    					$.blockUI();                        		    		    				},                        									    success: function(data) {                        									        if (data > 0) {                        									        	Swal.fire({                                    		                                icon: "success",                                    		                                title: "Se anuló correctamente el usuario <?php echo $objUsuario->username; ?>",                                    										showCancelButton: false,                                    										confirmButtonColor: "#3085d6",                                    										confirmButtonText: "OK"                                    		                        	}).then((result) => {                                    										window.location.href = "./index.php?view=users";                                    		                        	})                        									        } else {                        									        	Swal.fire({                        		    		    							icon: "warning",                        		    		    							title: "Ocurrio un error al anular el usuario <?php echo $objUsuario->username; ?>"                        		    		    						})                        										    }                        									    },                        									    error: function() {                        									    	Swal.fire({                        	    		    							icon: "error",                        	    		    							title: "Ocurrio un error al anular el usuario <?php echo $objUsuario->username; ?>"                        	    		    						})                        									    },                        		    		    				complete: function(data) {                        		    		    					$("#lnkdel<?php echo $objUsuario->id; ?>").removeAttr("disabled");                        		    		    					$.unblockUI();                        		    		    				}                        									});                        								}                        							})                        						});                        					</script>                        					<?php } ?>            							</td>        							</tr>
        						<?php
                                    }
                                ?>                                </tbody>
        					</table>        				</div>        			</div>        		</div>        	</div>
		</div>	</div></section>