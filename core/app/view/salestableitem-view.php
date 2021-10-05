<?php
    $texto = "Registrar Venta - En Mesa";
    
    $idPedido = $idPago = 0;
    $numComensales = "";
    $esMesero = true;
    
    $objUsuarioActual = UsuarioData::getById($_SESSION["user"]);
    if ($objUsuarioActual->getPerfil()->indicador != 2 && $objUsuarioActual->getPerfil()->indicador != 5) {
        $esMesero = false;
    }    
    
    $objSede = SedeData::getById($_SESSION["sede"]);
    $idSede = $objSede->id;
    $sede = $objSede->nombre;
    
    $idUsuario = $_GET["usuario"];
    $idPisoSede = $_GET["piso"];
    $idMesaPisoSede = $_GET["mesa"];
    
    $fecha = date("d/m/Y");    
    $objUsuario = UsuarioData::getById($idUsuario);
    if ($objUsuario) {
        $usuario = $objUsuario->nombres . " " . $objUsuario->apellidos;
    } else {
        file_put_contents("error" . date("Ymd") . ".log", date("d-m-Y") . ":Error en obtener datos - URL : " . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] . "\n", FILE_APPEND);
        file_put_contents("error" . date("Ymd") . ".log", date("d-m-Y") . ":Error en obtener datos - IdSede : " . $idSede . "\n", FILE_APPEND);
        file_put_contents("error" . date("Ymd") . ".log", date("d-m-Y") . ":Error en obtener datos - IdUsuario : " . $idUsuario . "\n", FILE_APPEND);
        file_put_contents("error" . date("Ymd") . ".log", date("d-m-Y") . ":Error en obtener datos - IdPisoSede : " . $idPisoSede . "\n", FILE_APPEND);
        file_put_contents("error" . date("Ymd") . ".log", date("d-m-Y") . ":Error en obtener datos - IdMesaPisoSede : " . $idMesaPisoSede . "\n", FILE_APPEND);
    }
    
    $objPisoXSede = PisoSedeData::getById($idPisoSede);
    $objPiso = $objPisoXSede->getPiso();
    $idPiso = $objPiso->id;
    $piso = $objPiso->nombre;
    
    $objMesaPisoSedeData = MesaPisoSedeData::getById($idMesaPisoSede);
    $objMesa = $objMesaPisoSedeData->getMesa();
    $idMesa = $objMesa->id;
    $mesa = $objMesa->nombre;
    
    $lstCategoria = CategoriaData::getAll(1, $idSede);
    $lstProducto = ProductoData::getAll(1, $idSede, $lstCategoria[0]->id);
    
    // Validar si tengo pedido pendiente
    $lstDetallePedido = array();
    $lstPedido = PedidoData::getMesaOcupadaXMozo(1, $idSede, $idPiso, $idMesa, $idUsuario);
    if (count($lstPedido) > 0) {
        $lstDetallePedido = DetallePedidoData::getProductosXPedido($lstPedido[0]->id);
        if (count($lstDetallePedido) > 0) {            
            $idPedido = $lstPedido[0]->id;
            
            $fecha = date("d/m/Y", strtotime($lstPedido[0]->fecha));
            
            $objSede = $lstPedido[0]->getSede();
            $sede = $objSede->nombre;
            
            $objPiso = $lstPedido[0]->getPiso();
            $piso = $objPiso->nombre;
            
            $objMesa = $lstPedido[0]->getMesa();
            $mesa = $objMesa->nombre;
            
            $objUsuario = $lstPedido[0]->getUsuario();
            $usuario = $objUsuario->nombres . " " . $objUsuario->apellidos;            
            
            $numComensales = $lstPedido[0]->num_comensales;
            
            $objPago = PagoData::getByPedido($idPedido);
            if ($objPago) {
                $idPago = $objPago->id;
            }
        }
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
    $tipoPedido = 0;
    $lstParametro = ParametroData::getAll(1, "TIPO PEDIDO", "EN MESA");
    if (count($lstParametro) > 0) {
        $tipoPedido = $lstParametro[0]->id;
    }
    unset($_SESSION["productos"]);
    
    $lstParametro = ParametroData::getAll(1, "OPCIONES GENERALES", "IP SERVER LOCAL");    
    $url = $lstParametro[0]->valor1;
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
            				<div class="col-md-2 col-sm-12">
            					<label for="piso">Piso :</label>
            					<input type="text" id="piso" name="piso" class="form-control" value="<?php echo $piso; ?>" disabled/>
            				</div>
            				<div class="col-md-4 col-sm-12">
            					<label for="usuario">Mesero(a) :</label>
            					<input type="text" id="usuario" name="usuario" class="form-control" value="<?php echo $usuario; ?>" disabled/>
            				</div>
            			</div>
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
            					<label for="mesa">Mesa :</label>
            					<input type="text" id="mesa" name="mesa" class="form-control" value="<?php echo $mesa; ?>" disabled/>
            				</div>
            				<div class="col-md-4 col-sm-12">
            					<label for="numcomensales">Num. Comensales :*</label>
            					<input type="text" id="numcomensales" name="numcomensales" class="form-control" value="<?php echo $numComensales; ?>" onkeypress="return soloNumeros(event)" required/>
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
    							<input type="text" id="cantidad" name="cantidad" class="form-control" placeholder="0" maxlength="2" value="1" onkeypress="return soloNumeros(event);"/>
            				</div>
            				<div class="col-md-3 col-sm-12">
    							<label for="comentario">Comentario :</label>
    							<input type="text" id="comentario" name="comentario" class="form-control" placeholder="Comentario" maxlength="50" value=""/>
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
    							<?php if (count($lstDetallePedido) == 0) { ?>
    							<button type="submit" id="btnRegistrar" class="btn btn-success">Registrar</button>
    							<?php } ?>
            					<button type="button" id="btnCancelar" class="btn btn-danger">Regresar</button>
            					<?php if (count($lstDetallePedido) > 0) { ?>
    							<button type="button" id="btnComanda" class="btn btn-info">Comanda</button>
    							<button type="button" id="btnPrecuenta" class="btn btn-warning">Precuenta</button>
    							<script type="text/javascript">
									$("#btnComanda").click(function(){
										$("#btnCancelar").attr("disabled","disabled");
										$("#btnComanda").attr("disabled","disabled");
										$("#btnPrecuenta").attr("disabled","disabled");
										var pedido = $("#id").val();
										$.post("./?action=utilitarios", {
        									pedido: pedido,
        									impresion: 0
        	                           	}, function (data) {
            	                           	if (data === "-1") {
                                       			Swal.fire({
                    								icon: "warning",
                    								title: "No existe impresora configurada"
                    							})
                                       		} else if (data !== "") {
                	                           	var id = data.split(",");
                	                           	for (var indice=0; indice<id.length; indice++) {
                    	                           	var area = id[indice];
                    	                        	$.post("./?action=utilitarios", {
                    									pedido: pedido,
                    									impresion: 1,
                    									area: area 
                	                           		}, function (data) {
                    	                           		var url = "http://<?php echo $url; ?>/print/print_ticket.php";
                    	                           		$.post(url, { datos: data });	
                	                           		})	
                	                           	}
            	                           	} else {
                                        		Swal.fire({
                    								icon: "warning",
                    								title: "No existe productos nuevos para imprimir"
                    							})
                    						}
            	                           	$("#btnCancelar").removeAttr("disabled");
        	                           		$("#btnComanda").removeAttr("disabled");
        	                           		$("#btnPrecuenta").removeAttr("disabled");
        	                           		$.unblockUI();
        	                           	});
									});
									$("#btnPrecuenta").click(function(){
										$("#btnCancelar").attr("disabled","disabled");
										$("#btnComanda").attr("disabled","disabled");
										$("#btnPrecuenta").attr("disabled","disabled");
										var pedido = $("#id").val();
										$.post("./?action=utilitarios", {
        									pedido: pedido,
        									impresion: 2
        	                           	}, function (data) {
        	                           		var url = "http://<?php echo $url; ?>/print/print_precuenta.php";
        	                           		$.post(url, { datos: data });
        	                           		$("#btnCancelar").removeAttr("disabled");
        	                           		$("#btnComanda").removeAttr("disabled");
        	                           		$("#btnPrecuenta").removeAttr("disabled");
        	                           		$.unblockUI();
        	                           	});
									});
    							</script>
    							<?php } ?>
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
    									<em class="fa fa-cutlery"></em>
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
    	    										icon: "warning",
    	    										title: "Seleccione una categoría"
    	    									})
    	    									return false;
    	    								}
    	    								if (producto === "") {
    	    									Swal.fire({
    	    										icon: "warning",
    	    										title: "Seleccione un producto"
    	    									})
    	    									return false;
    	    								}
    	    								if (cantidad === "") {
    	    									Swal.fire({
    	    										icon: "warning",
    	    										title: "Ingrese cantidad"
    	    									})
    	    									return false;
    	    								} else if (isNaN(cantidad)) {
    	    									Swal.fire({
    	    										icon: "warning",
    	    										title: "Ingrese cantidad válida"
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
        	    									setTimeout(function(){$("#comentario").trigger("focus")},1);
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
                                    				<th scope="col" style="width: 10%;">Cantidad</th>
                                    				<?php if (!$esMesero && $idPago > 0) { ?>
                                    				<th scope="col" style="text-align: right;">Cantidad Pagada</th>
                                    				<?php } ?>
                                    				<th scope="col" style="text-align: center;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                if (count($lstDetallePedido) > 0) {
                                                    $item = $indice = 1;
                                                    foreach ($lstDetallePedido as $objDetallePedido) {
                                                        $objProducto = $objDetallePedido->getProducto();
                                                        
                                                        $habilitado = "";
                                                        if ($objDetallePedido->cantidad_pagada > 0) {
                                                            $habilitado = "disabled";
                                                        }
                                            ?>
                                            	<tr>
                                                    <td style="text-align: left;"><?php echo $indice++; ?></td>
                                                    <td style="text-align: left;"><?php echo $objDetallePedido->categoria; ?></td>
                                                    <td style="text-align: left;"><?php echo $objDetallePedido->nom_producto; ?></td>
                                                    <td style="text-align: left;"><?php echo $objDetallePedido->tipo; ?></td>
                                                    <td style="text-align: left;">
                                                    	<input type="text" id="comentario<?php echo $item.$objDetallePedido->id; ?>" name="comentario<?php echo $item.$objProducto->id; ?>" value="<?php echo $objDetallePedido->comentario; ?>" maxlength="50" class="form-control" <?php echo $habilitado; ?>/>
                                                    </td>	
                                                    <td style="text-align: right;">
                                                    	<input type="text" id="cantidad<?php echo $item.$objDetallePedido->id; ?>" name="cantidad<?php echo $item.$objProducto->id; ?>" value="<?php echo $objDetallePedido->cantidad; ?>" maxlength="3" class="form-control" dir="rtl" onkeypress="return soloNumeros(event)" <?php echo $habilitado; ?>/>
                                                    </td>	
                                                    <?php if (!$esMesero && $idPago > 0) { ?>
                                                    <td style="text-align: right;"><?php echo number_format($objDetallePedido->cantidad_pagada, 2); ?></td>
                                                    <?php } ?>
                                                    <td style="text-align: center;">
                                                    	<a id="guardar<?php echo $item.$objProducto->id; ?>" title="Guardar" class="btn btn-success btn-xs" <?php echo $habilitado; ?>><em class="fa fa-save"></em></a>
                                                    	<?php if (!$esMesero) { ?>
                                                    	<a id="eliminar<?php echo $item.$objDetallePedido->id; ?>" title="Eliminar" class="btn btn-danger btn-xs" <?php echo $habilitado; ?>><em class="fa fa-trash"></em></a>
                                                    	<?php } ?>
                                                        <script type="text/javascript">
                                                        	$("#guardar<?php echo $item.$objProducto->id; ?>").click(function() {
                                                        		$("#guardar<?php echo $item.$objProducto->id; ?>").attr("disabled","disabled");
                                                        		$("#eliminar<?php echo $item.$objDetallePedido->id; ?>").attr("disabled","disabled");
																var comentario = $("#comentario<?php echo $item.$objDetallePedido->id; ?>").val();
                                                        		var cantidad = $("#cantidad<?php echo $item.$objDetallePedido->id; ?>").val();
																var opcion = $("#opcion").val();
																if (cantidad === "") {
							                                        document.getElementById("cantidad<?php echo $item.$objProducto->id; ?>").focus();
							                                        Swal.fire({
							                                            icon: "warning",
							                                            title: "Ingrese la cantidad"
							                    				    })
							                                    } else if (isNaN(cantidad)) {
							                                        $("#cantidad<?php echo $item.$objProducto->id; ?>").val("");
							                                        document.getElementById("cantidad<?php echo $item.$objProducto->id; ?>").focus();
							                                        Swal.fire({
							    	    							    icon: "warning",
							    	    						        title: "Ingrese cantidad válida"
							                                        })
							                                    } else {
																	$.blockUI();
                                                                	$.post("./?action=getdetailssale", {
                                                                        item: <?php echo $item; ?>,
                                                                        producto: <?php echo $objDetallePedido->producto; ?>,
                                                                        cantidad: cantidad,
                                                                        indicador: 5,
                                                                        idDetallePedido: <?php echo $objDetallePedido->id; ?>,
																		almacen: <?php echo $idAlmacen; ?>,
																		comentario: comentario
                                                                    }, function (data) {
                                                                    	if (data !== "0") {
                                                                        	if (opcion === "0") {
                                                                        		window.location.href = "./index.php?view=salestableitem&usuario=<?php echo $idUsuario; ?>&piso=<?php echo $idPisoSede; ?>&mesa=<?php echo $idMesaPisoSede; ?>";
                                						    				} else {
                                						    					window.location.href = "./index.php?view=salestableitem&usuario=<?php echo $idUsuario; ?>&piso=<?php echo $idPisoSede; ?>&mesa=<?php echo $idMesaPisoSede; ?>&opcion=<?php echo $opcion; ?>";
                                						    				}
                                                                        } else {
                                                                        	$("#guardar<?php echo $item.$objProducto->id; ?>").removeAttr("disabled");
                                                                    		$("#eliminar<?php echo $item.$objDetallePedido->id; ?>").removeAttr("disabled");
                                                                        	Swal.fire({
                            		    		    							icon: "error",
                            		    		    							title: "Ocurrio un error al actualizar el detalle del producto <?php echo $objDetallePedido->nom_producto; ?> del pedido"
                            		    		    						})
                                                                        	$.unblockUI();
                                                                        }
                                                                    });
																}
                                                        	});
                                                            $("#eliminar<?php echo $item.$objDetallePedido->id; ?>").click(function() {
                                                            	$("#guardar<?php echo $item.$objProducto->id; ?>").attr("disabled","disabled");
                                                        		$("#eliminar<?php echo $item.$objDetallePedido->id; ?>").attr("disabled","disabled");                                                            	
                                                        		Swal.fire({
                                        							title: "Desea anular el producto <?php echo $objDetallePedido->nom_producto; ?> del pedido",
                                    								icon: "warning",
                                    								showCancelButton: true,
                                    								confirmButtonColor: "#3085d6",
                                    								cancelButtonColor: "#d33",
                                    								confirmButtonText: "Anular",
                                    								cancelButtonText: "Cancelar"
                                    							}).then((result) => {
                                    								if (result.isConfirmed) {
                                    									$.ajax({
                                    									    type: "POST",
                                    									    url: "./index.php?action=getdetailssale",
                                    									    data: "id="+<?php echo $objDetallePedido->id; ?>+"&indicador=4",                        									    
                                    									    dataType: "html",
                                    									    beforeSend: function() {
                                            									$.blockUI();
                                            		    		    		},
                                    									    success: function(data) {
                                    									        if (data > 0) {
                                    									        	Swal.fire({
                                                		                                icon: "success",
                                                		                                title: "Se anuló correctamente el producto <?php echo $objDetallePedido->nom_producto; ?> del pedido",
                                                										showCancelButton: false,
                                                										confirmButtonColor: "#3085d6",
                                                										confirmButtonText: "OK"
                                                		                        	}).then((result) => {
                                                										var opcion = $("#opcion").val();
                                                    									if (opcion === "0") {
                                                											window.location.href = "./index.php?view=salestableitem&usuario=<?php echo $idUsuario; ?>&piso=<?php echo $idPisoSede; ?>&mesa=<?php echo $idMesaPisoSede; ?>";
                                                    									} else {
                                                    										window.location.href = "./index.php?view=salestableitem&usuario=<?php echo $idUsuario; ?>&piso=<?php echo $idPisoSede; ?>&mesa=<?php echo $idMesaPisoSede; ?>&opcion=<?php echo $opcion; ?>";
                                                    									}
                                                		                        	})
                                    									        } else {
                                    									        	$("#guardar<?php echo $item.$objProducto->id; ?>").removeAttr("disabled");
                                                                            		$("#eliminar<?php echo $item.$objDetallePedido->id; ?>").removeAttr("disabled");
                                    									        	Swal.fire({
                                    		    		    							icon: "error",
                                    		    		    							title: "Ocurrio un error al anular el producto <?php echo $objDetallePedido->nom_producto; ?> del pedido"
                                    		    		    						})
                                    										    }
                                    									    },
                                    									    error: function() {
                                    									    	$("#guardar<?php echo $item.$objProducto->id; ?>").removeAttr("disabled");
                                                                        		$("#eliminar<?php echo $item.$objDetallePedido->id; ?>").removeAttr("disabled");
                                    									    	Swal.fire({
                                    	    		    							icon: "error",
                                    	    		    							title: "Ocurrio un error al anular el producto <?php echo $objDetallePedido->nom_producto; ?> del pedido"
                                    	    		    						})
                                    									    },
                                    									    complete: function(data) {
                                    									    	$.unblockUI();
                                    		    		    				}
                                    									});
                                    								}
                                    								$("#guardar<?php echo $item.$objProducto->id; ?>").removeAttr("disabled");
                                                            		$("#eliminar<?php echo $item.$objDetallePedido->id; ?>").removeAttr("disabled");                                    								                                    							
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
        			$.blockUI();
            		<?php if ($opcion == 0) { ?>
        			location.href = "./index.php?view=salestable&usuario="+$("#idusuario").val()+"&piso="+$("#idpisosede").val();
        			<?php } else { ?>
        			location.href = "./index.php?view=trays";
        			<?php } ?>
        		});
        		$(document).ready(function(){
        			setTimeout(function(){$("#numcomensales").trigger("focus")},1);
        			<?php if ($idPedido > 0) { ?>
        			setTimeout(function(){$("#comentario").trigger("focus")},1);
        			<?php } ?>
        			$("#categoria").select2();
        			$("#panel1Content").on("shown.bs.collapse",function(){
        			    $(".panel1Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel1Content").on("hidden.bs.collapse",function(){
        			    $(".panel1Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});
        			$("#campo").on("keyup keypress", function(e) {
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
    		                                icon: "success",
    										title: "Se registró correctamente el pedido",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    										var usuario = $("#idusuario").val();
    						    			var piso = $("#idpisosede").val();
    						    			var mesa = $("#idmesapisosede").val();
    						    			window.location.href = "./index.php?view=salestableitem&usuario="+usuario+"&piso="+piso+"&mesa="+mesa;
    		                        	})
    		        				} else if (data == 0) {
    		        					Swal.fire({
    		    							icon: "error",
    		    							title: "Ocurrio un error al registrar el pedido"
    		    						})
    		        				} else {
    		        					Swal.fire({
    		    							icon: "error",
    		    							title: "No hay data registrar el pedido"
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al registrar el pedido"
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
    		    			numcomensales: {
    		                	required: "Campo obligatorio"
    		                }
    		            }
    		        });
    			});
    		</script>
        </div>
	</div>
</section>