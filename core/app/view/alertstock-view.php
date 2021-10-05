<?php
    $sede = $_SESSION["sede"];
    $empresa = $_SESSION["empresa"];
    $almacen = "";
    if (count($_POST) > 0) {
        $almacen = $_POST["almacen"];
    }
    $lstAlmacen = AlmacenData::getAll(1, $empresa, $sede);
    $lstInsumoXAlmacen = InsumoAlmacenData::getAll($almacen);
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Insumo</h3></div>
    			<div class="panel-body">
    				<form class="form-horizontal" method="post" id="supplies" action="" role="form" autocomplete="off">    			
            			<div class="form-group">
            				<div class="col-md-4 col-sm-12">
    							<label for="almacen">Almacen :</label>
    							<select id="almacen" name="almacen" class="form-control">
    								<option value="">TODOS</option>
    								<?php foreach ($lstAlmacen as $objAlmacen) { ?>
    								<option value="<?php echo $objAlmacen->id; ?>" <?php if ($objAlmacen->id == $almacen) { echo "selected"; } ?>><?php echo $objAlmacen->nombre; ?></option>
      								<?php } ?>
    							</select>
    						</div>
    						<div class="col-md-8 col-sm-12" style="padding-top: 25px;">
        						<button type="button" id="btnBuscar" class="btn btn-success" title="Buscar"><em class="fa fa-search"></em></button>
        						<button type="button" id="btnLimpiar" class="btn btn-danger" title="Limpiar"><em class="fa fa-eraser"></em></button>
        						<script type="text/javascript">
        							$("#btnBuscar").click(function(){
            							$.blockUI();
            		    				$("button").prop("disabled", true);
            		    				$("#supplies").submit();
            		    			});
            						$("#btnLimpiar").click(function(){
            							$.blockUI();
            							$("button").prop("disabled", true);
            		    				location.href = "./index.php?view=alertstock";
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
        								<th scope="col">Almacen</th>
        								<th scope="col">Insumo</th>        								
        								<th scope="col">Unidad Medida</th>
        								<th scope="col" style="text-align: left;">Stock</th>
        								<th scope="col" style="text-align: left;">Stock Min.</th>
        								<th scope="col" style="text-align: left;">Stock Max.</th>
        								<th scope="col">Indicador</th>        								
        							</tr>
        						</thead>
        						<tbody>
        						<?php
        						      foreach ($lstInsumoXAlmacen as $objInsumoXAlmacen) {
        						          $objInsumo = $objInsumoXAlmacen->getInsumo();
        						          $objUnidad = $objInsumoXAlmacen->getInsumo()->getUnidad();
        						          $objAlmacen = $objInsumoXAlmacen->getAlmacen();
        						          
        						          $icono = "icon_verde.ico";
        						          if ($objInsumoXAlmacen->stock < $objInsumoXAlmacen->stock_minimo) {
        						              $icono = "icon_rojo.ico";
        						          }
                                ?>
        							<tr>
            							<td style="text-align: left;"><?php echo $objAlmacen->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objInsumo->nombre; ?></td>
            							<td style="text-align: left;"><?php echo $objUnidad->nombre; ?></td>
            							<td style="text-align: right: ;"><?php echo number_format($objInsumoXAlmacen->stock, 2); ?></td>
            							<td style="text-align: right: ;"><?php echo number_format($objInsumoXAlmacen->stock_minimo, 2); ?></td>
            							<td style="text-align: right: ;"><?php echo number_format($objInsumoXAlmacen->stock_maximo, 2); ?></td>
            							<td style="text-align: left;"><img alt="icono" src="img/<?php echo $icono; ?>" border="0" class="img-thumbnail"></td>
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