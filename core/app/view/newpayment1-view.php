<?php
    $id = $idPedido = $descuento = $montoDescuentoVenta = $montoAbonado = $totalVenta = $indTipoPedido = $tipoPedido = 0;
    $fecha = date("d/m/Y");    
    $sede = $piso = $mesa = $nomUsuario = $url = $soloLectura = $contacto = $nomTipoPedido = "";
    $telefono = $contacto = $direccion = $hora = "";
    $texto = "Registrar Pago - Total";
    $textoBoton = "Pagar";
    $faltaComprobante = false;
    $lstDetallePedido = array();
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
    
        $objPedido = PedidoData::getById($id);
        $idPedido = $objPedido->id;

        // Sede
        $objSede = $objPedido->getSede();
        $sede = $objSede->nombre;
    
        // Piso
        $objPiso = $objPedido->getPiso();
        if ($objPiso) {
            $piso = $objPiso->nombre;
        }
    
        // Mesa
        $objMesa = $objPedido->getMesa();
        if ($objMesa) {
            $mesa = $objMesa->nombre == "" ? "-" : $objMesa->nombre;
        }
        
        // Mesero
        $objUsuario = $objPedido->getUsuario();
        $nomUsuario = $objUsuario->nombres . " " . $objUsuario->apellidos;
    
        $montoDescuentoVenta = $objPedido->descuento_pedido;
        $totalVenta = $objPedido->total - $montoDescuentoVenta;
        $estado = $objPedido->estado;
        if ($estado == 2 || $estado == 3) {            
            $soloLectura = "disabled";
            $totalVenta = $objPedido->total;
        }        
        $telefono = $objPedido->telefono;
        $contacto = $objPedido->datos;
        $direccion = $objPedido->direccion;
        $hora = $objPedido->hora;
        
        $objParametro = ParametroData::getById($objPedido->tipo);
        $tipoPedido = $objParametro->valor1;
        $indTipoPedido = $objParametro->valor2;
        $nomTipoPedido = $objParametro->nombre;
        
        $lstParametro = ParametroData::getAll(1, "OPCIONES GENERALES", "IP SERVER LOCAL");
        $url = $lstParametro[0]->valor1;
        
        $lstDetallePedido = DetallePedidoData::getProductosXPedido($idPedido);
    }
    $idCredito = 0;
    $numDocumento = $datosCliente = "";
    if (isset($_GET["credito"])) {
        $idCredito = $_GET["credito"];
        $objCredito = CreditoData::getById($idCredito);
        
        $numDocumento = $objCredito->num_documento;
        $datosCliente = $objCredito->datos; 
    }
    $lstFormaPago = ParametroData::getAll(1, "FORMA PAGO");
    if ($idCredito > 0) {
        $arrFormaPago = array();
        foreach ($lstFormaPago as $objFormaPago) {
            if ($objFormaPago->valor1 != 3) {
                $arrFormaPago[] = $objFormaPago;
            }
        }
        $lstFormaPago = $arrFormaPago;
    }
    $lstTipoTarjeta = ParametroData::getAll(1, "TIPO TARJETA");
    $lstTipoDocumento = ParametroData::getAll(1, "TIPO DOCUMENTO");
    $lstTipoPago = ParametroData::getAll(1, "TIPO PAGO");
    
    $idTipoPago = 0;
    foreach ($lstTipoPago as $objTipoPago) {
        if ($objTipoPago->valor1 == 1) {
            $idTipoPago = $objTipoPago->id;
        }
    }
    
    $idPago = 0;
    $lstHistorialPago = $lstHistorialDocumento = array();
    $objPago = PagoData::getByPedido($id);
    if ($objPago) {
        $idPago = $objPago->id;   
        $montoAbonado = $objPago->monto_pagado_efectivo + $objPago->monto_pagado_tarjeta;    
        $lstHistorialPago = HistorialPagoData::getAllByPago($objPago->id);    
        $lstHistorialDocumento = HistorialDocumentoData::getAllByPago($objPago->id);
        
        // En caso no tenga comprobante asociado al pedido
        if (count($lstHistorialDocumento) == 0) {
            $faltaComprobante = true;
        }
    } else {
        $faltaComprobante = true;
    }

    $idUsuario = $idCaja = 0;
    $objUsuario = UsuarioData::getById($_SESSION["user"]);
    if ($objUsuario->getPerfil()->indicador == 1) {
        // Cajero
        $idCaja = $objUsuario->caja;
        $idUsuario = $objUsuario->id;
    }
    
    $exonerado = 0;
    if ($_SESSION["exonerado"] == 1) {
        $exonerado = 1;
    }    
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newpayment" action="index.php?action=addsale" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>
        		<div class="panel-body">
        			<fieldset class="scheduler-border">
    					<legend class="scheduler-border">Datos del Pedido</legend>
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
            					<label for="fecha">Fecha :</label>
            					<input type="text" id="fecha" name="fecha" class="form-control" value="<?php echo $fecha; ?>" disabled/>
            				</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="sede">Sede :</label>
            					<input type="text" id="sede" name="sede" class="form-control" value="<?php echo $sede; ?>" disabled/>
            				</div>
            				<?php if ($tipoPedido > 0) { ?>
            				<div class="col-md-2 col-sm-12">
            					<label for="piso">Piso :</label>
            					<input type="text" id="piso" name="piso" class="form-control" value="<?php echo $piso; ?>" disabled/>
            				</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="mesa">Mesa :</label>
            					<input type="text" id="mesa" name="mesa" class="form-control" value="<?php echo $mesa; ?>" disabled/>
            				</div>
            				<?php } ?>
            				<div class="col-md-4 col-sm-12">
            					<label for="usuario">Mesero(a) :</label>
            					<input type="text" id="nomusuario" name="nomusuario" class="form-control" value="<?php echo $nomUsuario; ?>" disabled/>
            				</div>
            				<?php if ($tipoPedido == 0) { ?>
            				<div class="col-md-2 col-sm-12">
            					<label for="tipopedido">Tipo :</label>
            					<input type="text" id="tipopedido" name="tipopedido" class="form-control" value="<?php echo $nomTipoPedido; ?>" disabled/>
            				</div>
            				<?php } ?>
            			</div>
            			<?php if ($indTipoPedido > 0) { ?>
            			<div class="form-group">
            				<?php if ($indTipoPedido == 1) { ?>
            				<div class="col-md-2 col-sm-12">
            					<label for="telefono">Teléfono :</label>
            					<input type="text" id="telefono" name="telefono" class="form-control" value="<?php echo $telefono; ?>" disabled/>
            				</div>
            				<?php } ?>
            				<div class="col-md-4 col-sm-12">
            					<label for="contacto">Contacto :</label>
            					<input type="text" id="contacto" name="contacto" class="form-control" value="<?php echo $contacto; ?>" disabled/>
            				</div>
            				<?php if ($indTipoPedido == 1) { ?>
            				<div class="col-md-4 col-sm-12">
            					<label for="direccion">Dirección :</label>
            					<input type="text" id="direccion" name="direccion" class="form-control" value="<?php echo $direccion; ?>" disabled/>
            				</div>
            				<?php } ?>
            				<div class="col-md-2 col-sm-12">
            					<label for="hora">Hora :</label>
            					<input type="text" id="hora" name="hora" class="form-control" value="<?php echo $hora; ?>" disabled/>
            				</div>
            			</div>
            			<?php } ?>
            			<?php if ($idCredito > 0) { ?>
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
            					<label for="numdoccliente">Num. Documento :</label>
            					<input type="text" id="numdoccliente" name="numdoccliente" class="form-control" value="<?php echo $numDocumento; ?>" disabled/>
            				</div>
            				<div class="col-md-4 col-sm-12">
            					<label for="datoscliente">Cliente :</label>
            					<input type="text" id="datoscliente" name="datoscliente" class="form-control" value="<?php echo $datosCliente; ?>" disabled/>
            				</div>
            			</div>
            			<?php } ?>
            			<?php if ($soloLectura != "") { ?>
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
            					<label for="montototal">Monto Total :</label>
            					<input type="text" id="montototal" name="montototal" class="form-control" value="<?php echo number_format($totalVenta, 2); ?>" dir="rtl" readonly="readonly"/>
            				</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="montodescuento">Monto Descuento :</label>
            					<input type="text" id="montodescuento" name="montodescuento" class="form-control" value="<?php echo number_format($montoDescuentoVenta, 2); ?>" dir="rtl" readonly="readonly"/>
            				</div>            				
            				<div class="col-md-2 col-sm-12">
            					<label for="montoabonado">Monto Pagado :</label>
            					<input type="text" id="montoabonado" name="montoabonado" class="form-control" value="<?php echo number_format($montoAbonado, 2); ?>" dir="rtl" disabled="disabled"/>
            				</div>
            			</div>
            			<?php } ?>
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
                                                        <th scope="col">Producto</th>
                                                        <th scope="col">Tipo</th>
                                                        <th scope="col" style="text-align: right;">Cantidad</th>
                                        				<th scope="col" style="text-align: right;">Precio</th>                                    				
                                        				<th scope="col" style="text-align: right;">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                    if (count($lstDetallePedido) > 0) {
                                                        $item = 1;
                                                        $totalCantidad = $total = 0;
                                                        foreach ($lstDetallePedido as $objDetallePedido) {
                                                            $totalCantidad += $objDetallePedido->cantidad;
                                                            $total += $objDetallePedido->total;
                                                ?>
                                                	<tr>
                                                        <td style="text-align: left;"><?php echo $item++; ?></td>
                                                        <td style="text-align: left;"><?php echo $objDetallePedido->nom_producto; ?></td>
                                                        <td style="text-align: left;"><?php echo $objDetallePedido->getProducto()->getTipo()->nombre; ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objDetallePedido->cantidad, 2); ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objDetallePedido->precio_venta, 2); ?></td>
                                                        <td style="text-align: right;"><?php echo number_format($objDetallePedido->total, 2); ?></td>                                                        
                                                    </tr>
                                                <?php 
                                                        }
                                                    }
                                                ?>
                                                </tbody>
                                                <tfoot>
                                                	<tr>
                                                		<td style="text-align: left; font-weight: bold;" colspan="3">TOTAL</td>
                                                        <td style="text-align: right; font-weight: bold;"><?php echo number_format($totalCantidad, 2); ?></td>
                                                        <td style="text-align: right; font-weight: bold;"></td>
                                                        <td style="text-align: right; font-weight: bold;"><?php echo number_format($total, 2); ?></td>
                                                	</tr>
                                                </tfoot>
                                          	</table>
                                       		</div>
                                      	</div>
                                 	</div>
                            	</div>
                          	</div>
            			</div>
            		</fieldset>
        			<?php if ($soloLectura == "") { ?>
        			<fieldset class="scheduler-border">
    					<legend class="scheduler-border">Detalle de Pago</legend>
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
            					<label for="montototal">Monto Total :</label>
            					<input type="text" id="montototal" name="montototal" class="form-control" value="<?php echo number_format($totalVenta, 2); ?>" dir="rtl" readonly="readonly"/>
            					<input type="hidden" id="indformapago" name="indformapago" value="0"/>
            				</div>            				
            				<div class="col-md-2 col-sm-12">
            					<label for="formapago">Forma Pago :</label>
            					<select id="formapago" name="formapago" class="form-control">    							
        							<?php foreach ($lstFormaPago as $objFormaPago) { ?>
        							<option value="<?php echo $objFormaPago->id; ?>"><?php echo $objFormaPago->nombre; ?></option>
          							<?php } ?>
        						</select>
        						<script type="text/javascript">
    								$("#formapago").change(function(){
    									var formapago = $("#formapago").val();
    									$.blockUI();
    									$.post("./?action=utilitarios", {
    										formapago: formapago,
                                        }, function (data) {
                                            if (data === "0") {
                                                // Efectivo
                                                $("#lblmontoefectivo").hide();
                                                $("#lblmontotarjeta").hide();
                                                $("#lblnumdocumeto").hide();
                                                $("#lblcliente").hide();                                                
                                            	$("#montopagado").val("<?php echo number_format($totalVenta, 2); ?>");
                                                $("#lbltipotarjeta").hide();
                                                $("#lblnumope").hide();
                                                $("#divMixta").hide();
                                            	$("#divCredito").hide();
                                            	$("#numope").val("");
                                            	$("#montoefectivo").val("0.00");
                                            	$("#montotarjeta").val("0.00");
                                            	$("#numdocumento").val("");
                                            	$("#nomcliente").val("");
                                            	$("#numdocumento").removeAttr("required");
                                            	$("#nomcliente").removeAttr("required");
                                            	$("#generadoc").removeAttr("disabled");     	
                                            	document.getElementById("descuento").focus();
                                            } else if (data === "1") {
    											// Tarjeta
    											$("#lblmontoefectivo").hide();
    											$("#lblmontotarjeta").hide();
    											$("#lblnumdocumeto").hide();
                                                $("#lblcliente").hide();
                                            	$("#montopagado").val("<?php echo number_format($totalVenta, 2); ?>");
                                            	$("#lbltipotarjeta").show();
                                            	$("#lblnumope").show();
                                            	$("#divMixta").hide();
                                            	$("#divCredito").hide();
                                            	$("#numope").val("");
                                            	$("#montoefectivo").val("0.00");
                                            	$("#montotarjeta").val("0.00");
                                            	$("#numdocumento").val("");
                                            	$("#nomcliente").val("");
                                            	$("#numdocumento").removeAttr("required");
                                            	$("#nomcliente").removeAttr("required");
                                            	$("#generadoc").removeAttr("disabled");
                                            	document.getElementById("descuento").focus();
                                            } else if (data === "2") {
												// Mixta												
												$("#lblmontoefectivo").show();
												$("#lblmontotarjeta").show();
												$("#lblnumdocumeto").hide();
                                                $("#lblcliente").hide();
                                                $("#lbltipotarjeta").show();
												$("#montopagado").val("<?php echo number_format($totalVenta, 2); ?>");
												$("#lblnumope").show();
												$("#divMixta").show();
                                            	$("#divCredito").hide();
												$("#numope").val("");
												$("#montoefectivo").val("0.00");
												$("#montotarjeta").val("0.00");
												$("#numdocumento").val("");
                                            	$("#nomcliente").val("");
                                            	$("#numdocumento").removeAttr("required");
                                            	$("#nomcliente").removeAttr("required");
												$("#generadoc").removeAttr("disabled");
                                            	document.getElementById("descuento").focus();
                                            } else if (data === "3") {
                                            	// Credito
												$("#lblmontoefectivo").hide();
                                                $("#lblmontotarjeta").hide();
                                                $("#lblnumdocumeto").show();
                                                $("#lblcliente").show();
                                                $("#lbltipotarjeta").hide();
                                                $("#lblnumope").hide();
                                            	$("#montopagado").val("<?php echo number_format($totalVenta, 2); ?>");
                                            	$("#divMixta").hide();
                                            	$("#divCredito").show();
                                            	$("#numope").val("");
                                            	$("#montoefectivo").val("0.00");
                                            	$("#montotarjeta").val("0.00");
                                            	$("#numdocumento").val("");
                                            	$("#nomcliente").val("");
                                            	$("#numdocumento").attr("required","required");
                                            	$("#nomcliente").attr("required","required");
                                            	$("#generadoc").attr("disabled","disabled");
                                            	document.getElementById("descuento").focus();
                                            }
                                            $("#indformapago").val(data);
                                            $("#descuento").val("0.00");
                                            $.unblockUI();
                                        });
    								});
        						</script>
            			    </div>
            			    <div class="col-md-2 col-sm-12">
            					<label for="descuento">Descuento (%) :</label>
            					<input type="text" id="descuento" name="descuento" class="form-control" placeholder="0.00" value="<?php echo number_format($descuento, 2); ?>" dir="rtl" onkeypress="return filterFloat(event,this);"/>
            					<script type="text/javascript">
    								$("#descuento").blur(function(){
        								var descuento = $("#descuento").val();
        								if (descuento === "") {
            								$("#descuento").val("0.00");
            								$("#montopagado").val(<?php echo $totalVenta; ?>);
        								} else {
        									var montototal = <?php echo $totalVenta; ?>;
        									var calculo = (montototal * 1) * ((descuento * 1) / 100);
        									var montoactual = (montototal * 1) - Number(calculo.toFixed(1));
        									var montopagado = fillDecimals(Number(montoactual.toFixed(2)),2);
        									$("#montopagado").val(montopagado);

        									var montoefectivo = $("#montoefectivo").val();
        									var montotarjeta = $("#montotarjeta").val();
        									if ((montoefectivo * 1) > 0 && (montotarjeta * 1) > 0) {
        										document.getElementById("montoefectivo").focus();
        										$("#montotarjeta").val("0.00");		        									
            									Swal.fire({
    		    									icon: "warning",
    		    									title: "Ingresar nuevamente los montos a pagar en efectivo y tarjeta"
    		    								})	
        									}
        								}
    								});
    								$("#descuento").click(function(){
    									$("#descuento").val("");
    								});
    							</script>
            				</div>
            			    <div class="col-md-2 col-sm-12">
            					<label for="montopagado">Monto a pagar :*</label>
            					<input type="text" id="montopagado" name="montopagado" class="form-control" placeholder="0.00" value="<?php echo number_format($totalVenta, 2); ?>" dir="rtl" onkeypress="return filterFloat(event,this);" readonly/>
            				</div>            				
            				<div id="lbltipotarjeta" class="col-md-2 col-sm-12" style="display: none;">
            					<label for="tipotarjeta">Tipo Tarjeta :</label>
            					<select id="tipotarjeta" name="tipotarjeta" class="form-control">
            						<?php foreach ($lstTipoTarjeta as $objTipoTarjeta) { ?>
        							<option value="<?php echo $objTipoTarjeta->id; ?>"><?php echo $objTipoTarjeta->nombre; ?></option>
          							<?php } ?>
            					</select>
            				</div>
            			    <div id="lblnumope" class="col-md-2 col-sm-12" style="display: none;">
            					<label for="numope">Num. Operación :</label>
            					<input type="text" id="numope" name="numope" class="form-control" value="" maxlength="20"/>        					
            				</div>
            			</div>
            			<div id="divMixta" class="form-group" style="display: none;">
            				<div id="lblmontoefectivo" class="col-md-2 col-sm-12" style="display: none;">
            					<label for="montoefectivo">Monto Efectivo :</label>
            					<input type="text" id="montoefectivo" name="montoefectivo" class="form-control" placeholder="0.00" value="" dir="rtl" onkeypress="return filterFloat(event,this);"/>
            					<script type="text/javascript">
            						$("#montoefectivo").click(function(){
            							$("#montoefectivo").val("");
            						});
            						$("#montoefectivo").focus(function(){
            							$("#montoefectivo").val("");
            						});
            						$("#montoefectivo").blur(function(){
            							var montoefectivo = $("#montoefectivo").val();
            							if (montoefectivo === "") {
            								$("#montoefectivo").val("0.00");
            							}
            						});
            						$("#montoefectivo").change(function(){
        								var montoefectivo = $("#montoefectivo").val();
        								if (montoefectivo === "") {
        									document.getElementById("montoefectivo").focus();
        									Swal.fire({
		    									icon: "warning",
		    									title: "Ingrese el monto a pagar en efectivo"
		    								})
        								} else {
            								var resultado = (montoefectivo * 1);
            								var montoapagar = $("#montopagado").val();
            								if (resultado > (montoapagar * 1)) {
            									document.getElementById("montoefectivo").focus();
	            								Swal.fire({
			    									icon: "warning",
			    									title: "El monto a pagar en efectivo es mayor al monto del pedido"
			    								})
        									} else {
        										document.getElementById("montotarjeta").focus();
        										$("#montotarjeta").val("");
        									}
        								}
    								});
    							</script>
            				</div>
            				<div id="lblmontotarjeta" class="col-md-2 col-sm-12" style="display: none;">
            					<label for="montotarjeta">Monto Tarjeta :</label>
            					<input type="text" id="montotarjeta" name="montotarjeta" class="form-control" placeholder="0.00" value="" dir="rtl" onkeypress="return filterFloat(event,this);"/>
            					<script type="text/javascript">
                					$("#montotarjeta").click(function(){
            							$("#montotarjeta").val("");
            						});
            						$("#montotarjeta").focus(function(){
            							$("#montotarjeta").val("");
            						});
            						$("#montotarjeta").blur(function(){
            							var montotarjeta = $("#montotarjeta").val();
            							if (montotarjeta === "") {
            								$("#montotarjeta").val("0.00");
            							}
            						});
            						$("#montotarjeta").change(function(){
        								var montoefectivo = $("#montoefectivo").val();
        								var montotarjeta = $("#montotarjeta").val();
        								if (montotarjeta === "") {
        									document.getElementById("montotarjeta").focus();
            								Swal.fire({
		    									icon: "warning",
		    									title: "Ingrese el monto a pagar con tarjeta"
		    								})
        								} else {
            								var resultado = (montoefectivo * 1) + (montotarjeta * 1);
            								var montoapagar = $("#montopagado").val();
            								if (resultado > (montoapagar * 1)) {
            									document.getElementById("montotarjeta").focus();
	            								Swal.fire({
			    									icon: "warning",
			    									title: "La suma del monto a pagar en efectivo y tarjeta es mayor al monto del pedido"
			    								})
        									} else if (resultado < (montoapagar * 1)) {
            									var indformapago = $("#indformapago").val();
            									if (indformapago === "2") {
            										document.getElementById("montotarjeta").focus();
            										Swal.fire({
    			    									icon: "warning",
    			    									title: "La suma del monto a pagar en efectivo y tarjeta es menor al monto del pedido"
    			    								})
            									}
        									}
        								}
    								});
    							</script>
            				</div>
            			</div>
            			<div id="divCredito" class="form-group" style="display: none;">
                			<div id="lblnumdocumeto" class="col-md-2 col-sm-12" style="display: none;">
                				<label for="numdocumento">Num. Documento :*</label>
            					<input type="text" id="numdocumento" name="numdocumento" class="form-control" value="" maxlength="12" onkeypress="return soloNumeros(event)"/>
            					<script type="text/javascript">
									$("#numdocumento").change(function(){
										var numdocumento = $("#numdocumento").val();
										if (numdocumento === "") {
											$("#nomcliente").val("");
											$("#nomcliente").removeAttr("readonly");
										} else {
											$.blockUI();
											$.post("./?action=utilitarios", {
	    										numdocumento: numdocumento,
	                                        }, function (data) {
		                                        if (data !== "") {
    		                                    	$("#nomcliente").val(data);
    		                                    	$("#nomcliente").attr("readonly","readonly");
		                                        } else {
		                                        	$("#nomcliente").val("");
    		                                    	$("#nomcliente").removeAttr("readonly");
		                                        }
		                                        $.unblockUI();
	                                        });
										}
									});
            					</script>
            				</div>
            				<div id="lblcliente" class="col-md-4 col-sm-12" style="display: none;">
            					<label for="nomcliente">Cliente :*</label>
            					<input type="text" id="nomcliente" name="nomcliente" class="form-control" value="" maxlength="150"/>        					
            				</div>
            			</div>
            		</fieldset>
            		<?php } ?>
            		<?php if ($faltaComprobante) { ?>
            		<fieldset class="scheduler-border">
    					<legend class="scheduler-border">Detalle Comprobante de Pago</legend>
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
            					<label for="generadoc">Generar Documento :</label>
            					<select id="generadoc" name="generadoc" class="form-control">
            						<option value="0">NO</option>
            						<option value="1">SI</option>
            					</select>
            					<script type="text/javascript">
									$("#generadoc").change(function(){
										var opcion = $("#generadoc").val();
										if (opcion === "0") {
											$("#tipodoc").attr("disabled","disabled");
											$("#numdoc").attr("disabled","disabled");
											$("#nomape").attr("readonly","readonly");
											$("#nomape").removeAttr("required");
											$("#razon").attr("readonly","readonly");
											$("#direccion").attr("readonly","readonly");
											$("#razon").removeAttr("required");
											$("#direccion").removeAttr("required");
											if ($("#btnGenerar")) {
												$("#btnGenerar").html("");
												$("#btnGenerar").append("Generar Ticket");
											}
										} else if (opcion === "1") {
											$("#tipodoc").removeAttr("disabled");
											$("#numdoc").removeAttr("disabled");
											if ($("#btnGenerar")) {
												$("#btnGenerar").html("");
												$("#btnGenerar").append("Generar Comprobante");
											}
										}
										$("#numdoc").val("");
                                        $("#nomape").val("");
										$("#razon").val("");
										$("#direccion").val("");
										
										var indicador = $("#indicador").val();
										if (indicador === "3") {
											// Boleta
											$("#lbldatos").show();
                                        	$("#lblrazon").hide();
                                            $("#lbldireccion").hide();

                                            var montototal = $("#montototal").val().replace(/,/g, "");
                                            if ((montototal * 1) < 700) {
                                                $("#numdoc").removeAttr("required");
                                            	$("#nomape").removeAttr("required");
                                            } else {
                                            	$("#numdoc").attr("required","required");
                                            	$("#nomape").attr("required","required");
                                            }
										} else if (indicador === "1") {
											$("#lbldatos").hide();
                                            $("#lblrazon").show();
                                            $("#lbldireccion").show();
                                            $("#numdoc").attr("required","required");
										}
									});
            					</script>
            				</div>
            				<div class="col-md-2 col-sm-12">
            					<label for="tipodoc">Tipo Comprobante :</label>
            					<select id="tipodoc" name="tipodoc" class="form-control" disabled>    							
        							<?php foreach ($lstTipoDocumento as $objTipoDocumento) { ?>
        							<option value="<?php echo $objTipoDocumento->id; ?>"><?php echo $objTipoDocumento->nombre; ?></option>
          							<?php } ?>
        						</select>
        						<script type="text/javascript">
    								$("#tipodoc").change(function(){
    									var tipodoc = $("#tipodoc").val();
    									$.blockUI();
    									$.post("./?action=utilitarios", {
                                            tipodoc: tipodoc,
                                        }, function (data) {
                                            var resultado = data.split("|");
                                            $("#lblnumdoc").html(resultado[0]);
                                            $("#numdoc").prop("maxlength",resultado[1]);
                                            $("#indicador").val(resultado[2]);
                                            $("#numdoc").val("");
                                            $("#nomape").val("");
    										$("#razon").val("");
    										$("#direccion").val("");
                                            if (resultado[2] === "1") {
                                            	$("#lbldatos").hide();
                                                $("#lblrazon").show();
                                                $("#lbldireccion").show();
                                                $("#razon").attr("readonly","readonly");
                                                $("#razon").attr("required","required");
                                            	$("#direccion").attr("readonly","readonly");
                                            	$("#nomape").removeAttr("required");
                                            } else if (resultado[2] === "3") {
                                            	$("#lbldatos").show();
                                            	$("#lblrazon").hide();
                                                $("#lbldireccion").hide();
                                                $("#nomape").attr("readonly","readonly");
                                                $("#razon").removeAttr("required");
                                                
                                                var montototal = $("#montototal").val().replace(/,/g, "");
                                                if ((montototal * 1) < 700) {
                                                    $("#numdoc").removeAttr("required");
                                                	$("#nomape").removeAttr("required");
                                                } else {
                                                	$("#numdoc").attr("required","required");
                                                	$("#nomape").attr("required","required");
                                                }
                                            }
                                            $.unblockUI();
                                            $("#numdoc").focus();
                                        });
    								});
        						</script>
            				</div>
            				<div class="col-md-2 col-sm-12">
            					<label id="lblnumdoc" for="numdoc">DNI :</label>
            					<input type="text" id="numdoc" name="numdoc" class="form-control" placeholder="DNI" value="" maxlength="8" onkeypress="return soloNumeros(event)" disabled/>
            					<input type="hidden" id="indicador" name="indicador" value="3"/>
            					<input type="hidden" id="cliente" name="cliente" value=""/>
            					<script type="text/javascript">
    								$("#numdoc").change(function(){
    									var tipodoc = $("#tipodoc").val();
    									var numdoc = $("#numdoc").val();
    									var indicador = $("#indicador").val();
    									var validaciones = false; 
    									
    									// Boleta
    									if (indicador === "3") {
    										if (numdoc !== "") {
        										if (numdoc.length < 8) {
            										document.getElementById("numdoc").focus();
        											Swal.fire({
        		    									icon: "warning",
        		    									title: "DNI deber ser de 8 dígitos"
        		    								})		    								
        										} else if (isNaN(numdoc)) {
        											document.getElementById("numdoc").focus();
            										Swal.fire({
        		    									icon: "warning",
        		    									title: "DNI sólo debe contener valores numéricos"
        		    								})		    								
        										} else {
        											validaciones = true;
        										}
    										}
    									} else if (indicador === "1") {
    										if (numdoc !== "") {
        										if (numdoc.length < 11) {
        											document.getElementById("numdoc").focus();
        											Swal.fire({
        		    									icon: "warning",
        		    									title: "RUC deber ser de 11 dígitos"
        		    								})		    								
        										} else if (isNaN(numdoc)) {
        											document.getElementById("numdoc").focus();
        											Swal.fire({
        		    									icon: "warning",
        		    									title: "RUC sólo debe contener valores numéricos"
        		    								})
        										} else {
        											validaciones = true;
        										}
    										}
    									}
    									if (!validaciones) {
    										if (indicador === "1") {
												$("#razon").attr("readonly","readonly");
												$("#direccion").attr("readonly","readonly");
    										} else if (indicador === "3") {
    											$("#nomape").attr("readonly","readonly");
    											$("#nomape").removeAttr("required");
    										}
    										$("#numdoc").val("");
    										$("#nomape").val("");
    										$("#razon").val("");
    										$("#direccion").val("");
    										$("#cliente").val("");
    										document.getElementById("numdoc").focus();
    									} else {
        									$.blockUI();
        									$.post("./?action=utilitarios", {
        										tipodoc: tipodoc,
        										numdoc: numdoc
                                            }, function (data) {
                                            	var resultado = data.split("@");
                                                if (resultado[2] === "1") {
                                                	$("#nomape").val("");
                                                    $("#razon").val(resultado[0]);
                                                    $("#direccion").val(resultado[1]);
                                                    $("#cliente").val(resultado[3]);
                                                    $("#razon").attr("readonly","readonly");
                                                	$("#direccion").attr("readonly","readonly");
                                                } else if (resultado[2] === "0") {
                                                    $("#nomape").val(resultado[0]);
                                                    $("#razon").val("");
                                                    $("#direccion").val("");
                                                    $("#cliente").val(resultado[3]);
                                                    $("#nomape").attr("readonly","readonly");
                                                }
                                                if (resultado[0] === "") {
                                                	if (indicador === "1") {
                                                    	Swal.fire({
            		    									icon: "error",
            		    									title: "RUC inválido"
            		    								})
            		    								$("#razon").removeAttr("readonly");
                                                    	$("#direccion").removeAttr("readonly");
                                                    	$("#razon").attr("required","required");
                                                    	$("#direccion").attr("required","required");
                                                    	document.getElementById("razon").focus();
                                                	} else if (indicador === "3") {
                                                    	Swal.fire({
            		    									icon: "error",
            		    									title: "DNI inválido"
            		    								})
            		    								$("#nomape").removeAttr("readonly");
                                                    	$("#nomape").attr("required","required");
                                                    	document.getElementById("nomape").focus();
                                                	}
                                                	$("#cliente").val("");
                                                }
                                                $.unblockUI();
                                        	});
    									}
    								});
        						</script>
            				</div>
            				<div id="lbldatos" class="col-md-6 col-sm-12">
            					<label for="nomape">Nombres y Apellidos :*</label>
    							<input type="text" id="nomape" name="nomape" class="form-control" value="" readonly placeholder="Nombres y Apellidos" maxlength="150"/>        					        					
            				</div>
            				<div id="lblrazon" class="col-md-6 col-sm-12" style="display: none;">
            					<label for="razon">Razón Social :*</label>
    							<input type="text" id="razon" name="razon" class="form-control" value="" readonly placeholder="Razón Social" maxlength="150"/>
            				</div>
            			</div>
            			<div class="form-group">
            				<div id="lbldireccion" class="col-md-6 col-sm-12" style="display: none;">
            					<label for="direccion">Dirección :*</label>
    							<input type="text" id="direccion" name="direccion" class="form-control" value="" readonly placeholder="Dirección" maxlength="150"/>
            				</div>
            			</div>
            		</fieldset>
        			<?php } ?>
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
        					<button type="submit" id="btnRegistrar" class="btn btn-success" <?php echo $soloLectura; ?>><?php echo $textoBoton; ?></button>
        					<?php if ($faltaComprobante && $soloLectura != "") { ?>
        					<button type="submit" id="btnGenerar" class="btn btn-success">Generar Comprobante</button>
        					<?php } ?>
        					<?php if ($tipoPedido == 0 && $idCredito == 0 && $idPago > 0) { ?>
        					<button type="button" id="btnComanda" class="btn btn-info">Comanda</button>
    		    			<?php } ?>
        					<button type="button" id="btnCancelar" class="btn btn-danger">Regresar</button>
        					<input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>
        					<input type="hidden" id="idpago" name="idpago" value="<?php echo $idPago; ?>"/>
        					<input type="hidden" id="accion" name="accion" value="3"/>
        					<input type="hidden" id="tipopago" name="tipopago" value="<?php echo $idTipoPago; ?>"/>
        					<input type="hidden" id="caja" name="caja" value="<?php echo $idCaja; ?>"/>
        					<input type="hidden" id="usuario" name="usuario" value="<?php echo $idUsuario; ?>"/>
        					<input type="hidden" id="idcredito" name="idcredito" value="<?php echo $idCredito; ?>"/>
        				</div>
        			</div>
        			<?php if (count($lstHistorialPago) > 0) { ?>
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
  							<div class="panel panel-primary" id="test2Pane2">
    							<div class="panel-heading">
    								<strong>Historial Pagos</strong>
      								<a data-target="#panel2Content" data-parent="#test2Panel" data-toggle="collapse"><span class="pull-right"><i class="panel2Icon fa fa-arrow-up"></i></span></a>
    							</div>
    							<div class="panel-collapse collapse in" id="panel2Content">
      								<div class="panel-body">
                                    	<div class="table-responsive-md table-responsive">
                                    	<table class="table table-hover">
                                            <thead>
                                                <tr class="btn-primary">
                                                	<th scope="col">Código</th>
                                                    <th scope="col">Fecha y Hora</th>
                                                    <th scope="col">Producto</th>
                                                    <th scope="col" style="text-align: right;">Cantidad</th>
                                                    <th scope="col">Forma Pago</th>
                                                    <th scope="col">Tipo Tarjeta</th>
                                                    <th scope="col">Num. Operación</th>
                                                    <th scope="col" style="text-align: right;">Monto Pagado</th>
                                                    <th scope="col">Num. Comprobante</th>
                                                    <th scope="col" style="text-align: center;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                    foreach ($lstHistorialPago as $objHistorialPago) {
                                                        $objPago = $objHistorialPago->getPago();
                                                        
                                                        $detallePago = "";
                                                        if ($objHistorialPago->getFormaPago()->valor1 == 2) {
                                                            $detallePago = "<br><b>(Efectivo: ".number_format($objHistorialPago->monto_efectivo, 2).")<br>".
                                                                "(Tarjeta: ".number_format($objHistorialPago->monto_tarjeta, 2).")</b>";
                                                        }
                                                        
                                                        $totalListado = 0;
                                                        $lstProducto = $lstCantidad = "";
                                                        $lstDetalleHistorialPago = $objHistorialPago->getDetalleHistorialPago();
                                                        foreach ($lstDetalleHistorialPago as $objDetalleHistorialPago) {
                                                            $lstProducto .= $objDetalleHistorialPago->nom_producto."<br>";
                                                            $lstCantidad .= number_format($objDetalleHistorialPago->cantidad, 2)."<br>";
                                                            $totalListado += $objDetalleHistorialPago->total;
                                                        }
                                                        $totalListado = $objPago->monto_total - $objPago->monto_descuento;
                                                        
                                                        $idTipoTarjeta = 0;
                                                        $nomTipoTarjeta = "";
                                                        if ($objHistorialPago->getTipoTarjeta()) {
                                                            $idTipoTarjeta = $objHistorialPago->getTipoTarjeta()->id;
                                                            $nomTipoTarjeta = $objHistorialPago->getTipoTarjeta()->nombre;
                                                        }
                                                        $numComprobante = "";
                                                        if ($objHistorialPago->getComprobante()) {
                                                            $numComprobante = $objHistorialPago->getComprobante()->fe_comprobante_ser . "-" . $objHistorialPago->getComprobante()->fe_comprobante_cor;
                                                        }
                                            ?>
                                            	<tr id="row<?php echo $objHistorialPago->id; ?>">
                                            		<td style="text-align: left;"><?php echo str_pad($objHistorialPago->id, 8, "0", STR_PAD_LEFT); ?></td>
                                            		<td style="text-align: left;"><?php echo date("d/m/Y H:i", strtotime($objHistorialPago->fecha)); ?></td>
                                            		<td style="text-align: left;"><?php echo $lstProducto; ?></td>
                                            		<td style="text-align: right;"><?php echo $lstCantidad; ?></td>
                                            		<td style="text-align: left;"><?php echo $objHistorialPago->getFormaPago()->nombre; ?></td>
                                            		<td style="text-align: left;"><?php echo $nomTipoTarjeta; ?></td>
                                            		<td style="text-align: left;"><?php echo $objHistorialPago->num_operacion; ?></td>
                                            		<td style="text-align: right;"><?php echo number_format($totalListado, 2).$detallePago; ?></td>
                                            		<td style="text-align: left;"><?php echo $numComprobante; ?></td>
                                            		<td style="text-align: center;">
                                            			<a id="lnkedit<?php echo $objHistorialPago->id; ?>" title="Editar" class="btn btn-info btn-xs" <?php if ($objHistorialPago->getFormaPago()->valor1 >= 2) { echo "disabled"; } ?>><em class="fa fa-pencil-square-o"></em></a>
                                                        <script type="text/javascript">
                                    						$("#lnkedit<?php echo $objHistorialPago->id; ?>").click(function() {
                                        						$("#row<?php echo $objHistorialPago->id; ?>").hide();
                                        						$("#rowedit<?php echo $objHistorialPago->id; ?>").show();

                                        						<?php if ($nomTipoTarjeta == "") { ?>
                                        							$("#tipotarjeta<?php echo $objHistorialPago->id; ?>").hide();
                                        							$("#numope<?php echo $objHistorialPago->id; ?>").hide();
                                        						<?php } ?>
                                    						});
                                    					</script>
                                                  	</td>
                                            	</tr>
                                            	<?php if ($objHistorialPago->getFormaPago()->valor1 < 2) { ?>
                                            	<tr id="rowedit<?php echo $objHistorialPago->id; ?>" style="display: none;">
                                            		<td style="text-align: left;"><?php echo str_pad($objHistorialPago->id, 8, "0", STR_PAD_LEFT); ?></td>
                                            		<td style="text-align: left;"><?php echo date("d/m/Y H:i", strtotime($objHistorialPago->fecha)); ?></td>
                                            		<td style="text-align: left;"><?php echo $lstProducto; ?></td>
                                            		<td style="text-align: right;"><?php echo $lstCantidad; ?></td>                                            		
                                            		<td style="text-align: left;">
                                            			<select id="formapago<?php echo $objHistorialPago->id; ?>" name="formapago<?php echo $objHistorialPago->id; ?>" class="form-control">
															<?php
                                                			     foreach ($lstFormaPago as $objFormaPago) {
                                                			         if ($objFormaPago->valor1 < 2) {
                                                			?>
                                							<option value="<?php echo $objFormaPago->id; ?>" <?php if ($objFormaPago->id == $objHistorialPago->getFormaPago()->id) { echo "selected"; } ?>><?php echo $objFormaPago->nombre; ?></option>
                                  							<?php
                                                			         }
                                                			     }
                                                            ?>
                                  						</select>
                                  						<script type="text/javascript">
                            								$("#formapago<?php echo $objHistorialPago->id; ?>").change(function(){
                            									var formapago = $("#formapago<?php echo $objHistorialPago->id; ?>").val();

                            									$.blockUI();
                            									$.post("./?action=utilitarios", {
                            										formapago: formapago,
                                                                }, function (data) {
                                                                    if (data === "0") {
                                                                        // Efectivo
                                                                        $("#tipotarjeta<?php echo $objHistorialPago->id; ?>").hide();
                                                                    	$("#numope<?php echo $objHistorialPago->id; ?>").val("");
                                                                    	$("#numope<?php echo $objHistorialPago->id; ?>").hide();
                                                                    } else if (data === "1") {
                            											// Tarjeta
                                                                    	$("#tipotarjeta<?php echo $objHistorialPago->id; ?>").show();
                                                                    	$("#numope<?php echo $objHistorialPago->id; ?>").val("");
                                                                    	$("#numope<?php echo $objHistorialPago->id; ?>").show();
                                                                    }
                                                                    $.unblockUI();
                                                                });
                            								});
                                						</script>		
                                            		</td>
                                            		<td style="text-align: left;">
                                            			<select id="tipotarjeta<?php echo $objHistorialPago->id; ?>" name="tipotarjeta<?php echo $objHistorialPago->id; ?>" class="form-control">
                                            				<?php foreach ($lstTipoTarjeta as $objTipoTarjeta) { ?>
                                							<option value="<?php echo $objTipoTarjeta->id; ?>" <?php if ($objTipoTarjeta->id == $idTipoTarjeta) { echo "selected"; } ?>><?php echo $objTipoTarjeta->nombre; ?></option>
                                  							<?php } ?>
                                            			</select>                                            			
                                            		</td>
                                            		<td style="text-align: left;">
                                            			<input type="text" id="numope<?php echo $objHistorialPago->id; ?>" name="numope<?php echo $objHistorialPago->id; ?>" class="form-control" value="<?php echo $objHistorialPago->num_operacion; ?>" maxlength="20"/>
                                            		</td>
                                            		<td style="text-align: right;"><?php echo number_format($totalListado, 2); ?></td>
                                            		<td style="text-align: left;"><?php echo $numComprobante; ?></td>
                                            		<td style="text-align: center;">
                                                        <a id="lnksave<?php echo $objHistorialPago->id; ?>" title="Grabar" class="btn btn-success btn-xs"><em class="fa fa-floppy-o"></em></a>
                        								<script type="text/javascript">
                                    						$("#lnksave<?php echo $objHistorialPago->id; ?>").click(function() {
                                    							Swal.fire({
                                        							title: "Desea actualizar el pago <?php echo str_pad($objHistorialPago->id, 8, "0", STR_PAD_LEFT); ?>",
                                    								icon: "warning",
                                    								showCancelButton: true,
                                    								confirmButtonColor: "#3085d6",
                                    								cancelButtonColor: "#d33",
                                    								confirmButtonText: "Actualizar",
                                    								cancelButtonText: "Cancelar"
                                    							}).then((result) => {
                                    								if (result.isConfirmed) {
                                    									$.ajax({
                                    									    type: "post",                                    									    
                                    									    url: "./index.php?action=addpayment",
                                    									    dataType: "html",
                                    									    data: "id=<?php echo $objHistorialPago->id; ?>&accion=2&formapago="+$("#formapago<?php echo $objHistorialPago->id; ?>").val()+
                                            									    "&tipotarjeta="+$("#tipotarjeta<?php echo $objHistorialPago->id; ?>").val()+"&numope="+$("#numope<?php echo $objHistorialPago->id; ?>").val(),
                                            								beforeSend: function() {
                                            									$.blockUI();
                                            		    		    		},
                                    									    success: function(data) {
                                    									        if (data > 0) {
                                    									        	Swal.fire({
                                                		                                icon: "success",
                                                		                                title: "Se actualizó correctamente el pago <?php echo str_pad($objHistorialPago->id, 8, "0", STR_PAD_LEFT); ?>",
                                                										showCancelButton: false,
                                                										confirmButtonColor: "#3085d6",
                                                										confirmButtonText: "OK"
                                                		                        	}).then((result) => {
                                                										window.location.href = "./index.php?view=newpayment1&id=<?php echo $id; ?>";
                                                		                        	})
                                    									        } else {
                                    									        	Swal.fire({
                                    		    		    							icon: "warning",
                                    		    		    							title: "Ocurrio un error al actualizar el pago <?php echo str_pad($objHistorialPago->id, 8, "0", STR_PAD_LEFT); ?>"
                                    		    		    						})
                                    										    }
                                    									    },
                                    									    error: function() {
                                    									    	Swal.fire({
                                    	    		    							icon: "error",
                                    	    		    							title: "Ocurrio un error al actualizar el pago <?php echo str_pad($objHistorialPago->id, 8, "0", STR_PAD_LEFT); ?>"
                                    	    		    						})
                                    									    },
                                    									    complete: function(data) {
                                    									    	$.unblockUI();
                                    		    		    				}
                                    									});
                                    								} else {
                                    									window.location.href = "./index.php?view=newpayment1&id=<?php echo $id; ?>";
                                    								}
                                    							})
                                    						});
                                    					</script>
													</td>
                                            	</tr>
                                            	<?php } ?>
                                            <?php
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
                   	<?php } ?>
        			<?php if (count($lstHistorialDocumento) > 0) { ?>
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
  							<div class="panel panel-primary" id="test3Pane3">
    							<div class="panel-heading">
    								<strong>Historial Comprobantes</strong>
      								<a data-target="#panel3Content" data-parent="#test3Panel" data-toggle="collapse"><span class="pull-right"><i class="panel3Icon fa fa-arrow-up"></i></span></a>
    							</div>
    							<div class="panel-collapse collapse in" id="panel3Content">
      								<div class="panel-body">
                                    	<div class="table-responsive-md table-responsive">
                                    	<table class="table table-hover">
                                            <thead>
                                                <tr class="btn-primary">
                                                    <th scope="col">Fecha y Hora</th>
                                                    <th scope="col">Num. Documento</th>
                                                    <th scope="col">Cliente</th>                                                    
                                                    <th scope="col" style="text-align: right;">SubTotal</th>
                                                    <th scope="col" style="text-align: right;">IGV</th>
                                                    <th scope="col" style="text-align: right;">Total</th>
                                                    <th scope="col" style="text-align: center;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                    foreach ($lstHistorialDocumento as $objHistorialDocumento) {
                                                        $objComprobante = $objHistorialDocumento->getComprobante();
                                                        $montoComprobante = $objComprobante->fe_comprobante_totvengra;
                                                        if ($exonerado == 1) {
                                                            $montoComprobante = $objComprobante->fe_comprobante_totvenexo;
                                                        }
                                            ?>
                                            	<tr>
                                            		<td style="text-align: left;"><?php echo date("d/m/Y H:i", strtotime($objComprobante->fe_comprobante_reg)); ?></td>
                                            		<td style="text-align: left;"><?php echo $objComprobante->fe_comprobante_ser."-".$objComprobante->fe_comprobante_cor; ?></td>
                                            		<td style="text-align: left;"><?php echo $objComprobante->tb_cliente_numdoc."-".$objComprobante->tb_cliente_nom; ?></td>
                                            		<td style="text-align: right;"><?php echo number_format($montoComprobante, 2); ?></td>
                                            		<td style="text-align: right;"><?php echo number_format($objComprobante->fe_comprobante_sumigv, 2); ?></td>
                                            		<td style="text-align: right;"><?php echo number_format($objComprobante->fe_comprobante_imptot, 2); ?></td>
                                            		<td style="text-align: center;">
                                            			<a href="ticket.php?id=<?php echo $objHistorialDocumento->id; ?>" title="Imprimir" class="btn btn-success btn-xs" target="_blank"><em class="fa fa-print"></em></a>
                                            			<a id="lnkdel<?php echo $objHistorialDocumento->id; ?>" title="Anular" class="btn btn-danger btn-xs"><em class="fa fa-trash"></em></a>
                        								<script type="text/javascript">
                                    						$("#lnkdel<?php echo $objHistorialDocumento->id; ?>").click(function() {
                                    							Swal.fire({
                                        							title: "Desea anular el documento <?php echo $objComprobante->fe_comprobante_ser."-".$objComprobante->fe_comprobante_cor; ?>",
                                    								icon: "warning",
                                    								showCancelButton: true,
                                    								confirmButtonColor: "#3085d6",
                                    								cancelButtonColor: "#d33",
                                    								confirmButtonText: "Anular",
                                    								cancelButtonText: "Cancelar"
                                    							}).then((result) => {
                                    								if (result.isConfirmed) {
                                    									$.ajax({
                                    									    type: "post",
                                    									    url: "./?action=adddocument",
                                    									    dataType: "html",
                                    									    data: "id=<?php echo $objHistorialDocumento->id; ?>&accion=2",                                    									    
                                    									    beforeSend: function() {
                                            									$.blockUI();
                                            		    		    		},
                                    									    success: function(data) {
                                    									        if (data > 0) {
                                    									        	Swal.fire({
                                                		                                icon: "success",
                                                		                                title: "Se anuló correctamente el documento <?php echo $objComprobante->fe_comprobante_ser."-".$objComprobante->fe_comprobante_cor; ?>",
                                                										showCancelButton: false,
                                                										confirmButtonColor: "#3085d6",
                                                										confirmButtonText: "OK"
                                                		                        	}).then((result) => {
                                                										window.location.href = "./index.php?view=newpayment1&id=<?php echo $id; ?>";
                                                		                        	})
                                    									        } else {
                                    									        	Swal.fire({
                                    		    		    							icon: "warning",
                                    		    		    							title: "Ocurrio un error al anular el documento <?php echo $objComprobante->fe_comprobante_ser."-".$objComprobante->fe_comprobante_cor; ?>"
                                    		    		    						})
                                    										    }
                                    									    },
                                    									    error: function() {
                                    									    	Swal.fire({
                                    	    		    							icon: "error",
                                    	    		    							title: "Ocurrio un error al anular el documento <?php echo $objComprobante->fe_comprobante_ser."-".$objComprobante->fe_comprobante_cor; ?>"																
                                    	    		    						})                        									        
                                    									    },
                                    									    complete: function(data) {
                                    		    		    					$.unblockUI();
                                    		    		    				}
                                    									});                        									
                                    								}
                                    							})
                                    						});
                                    					</script>
                                            		</td>
                                            	</tr>
                                            <?php 
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
                   	<?php } ?>        			
        		</div>
        	</div>
    		</form>
    		<script type="text/javascript">
    			$("#btnCancelar").click(function(){
    				$.blockUI();
    				$("button").prop("disabled", true);
    				location.href = "./index.php?view=trays";
    			});
    			$("#btnComanda").click(function(){
					$.blockUI();    				
    				$("button").prop("disabled", true);
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
                       	$("button").prop("disabled", false);
                   		$.unblockUI();
                   	});
    			});
    			$(document).ready(function(){
    				setTimeout(function(){$("#descuento").trigger("focus")},1);
    				<?php if ($idCredito > 0) { ?>
    				setTimeout(function(){$("#formapago").trigger("focus")},1);
    				$("#descuento").attr("readonly","readonly");
    				<?php } ?>
    				$("#montoefectivo").on("keyup keypress", function(e) {
            			var keyCode = e.keyCode || e.which;
        			    if (keyCode === 13) {
            				e.preventDefault();
        			       	return false;
						}
        			});
    				$("#montotarjeta").on("keyup keypress", function(e) {
            			var keyCode = e.keyCode || e.which;
        			    if (keyCode === 13) {
            				e.preventDefault();
        			       	return false;
						}
        			});
        			$("#numdoc").on("keyup keypress", function(e) {
            			var keyCode = e.keyCode || e.which;
        			    if (keyCode === 13) {
            				e.preventDefault();
        			       	return false;
						}
        			});
        			$("#montopagado").on("keyup keypress", function(e) {
            			var keyCode = e.keyCode || e.which;
        			    if (keyCode === 13) {
            				e.preventDefault();
        			       	return false;
						}
        			});
        			$("#panel1Content").on("shown.bs.collapse",function(){
        			    $(".panel1Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel1Content").on("hidden.bs.collapse",function(){
        			    $(".panel1Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});
        			$("#panel2Content").on("shown.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel2Content").on("hidden.bs.collapse",function(){
        			    $(".panel2Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});
        			$("#panel3Content").on("shown.bs.collapse",function(){
        			    $(".panel3Icon").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        			});	
        			$("#panel3Content").on("hidden.bs.collapse",function(){
        			    $(".panel3Icon").removeClass("fa-arrow-up").addClass("fa-arrow-down");
        			});
        		});
    			$(function(){
    				<?php if ($faltaComprobante && $soloLectura == "") { ?>
    				$("#newpayment").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addsale",
    		    				dataType: "html",
    		    				data: $("#newpayment").serialize(),
    		    				beforeSend: function() {
    		    					$("button").prop("disabled", true);
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se registró correctamente el pago",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    		                        		<?php if (!($tipoPedido == 0 && $idCredito == 0)) { ?>
        		                        	window.location.href = "./index.php?view=newpayment1&id=<?php echo $id; ?>";
        		                        	<?php } else if ($tipoPedido == 0) { ?>
        		                        	window.location.href = "./index.php?view=newpayment1&id=<?php echo $id; ?>";
        		                        	<?php } ?>
    		                        	})
    		        				} else if (data < 0) {
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Los montos ingresados no son correctos (Monto Efectivo / Monto Tarjeta)"
    		    						})
    		        				} else {
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Ocurrio un error al registrar el pago"
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al registrar el pago"
    								})
    		    				},
    		    				complete: function(data) {
    		    					$("button").prop("disabled", false);
    		    					$.unblockUI();
    		    				}
    		    			});
    		    		}
    		        });
    		        <?php } else { ?>
    		        $("#newpayment").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=adddocument",    		    				
    		    				dataType: "html",
    		    				data: $("#newpayment").serialize(),
    		    				beforeSend: function() {
    		    					$("button").prop("disabled", true);
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		        				if (data !== "") {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se generó correctamente el comprobante",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
        		                        	window.location.href = "./index.php?view=newpayment1&id=<?php echo $id; ?>";
    		                        	})        					
    		        				} else {
    		        					Swal.fire({
    		    							icon: "error",
    		    							title: "Ocurrio un error al generar el comprobante"
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al generar el comprobante"
    								})
    		    				},
    		    				complete: function(data) {
    		    					$("button").prop("disabled", false);
    		    					$.unblockUI();
    		    				}
    		    			});
    		    		}
    		        });
    		        <?php } ?>
    			});    		
    		</script>
    	</div>
    </div>
</section>