<?php
    $lstTipo = ParametroData::getAll(1, "TIPO REPORTE", "POR RANGO");        
    $tipo = $lstTipo[0]->id;
    $fechaInicio = date("d/m/Y");
    $fechaFin = date("d/m/Y");
    $anho = $anhoInicio = $anhoFin = "";
    $mesInicio = $mesFin = "";
    $indicador = 0;
    if (count($_POST) > 0) {
        $tipo = $_POST["tipo"];    
        $indicador = $_POST["tiporeporte"];
        if ($indicador == 0) {
            $fechaInicio = $_POST["fechai"];
            $fechaFin = $_POST["fechaf"];
        } else if ($indicador == 1) {
            $anho = $_POST["anho"];
            $mesInicio = $_POST["mesi"];
            $mesFin = $_POST["mesf"];
        } else if ($indicador == 2) {
            $anhoInicio = $_POST["anhoi"];
            $anhoFin = $_POST["anhof"];
        }
    }
    
    $lstPago = array();
    if ($indicador == 0) {
        $arrFecha = explode("/", $fechaInicio);
        $fechaInicioTmp = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
        
        $arrFecha = explode("/", $fechaFin);
        $fechaFinTmp = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
        
        $sd = strtotime($fechaInicioTmp);
        $ed = strtotime($fechaFinTmp);
                    
        for ($i = $sd; $i <= $ed; $i += (60 * 60 * 24)) {
            $lstPago[$i] = PagoData::getVentaGroupByFecha($_SESSION["sede"], date("Y-m-d", $i), date("Y-m-d", $i));
        }
    } else if ($indicador == 1) {
        for ($i = $mesInicio; $i <= $mesFin; $i++) {
            $lstPago[$i] = PagoData::getVentaGroupByMes($_SESSION["sede"], $anho, $i);
        }
    } else if ($indicador == 2) {
        for ($i = $anhoInicio; $i <= $anhoFin; $i++) {
            $lstPago[$i] = PagoData::getVentaGroupByAnho($_SESSION["sede"], $i);
        }
    }
    $lstTipo = ParametroData::getAll(1, "TIPO REPORTE");
    $lstAnho = ParametroData::getAllAnho();
    $lstMes = ParametroData::getAllMes();
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
    	<?php if ($indicador == 0) { ?>
    	$("#divfechai").css("display", "block");
    	$("#divfechaf").css("display", "block");

    	$("#divanho").css("display", "none");
    	$("#divmesi").css("display", "none");
    	$("#divmesf").css("display", "none");

    	$("#divanhoi").css("display", "none");
    	$("#divanhof").css("display", "none");
    	<?php } else if ($indicador == 1) { ?>
    	$("#divfechai").css("display", "none");
    	$("#divfechaf").css("display", "none");

    	$("#divanho").css("display", "block");
    	$("#divmesi").css("display", "block");
    	$("#divmesf").css("display", "block");

    	$("#divanhoi").css("display", "none");
    	$("#divanhof").css("display", "none");
    	<?php } else if ($indicador == 2) { ?>
    	$("#divfechai").css("display", "none");
    	$("#divfechaf").css("display", "none");

    	$("#divanho").css("display", "none");
    	$("#divmesi").css("display", "none");
    	$("#divmesf").css("display", "none");

    	$("#divanhoi").css("display", "block");
    	$("#divanhof").css("display", "block");
    	<?php } ?>
	});
</script>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Reporte Ventas</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="report2" action="" role="form" autocomplete="off">    			
            			<div class="form-group">
            				<div class="col-md-2 col-sm-12">
        						<label for="tipo">Tipo :</label>
            					<select id="tipo" name="tipo" class="form-control">
        							<?php foreach ($lstTipo as $objTipo) { ?>
        							<option value="<?php echo $objTipo->id; ?>" <?php if ($objTipo->id == $tipo) { echo "selected"; } ?>><?php echo $objTipo->nombre; ?></option>
          							<?php } ?>
        						</select>
        						<script type="text/javascript">
            						$("#tipo").change(function() {
            							var tipo = $("#tipo").val();
            							$.blockUI();
                                        $.post("./?action=utilitarios", {
                                            tiporeporte: tipo
                                        }, function (data) {
                                        	if (data === "0") {
                                            	$("#divfechai").css("display", "block");
                                            	$("#divfechaf").css("display", "block");

                                            	$("#divanho").css("display", "none");
                                            	$("#divmesi").css("display", "none");
                                            	$("#divmesf").css("display", "none");

                                            	
                                            	$("#divanhoi").css("display", "none");
                                            	$("#divanhof").css("display", "none");
                                        	} else if (data === "1") {
                                        		$("#divfechai").css("display", "none");
                                            	$("#divfechaf").css("display", "none");
												
                                            	$("#divanho").css("display", "block");
                                            	$("#divmesi").css("display", "block");
                                            	$("#divmesf").css("display", "block");

                                            	$("#divanhoi").css("display", "none");
                                            	$("#divanhof").css("display", "none");
                                        	} else if (data === "2") {
                                        		$("#divfechai").css("display", "none");
                                            	$("#divfechaf").css("display", "none");

                                            	$("#divanho").css("display", "none");
                                            	$("#divmesi").css("display", "none");
                                            	$("#divmesf").css("display", "none");

                                            	$("#divanhoi").css("display", "block");
                                            	$("#divanhof").css("display", "block");
                                        	}
                                        	$("#tiporeporte").val(data);
                                        	$.unblockUI();
                                        });
                                    });
        						</script>
        					</div>
            				<div class="col-md-2 col-sm-12" id="divfechai">
                				<label for="fecha">Fecha Inicio:</label>
        						<input type="text" id="fechai" name="fechai" value="<?php echo $fechaInicio; ?>" class="form-control" readonly/>
        					</div>
        					<div class="col-md-2 col-sm-12" id="divfechaf">
                				<label for="fecha">Fecha Fin:</label>
        						<input type="text" id="fechaf" name="fechaf" value="<?php echo $fechaFin; ?>" class="form-control" readonly/>
        					</div>        					
        					<div class="col-md-2 col-sm-12" id="divanho" style="display: none;">
                				<label for="anho">Año :</label>
        						<select id="anho" name="anho" class="form-control">
        							<?php foreach ($lstAnho as $objAnho) { ?>
        							<option value="<?php echo $objAnho->id; ?>" <?php if ($objAnho->id == $anho) { echo "selected"; } ?>><?php echo $objAnho->anho; ?></option>
          							<?php } ?>
        						</select>
        					</div>
        					<div class="col-md-2 col-sm-12" id="divmesi" style="display: none;">
                				<label for="mesi">Mes Inicio :</label>
        						<select id="mesi" name="mesi" class="form-control">
        							<?php foreach ($lstMes as $objMes) { ?>
        							<option value="<?php echo $objMes->id; ?>" <?php if ($objMes->id == $mesInicio) { echo "selected"; } ?>><?php echo $objMes->mes; ?></option>
          							<?php } ?>
        						</select>
        					</div>
        					<div class="col-md-2 col-sm-12" id="divmesf" style="display: none;">
                				<label for="mesf">Mes Fin :</label>
        						<select id="mesf" name="mesf" class="form-control">
        							<?php foreach ($lstMes as $objMes) { ?>
        							<option value="<?php echo $objMes->id; ?>" <?php if ($objMes->id == $mesFin) { echo "selected"; } ?>><?php echo $objMes->mes; ?></option>
          							<?php } ?>
        						</select>
        					</div>
        					<div class="col-md-2 col-sm-12" id="divanhoi" style="display: none;">
                				<label for="anho">Año Inicio :</label>
        						<select id="anhoi" name="anhoi" class="form-control">
        							<?php foreach ($lstAnho as $objAnho) { ?>
        							<option value="<?php echo $objAnho->id; ?>" <?php if ($objAnho->id == $anhoInicio) { echo "selected"; } ?>><?php echo $objAnho->anho; ?></option>
          							<?php } ?>
        						</select>
        					</div>
        					<div class="col-md-2 col-sm-12" id="divanhof" style="display: none;">
                				<label for="anho">Año Fin :</label>
        						<select id="anhof" name="anhof" class="form-control">
        							<?php foreach ($lstAnho as $objAnho) { ?>
        							<option value="<?php echo $objAnho->id; ?>" <?php if ($objAnho->id == $anhoFin) { echo "selected"; } ?>><?php echo $objAnho->anho; ?></option>
          							<?php } ?>
        						</select>
        					</div>
        					<div class="col-md-4 col-sm-12" style="padding-top: 25px;">
        						<button type="button" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>
        						<button type="button" id="btnLimpiar" class="btn btn-danger" title="Limpiar"><em class="fa fa-eraser"></em></button>        						
        						<?php if ($indicador == 0) { ?>
        						<a class="btn btn-primary" id="btnExportar" href="excelReporte.php?sede=<?php echo $_SESSION["sede"]; ?>&fechaInicio=<?php echo $fechaInicio; ?>&fechaFin=<?php echo $fechaFin; ?>&opcion=2&indicador=<?php echo $indicador; ?>" role="button" title="Descargar" target="_blank"><em class="fa fa-download"></em></a>
        						<?php } else if ($indicador == 1) { ?>
        						<a class="btn btn-primary" id="btnExportar" href="excelReporte.php?sede=<?php echo $_SESSION["sede"]; ?>&anho=<?php echo $anho; ?>&mesInicio=<?php echo $mesInicio; ?>&mesFin=<?php echo $mesFin; ?>&opcion=2&indicador=<?php echo $indicador; ?>" role="button" title="Descargar" target="_blank"><em class="fa fa-download"></em></a>
        						<?php } else if ($indicador == 2) { ?>
        						<a class="btn btn-primary" id="btnExportar" href="excelReporte.php?sede=<?php echo $_SESSION["sede"]; ?>&anhoInicio=<?php echo $anhoInicio; ?>&anhoFin=<?php echo $anhoFin; ?>&opcion=2&indicador=<?php echo $indicador; ?>" role="button" title="Descargar" target="_blank"><em class="fa fa-download"></em></a>
        						<?php } ?>
        						<input type="hidden" id="tiporeporte" name="tiporeporte" value="<?php echo $indicador; ?>"/>
        						<script type="text/javascript">
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=report2";
            		    			});
            		    			$("#btnBuscar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            							$("#report2").submit();
            		    			});
        						</script>
            				</div>
    					</div>
            		</form>
            	</div>
            </div>
		</div>
	</div>
	<div class="box box-primary">
		<div class="box box-success">
            <div class="box-header">
    			<div class="box-title">Reporte Ventas</div>
    		</div>
    		<div class="box-body">
    			<div id="graph1" class="animate" data-animate="fadeInUp"></div>
    		</div>
    	</div>
	</div>
            	
</section>
<script type="text/javascript">
	<?php
        echo "var c=0;";
        echo "var dates1 = Array();";
        echo "var data1 = Array();";
        echo "var total1 = Array();";
        if ($indicador == 0) {
            for ($i = $sd; $i <= $ed; $i += (60 * 60 * 24)) {
                echo "dates1[c] = \"" . date("d/m/y", strtotime(date("Y-m-d", $i))) . "\";";
                echo "data1[c] = " . $lstPago[$i][0]->total . ";";
                echo "total1[c] = {x: dates1[c],y: data1[c]};";
                echo "c++;";
            }
        } else if ($indicador == 1) {
            for ($i = $mesInicio; $i <= $mesFin; $i++) {
                $mes = "";
                if ($i == 1) {
                    $mes = "ENE";
                }
                if ($i == 2) {
                    $mes = "FEB";
                }
                if ($i == 3) {
                    $mes = "MAR";
                }
                if ($i == 4) {
                    $mes = "ABR";
                }
                if ($i == 5) {
                    $mes = "MAY";
                }
                if ($i == 6) {
                    $mes = "JUN";
                }
                if ($i == 7) {
                    $mes = "JUL";
                }                
                if ($i == 8) {
                    $mes = "AGO";
                }
                if ($i == 9) {
                    $mes = "SET";
                }
                if ($i == 10) {
                    $mes = "OCT";
                }
                if ($i == 11) {
                    $mes = "NOV";
                }
                if ($i == 12) {
                    $mes = "DIC";
                }
                echo "dates1[c] = '" . $mes . "';";
                echo "data1[c] = " . $lstPago[$i][0]->total . ";";
                echo "total1[c] = {x: dates1[c],y: data1[c]};";
                echo "c++;";
            }
        } else if ($indicador == 2) {
            for ($i = $anhoInicio; $i <= $anhoFin; $i++) {
                $objParametro = ParametroData::getByAnho($i);
                echo "dates1[c] = '" . $objParametro->anho . "';";
                echo "data1[c] = " . $lstPago[$i][0]->total . ";";
                echo "total1[c] = {x: dates1[c],y: data1[c]};";
                echo "c++;";
            }
        }
    ?>
    Morris.Bar({
  		element: "graph1",
  		data: total1,
  		xkey: "x",
  		ykeys: ["y"],
  		labels: ["Total Ventas"],
  		resize: true
	}).on("click", function(i, row){
  		console.log(i, row);
	});	
</script>