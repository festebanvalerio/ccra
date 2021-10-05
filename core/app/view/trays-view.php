<?php
    $tipo = $piso = $mesero = "";
    $estado = "1";
    $fechaInicio = date("d/m/Y");
    $fechaFin = date("d/m/Y");
    if (count($_POST) > 0) {
        $tipo = $_POST["tipo"];
        $piso = $_POST["piso"];
        $estado = $_POST["estado"];
        $fechaInicio = $_POST["fechai"];
        $fechaFin = $_POST["fechaf"];
        $mesero = $_POST["mesero"];
        
        $_SESSION["trays_tipo"] = $tipo;
        $_SESSION["trays_piso"] = $piso;
        $_SESSION["trays_estado"] = $estado;
        $_SESSION["trays_fechai"] = $fechaInicio;
        $_SESSION["trays_fechaf"] = $fechaFin;
        $_SESSION["trays_mesero"] = $mesero;
    } else {
        if (isset($_SESSION["trays_tipo"]) && isset($_SESSION["trays_piso"]) && isset($_SESSION["trays_estado"]) && isset($_SESSION["trays_fechai"]) &&
            isset($_SESSION["trays_fechaf"]) && isset($_SESSION["trays_mesero"])) {
            $tipo = $_SESSION["trays_tipo"];
            $piso = $_SESSION["trays_piso"];
            $estado = $_SESSION["trays_estado"];
            $fechaInicio = $_SESSION["trays_fechai"];
            $fechaFin = $_SESSION["trays_fechaf"];
            $mesero = $_SESSION["trays_mesero"];
        }
    }
    $lstParametro = ParametroData::getAll(1, "OPCIONES GENERALES", "IP SERVER LOCAL");
    $url = $lstParametro[0]->valor1;
    
    $objPerfil = PerfilData::getInfoPerfil(1, 2);
    
    $lstEstado = EstadoPedidoData::getAll();    
    $lstPedido = PedidoData::getAll($estado, $_SESSION["sede"], $tipo, $piso, $fechaInicio, $fechaFin, $mesero);
    $lstPisoXSede = PisoSedeData::getPisoXSede($_SESSION["sede"]);
    $lstTipo = ParametroData::getAll(1, "TIPO PEDIDO");
    $lstMesero = UsuarioData::getAll(1, $objPerfil->id, $_SESSION["sede"]);
?>
<script type="text/javascript">
	$(function() {
		$("#fechai").datepicker({
    		dateFormat: "dd/mm/yy",
    		changeMonth: true,
    		changeYear: true,
    		maxDate: "0",
    		yearRange: "1900:<?php echo date("Y"); ?>"
    	});
		$("#fechaf").datepicker({
    		dateFormat: "dd/mm/yy",
    		changeMonth: true,
    		changeYear: true,
    		maxDate: "0",
    		yearRange: "1900:<?php echo date("Y"); ?>"
    	});
	});
</script>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Bandeja</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="sales" action="" role="form" autocomplete="off">    			
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
                				<label for="fechai">Fecha Inicio:</label>
        						<input type="text" id="fechai" name="fechai" value="<?php echo $fechaInicio; ?>" class="form-control" readonly/>
        					</div>
        					<div class="col-md-2 col-sm-12">
                				<label for="fechaf">Fecha Fin:</label>
        						<input type="text" id="fechaf" name="fechaf" value="<?php echo $fechaFin; ?>" class="form-control" readonly/>
        					</div>
        					<div class="col-md-2 col-sm-12">
    							<label for="tipo">Tipo :</label>
    							<select id="tipo" name="tipo" class="form-control">
    								<option value="">TODOS</option>
    								<?php foreach ($lstTipo as $objTipo) { ?>
    								<option value="<?php echo $objTipo->id; ?>" <?php if ($objTipo->id == $tipo) { echo "selected"; } ?>><?php echo $objTipo->nombre; ?></option>
      								<?php } ?>
    							</select>
    						</div>
        					<div class="col-md-2 col-sm-12">
    							<label for="piso">Piso :</label>
    							<select id="piso" name="piso" class="form-control">
    								<option value="">TODOS</option>
    								<?php foreach ($lstPisoXSede as $objPisoXSede) { ?>
    								<option value="<?php echo $objPisoXSede->piso; ?>" <?php if ($objPisoXSede->id == $piso) { echo "selected"; } ?>><?php echo $objPisoXSede->getPiso()->nombre; ?></option>
      								<?php } ?>
    							</select>
    						</div>
    						<div class="col-md-4 col-sm-12">
    							<label for="mesero">Mesero(a) :</label>
    							<select id="mesero" name="mesero" class="form-control">
    								<option value="">TODOS</option>
    								<?php foreach ($lstMesero as $objMesero) { ?>
    								<option value="<?php echo $objMesero->id; ?>" <?php if ($objMesero->id == $mesero) { echo "selected"; } ?>><?php echo $objMesero->nombres." ".$objMesero->apellidos; ?></option>
      								<?php } ?>
    							</select>
    						</div>
    					</div>
    					<div class="form-group">
            				<div class="col-md-2 col-sm-12">
    							<label for="estado">Estado :</label>
    							<select id="estado" name="estado" class="form-control">
    								<option value="">TODOS</option>
    								<?php foreach ($lstEstado as $objEstado) { ?>
    								<option value="<?php echo $objEstado->id; ?>" <?php if ($objEstado->id == $estado) { echo "selected"; } ?>><?php echo $objEstado->nombre; ?></option>
      								<?php } ?>
    							</select>
    						</div>
    						<div class="col-md-10 col-sm-12" style="padding-top: 25px;">
        						<button type="button" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>
        						<button type="button" id="btnRefrescar" class="btn btn-info" title="Refrescar"><em class="fa fa-refresh"></em></button>
        						<button type="button" id="btnLimpiar" class="btn btn-danger" title="Limpiar"><em class="fa fa-eraser"></em></button>
        						<script type="text/javascript">
        							$("#btnBuscar").click(function(){
            							$.blockUI();
            		    				$("button").prop("disabled", true);
            		    				$("#sales").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
                						$.post("index.php?action=deletesearch", {
                							opcion: "trays"
                                        }, function (data) {
                                        	location.href = "./index.php?view=trays";
                                        });
            		    			});
            						$("#btnRefrescar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=trays";
            		    			});
        						</script>
            				</div>
    					</div>
            		</form>
					<div class="table-responsive">
        				<div class="box-body">
        					<table class="table table-bordered table-hover datatable table-nowrap">
        						<thead>
        							<tr>
        								<th scope="col">Num. Pedido</th>
        								<th scope="col">Fecha y Hora</th>
        								<th scope="col">Tipo</th>
        								<th scope="col">Piso</th>
        								<th scope="col">Mesa</th>
        								<th scope="col" style="width: 15%;">Mesero(a)</th>
        								<th scope="col">Contacto</th>        								
        								<th scope="col" style="text-align: right;">SubTotal</th>
        								<th scope="col" style="text-align: right;">IGV</th>
        								<th scope="col" style="text-align: right;">Total</th>        								
        								<th scope="col">Estado</th>
        								<th scope="col">Acciones</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						    $totalSubTotal = $totalIgv = $totalImporte = 0;
        						    foreach ($lstPedido as $objPedido) {
        						        $codigo = str_pad($objPedido->id, 8, "0", STR_PAD_LEFT);
        						        $objEstado = $objPedido->getEstado();
        						        $objMesaPisoSede = $objPedido->getMesaPisoSede($objPedido->getPisoSede()->id, $objPedido->mesa);
        						        
        						        $existePago = false;
        						        $objPago = $objPedido->getPago();
        						        $contacto = "-";
        						        if ($objPago) {
            						        $lstHistorialPago = $objPago->getHistorialPago();
            						        if (count($lstHistorialPago) > 0) {
            						            $existePago = true;
            						            
            						            // En el caso que sea a credito
            						            /*if ($objPedido->estado == 3) {
            						                $objHistorialPago = $lstHistorialPago[0];
            						            }*/
            						        }
            						        $opcionEliminar = "disabled";
        						        }
        						        if ($objPedido->getTipo()->valor2 == 1 || $objPedido->getTipo()->valor2 == 2) {
        						            $contacto = $objPedido->datos;
        						        }
        						        $opcionEditar = $objPedido->getTipo()->valor1;
        						        $opcionEliminar = "";
        						        
        						        $mesa = "-";
        						        if ($objPedido->getMesa()) {
        						            $mesa = $objPedido->getMesa()->nombre;
        						        }
        						        $piso = "-";
        						        if ($objPedido->getPiso()) {
        						            $piso = $objPedido->getPiso()->nombre;
        						        }
        						        
        						        $totalSubTotal += $objPedido->subtotal;
        						        $totalIgv += $objPedido->igv;
        						        $totalImporte += $objPedido->total;
        						        
        						        $cadena = "";
        						        $lstArea = $objPedido->getAreaXPedido($objPedido->id, 0);
        						        if (count($lstArea) > 0) {
        						            $lstArea = AreaData::getAllAreaXProducto($objPedido->id, 1);
        						            if (count($lstArea) > 0) {
        						                $area = "";
        						                foreach ($lstArea as $objArea) {
        						                    $area .= $objArea->id.",";
        						                }
        						                $cadena .= substr($area, 0, strlen($area) - 1);
        						            }
        						        }
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo $codigo; ?></td>
            							<td style="text-align: left;"><?php echo date("d/m/Y H:i", strtotime($objPedido->fecha)); ?></td>
            							<td style="text-align: left;"><?php echo $objPedido->getTipo()->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $piso; ?></td>
            							<td style="text-align: left;"><?php echo $mesa; ?></td>
            							<td style="text-align: left;"><?php echo $objPedido->getUsuario()->nombres." ". $objPedido->getUsuario()->apellidos; ?></td>
            							<td style="text-align: left;"><?php echo $contacto; ?></td>
            							<td style="text-align: right;"><?php echo number_format($objPedido->subtotal, 2); ?>
            							<td style="text-align: right;"><?php echo number_format($objPedido->igv, 2); ?>
            							<td style="text-align: right;"><?php echo number_format($objPedido->total, 2); ?>
            							<td style="text-align: left;"><?php echo $objEstado->nombre; ?></td>
            							<td style="text-align: left;">
            								<?php if ($objEstado->opcion == 0 || $objEstado->opcion == 2) { ?>
            								<a href="index.php?view=detailpayment&id=<?php echo $objPedido->id; ?>&opcion=1" title="Ver Detalle" class="btn btn-warning btn-xs"><em class="fa fa-search-plus"></em></a>
            								<?php if ($objPedido->fecha == date("Y-m-d")) { ?>
            								<a href="index.php?view=newpayment1&id=<?php echo $objPedido->id; ?>" title="Reapertura Pedido" class="btn btn-info btn-xs"><em class="fa fa-pencil-square-o"></em></a>
            								<?php } ?>
            								<?php if ($cadena != "" && $objEstado->opcion == 2) { ?>
            								<a id="lnkcomanda<?php echo $objPedido->id; ?>" title="Imprimir Comanda" class="btn btn-success btn-xs"><em class="fa fa-print"></em></a>
            								<script type="text/javascript">
            									$("#lnkcomanda<?php echo $objPedido->id; ?>").click(function() {
            										$("#lnkcomanda<?php echo $objPedido->id; ?>").attr("disabled", "disabled");
                									$.blockUI();                									                									
                                    				var data = "<?php echo $cadena; ?>";
                                                   	var id = data.split(",");
                                                   	for (var indice=0; indice<id.length; indice++) {
                        	                           	var area = id[indice];
                        	                        	$.post("./?action=utilitarios", {
                        									pedido: <?php echo $objPedido->id; ?>,
                        									impresion: 1,
                        									area: area 
                                                   		}, function (data) {
                                                   			if (data !== "") {
                            	                           		var url = "http://<?php echo $url; ?>/print/print_ticket.php";
                            	                           		$.post(url, { datos: data });
                            	                           	} else {
                            	                           		Swal.fire({
                                    								icon: "warning",
                                    								title: "No existe productos nuevos para imprimir"
                                    							})
                            	                           	}	
                                                   		})
                                                   	}
                                                   	$("#lnkcomanda<?php echo $objPedido->id; ?>").removeAttr("disabled");
                                                   	$.unblockUI();
            									});
            								</script>
            								<?php } ?>
            								<?php } ?>
            								<?php
            								    if ($objEstado->opcion == 1) {
            								        if ($opcionEditar == 1) {
            							    ?>
											<a href="index.php?view=salestableitem&usuario=<?php echo $objPedido->mozo; ?>&piso=<?php echo $objPedido->getPisoSede()->id; ?>&mesa=<?php echo $objMesaPisoSede->id; ?>&opcion=1" title="Editar" class="btn btn-warning btn-xs"><em class="fa fa-pencil-square-o"></em></a>
											<a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#exampleModal<?php echo $objPedido->id; ?>" title="Transferencia"><em class="fa fa-share"></em></a>
											<?php
            								        }
											?>        									
            								<a id="lnkpay<?php echo $objPedido->id; ?>" title="Pagar" class="btn btn-success btn-xs"><em class="fa fa-money"></em></a>
            								<script type="text/javascript">
                								$("#lnkpay<?php echo $objPedido->id; ?>").click(function() {
                    								<?php if (!$existePago) { ?> 
                										Swal.fire({
                											title: "Elegir la forma de pagar el pedido <?php echo $codigo; ?>",
                											icon: "warning",
                											showCancelButton: true,
                											confirmButtonText: "Total",
                											cancelButtonText: "Parcial",
                											confirmButtonColor: "#3085d6",
                            								cancelButtonColor: "#d33"
                										}).then((result) => {
                											if (result.isConfirmed) {
                										 		window.location.href = "./index.php?view=newpayment1&id=<?php echo $objPedido->id; ?>";
                											} else {
                												window.location.href = "./index.php?view=newpayment2&id=<?php echo $objPedido->id; ?>";
                											}
                										})
                    								<?php } else { ?>
                    									window.location.href = "./index.php?view=newpayment2&id=<?php echo $objPedido->id; ?>";
                    								<?php } ?>
            									});                								
                        					</script>
                        					<?php } ?>
                        					<?php if ($objEstado->opcion == 1) { ?>
            								<a href="ticketPedido.php?id=<?php echo $objPedido->id; ?>" title="Imprimir Pedido" class="btn btn-info btn-xs" target="_blank"><em class="fa fa-print"></em></a>            								
            								<?php } ?>
            								<?php if ($objEstado->opcion > 0 && date("Y-m-d", strtotime($objPedido->fecha)) == date("Y-m-d")) { ?>
            								<a id="lnkdel<?php echo $objPedido->id; ?>" title="Anular" class="btn btn-danger btn-xs" <?php echo $opcionEliminar; ?>><em class="fa fa-trash"></em></a>            								
            								<script type="text/javascript">
                								$("#lnkdel<?php echo $objPedido->id; ?>").click(function() {
                        							Swal.fire({
                            							title: "Desea anular el pedido <?php echo $codigo; ?>",
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
                        									    url: "./?action=addsale",
                        									    data: "id=<?php echo $objPedido->id; ?>&accion=2",
                        									    dataType: "html",
                        									    beforeSend: function() {
                        		    		    					$("#lnkdel<?php echo $objPedido->id; ?>").attr("disabled","disabled");
                        		    		    					$.blockUI();
                        		    		    				},
                        									    success: function(data) {
                        									        if (data > 0) {
                        									        	Swal.fire({
                                    		                                icon: "success",
                                    		                                title: "Se anuló correctamente el pedido <?php echo $codigo; ?>",
                                    										showCancelButton: false,
                                    										confirmButtonColor: "#3085d6",
                                    										confirmButtonText: "OK"
                                    		                        	}).then((result) => {
                                    										window.location.href = "./index.php?view=trays";
                                    		                        	})
                        									        } else {
                        									        	Swal.fire({
                        		    		    							icon: "warning",
                        		    		    							title: "Ocurrio un error al anular el pedido <?php echo $codigo; ?>"
                        		    		    						})
                        										    }
                        									    },
                        									    error: function() {
                        									    	Swal.fire({
                        	    		    							icon: "error",
                        	    		    							title: "Ocurrio un error al anular el pedido <?php echo $codigo; ?>"
                        	    		    						})                        									        
                        									    },
                        									    complete: function(data) {
                        		    		    					$("#lnkdel<?php echo $objPedido->id; ?>").removeAttr("disabled");
                        		    		    					$.unblockUI();
                        		    		    				}
                        									});
                        								}
                        							})
                        						});
            								</script>
            								<?php } ?>
            							</td>
        							</tr>
        						<?php
                                    }
                                ?>
                                </tbody>
                                <tfoot>
                                	<tr>
                                    	<td colspan="7" style="font-weight: bold;">TOTAL</td>
                                    	<td style="text-align: right; font-weight: bold;"><?php echo number_format($totalSubTotal, 2); ?></td>
                                    	<td style="text-align: right; font-weight: bold;"><?php echo number_format($totalIgv, 2); ?></td>
                                    	<td style="text-align: right; font-weight: bold;"><?php echo number_format($totalImporte, 2); ?></td>
                                    	<td colspan="2"></td>
                                    </tr>
                                </tfoot>
        					</table>
        				</div>
        			</div>
        		</div>
        	</div>
		</div>
	</div>
	<!-- Modal Editar -->
	<?php
	   foreach ($lstPedido as $objPedido) { 
	       $codigo = str_pad($objPedido->id, 8, "0", STR_PAD_LEFT);
	?>
	<!-- Modal -->
    <div class="modal fade" id="exampleModal<?php echo $objPedido->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
        	<div class="modal-content">
          		<div class="modal-header btn-primary">
            		<h5 class="modal-title" id="exampleModalLabel"><strong>Transferencia Mesa: Pedido <?php echo $codigo; ?></strong></h5>
          		</div>
          		<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<div class="col-md-4 col-sm-12">
                					<label for="piso">Piso :*</label>
                					<select id="pisosede<?php echo $objPedido->id; ?>" name="pisosede<?php echo $objPedido->id; ?>" class="form-control">                					
                						<option value="">SELECCIONE</option>
                						<?php foreach ($lstPisoXSede as $objPisoXSede) { ?>
        								<option value="<?php echo $objPisoXSede->id; ?>"><?php echo $objPisoXSede->getPiso()->nombre; ?></option>
          								<?php } ?>
                					</select>
                					<script type="text/javascript">
										$("#pisosede<?php echo $objPedido->id; ?>").change(function(){
											var piso = $("#pisosede<?php echo $objPedido->id; ?>").val();
											var sede = $("#sede<?php echo $objPedido->id; ?>").val();
											if (piso !== "") {
												$.blockUI();																					
												$.post("./?action=utilitarios", {
                									idpisosede: piso,
                									sede: sede
                	                           	}, function (data) {
                    	                           	var resultado = data.split("|");
                    	                           	if (resultado[0] !== "") {
                        	                           	$("#mesa<?php echo $objPedido->id; ?>").html("");
                        	                           	$("#mesa<?php echo $objPedido->id; ?>").append(data);
                        	                           	$("#mesa<?php echo $objPedido->id; ?>").removeAttr("disabled");
                        	                           	$("#piso<?php echo $objPedido->id; ?>").val(resultado[1]);
                    	                           	} else {
                    	                           		$("#mesa<?php echo $objPedido->id; ?>").html("");
														$("#mesa<?php echo $objPedido->id; ?>").append("");
														$("#mesa<?php echo $objPedido->id; ?>").attr("disabled","disabled");
														$("#piso<?php echo $objPedido->id; ?>").val("");
                    	                           	}
                    	                           	$("#btnGuardar<?php echo $objPedido->id; ?>").removeAttr("disabled");
                    	                           	$.unblockUI();
                	                           	});				
											} else {
												$("#mesa<?php echo $objPedido->id; ?>").html("");
												$("#mesa<?php echo $objPedido->id; ?>").append("<option value=''>SELECCIONE</option>");
												$("#mesa<?php echo $objPedido->id; ?>").attr("disabled","disabled");
												$("#piso<?php echo $objPedido->id; ?>").val("");
												$("#btnGuardar<?php echo $objPedido->id; ?>").attr("disabled","disabled");
											}
										});
                					</script>
                				</div>
                				<div class="col-md-4 col-sm-12">
                					<label for="mesa">Mesa :*</label>
                					<select id="mesa<?php echo $objPedido->id; ?>" name="mesa<?php echo $objPedido->id; ?>" class="form-control" disabled>                					
                						<option value="">SELECCIONE</option>                                                    						
                					</select>
                				</div>
                			</div>
						</div>
					</div>
          		</div>
          		<div class="modal-footer">
                    <button type="button" id="btnGuardar<?php echo $objPedido->id; ?>" class="btn btn-primary" disabled>Guardar</button>
                    <button type="button" id="btnCerrar<?php echo $objPedido->id; ?>" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <input type="hidden" id="idpedido<?php echo $objPedido->id; ?>" name="idpedido<?php echo $objPedido->id; ?>" value="<?php echo $objPedido->id; ?>"/>
                    <input type="hidden" id="piso<?php echo $objPedido->id; ?>" name="piso<?php echo $objPedido->id; ?>" value=""/>
                    <input type="hidden" id="sede<?php echo $objPedido->id; ?>" name="sede<?php echo $objPedido->id; ?>" value="<?php echo $objPedido->sede; ?>"/>
                    <script type="text/javascript">
                        $("#btnCerrar").click(function(){
                        	$("#pisosede<?php echo $objPedido->id; ?>").val("");
                        	$("#mesa<?php echo $objPedido->id; ?>").val("");
                    	});
						$("#btnGuardar<?php echo $objPedido->id; ?>").click(function(){
    						var piso = $("#piso<?php echo $objPedido->id; ?>").val();
							var mesa = $("#mesa<?php echo $objPedido->id; ?>").val();
							var pedido = $("#idpedido<?php echo $objPedido->id; ?>").val();
							if (piso === "") {
								Swal.fire({
									icon: "warning",
									title: "Seleccione el piso"
								})
							} else if (mesa === "") {
								Swal.fire({
									icon: "warning",
									title: "Seleccione la mesa"
								})
							} else {
								$("#btnGuardar<?php echo $objPedido->id; ?>").attr("disabled","disabled");
								$("#btnCerrar<?php echo $objPedido->id; ?>").attr("disabled","disabled");
								$.post("./?action=utilitarios", {
									idpedido: pedido,
									idmesa: mesa,
									idpiso: piso
	                           	}, function (data) {
    	                           	if (data !== "") {
    	                           		Swal.fire({
	    									icon: "success",
	    									title: "Se realizó la transferencia en forma exitosa",
	    									timer: 10000
	    								})
	    								window.location.href = "./index.php?view=trays";		
    	                           	} else {
    	                           		Swal.fire({
	    									icon: "error",
	    									title: "Ocurrio un error al realizar la transferencia"
	    								})
    	                           	}
    	                           	$.unblockUI();
	                           	});
							}
						});
                    </script>
          		</div>
        	</div>
      	</div>
    </div>
	<?php } ?>
</section>