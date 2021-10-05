<?php
    $texto = "Registrar Venta - Crédito";
    
    $idPedido = 0;
    $objSede = SedeData::getById($_SESSION["sede"]);
    $idSede = $objSede->id;
    $sede = $objSede->nombre;
    
    $idUsuario = $_SESSION["user"];
    $idCredito = $idPisoSede = $idMesaPisoSede = $idPiso = $idMesa = 0;
    
    $fecha = date("d/m/Y");
    
    $objUsuario = UsuarioData::getById($idUsuario);
    $usuario = $objUsuario->nombres . " " . $objUsuario->apellidos;
    
    $idPiso = 0;
    $objCaja = $objUsuario->getCaja();
    if ($objCaja) {
        $objPiso = $objCaja->getPiso();
        $idPiso = $objPiso->id;
    }
    
    $lstCategoria = CategoriaData::getAll(1);
    $lstProducto = ProductoData::getAll(1, $idSede, $lstCategoria[0]->id);
    
    $numDocumento = $cliente = "";
    
    if (isset($_GET["id"])) {
        $idCredito = $_GET["id"];
        $objCredito = CreditoData::getById($idCredito);
        
        $numDocumento = $objCredito->num_documento;
        $cliente = $objCredito->datos;
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
    
    $lstHistorialCaja = HistorialCajaData::getAll(1, $_SESSION["sede"], $_SESSION["caja"], $fecha);
    
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
            					<label for="numdoc">Num. Documento :</label>
            					<input type="text" id="numdoc" name="numdoc" class="form-control" value="<?php echo $numDocumento; ?>" readonly/>
            				</div>
            				<div class="col-md-4 col-sm-12">
            					<label for="cliente">Cliente :*</label>
            					<input type="text" id="cliente" name="cliente" class="form-control" value="<?php echo $cliente; ?>" readonly/>
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
    						<div class="col-md-4 col-sm-12">
        						<label for="buscar">Buscar :</label>
        						<input type="text" id="campo" name="campo" class="form-control" placeholder="" maxlength="50" value=""/>    						
        					</div>
        					<div class="col-md-2 col-sm-12" style="padding-top: 25px;">
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
    					</div>
    					<div class="form-group">
    						<div class="col-md-2 col-sm-12">
    							<label for="cantidad">Cantidad :</label>
    							<input type="text" id="cantidad" name="cantidad" class="form-control" placeholder="0.00" maxlength="2" value="1" onkeypress="return soloNumeros(event);"/>
            				</div>
            				<div class="col-md-4 col-sm-12">
    							<label for="comentario">Comentario :</label>
    							<input type="text" id="comentario" name="comentario" class="form-control" placeholder="Comentario" maxlength="50" value="" readonly/>
            				</div>
            				<div class="col-md-6 col-sm-12" style="padding-top: 25px;">
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
    	    									idPedido: idPedido,
    	    									credito: 1
    	    	                           	}, function (data) {
    		    	                           	$("#tabla").html(data);
        	    								$("#btnAgregar").removeAttr("disabled");
        	    								$("#cantidad").val("1");
        	    								$("#comentario").val("");        	    								
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
    						<div class="col-md-12 col-sm-6 col-xs-12">
    							<label for="mensajes">No hay productos asociados a esa categoría</label>
    						</div>
    						<?php 
    						      }
    						?>
    					</div>
    				</fieldset>
					<div class="form-group col-lg-offset-2 col-lg-12">
        				<div class="box">
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
                                    				<th scope="col" style="text-align: right;">Precio</th>
                                    				<th scope="col" style="text-align: right;">Total</th>
                                    				<th scope="col" style="text-align: center;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
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
        					<input type="hidden" id="idcredito" name="idcredito" value="<?php echo $idCredito; ?>"/>
        					<input type="hidden" id="accion" name="accion" value="1"/>
        				</div>
        			</div>        			
        		</div>
        	</div>
        	</form>
        	<script type="text/javascript">
        		$("#btnCancelar").click(function(){
            		location.href = "./index.php?view=credits";
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
            		setTimeout(function(){$("#campo").trigger("focus")},1);
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
    										window.location.href = "./index.php?view=newpayment1&id="+data+"&credito=<?php echo $idCredito; ?>";
    		                        	})        					
    		        				} else if (data == 0) {
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Ocurrio un error al registrar el pedido"
    		    						})
    		        				} else {
    		        					Swal.fire({
    		    							icon: "warning",
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
    		    		}
    		        });
    			});
    		</script>
        </div>
	</div>
</section>	       