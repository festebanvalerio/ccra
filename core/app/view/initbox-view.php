<?php
    $id = $idHistorialCaja = 0;
    $nomSede = $nomPiso = $nomCaja = "";
    $fecha = "";
    $fechaApertura = date("d/m/Y H:i");
    $fechaCierre = "";
    $montoApertura = $montoCierre = $montoCalculado = 0;
    $soloLecturaApertura = $soloLecturaCierre = "";
    $texto = "Apertura/Cierre Caja";
    $textoBoton = "Registrar";
    $mensaje = "la apertura";
    $existeApertura = false;
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
        $objCaja = CajaData::getById($id);
        $nomCaja = $objCaja->nombre; 
        
        $objPiso = $objCaja->getPiso();
        $idPiso = $objPiso->id;
        $nomPiso = $objPiso->nombre;
        
        if (isset($_GET["fecha"])) {
            $fecha = $_GET["fecha"];
        } else {
            $fecha = date("d/m/Y");
        }
        
        $lstHistorialCaja = HistorialCajaData::getAll(1, $objCaja->sede, $objCaja->id, $fecha);
        if (count($lstHistorialCaja) > 0) {
            $idHistorialCaja = $lstHistorialCaja[0]->id;
            
            $fechaApertura = date("d/m/Y H:i", strtotime($lstHistorialCaja[0]->fecha_apertura));
            $montoApertura = $lstHistorialCaja[0]->monto_apertura;
            if ($objCaja->estado == 2) {
                $fechaCierre = date("d/m/Y H:i", strtotime($lstHistorialCaja[0]->fecha_cierre));
                $montoCierre = $lstHistorialCaja[0]->monto_cierre;
                
                $soloLecturaApertura = $soloLecturaCierre = "disabled";
            } else {
                $soloLecturaApertura = "disabled";
                
                //$objPago = PagoData::getMontoConsultado(date("Y-m-d"));
                //$montoCalculado = $objPago->efectivo + $objPago->tarjeta;
                
                $fechaCierre = date("d/m/Y H:i");
                
                $mensaje = "el cierre";
                $existeApertura = true;
            }
        } else {
            $soloLecturaCierre = "disabled";
        }
    }
    $opcion = "";
    if (isset($_GET["opcion"])) {
        $opcion = $_GET["opcion"];        
    }
    $objSede = SedeData::getById($_SESSION["sede"]);
    $idSede = $objSede->id;
    $nomSede = $objSede->nombre;    
?>
<section class="content">	
	<div class="row">
		<div class="col-md-12">
    		<form class="form-horizontal" method="post" id="box" action="index.php?action=addbox" role="form" autocomplete="off">
    		<div class="panel panel-primary">
				<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title"><?php echo $texto; ?></h3></div>
        		<div class="panel-body">
        			<div class="form-group">
        				<div class="col-md-4 col-sm-12">
        					<label for="sede">Sede :</label>
        					<input type="text" id="nomsede" name="nomsede" class="form-control" placeholder="Sede" value="<?php echo $nomSede; ?>" disabled/>        					
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="piso">Piso :</label>
        					<input type="text" id="nompiso" name="nompiso" class="form-control" placeholder="Piso" value="<?php echo $nomPiso; ?>" disabled/>        					
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="caja">Caja :</label>
        					<input type="text" id="nomcaja" name="nomcaja" class="form-control" placeholder="Caja" value="<?php echo $nomCaja; ?>" disabled/>        					
        				</div>
        				<div class="col-md-2 col-sm-12">
                			<label for="fecha">Fecha Apertura :*</label>
        					<input type="text" id="fechai" name="fechai" value="<?php echo $fechaApertura; ?>" class="form-control" readonly/>
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="montoapertura">Monto Apertura :*</label>
            				<input type="text" id="montoapertura" name="montoapertura" class="form-control" maxlength="10" placeholder="0.00" value="<?php echo number_format($montoApertura, 2); ?>" dir="rtl" required onkeypress="return filterFloat(event,this);" <?php echo $soloLecturaApertura; ?>/>
            				<script type="text/javascript">
            					$("#montoapertura").click(function(){
            						$("#montoapertura").val("");
            					})
            					$("#montoapertura").blur(function(){
        							var montoapertura = $("#montoapertura").val();
        							if (montoapertura !== "") {
        								if (isNaN(montoapertura)) {
        									$("#montoapertura").val("0.00");
    										document.getElementById("montoapertura").focus();    										
    										Swal.fire({
    	    									icon: "warning",
    	    									title: "Sólo valores numéricos (monto apertura)"
    	    								})
    									}
    								} else {
    									$("#montoapertura").val("0.00");
    								}    								    						
        						})
            				</script>
        				</div>
        			</div>
        			<div class="form-group">
        				<div class="col-md-2 col-sm-12">
                			<label for="fecha">Fecha Cierre :*</label>
        					<input type="text" id="fechaf" name="fechaf" value="<?php echo $fechaCierre; ?>" placeholder="dd/mm/yyyy hh:mm" class="form-control" <?php echo $soloLecturaCierre; ?> readonly/>
        				</div>
        				<div class="col-md-2 col-sm-12" style="display: none;">
        					<label for="montocalculado">Monto Calculado :*</label>
            				<input type="text" id="montocalculado" name="montocalculado" class="form-control" maxlength="10" placeholder="0.00" value="<?php echo number_format($montoCalculado, 2); ?>" dir="rtl" onkeypress="return filterFloat(event,this);" disabled/>
        				</div>
        				<div class="col-md-2 col-sm-12">
        					<label for="montocierre">Monto Cierre :*</label>
            				<input type="text" id="montocierre" name="montocierre" class="form-control" maxlength="10" placeholder="0.00" value="<?php echo number_format($montoCierre, 2); ?>" dir="rtl" onkeypress="return filterFloat(event,this);" <?php echo $soloLecturaCierre; ?>/>
            				<script type="text/javascript">
            					$("#montocierre").click(function(){
            						$("#montocierre").val("");
            					})
            					$("#montocierre").blur(function(){
        							var montocierre = $("#montocierre").val();
        							if (montocierre !== "") {
        								if (isNaN(montocierre)) {
        									$("#montocierre").val("0.00");
    										document.getElementById("montocierre").focus();    										
    										Swal.fire({
    	    									icon: "warning",
    	    									title: "Sólo valores numéricos (monto cierre)"
    	    								})
    									}
    								} else {
    									$("#montocierre").val("0.00");
    								}    								    						
        						})
            				</script>
        				</div>
        			</div>
					<div class="form-group">
        				<div class="col-md-12 col-sm-12">
        					<button type="submit" id="btnRegistrar" class="btn btn-success"><?php echo $textoBoton; ?></button>
        					<button type="button" id="btnCancelar" class="btn btn-danger">Cancelar</button>
        					<input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>
        					<input type="hidden" id="sede" name="sede" value="<?php echo $idSede; ?>"/>
        					<input type="hidden" id="piso" name="piso" value="<?php echo $idPiso; ?>"/>
        					<input type="hidden" id="historial" name="historial" value="<?php echo $idHistorialCaja; ?>"/>
        					<input type="hidden" id="fecha" name="fecha" value="<?php echo $fecha; ?>"/>
        					<input type="hidden" id="accion" name="accion" value="3"/>        					
        				</div>
        			</div>
        		</div>
        	</div>
    		</form>
    		<script type="text/javascript">
    			$("#btnCancelar").click(function(){
    				$.blockUI();
            		$("button").prop("disabled", true);
        			<?php if ($opcion == "") { ?>
    				location.href = "./index.php?view=openbox";
    				<?php } else {?>
    				location.href = "./index.php?view=inits";
    				<?php } ?>
    			});
    			$(document).ready(function(){
        			<?php if (!$existeApertura) { ?>
        			document.getElementById("montoapertura").focus();
        			<?php } else { ?>
        			document.getElementById("montocierre").focus();
        			$("#montocierre").attr("required", "required");
        			<?php } ?>
        		});
    			$(function(){
    				<?php if (!$existeApertura) { ?>
        			document.getElementById("montoapertura").focus();
        			<?php } else { ?>
        			document.getElementById("montocierre").focus();
        			<?php } ?>
    				$("#box").validate({
    		        	submitHandler: function(){
    		    			$.ajax({
    		    				type: "POST",
    		    				url: "./index.php?action=addbox",
    		    				async: true,
    		    				dataType: "html",
    		    				data: $("#box").serialize(),
    		    				beforeSend: function() {
    		    					$("#btnRegistrar").attr("disabled", "disabled");
    		    					$("#btnCancelar").attr("disabled", "disabled");
    		    					$.blockUI();
    		    				},
    		    				success: function(data) {
    		    					<?php if (!$existeApertura) { ?>
                        			document.getElementById("montoapertura").focus();
                        			<?php } else { ?>
                        			document.getElementById("montocierre").focus();
                        			<?php } ?>
    		        				if (data > 0) {
    		        					Swal.fire({
    		                                icon: "success",
    										title: "Se registró correctamente <?php echo $mensaje; ?> de la caja <?php echo $nomCaja; ?> (Piso: <?php echo $nomPiso ?> - Sede: <?php echo $nomSede; ?>",
    										showCancelButton: false,
    										confirmButtonColor: "#3085d6",
    										confirmButtonText: "OK"
    		                        	}).then((result) => {
    										window.location.href = "./index.php?view=openbox";    										
    		                        	})        					
    		        				} else {
    		        					Swal.fire({
    		    							icon: "warning",
    		    							title: "Ocurrio un error al registrar <?php echo $mensaje; ?> de la caja <?php echo $nomCaja; ?> (Piso: <?php echo $nomPiso ?> - Sede: <?php echo $nomSede; ?>"																	
    		    						})
    		        				}
    		    				},
    		    				error: function(data) {
    		    					<?php if (!$existeApertura) { ?>
                        			document.getElementById("montoapertura").focus();
                        			<?php } else { ?>
                        			document.getElementById("montocierre").focus();
                        			<?php } ?>
    		    					Swal.fire({
    									icon: "error",
    									title: "Ocurrio un error al registrar <?php echo $mensaje; ?> de la caja <?php echo $nomCaja; ?> (Piso: <?php echo $nomPiso ?> - Sede: <?php echo $nomSede; ?>"
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