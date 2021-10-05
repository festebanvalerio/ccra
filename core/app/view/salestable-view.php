<?php
    $texto = "Registrar Venta - Mesero / Piso / Mesa";
    
    $objSede = SedeData::getById($_SESSION["sede"]);
    $idSede = $objSede->id;
    $sede = $objSede->nombre;
    
    $idUsuario = $_GET["usuario"];
    $idPisoSede = $_GET["piso"];
    
    if (!is_numeric($idUsuario) || !is_numeric($idPisoSede)) {
        header("Location: index.php?view=sales");
    }
    
    $fecha = date("d/m/Y");    
    $usuario = "";
    $objUsuario = UsuarioData::getById($idUsuario);
    if ($objUsuario) {
        $usuario = $objUsuario->nombres." ".$objUsuario->apellidos;
    }
    $idPiso = 0;
    $piso = "";
    $objPisoXSede = PisoSedeData::getById($idPisoSede);
    if ($objPisoXSede) {
        $objPiso = $objPisoXSede->getPiso();
        if ($objPiso) {
            $idPiso = $objPiso->id;
            $piso = $objPiso->nombre;
        }
    }
    $lstMesaXPisoXSede = MesaPisoSedeData::getMesaxPisoxSede($idPisoSede);
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="newsale" action="index.php?action=addsale" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>
        		<div class="panel-body">
        			<div class="form-group">
        				<div class="col-md-2 col-sm-12">
        					<label for="fecha">Fecha :*</label>
        					<input type="text" id="fecha" name="fecha" class="form-control" placeholder="Fecha" maxlength="10" value="<?php echo $fecha; ?>" readonly/>
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="sede">Sede :*</label>
        					<input type="text" id="sede" name="sede" class="form-control" placeholder="Sede" maxlength="100" value="<?php echo $sede; ?>" readonly/>
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="piso">Piso :*</label>
        					<input type="text" id="piso" name="piso" class="form-control" placeholder="Piso" maxlength="100" value="<?php echo $piso; ?>" readonly/>
        				</div>
        				<div class="col-md-4 col-sm-12">
        					<label for="usuario">Usuario :*</label>
        					<input type="text" id="usuario" name="usuario" class="form-control" placeholder="Usuario" maxlength="100" value="<?php echo $usuario; ?>" readonly/>
        				</div>
        			</div>
        			<div class="form-group">
        				<div class="col-md-2 col-sm-12">
        					<label for="mesas">Mesas :</label>
        				</div>
        			</div>
        			<div class="form-group">
        				<?php
        				    foreach ($lstMesaXPisoXSede as $objMesaXPisoXSede) {
        				        $idMesa = $objMesaXPisoXSede->getMesa()->id;
        				        $mesa = $objMesaXPisoXSede->getMesa()->nombre;
        				        
        				        $pedidoEnCursoXUsuario = false;
        				        $pedidoEnCursoXOtroUsuario = false;
        				        $otroUsuario = "";
        				        
        				        // Validar si el mesero tiene pedido en curso en esa mesa
        				        $lstPedido = PedidoData::getMesaOcupadaXMozo(1, $objMesaXPisoXSede->getPisoSede()->getSede()->id,
        				            $objMesaXPisoXSede->getPisoSede()->getPiso()->id, $objMesaXPisoXSede->getMesa()->id,
        				            $idUsuario);
        				        if (count($lstPedido) > 0) {
        				            $pedidoEnCursoXUsuario = true;
        				        } else {
        				            $lstPedido = PedidoData::getMesaOcupadaXMozo(1, $objMesaXPisoXSede->getPisoSede()->getSede()->id,
        				                $objMesaXPisoXSede->getPisoSede()->getPiso()->id, $objMesaXPisoXSede->getMesa()->id,
        				                $idUsuario, 1);
        				            if (count($lstPedido) > 0) {
        				                $pedidoEnCursoXOtroUsuario = true;
        				                $otroUsuario = '<span style="color: red;">'.$lstPedido[0]->getUsuario()->nombres.'</span>';
        				            }
        				        }
        				        
        				        $mesaLibre = "";
        				        $indicadorMesaLibre = false;
        				        if (!$pedidoEnCursoXUsuario && !$pedidoEnCursoXOtroUsuario) {
        				            $mesaLibre = 'class="info-box-icon bg-blue" style="cursor: pointer;"';
        				            $indicadorMesaLibre = true;
        				        } else if ($pedidoEnCursoXUsuario) {
        				            $mesaLibre = 'class="info-box-icon bg-orange" style="cursor: pointer;"';
        				            $indicadorMesaLibre = true;
        				        } else if ($pedidoEnCursoXOtroUsuario) {
        				            $mesaLibre = 'class="info-box-icon bg-red"';
        				        }
        				?>
        				<div class="col-md-2 col-sm-6 col-xs-12">
							<div class="info-box">
								<span id="mesa<?php echo $objMesaXPisoXSede->id; ?>" <?php echo $mesaLibre; ?>>
									<em class="fa fa-table"></em>
								</span>
								<div class="info-box-content">					
									<span class="info-box-number" style="font-size: 12px;"><?php echo $mesa; ?><br/><?php echo $otroUsuario; ?></span>
								</div>
								<?php if ($indicadorMesaLibre) { ?>
								<script type="text/javascript">
									$("#mesa<?php echo $objMesaXPisoXSede->id; ?>").click(function(){
										$.blockUI();

										$.post("./?action=utilitarios", {
	    									sede: <?php echo $idSede; ?>,
	    									piso: <?php echo $idPiso; ?>,
	    	    							mesa: <?php echo $idMesa; ?>,
	    	    	    					usuario: <?php echo $idUsuario; ?>
	    	                           	}, function (data) {
	        	                           	if (data === "0") {
	        	                           		location.href = "./index.php?view=salestableitem&usuario=<?php echo $idUsuario; ?>&piso=<?php echo $idPisoSede; ?>&mesa=<?php echo $objMesaXPisoXSede->id; ?>";
	        	                           	} else {   	
	    	                           			$.unblockUI();
	    	                           			Swal.fire({
		    										icon: 'warning',
		    										title: 'La mesa está actualmente atendida por ' + data
		    									})
	        	                           	}
	    	                           	});
										
									});
								</script>
								<?php } ?>
							</div>
						</div>
        				<?php
        				    }
        			    ?>
        			</div>        			
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
        					<button type="button" id="btnRefrescar" class="btn btn-success">Refrescar</button>
        					<button type="button" id="btnRegresar" class="btn btn-danger">Regresar</button>        					
        				</div>
        			</div>
        		</div>
        	</div>
        	</form>
        	<script type="text/javascript">
        		$("#btnRefrescar").click(function(){
        			location.href = "./index.php?view=salestable&usuario=<?php echo $idUsuario; ?>&piso=<?php echo $idPisoSede; ?>";
        		});
        		$("#btnRegresar").click(function(){
        			location.href = "./index.php?view=sales";
        		});    			
    		</script>
        </div>
	</div>
</section>