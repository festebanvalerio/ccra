<?php

$cadena = "";
if (count($_POST) > 0) {
    if ($_POST["accion"] == 1) {
        if (isset($_POST["insumo"]) && isset($_POST["almacen"]) && isset($_POST["stock"]) && isset($_POST["stockmin"]) && isset($_POST["stockmax"])) {
            $lstInsumoXAlmacen = InsumoAlmacenData::getAllByInsumo(1, $_POST["insumo"], $_POST["almacen"]);
            if (count($lstInsumoXAlmacen) > 0) {
                echo -1;
            } else {
                $objInsumoAlmacen = new InsumoAlmacenData();
                $objInsumoAlmacen->insumo = $_POST["insumo"];
                $objInsumoAlmacen->almacen = $_POST["almacen"];
                $objInsumoAlmacen->stock = $_POST["stock"];
                $objInsumoAlmacen->stock_minimo = $_POST["stockmin"];
                $objInsumoAlmacen->stock_maximo = $_POST["stockmax"];
                $objInsumoAlmacen->estado = 1;
                $resultado = $objInsumoAlmacen->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    $objAlmacen = AlmacenData::getById($_POST["almacen"]);
                    
                    $objMovimiento = new MovimientoData();
                    $objMovimiento->sede = $objAlmacen->sede;
                    $objMovimiento->insumo = $_POST["insumo"];
                    $objMovimiento->tipo = 1;
                    $objMovimiento->cantidad = $_POST["stock"];
                    $objMovimiento->detalle = "REGISTRO STOCK INICIAL";
                    $objMovimiento->modulo = "0";
                    $objMovimiento->fecha = date("Y-m-d H:i:s");
                    $objMovimiento->estado = 1;
                    $objMovimiento->add();
                    
                    $lstInsumoXAlmacen = InsumoAlmacenData::getAllByInsumo(1, $_POST["insumo"]);
                    
                    $item = 1;
                    $totalStock = 0;
                    
                    $cadena = "
                        <table class='table table-hover'>
                            <thead>
                                <tr class='btn-primary'>
                                    <th scope='col'>Item</th>
                                    <th scope='col'>Almacen</th>
                                    <th scope='col' style='text-align: right;'>Stock</th>
                                    <th scope='col' style='text-align: right;'>Stock Mínimo</th>
                                    <th scope='col' style='text-align: right;'>Stock Máximo</th>
                                    <th scope='col' style='text-align: center;'>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>";
                    
                    foreach ($lstInsumoXAlmacen as $objInsumoXAlmacen) {
                        $objAlmacen = $objInsumoXAlmacen->getAlmacen();                            
                            
                        $cadena .= "
                                <tr>
                                    <td style='text-align: left;'>".$item."</td>
                                    <td style='text-align: left;'>".$objAlmacen->nombre."</td>
                                    <td style='text-align: right;'>".number_format($objInsumoXAlmacen->stock, 2)."</td>
                                    <td style='text-align: right;'>".number_format($objInsumoXAlmacen->stock_minimo, 2)."</td>
                                    <td style='text-align: right;'>".number_format($objInsumoXAlmacen->stock_maximo, 2)."</td>
                                    <td style='text-align: center;'>
                                        <a id='eliminar".$item.$objInsumoXAlmacen->id."' title='Eliminar' class='btn btn-danger btn-xs'><em class='fa fa-trash'></em></button>
                                        <script type='text/javascript'>
                                        	$('#eliminar".$item.$objInsumoXAlmacen->id."').click(function() {
                                                $('#eliminar".$item.$objInsumoXAlmacen->id."').attr('disabled','disabled');
                                                $.blockUI();
                                            	var almacen = ".$objAlmacen->id.";
                                            	$.post('./?action=listwarehouse', {
                                                    id: ".$objInsumoXAlmacen->id.",
                                                    item: ".$item.",
                                                    almacen: almacen,
                                                    accion: 2
                                                }, function (data) {
                                                    $('#tabla').html('');
                                                    $('#tabla').append(data);
                                                    $.unblockUI();
                                                });
                                        	});
                                        </script>
                                    </td>
                                </tr>";
                            $totalStock += $objInsumoXAlmacen->stock;
                            $item++;                            
                    }
                    $cadena .= "
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan='2' style='text-align: left;'><strong>TOTAL</strong></td>
                                    <td style='text-align: right;'><strong>".number_format($totalStock, 2)."</strong></td>
                                    <td colspan='3'></td>
                                </tr>
                            </tfoot>
                        </table>";
                    
                    echo $cadena;
                } else {
                    echo 0;
                }                    
            }
        } else {
            echo 0;
        }
    } else if ($_POST["accion"] == 2) {
        $objInsumoAlmacen = InsumoAlmacenData::getById($_POST["id"]);
        $objInsumoAlmacen->estado = 0;
        $resultado = $objInsumoAlmacen->delete();
        if (isset($resultado[0]) && $resultado[0] == 1) {
            $objAlmacen = AlmacenData::getById($objInsumoAlmacen->almacen);
            
            $objMovimiento = new MovimientoData();
            $objMovimiento->sede = $objAlmacen->sede;
            $objMovimiento->insumo = $objInsumoAlmacen->insumo;
            $objMovimiento->tipo = 0;
            $objMovimiento->cantidad = $objInsumoAlmacen->stock;
            $objMovimiento->detalle = "STOCK INICIAL ANULADO";
            $objMovimiento->modulo = "0";
            $objMovimiento->fecha = date("Y-m-d H:i:s");
            $objMovimiento->estado = 1;
            $objMovimiento->add();
        }
        $lstInsumoXAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objInsumoAlmacen->insumo);
        
        $item = 1;
        $totalStock = 0;
        
        $cadena = "
                    <table class='table table-hover'>
                        <thead>
                            <tr class='btn-primary'>
                                <th scope='col'>Item</th>
                                <th scope='col'>Almacen</th>
                                <th scope='col' style='text-align: right;'>Stock</th>
                                <th scope='col' style='text-align: right;'>Stock Mínimo</th>
                                <th scope='col' style='text-align: right;'>Stock Máximo</th>
                                <th scope='col' style='text-align: center;'>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>";
        
        if (count($lstInsumoXAlmacen) > 0) {
            foreach ($lstInsumoXAlmacen as $objInsumoXAlmacen) {
                $objAlmacen = $objInsumoXAlmacen->getAlmacen();
                
                $cadena .= "
                                <tr>
                                    <td style='text-align: left;'>".$item."</td>
                                    <td style='text-align: left;'>".$objAlmacen->nombre."</td>
                                    <td style='text-align: right;'>".number_format($objInsumoXAlmacen->stock, 2)."</td>
                                    <td style='text-align: right;'>".number_format($objInsumoXAlmacen->stock_minimo, 2)."</td>
                                    <td style='text-align: right;'>".number_format($objInsumoXAlmacen->stock_maximo, 2)."</td>
                                    <td style='text-align: center;'>
                                        <a id='eliminar".$item.$objInsumoXAlmacen->id."' title='Eliminar' class='btn btn-danger btn-xs'><em class='fa fa-trash'></em></button>
                                        <script type='text/javascript'>
                                        	$('#eliminar".$item.$objInsumoXAlmacen->id."').click(function() {
                                                $('#eliminar".$item.$objInsumoXAlmacen->id."').attr('disabled','disabled');
                                                $.blockUI();
                                            	var almacen = ".$objAlmacen->id.";
                                            	$.post('./?action=listwarehouse', {
                                                    id: ".$objInsumoXAlmacen->id.",
                                                    item: ".$item.",
                                                    almacen: almacen,
                                                    accion: 2
                                                }, function (data) {
                                                    $('#tabla').html('');
                                                    $('#tabla').append(data);
                                                    $.unblockUI();
                                                });
                                        	});
                                        </script>
                                    </td>
                                </tr>";
                $totalStock += $objInsumoXAlmacen->stock;
                $item++;
            }
            $cadena .= "
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan='2' style='text-align: left;'><strong>TOTAL</strong></td>
                                    <td style='text-align: right;'><strong>".number_format($totalStock, 2)."</strong></td>
                                    <td colspan='3'></td>
                                </tr>
                            </tfoot>
                        </table>";
        } else {
            $cadena .= "

                            </tbody>
                        </table>";
        }
        
        echo $cadena;
    } else if ($_POST["accion"] == 3) {
        $objInsumoAlmacen = InsumoAlmacenData::getById($_POST["id"]);
        $objInsumoAlmacen->stock = str_replace(",", "", $_POST["stock"]);
        $objInsumoAlmacen->stock_minimo = str_replace(",", "", $_POST["stockmin"]);
        $objInsumoAlmacen->stock_maximo = str_replace(",", "", $_POST["stockmax"]);
        $resultado = $objInsumoAlmacen->update();
        if (isset($resultado[0]) && $resultado[0] == 1) {
            echo $resultado[0];
        } else {
            echo 0;
        }
    }
}