<?php
    $numDocumento = $datos = "";
    if (count($_POST) > 0) {
        $numDocumento = $_POST["numdoc"];
        $datos = $_POST["datos"];
        
        $_SESSION["credits_numdoc"] = $numDocumento;
        $_SESSION["credits_datos"] = $datos;
    } else {
        if (isset($_SESSION["credits_numdoc"]) && isset($_SESSION["credits_datos"])) {
            $numDocumento = $_SESSION["credits_numdoc"];
            $datos = $_SESSION["credits_datos"];
        }
    }
    $lstCredito = CreditoData::getAll($numDocumento, $datos);
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Créditos</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="credits" action="" role="form" autocomplete="off">    			
            			<div class="form-group">            				
            				<div class="col-md-2 col-sm-12">
                				<label for="numdoc">Num. Documento :</label>
        						<input type="text" id="numdoc" name="numdoc" value="<?php echo $numDocumento; ?>" class="form-control" placeholder="Num. Documento" maxlength="12" onkeypress="return soloNumeros(event)"/>
        					</div>
        					<div class="col-md-4 col-sm-12">
                				<label for="datos">Cliente :</label>
        						<input type="text" id="datos" name="datos" value="<?php echo $datos; ?>" class="form-control" placeholder="Cliente" maxlength="150"/>
        					</div>
        					<div class="col-md-4 col-sm-12"></div>
        					<div class="col-md-2 col-sm-12" style="padding-top: 25px;">
        						<button type="submit" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>
        						<button type="button" id="btnLimpiar" class="btn btn-danger" title="Limpiar"><em class="fa fa-eraser"></em></button>
        						<script type="text/javascript">
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$.post("index.php?action=deletesearch", {
                							opcion: "credits"                							
                                        }, function (data) {
                                        	location.href = "./index.php?view=credits";
                                        });
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
        								<th scope="col">Item</th>
        								<th scope="col">Num. Documento</th>
        								<th scope="col">Cliente</th>
        								<th scope="col" style="text-align: right;">Deuda</th>
        								<th scope="col" style="text-align: right;">Abono</th>
        								<th scope="col" style="text-align: right;">Saldo</th>
        								<th scope="col">Acciones</th>
        							</tr>
        						</thead>
        						<tbody>
        						<?php        					
        						      $item = 1;
        						      foreach ($lstCredito as $objCredito) {
        						          $deshabilitado = "";
        						          $saldo = $objCredito->monto - $objCredito->abono;
        						          if ($saldo == 0) {
        						              $deshabilitado = "disabled";
        						          }
                                ?>
        							<tr>
										<td style="text-align: left;"><?php echo $item++ ?></td>
										<td style="text-align: left;"><?php echo $objCredito->num_documento; ?></td>
										<td style="text-align: left;"><?php echo $objCredito->datos ?></td>
										<td style="text-align: right;"><?php echo number_format($objCredito->monto, 2); ?></td>
										<td style="text-align: right;"><?php echo number_format($objCredito->abono, 2); ?></td>
										<td style="text-align: right;"><?php echo number_format($saldo, 2); ?></td>
										<td style="text-align: left;">
            								<a href="index.php?view=salescredititem&id=<?php echo $objCredito->id; ?>" title="Registrar Pago Crédito" class="btn btn-success btn-xs" <?php echo $deshabilitado; ?>><em class="fa fa fa-pencil-square-o"></em></a>
											<a href="index.php?view=detailcredit&id=<?php echo $objCredito->id; ?>" title="Detalle" class="btn btn-warning btn-xs"><em class="fa fa-search-plus"></em></a>            								            											
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
</section>