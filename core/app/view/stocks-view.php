<?php    $empresa = $_SESSION["empresa"];    $sede = $_SESSION["sede"];    $lstStock = array();    $lstAlmacen = AlmacenData::getAll(1, $empresa, $sede);    if (count($lstAlmacen) > 0) {        $objAlmacen = $lstAlmacen[0];        $lstStock = InsumoAlmacenData::getAllByInsumo(1, "", $objAlmacen->id);            }?><section class="content">	<div class="row">		<div class="col-md-12">			<div class="panel panel-primary">    			<div class="panel-heading" style="background-color: #3c8dbc;"><h3 class="panel-title">Stock</h3></div>    			<div class="panel-body">    			    		    				<form class="form-horizontal" method="post" id="stocks" action="" role="form" autocomplete="off">    			            			<div class="form-group">            				<div class="col-md-8 col-sm-12" style="padding-top: 25px;">        						<button type="button" id="btnAjuste" class="btn btn-success">Ajuste de Inventario</button>        						<button type="button" id="btnTransferencia" class="btn btn-info">Transferencia</button>        						<a class="btn btn-primary" id="btnExportar" href="excelStock.php" role="button" title="Buscar" target="_blank">Exportar</a>        						<script type="text/javascript">            						$("#btnAjuste").click(function(){            							$.blockUI();            							$("button").prop("disabled", true);            		    				location.href = "./index.php?view=newadjustment";            		    			});            						$("#btnTransferencia").click(function(){            							$.blockUI();            							$("button").prop("disabled", true);            		    				location.href = "./index.php?view=newtransfer";            		    			});            						        						</script>            				</div>    					</div>            		</form>					<div class="table-responsive">        				<div class="box-body">        					<table class="table table-bordered table-hover datatable table-nowrap">        						<thead>        							<tr>        								<th scope="col">Sede</th>        								<th scope="col">Almacen</th>        								<th scope="col">Insumo</th>        								<th scope="col">Unidad</th>        								<th scope="col" style="text-align: right;">Stock</th>        								        								<th scope="col">Acciones</th>        							</tr>        						</thead>        						<tbody>
        						<?php        						    foreach ($lstStock as $objStock) {        						        $objAlmacen = $objStock->getAlmacen();        						        $objInsumo = $objStock->getInsumo();        						        $objUnidad = $objInsumo->getUnidad();        						        $objSede = $objAlmacen->getSede();
                                ?>
        							<tr>        								<td style="text-align: left;"><?php echo $objSede->nombre; ?></td>            							<td style="text-align: left;"><?php echo $objAlmacen->nombre; ?></td>            							<td style="text-align: left;"><?php echo $objInsumo->nombre; ?></td>            							<td style="text-align: left;"><?php echo $objUnidad->abreviatura; ?></td>            							<td style="text-align: right;"><?php echo number_format($objStock->stock, 2); ?></td>            							<td style="text-align: left;">            								<a href="index.php?view=detailstock&insumo=<?php echo $objInsumo->id; ?>&almacen=<?php echo $objAlmacen->id; ?>" title="Detalle" class="btn btn-success btn-xs"><em class="fa fa-search-plus"></em></a>                        					            							</td>        							</tr>
        						<?php
                                    }
                                ?>                                </tbody>
        					</table>        				</div>        			</div>        		</div>        	</div>
		</div>	</div></section>