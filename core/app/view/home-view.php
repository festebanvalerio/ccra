<?php
    $sede = $_SESSION["sede"];
    
    $lstNumDias = ParametroData::getAll(1, "OPCIONES GENERALES", "NUM DIAS REPORTE");
    $numDiasReporte = $lstNumDias[0]->valor1;
    $textoNumDiasReporte = $lstNumDias[0]->valor2;

    $dateB = new DateTime(date("Y-m-d"));
    $dateA = $dateB->sub(DateInterval::createFromDateString($numDiasReporte));
    $sd = strtotime(date_format($dateA, "Y-m-d"));
    $ed = strtotime(date("Y-m-d"));
    
    $ventas = $pedidos = $tickets = $anuladas = array();
    for ($i = $sd; $i <= $ed; $i += (60 * 60 * 24)) {
        $ventas[$i] = PagoData::getVentaGroupByFecha($sede, date("Y-m-d", $i), date("Y-m-d", $i));
        
        $pedidos[$i] = PedidoData::getPedidoGroupByFecha($sede, date("Y-m-d", $i), date("Y-m-d", $i));
    
        $tickets[$i] = PedidoData::getTicketMedioGroupByFecha($sede, date("Y-m-d", $i), date("Y-m-d", $i));
        
        $anuladas[$i] = PedidoData::getPedidoGroupByFecha($sede, date("Y-m-d", $i), date("Y-m-d", $i), 1);
    }
    
    $objPedido = PedidoData::getNumComensalesByFecha($sede, date("Y-m-d"));
    $numComensales = $objPedido->num_comensales;
    
    $objPedido = PedidoData::getTicketMedioByFecha($sede, date("Y-m-d"));
    $ticketMedio = $objPedido->total;
    
    $productoMasVendido = $productoMasVendidoGeneral = "";
    $totalProductoMasVendido = $totalProductoMasVendidoGeneral = 0;
    $objProductoMasVendidoGeneral = PagoData::getPlatoMasVendidoGeneral($sede, date("Y-m-d"));
    if ($objProductoMasVendidoGeneral) {
        $productoMasVendidoGeneral = $objProductoMasVendidoGeneral->producto;
        $totalProductoMasVendidoGeneral = $objProductoMasVendidoGeneral->total;
    }
    $objProductoMasVendido = PagoData::getPlatoMasVendido($sede, date("Y-m-d"));
    if ($objProductoMasVendido) {
        $productoMasVendido = $objProductoMasVendido->producto;
        $totalProductoMasVendido = $objProductoMasVendido->total;
    }
    
    $lstTipo = ParametroData::getAll(1, "TIPO PEDIDO", "EN MESA");
    $tipoEnMesa = $lstTipo[0]->id;
    
    $lstTipo = ParametroData::getAll(1, "TIPO PEDIDO", "DELIVERY");
    $tipoDelivery = $lstTipo[0]->id;
    
    $lstTipo = ParametroData::getAll(1, "TIPO PEDIDO", "PARA LLEVAR");
    $tipoParaLlevar = $lstTipo[0]->id;
    
    $objPedido = PedidoData::getVentaXTipoByFecha($sede, date("Y-m-d"), $tipoEnMesa);
    $totalVentasEnMesa = $objPedido->total;
    
    $objPedido = PedidoData::getVentaXTipoByFecha($sede, date("Y-m-d"), $tipoDelivery);
    $totalVentasDelivery = $objPedido->total;
    
    $objPedido = PedidoData::getVentaXTipoByFecha($sede, date("Y-m-d"), $tipoParaLlevar);
    $totalVentasParaLlevar = $objPedido->total;
    
    $totalVentas = $totalVentasEnMesa + $totalVentasDelivery + $totalVentasParaLlevar;
?>
<section class="content">
	<div class="row">
		<div class="col-md-3 col-sm-12 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-teal"><em class="fa fa-male"></em></span>
				<div class="info-box-content">
					<span class="info-box-text">Num. Comensales <br> <?php echo date("d/m/Y"); ?></span>
					<span class="info-box-number"><?php echo number_format($numComensales); ?></span>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-12 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-orange"><em class="fa fa-money"></em></span>
				<div class="info-box-content">
					<span class="info-box-text">Total Ventas <br> <?php echo date("d/m/Y"); ?></span>
					<span class="info-box-number"><?php echo number_format($totalVentas, 2); ?></span>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-12 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-maroon"><em class="fa fa-table"></em></span>
				<div class="info-box-content">
					<span class="info-box-text">En Mesa <br> <?php echo date("d/m/Y"); ?></span>
					<span class="info-box-number"><?php echo number_format($totalVentasEnMesa, 2); ?></span>
				</div>
			</div>
		</div>		
		<div class="col-md-3 col-sm-12 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-blue"><em class="fa fa-cart-arrow-down"></em></span>
				<div class="info-box-content">
					<span class="info-box-text">Para Llevar <br> <?php echo date("d/m/Y"); ?></span>
					<span class="info-box-number"><?php echo number_format($totalVentasParaLlevar, 2); ?></span>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-12 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-green"><em class="fa fa-motorcycle"></em></span>
				<div class="info-box-content">
					<span class="info-box-text">Delivery <br> <?php echo date("d/m/Y"); ?></span>
					<span class="info-box-number"><?php echo number_format($totalVentasDelivery, 2); ?></span>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-12 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-yellow"><em class="fa fa-bar-chart"></em></span>
				<div class="info-box-content">
					<span class="info-box-text">Ticket Medio <br> <?php echo date("d/m/Y"); ?></span>
					<span class="info-box-number"><?php echo number_format($ticketMedio, 2); ?>
					</span>
				</div>
			</div>
		</div>
		<?php if ($objProductoMasVendido) { ?>
		<div class="col-md-3 col-sm-12 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-teal"><em class="fa fa-cutlery"></em></span>
				<div class="info-box-content">
					<span class="info-box-text"><?php echo $productoMasVendido; ?> <br> <?php echo date("d/m/Y"); ?></span>
					<span class="info-box-number"><?php echo number_format($totalProductoMasVendido); ?>
					</span>
				</div>
			</div>
		</div>
		<?php } ?>
		<?php if ($objProductoMasVendidoGeneral) { ?>
		<div class="col-md-3 col-sm-12 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-navy"><em class="fa fa-cutlery"></em></span>
				<div class="info-box-content">
					<span class="info-box-text">Producto + Vendido<br>(<?php echo $productoMasVendidoGeneral; ?>)</span>
					<span class="info-box-number"><?php echo number_format($totalProductoMasVendidoGeneral); ?>
					</span>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	<div class="box box-primary">
		<div class="box-header">
			<div class="box-title">Ventas de los ultimos <?php echo $textoNumDiasReporte; ?> días</div>
		</div>
		<div class="box-body">
			<div id="graph1" class="animate" data-animate="fadeInUp"></div>
		</div>
	</div>
	<script type="text/javascript">
	<?php
        echo "var c=0;";
        echo "var dates1=Array();";
        echo "var data1=Array();";
        echo "var total1=Array();";
        for ($i = $sd; $i <= $ed; $i += (60 * 60 * 24)) {
            echo "dates1[c]=\"" . date("Y-m-d", $i) . "\";";
            echo "data1[c]=" . $ventas[$i][0]->total . ";";            
            echo "total1[c]={x: dates1[c],y: data1[c]};";            
            echo "c++;";
        }       
    ?>
    Morris.Area({
  		element: "graph1",
  		data: total1,
  		xkey: "x",
  		ykeys: ["y"],
  		labels: ["Y"],
  		lineColors: ["green"],
  		resize: true
	}).on("click", function(i, row){
  		console.log(i, row);
	});	
	</script>
	<div class="box box-primary">
		<div class="box-header">
			<div class="box-title">Ticket Medio de los ultimos <?php echo $textoNumDiasReporte; ?> días</div>
		</div>
		<div class="box-body">
			<div id="graph2" class="animate" data-animate="fadeInUp"></div>
		</div>
	</div>	
	<script type="text/javascript">
    <?php
        echo "var c=0;";
        echo "var dates1=Array();";
        echo "var data1=Array();";
        echo "var total1=Array();";
        for ($j = $sd; $j <= $ed; $j += (60 * 60 * 24)) {
            echo "dates1[c]=\"" . date("Y-m-d", $j) . "\";";
            echo "data1[c]=" . $tickets[$j][0]->total . ";";
            echo "total1[c]={x: dates1[c],y: data1[c]};";
            echo "c++;";
        }       
    ?>
	Morris.Area({
  		element: "graph2",
  		data: total1,
  		xkey: "x",
  		ykeys: ["y"],
  		labels: ["Y"],
  		lineColors: ["blue"],
  		resize: true
	}).on("click", function(i, row){
  		console.log(i, row);
	});	
	</script>
	<div class="box box-primary">
		<div class="box-header">
			<div class="box-title">Pedidos Anulados de los ultimos <?php echo $textoNumDiasReporte; ?> días</div>
		</div>
		<div class="box-body">
			<div id="graph3" class="animate" data-animate="fadeInUp"></div>
		</div>
	</div>	
	<script type="text/javascript">
	<?php
        echo "var c=0;";
        echo "var dates1=Array();";
        echo "var data1=Array();";
        echo "var total1=Array();";
        for ($i = $sd; $i <= $ed; $i += (60 * 60 * 24)) {
            echo "dates1[c]=\"" . date("Y-m-d", $i) . "\";";
            echo "data1[c]=" . $anuladas[$i][0]->total . ";";
            echo "total1[c]={x: dates1[c],y: data1[c]};";
            echo "c++;";
        }       
    ?>
    Morris.Area({
  		element: "graph3",
  		data: total1,
  		xkey: "x",
  		ykeys: ["y"],
  		labels: ["Y"],
  		lineColors: ["red"],
  		resize: true
	}).on("click", function(i, row){
  		console.log(i, row);
	});	
	</script>
</section>