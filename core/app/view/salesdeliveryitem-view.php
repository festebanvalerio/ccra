<?php
    $texto = "Registrar Venta - Delivery";
    
    $idPedido = 0;
    $objSede = SedeData::getById($_SESSION["sede"]);
    $idSede = $objSede->id;
    $sede = $objSede->nombre;
    
    $idUsuario = $_SESSION["user"];
    $idPisoSede = $idMesaPisoSede = $idPiso = $idMesa = 0;
    
    $fecha = date("d/m/Y");
    
    $objUsuario = UsuarioData::getById($idUsuario);
    $usuario = $objUsuario->nombres . " " . $objUsuario->apellidos;
    
    $idPiso = 0;
    $objCaja = $objUsuario->getCaja();
    if ($objCaja) {
        $objPiso = $objCaja->getPiso();
        $idPiso = $objPiso->id;
    } else {
        $idPiso = 1;
    }
    
    $lstCategoria = CategoriaData::getAll(1);
    $lstProducto = ProductoData::getAll(1, $idSede, $lstCategoria[0]->id);
    
    $telefono = $datos = $direccion = $hora = "";
    
    $lstDetallePedido = array();
    if (isset($_GET["id"])) {
        $idPedido = $_GET["id"];
        $objPedido = PedidoData::getById($idPedido);
        
        $fecha = date("d/m/Y", strtotime($objPedido->fecha));
        
        $objSede = $objPedido->getSede();
        $sede = $objSede->nombre;
        
        $objUsuario = $objPedido->getUsuario();
        $usuario = $objUsuario->nombres . " " . $objUsuario->apellidos;
        
        $telefono = $objPedido->telefono;
        $datos = $objPedido->datos;
        $direccion = $objPedido->direccion;
        $hora = $objPedido->hora;
        
        $lstDetallePedido = DetallePedidoData::getProductosXPedido($objPedido->id);
    }
    
    $idAlmacen = 0;
    $lstAlmacen = AlmacenData::getAll(1, $_SESSION["empresa"], $idSede);
    if (count($lstAlmacen) > 0) {
        $idAlmacen = $lstAlmacen[0]->id;
    }
    
    $opcion = 0;
    if (isset($_GET["opcion"])) {
        $opcion = $_GET["opcion"];
    }
    
    // Tipo Pedido
    $tipoPedido = "";
    $lstParametro = ParametroData::getAll(1, "TIPO PEDIDO", "DELIVERY");
    if (count($lstParametro) > 0) {
        $tipoPedido = $lstParametro[0]->id;
    }
    
    $lstHistorialCaja = array();
    if ($objCaja) {
        $lstHistorialCaja = HistorialCajaData::getAll(1, $_SESSION["sede"], $_SESSION["caja"], $fecha);
    } else {
        $lstHistorialCaja = HistorialCajaData::getAll(1, $_SESSION["sede"], "", $fecha);
    }
    
    unset($_SESSION["productos"]);
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newsale" action="index.php?action=addsale" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>
        		<div class="panel-body">
        			<fieldset class="scheduler-border">
        				<legend class="scheduler-border">Datos Generales</legend>
            			<div class="form-group">        				
            				<div class="col-md-2 col-sm-12">
            					<label for="fecha">Fecha :</label>
            					<input type="text" id="fecha" name="fecha" class="form-control" value="<?php echo $fecha; ?>" disabled/>
            				</div>
            				<div class="col-md-4 col-sm-12">
            					<label for="sede">Sede :</label>
            					<input type="text" id="sede" name="sede" class="form-control" value="<?php echo $sede; ?>" disabled/>
            				</div>
            				<div class="col-md-4 col-sm-12">
            					<label for="usuario">Usuario :</label>
            					<input type="text" id="usuario" name="usuario" class="form-control" value="<?php echo $usuario; ?>" disabled/>
            				</div>
            			</div>
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
            					<label for="telefono">Teléfono :</label>
            					<input type="text" id="telefono" name="telefono" placeholder="Teléfono" class="form-control" value="<?php echo $telefono; ?>" maxlength="9" onkeypress="return soloNumeros(event)"/>
            				</div>
            				<div class="col-md-4 col-sm-12">
            					<label for="contacto">Contacto :*</label>
            					<input type="text" id="datos" name="datos" placeholder="Contacto" class="form-control" value="<?php echo $datos; ?>" maxlength="150" required/>
            				</div>
            				<div class="col-md-4 col-sm-12">
            					<label for="direccion">Dirección :</label>
            					<input type="text" id="direccion" name="direccion" placeholder="Dirección" class="form-control" value="<?php echo $direccion; ?>" maxlength="150"/>
            				</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="hora">Hora :*</label>
            					<input type="text" id="hora" name="hora" placeholder="hh:mm" class="form-control" value="<?php echo $hora; ?>" maxlength="5" required/>
            				</div>
            			</div>
            		</fieldset>
            		<fieldset class="scheduler-border">
        				<legend class="scheduler-border">Datos del Producto</legend>
            			<div class="form-group">
            				<div class="col-md-4 col-sm-12">
        						<label for="categoria">Categoría :</label>
            					<select id="categoria" name="categoria" class="form-control">
        							<?php foreach ($lstCategoria as $objCategoria) { ?>
        							<option value="<?php echo $objCategoria->id; ?>"><?php echo $objCategoria->nombre; ?></option>
          							<?php } ?>
        						</select>
        						<script type="text/javascript">
    								$("#categoria").change(function(){
    									var categoria = $("#categoria").val();
    									var sede = <?php echo $idSede; ?>;
    									var idPedido = $("#id").val();
    									
    									$.blockUI();
    									$.post("./?action=utilitarios", {
        									categoria: categoria,
        									sede: sede,
        									idPedido: idPedido,
        									vista: 1
        	                           	}, function (data) {
            	                           	$("#lstProducto").html(data);
            	                           	
        	                           		$.unblockUI();
        	                           	});
    								});
        						</script>
    						</div>
    						<div class="col-md-2 col-sm-12">
        						<label for="buscar">Buscar :</label>
        						<input type="text" id="campo" name="campo" class="form-control" placeholder="" maxlength="50" value=""/>    						
        					</div>
        					<div class="col-md-1 col-sm-12" style="padding-top: 2%; width: 5%;">
        						<button type="button" id="btnBuscar" class="btn btn-success"><em class="fa fa-search"></em></button>
        						<script type="text/javascript">
    								$("#btnBuscar").click(function(){
    									var categoria = $("#categoria").val();
    									var sede = <?php echo $idSede; ?>;
    									var campo = $("#campo").val();
    									var idPedido = $("#id").val();
    									
    									$.blockUI();
    									$.post("./?action=utilitarios", {
        									categoria: categoria,
        									sede: sede,
        									campo: campo,
        									idPedido: idPedido,
        									vista: 1
        	                           	}, function (data) {
            	                           	$("#lstProducto").html(data);
            	                           	$("#campo").val("");
        	                           		$.unblockUI();
        	                           	});
    								});	
        						</script>
        					</div>
    						<div class="col-md-2 col-sm-12">
    							<label for="cantidad">Cantidad :</label>
    							<input type="text" id="cantidad" name="cantidad" class="form-control" placeholder="" maxlength="2" value="1" onkeypress="return soloNumeros(event);"/>
            				</div>
            				<div class="col-md-3 col-sm-12">
    							<label for="comentario">Comentario :</label>
    							<input type="text" id="comentario" name="comentario" class="form-control" placeholder="" maxlength="50" value=""/>
            				</div>
            			</div>
            			<div class="form-group">
            				<div class="col-md-12 col-sm-12">
            					<button type="button" id="btn1" class="btn btn-info"><span style="font-size: 20px; font-weight: bold;">1</span></button>
            					<button type="button" id="btn2" class="btn btn-info"><span style="font-size: 20px; font-weight: bold;">2</span></button>
            					<button type="button" id="btn3" class="btn btn-info"><span style="font-size: 20px; font-weight: bold;">3</span></button>
            					<button type="button" id="btn4" class="btn btn-info"><span style="font-size: 20px; font-weight: bold;">4</span></button>
            					<button type="button" id="btn5" class="btn btn-info"><span style="font-size: 20px; font-weight: bold;">5</span></button>
            					<button type="button" id="btn6" class="btn btn-info"><span style="font-size: 20px; font-weight: bold;">6</span></button>
            					<button type="button" id="btn7" class="btn btn-info"><span style="font-size: 20px; font-weight: bold;">7</span></button>
            					<button type="button" id="btn8" class="btn btn-info"><span style="font-size: 20px; font-weight: bold;">8</span></button>
            					<button type="button" id="btn9" class="btn btn-info"><span style="font-size: 20px; font-weight: bold;">9</span></button>
            					<button type="button" id="btn0" class="btn btn-info"><span style="font-size: 20px; font-weight: bold;">0</span></button>
            					<button type="button" id="btnb" class="btn btn-info"><span style="font-size: 20px; font-weight: bold;">DEL</span></button>
            					<script type="text/javascript">
    								$("#btn1").click(function(){
    									var cantidad = $("#cantidad").val();
    									if (cantidad !== "") {
    										if (cantidad.length <= 1) {
    											cantidad = cantidad.concat("1");
    										}
    									} else {
    										cantidad = "1";
    									}
    									$("#cantidad").val(cantidad);
    								});
    								$("#btn2").click(function(){
    									var cantidad = $("#cantidad").val();
    									if (cantidad !== "") {
    										if (cantidad.length <= 1) {
    											cantidad = cantidad.concat("2");
    										}
    									} else {
    										cantidad = "2";
    									}
    									$("#cantidad").val(cantidad);
    								});
    								$("#btn3").click(function(){
    									var cantidad = $("#cantidad").val();
    									if (cantidad !== "") {
    										if (cantidad.length <= 1) {
    											cantidad = cantidad.concat("3");
    										}
    									} else {
    										cantidad = "3";
    									}
    									$("#cantidad").val(cantidad);
    								});
    								$("#btn4").click(function(){
    									var cantidad = $("#cantidad").val();
    									if (cantidad !== "") {
    										if (cantidad.length <= 1) {
    											cantidad = cantidad.concat("4");
    										}
    									} else {
    										cantidad = "4";
    									}
    									$("#cantidad").val(cantidad);
    								});
    								$("#btn5").click(function(){
    									var cantidad = $("#cantidad").val();
    									if (cantidad !== "") {
    										if (cantidad.length <= 1) {
    											cantidad = cantidad.concat("5");
    										}
    									} else {
    										cantidad = "5";
    									}
    									$("#cantidad").val(cantidad);
    								});
    								$("#btn6").click(function(){
    									var cantidad = $("#cantidad").val();
    									if (cantidad !== "") {
    										if (cantidad.length <= 1) {
    											cantidad = cantidad.concat("6");
    										}
    									} else {
    										cantidad = "6";
    									}
    									$("#cantidad").val(cantidad);
    								});
    								$("#btn7").click(function(){
    									var cantidad = $("#cantidad").val();
    									if (cantidad !== "") {
    										if (cantidad.length <= 1) {
    											cantidad = cantidad.concat("7");
    										}
    									} else {
    										cantidad = "7";
    									}
    									$("#cantidad").val(cantidad);
    								});
    								$("#btn8").click(function(){
    									var cantidad = $("#cantidad").val();
    									if (cantidad !== "") {
    										if (cantidad.length <= 1) {
    											cantidad = cantidad.concat("8");
    										}
    									} else {
    										cantidad = "8";
    									}
    									$("#cantidad").val(cantidad);
    								});
    								$("#btn9").click(function(){
    									var cantidad = $("#cantidad").val();
    									if (cantidad !== "") {
    										if (cantidad.length <= 1) {
    											cantidad = cantidad.concat("9");
    										}
    									} else {
    										cantidad = "9";
    									}
    									$("#cantidad").val(cantidad);
    								});
    								$("#btn0").click(function(){
    									var cantidad = $("#cantidad").val();
    									if (cantidad !== "") {
    										if (cantidad.length <= 1 && (cantidad * 1) > 0) {											
    											cantidad = cantidad.concat("0");
    										}
    									} else {
    										cantidad = "";
    									}
    									$("#cantidad").val(cantidad);
    								});
    								$("#btnb").click(function(){
    									$("#cantidad").val("");
    								});
            					</script>
    						</div>
    					</div>
    					<div class="form-group">
    						<div class="col-md-12 col-sm-12">
    							<button type="submit" id="btnRegistrar" class="btn btn-success">Registrar</button>
    							<button type="button" id="btnCancelar" class="btn btn-danger">Regresar</button>
    						</div>
    					</div>
    					<div class="form-group">
    						<div class="col-md-2 col-sm-12">
            					<label for="producto">Productos :</label>
            				</div>
            			</div>
            			<div class="form-group" id="lstProducto">	
    						<?php
    						      if (count($lstProducto) > 0) {
    						          foreach ($lstProducto as $objProducto) {
    				        ?>							
        					<div class="col-md-2 col-sm-12">
    							<div class="info-box">
    								<span id="producto<?php echo $objProducto->id; ?>" class="info-box-icon bg-green" style="cursor: pointer;">
    									<em class="fa fa-cloud"></em>
    								</span>
    								<div class="info-box-content">					
    									<span class="info-box-number" style="font-size: 11px;"><?php echo str_replace(" ", "<br/>", $objProducto->nombre); ?></span>
    								</div>
    								<script type="text/javascript">
    									$("#producto<?php echo $objProducto->id; ?>").click(function(){
    										var idPedido = $("#id").val();
    										var categoria = $("#categoria").val();
    	    								var producto = <?php echo $objProducto->id; ?>;
    	    								var cantidad = $("#cantidad").val();
    	    								var comentario = $("#comentario").val();
    	    								var opcion = $("#opcion").val();
    	    								
    	    								if (categoria === "") {
    	    									Swal.fire({
    	    										icon: 'warning',
    	    										title: 'Seleccione una categoría'
    	    									})
    	    									return false;
    	    								}
    	    								if (producto === "") {
    	    									Swal.fire({
    	    										icon: 'warning',
    	    										title: 'Seleccione un producto'
    	    									})
    	    									return false;
    	    								}
    	    								if (cantidad === "") {
    	    									Swal.fire({
    	    										icon: 'warning',
    	    										title: 'Ingrese cantidad'
    	    									})
    	    									return false;
    	    								} else if (isNaN(cantidad)) {
    	    									Swal.fire({
    	    										icon: 'warning',
    	    										title: 'Ingrese cantidad válida'
    	    									})
    	    									return false;
    	    								}
    	    								$("#btnAgregar").attr("disabled","disabled");
    	    								$.blockUI();    								
    	    								$.post("./?action=getdetailssale", {
    	    									categoria: categoria,
    	    									producto: producto,
    	    									cantidad: cantidad,
    	    									comentario: comentario,
    	    									indicador: 1,
    	    									idPedido: idPedido
    	    	                           	}, function (data) {
    		    	                           	if (idPedido == 0) {
        	    									$("#tabla").html(data);
        	    									$("#btnAgregar").removeAttr("disabled");
        	    									$("#cantidad").val("1");
        	    									$("#comentario").val("");
        	    									document.getElementById("comentario").focus();
    		    	                           	} else {
    		    	                           		var usuario = $("#idusuario").val();
        						    				var piso = $("#idpisosede").val();
        						    				var mesa = $("#idmesapisosede").val();
    
        						    				if (opcion === "0") {
    		    	                           			window.location.href = "./index.php?view=salestableitem&usuario="+usuario+"&piso="+piso+"&mesa="+mesa;
        						    				} else {
        						    					window.location.href = "./index.php?view=salestableitem&usuario="+usuario+"&piso="+piso+"&mesa="+mesa+"&opcion="+opcion;
        						    				}
    		    	                           	}
    	    									$.unblockUI();
    	    	                            });
    									});
    								</script>
    							</div>
    						</div>
    						<?php
    						          }
    						      } else {
    						?>
    						<div class="col-md-12 col-sm-12">
    							<label for="mensajes">No hay productos asociados a esa categoría</label>
    						</div>
    						<?php 
    						      }
    						?>
    					</div>
    				</fieldset>
					<div class="form-group">
        				<div class="col-md-12 col-sm-12">
  							<div class="panel panel-primary" id="test1Pane1">
    							<div class="panel-heading">
    								<strong>Listado de Productos</strong>
      								<a data-target="#panel1Content" data-parent="#test1Panel" data-toggle="collapse"><span class="pull-right"><i class="panel1Icon fa fa-arrow-up"></i></span> </a>
    							</div>
    							<div class="panel-collapse collapse in" id="panel1Content">
      								<div class="panel-body">
                                    	<div class="table-responsive-md table-responsive" id="tabla">
                                    	<table class="table table-hover">
                                            <thead>
                                                <tr class="btn-primary">
                                                    <th scope="col">Item</th>
                                                    <th scope="col">Categoría</th>
                                                    <th scope="col">Producto</th>
                                                    <th scope="col">Tipo</th>
                                                    <th scope="col">Comentario</th>
                                    				<th scope="col" style="text-align: right;">Cantidad</th>
                                    				<th scope="col" style="text-align: center;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                if (count($lstDetallePedido) > 0) {
                                                    $item = $indice = 1;
                                                    foreach ($lstDetallePedido as $objDetallePedido) {                                                        
                                            ?>
                                            	<tr>
                                                    <td style='text-align: left;'><?php echo $indice++; ?></td>
                                                    <td style='text-align: left;'><?php echo $objDetallePedido->categoria; ?></td>
                                                    <td style='text-align: left;'><?php echo $objDetallePedido->nom_producto; ?></td>
                                                    <td style='text-align: left;'><?php echo $objDetallePedido->tipo; ?></td>
                                                    <td style='text-align: left;'><input type="text" id="comentario<?php echo $item.$objDetallePedido->id; ?>" name="comentario<?php echo $item.$objProducto->id; ?>" value="<?php echo $objDetallePedido->comentario; ?>" maxlength='50'/></td>	
                                                    <td style='text-align: right;'><?php echo number_format($objDetallePedido->cantidad, 2); ?></td>
                                                    <td style='text-align: center;'>
                                                    	<a id="eliminar<?php echo $item.$objDetallePedido->id; ?>" title="Eliminar" class="btn btn-danger btn-xs">X</a>                            
                                                        <script type='text/javascript'>
                                                            $("#comentario<?php echo $item.$objDetallePedido->id; ?>").blur(function() {
                                                                var comentarioActual = $("#comentario<?php echo $item.$objDetallePedido->id; ?>").val().trim();
                                                                var comentarioAnterior = '<?php echo $objDetallePedido->comentario; ?>';
                                                                var producto = <?php echo $objDetallePedido->producto; ?>;
                                                                var opcion = $("#opcion").val();
                                                                if (comentarioActual !== comentarioAnterior) {
                                                                	$.blockUI();
                                                                    $.post("./?action=getdetailssale", {
                                                                        item: <?php echo $item; ?>,
                                                                        producto: producto,
                                                                        comentario: comentarioActual,
                                                                        indicador: 3,
                                                                        idDetallePedido: <?php echo $objDetallePedido->id; ?>
                                                                    }, function (data) {
                                                                    	if (opcion === "0") {
                                                                    		window.location.href = "./index.php?view=salestableitem&usuario=<?php echo $idUsuario; ?>&piso=<?php echo $idPisoSede; ?>&mesa=<?php echo $idMesaPisoSede; ?>";
                            						    				} else {
                            						    					window.location.href = "./index.php?view=salestableitem&usuario=<?php echo $idUsuario; ?>&piso=<?php echo $idPisoSede; ?>&mesa=<?php echo $idMesaPisoSede; ?>+&opcion=<?php echo $opcion; ?>";
                            						    				}
                                                                    	
                                                                    }); 
                                                                }
                                                            });
                                                            $("#eliminar<?php echo $item.$objDetallePedido->id; ?>").click(function() {
                                                        		$("#eliminar<?php echo $item.$objDetallePedido->id; ?>").attr("disabled","disabled");                                                            	
                                                        		Swal.fire({
                                        							title: 'Desea anular el producto <?php echo $objDetallePedido->nom_producto; ?> del pedido',
                                    								text: '',
                                    								icon: 'warning',
                                    								showCancelButton: true,
                                    								confirmButtonColor: '#3085d6',
                                    								cancelButtonColor: '#d33',
                                    								confirmButtonText: 'Anular',
                                    								cancelButtonText: 'Cancelar'
                                    							}).then((result) => {
                                    								if (result.isConfirmed) {
                                    									$.blockUI();
                                    									$.ajax({
                                    									    type: "POST",
                                    									    url: "./index.php?action=getdetailssale",
                                    									    data: "id="+<?php echo $objDetallePedido->id; ?>+"&indicador=4",                        									    
                                    									    dataType: "html",
                                    									    success: function(data) {
                                    									        if (data > 0) {
                                    									        	Swal.fire({
                                                		                                icon: 'success',
                                                		                                title: 'Se anuló correctamente el producto <?php echo $objDetallePedido->nom_producto; ?> del pedido',
                                                										showCancelButton: false,
                                                										confirmButtonColor: '#3085d6',
                                                										confirmButtonText: 'OK'
                                                		                        	}).then((result) => {
                                                										if (result.value) {
                                                    										var opcion = $("#opcion").val();
                                                    										if (opcion === "0") {
                                                												window.location.href = "./index.php?view=salestableitem&usuario=<?php echo $idUsuario; ?>&piso=<?php echo $idPisoSede; ?>&mesa=<?php echo $idMesaPisoSede; ?>";
                                                    										} else {
                                                    											window.location.href = "./index.php?view=salestableitem&usuario=<?php echo $idUsuario; ?>&piso=<?php echo $idPisoSede; ?>&mesa=<?php echo $idMesaPisoSede; ?>+&opcion=<?php echo $opcion; ?>";
                                                    										}
                                                										}
                                                		                        	})
                                    									        } else {
                                    									        	Swal.fire({
                                    		    		    							icon: 'error',
                                    		    		    							title: 'Ocurrio un error al anular el producto <?php echo $objDetallePedido->nom_producto; ?> del pedido'
                                    		    		    						})
                                    										    }
                                    									    },
                                    									    error: function() {
                                    									    	Swal.fire({
                                    	    		    							icon: 'error',
                                    	    		    							title: 'Ocurrio un error al anular el producto <?php echo $objDetallePedido->nom_producto; ?> del pedido'
                                    	    		    						})
                                    									    }
                                    									});
                                    								}
                                    								$("#eliminar<?php echo $item.$objDetallePedido->id; ?>").removeAttr("disabled");
                                    								$.unblockUI();
                                    							})
                                                        	});
                                                        </script>
                                                    </td>
                                                </tr>
                                            <?php 
                                                    }
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
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
        					<input type="hidden" id="id" name="id" value="<?php echo $idPedido; ?>"/>
        					<input type="hidden" id="idsede" name="idsede" value="<?php echo $idSede; ?>"/>
        					<input type="hidden" id="idusuario" name="idusuario" value="<?php echo $idUsuario; ?>"/>
        					<input type="hidden" id="idpiso" name="idpiso" value="<?php echo $idPiso; ?>"/>
        					<input type="hidden" id="idpisosede" name="idpisosede" value="<?php echo $idPisoSede; ?>"/>
        					<input type="hidden" id="idmesa" name="idmesa" value="<?php echo $idMesa; ?>"/>
        					<input type="hidden" id="idmesapisosede" name="idmesapisosede" value="<?php echo $idMesaPisoSede; ?>"/>
        					<input type="hidden" id="idempresa" name="idempresa" value="<?php echo $_SESSION["empresa"]; ?>"/>
        					<input type="hidden" id="idalmacen" name="idalmacen" value="<?php echo $idAlmacen; ?>"/>
        					<input type="hidden" id="tipopedido" name="tipopedido" value="<?php echo $tipoPedido; ?>"/>
        					<input type="hidden" id="opcion" name="opcion" value="<?php echo $opcion; ?>"/>
        					<input type="hidden" id="accion" name="accion" value="1"/>
        				</div>
        			</div>        			
        		</div>
        	</div>
        	</form>
        	<script type="text/javascript">
        		$("#btnCancelar").click(function(){
            		location.href = "./index.php?view=trays";
        		});
        		$(document).ready(function(){
					<?php if (count($lstHistorialCaja) == 0) { ?>
					Swal.fire({
						icon: 'warning',
						title: 'No existe apertura de caja',
						showConfirmButton: false,
						timer: 3000
					}).then((result) => {
						window.location.href = "./index.php?view=openbox";
					})
					<?php } ?>
            		document.getElementById("telefono").focus();
        			<?php if ($idPedido > 0) { ?>
        			document.getElementById("comentario").focus();
        			<?php } ?>
        			$("#categoria").select2();
        			$("#panel1Content").on('shown.bs.collapse',function(){
        			    $(".panel1Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel1Content").on('hidden.bs.collapse',function(){
        			    $(".panel1Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});
        			$('#campo').on('keyup keypress', function(e) {
            			var keyCode = e.keyCode || e.which;
        			    if (keyCode === 13) {
            				e.preventDefault();
        			       	return false;
						}
        			});
        		});
        		$(function(){
    				$("#newsale").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addsale",
    		    				async: true,
    		    				dataType: "html",
    		    				data: $("#newsale").serialize(),
    		    				beforeSend: function() {
    		    					$("#btnRegistrar").attr("disabled", "disabled");
    		    					$("#btnCancelar").attr("disabled", "disabled");
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: 'success',
    										title: 'Se registró correctamente el pedido',
    										showCancelButton: false,
    										confirmButtonColor: '#3085d6',
    										confirmButtonText: 'OK'
    		                        	}).then((result) => {
    										if (result.value) {
    											window.location.href = "./index.php?view=newpayment1&id="+data;
    										}
    		                        	})        					
    		        				} else if (data == 0) {
    		        					Swal.fire({
    		    							icon: 'error',
    		    							title: 'Ocurrio un error al registrar el pedido'
    		    						})
    		        				} else {
    		        					Swal.fire({
    		    							icon: 'error',
    		    							title: 'No hay data registrar el pedido'
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: 'error',
    									title: 'Ocurrio un error al registrar el pedido'
    								})
    		    				},
    		    				complete: function(data) {
    		    					$("#btnRegistrar").removeAttr("disabled");
    		    					$("#btnCancelar").removeAttr("disabled");
    		    					$.unblockUI();
    		    				}
    		    			});
    		    		},
    		    		messages: {
    		    			telefono: {
    		                	required: "Campo obligatorio"
    		                },
    		                datos: {
    		                	required: "Campo obligatorio"
    		                },
    		                direccion: {
    		                	required: "Campo obligatorio"
    		                }
    		            }
    		        });
    			});
    		</script>
        </div>
	</div>
</section>	       