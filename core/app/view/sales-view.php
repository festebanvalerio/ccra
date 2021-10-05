<?php
    $texto = "Registrar Venta - Mesero / Piso";    
    $objSede = SedeData::getById($_SESSION["sede"]);
    $idSede = $objSede->id;    
    $sede = $objSede->nombre;
    $fecha = date("d/m/Y");
    
    $esMesero = false;
    $lstMozo = NULL;
    $idPerfilUsuario = $_SESSION["perfil"];
    $objPerfilUsuario = PerfilData::getById($idPerfilUsuario);
    if ($objPerfilUsuario->indicador == 2) {
        // Si el usuario es mesero
        $lstMozo = UsuarioData::getAllMesero(1, $_SESSION["sede"], $_SESSION["user"]);
        $esMesero = true;
    } else {
        $lstMozo = UsuarioData::getAllMesero(1, $_SESSION["sede"]);
    }    
    $lstPisoXSede = PisoSedeData::getPisoXSede($idSede);    
    $lstHistorialCaja = HistorialCajaData::getAll(1, $_SESSION["sede"], "", $fecha);
?>
<script type="text/javascript">
	$(document).ready(function(){
		<?php if ($esMesero) { ?>
    		$("#usuario").val("<?php echo $lstMozo[0]->id; ?>");
    		$("#mozosel").val("<?php echo $lstMozo[0]->nombres; ?>");
		<?php } ?>
		<?php if (count($lstPisoXSede) == 1) { ?>
			$("#piso").val("<?php echo $lstPisoXSede[0]->id; ?>");
			$("#pisosel").val("<?php echo $lstPisoXSede[0]->getPiso()->nombre; ?>");
		<?php } ?>
	});
</script>
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
        				<div class="col-md-4 col-sm-12">
        					<label for="sede">Sede :*</label>
        					<input type="text" id="sede" name="sede" class="form-control" placeholder="Sede" maxlength="100" value="<?php echo $sede; ?>" readonly/>
        				</div>
        			</div>
        			<div class="form-group">
        				<div class="col-md-2 col-sm-12">
        					<label for="mozos">Meseros :</label>        					
        				</div>
        			</div>
        			<div class="form-group">
        				<?php
        				    if (count($lstMozo) > 0) {
        				        foreach ($lstMozo as $objMozo) {
        				            $mesero = $objMozo->nombres;        				    
        				?>
        				<div class="col-md-2 col-sm-6 col-xs-12">
							<div class="info-box">
								<span id="usuario<?php echo $objMozo->id; ?>" class="info-box-icon bg-blue" style="cursor: pointer;">
									<em class="fa fa-male"></em>
								</span>
								<div class="info-box-content">					
									<span class="info-box-number" style="font-size: 12px;"><?php echo $mesero; ?></span>
								</div>
								<script type="text/javascript">
									$("#usuario<?php echo $objMozo->id; ?>").click(function(){
										$("#usuario").val("<?php echo $objMozo->id; ?>");
										$("#mozosel").val("<?php echo $mesero; ?>");
										if ($("#pisosel").val() !== "" && $("#mozosel").val() !== "") {
											$.blockUI();
											
											var usuario = $("#usuario").val();
						    				var piso = $("#piso").val();
											var indicador = $("#indicador").val();
						
						    				if (indicador !== "0") {
												location.href = "./index.php?view=salestable&usuario="+usuario+"&piso="+piso;
						    				} else {
						    					$.unblockUI();
						    					Swal.fire({
	    	    									icon: 'warning',
	    	    									title: 'No existe apertura de caja'
	    	    								})
						    				}
										}
									});
								</script>
							</div>
						</div>
        				<?php
        				        }
        				    } else {
        			    ?>
        			    <div class="col-md-12 col-sm-6 col-xs-12">
        			    	<label for="mensaje">No hay meseros registrados para esta sede</label>
        			    </div>
        			    <?php 
        				    }
        			    ?>
        			</div>
        			<div class="form-group">
        				<div class="col-md-2 col-sm-12">
        					<label for="mozo">Mesero Seleccionado :</label>
        					<input type="text" id="mozosel" name="mozosel" class="form-control" placeholder="" value="" readonly/>        					
        				</div>
        			</div>
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
        					<label for="pisos">Pisos :</label>
        				</div>
        			</div>
        			<div class="form-group">        			
        				<?php
        				    foreach ($lstPisoXSede as $objPisoXSede) {
        				        $piso = $objPisoXSede->getPiso()->nombre;        				    
        				?>
        				<div class="col-md-2 col-sm-6 col-xs-12">
							<div class="info-box">
								<span id="piso<?php echo $objPisoXSede->id; ?>" class="info-box-icon bg-green" style="cursor: pointer;">
									<em class="fa fa-th-list"></em>
								</span>
								<div class="info-box-content">													
									<span class="info-box-number" style="font-size: 12px;"><?php echo $piso; ?></span>
								</div>
								<script type="text/javascript">
									$("#piso<?php echo $objPisoXSede->id; ?>").click(function(){
										$("#piso").val("<?php echo $objPisoXSede->id; ?>");
										$("#pisosel").val("<?php echo $piso; ?>");
										if ($("#mozosel").val() !== "") {
											$.blockUI();
											
											var usuario = $("#usuario").val();
						    				var piso = $("#piso").val();
						    				var indicador = $("#indicador").val();
											
						    				if (indicador !== "0") {
												location.href = "./index.php?view=salestable&usuario="+usuario+"&piso="+piso;
						    				} else {
						    					$.unblockUI();
						    					Swal.fire({
	    	    									icon: 'warning',
	    	    									title: 'No existe apertura de caja'
	    	    								})
						    				}
										}
									});
								</script>
							</div>
						</div>        				
        				<?php
        				    }
        				?>        				
        			</div>
        			<div class="form-group">
        				<div class="col-md-2 col-sm-12">
        					<label for="piso">Piso Seleccionado :</label>
        					<input type="text" id="pisosel" name="pisosel" class="form-control" placeholder="" value="" readonly/>        					
        				</div>
        			</div>
        			<div class="form-group">
        				<div class="col-md-12 col-sm-12">
        					<input type="hidden" id="sede" name="sede" value="<?php echo $idSede; ?>"/>
        					<input type="hidden" id="usuario" name="usuario" value=""/>
        					<input type="hidden" id="piso" name="piso" value=""/>
        					<input type="hidden" id="indicador" name="indicador" value="<?php echo count($lstHistorialCaja); ?>"/>
        					<input type="hidden" id="accion" name="accion" value="1"/>        					        				
        				</div>
        			</div>
        		</div>
        	</div>
        	</form>        	
        </div>
	</div>
</section>	       