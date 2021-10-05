<?php

$cadena = "";
if (count($_POST) > 0) {
    if ($_POST["accion"] == 1) {
        if (isset($_POST["insumo"]) && isset($_POST["unidadbase"]) && isset($_POST["unidadalt"]) && isset($_POST["factor"])) {
            $lstEquivalencia = EquivalenciaData::getAllByInsumo(1, $_POST["insumo"]);
            if (count($lstEquivalencia) > 0) {
                echo -1;
            } else {
                $objEquivalencia = new EquivalenciaData();
                $objEquivalencia->insumo = $_POST["insumo"];
                $objEquivalencia->unidad_base = $_POST["unidadbase"];
                $objEquivalencia->factor = $_POST["factor"];
                $objEquivalencia->unidad_alternativa = $_POST["unidadalt"];
                $objEquivalencia->estado = 1;
                $resultado = $objEquivalencia->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    $lstEquivalencia = EquivalenciaData::getAllByInsumo(1, $_POST["insumo"]);
                    
                    $item = 1;
                    
                    $cadena = "
                        <table class='table table-hover'>
                            <thead>
                                <tr class='btn-primary'>
                                    <th scope='col'>Item</th>
                                    <th scope='col'></th>
                                    <th scope='col'>Unidad Compra</th>
                                    <th scope='col'></th>
                                    <th scope='col' style='text-align: right;'>Factor</th>
                                    <th scope='col'>Unidad Almacen</th>
                                    <th scope='col' style='text-align: center;'>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>";
                    
                    foreach ($lstEquivalencia as $objEquivalencia) {
                        $objUnidadAlmacen = $objEquivalencia->getUnidadBase();
                        $objUnidadCompra = $objEquivalencia->getUnidadAlternativa();
                            
                        $cadena .= "
                                <tr>
                                    <td style='text-align: left;'>".$item."</td>
                                    <td style='text-align: center;'>".number_format(1, 2)."</td>
                                    <td style='text-align: left;'>".$objUnidadCompra->nombre."</td>
                                    <td style='text-align: center;'>=</td>
                                    <td style='text-align: right;'>".number_format($objEquivalencia->factor, 2)."</td>
                                    <td style='text-align: left;'>".$objUnidadAlmacen->nombre."</td>
                                    <td style='text-align: center;'>
                                        <a id='eliminar".$item.$objEquivalencia->id."' title='Eliminar' class='btn btn-danger btn-xs'><em class='fa fa-trash'></em></button>
                                        <script type='text/javascript'>
                                        	$('#eliminar".$item.$objEquivalencia->id."').click(function() {
                                                $('#eliminar".$item.$objEquivalencia->id."').attr('disabled','disabled');
                                                $.blockUI();
                                            	$.post('./?action=listwarehouse', {
                                                    id: ".$objEquivalencia->id.",
                                                    item: ".$item.",
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
                            $item++;                            
                    }
                    $cadena .= "
                            </tbody>
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
        $objEquivalencia = EquivalenciaData::getById($_POST["id"]);
        $objEquivalencia->estado = 0;
        $resultado = $objEquivalencia->delete();
        if (isset($resultado[0]) && $resultado[0] == 1) {
            $lstEquivalencia = EquivalenciaData::getAllByInsumo(1, $objEquivalencia->insumo);
                
            $item = 1;
            
            $cadena = "
                        <table class='table table-hover'>
                            <thead>
                                <th scope='col'>Item</th>
                                <th scope='col'></th>
                                <th scope='col'>Unidad Compra</th>
                                <th scope='col'></th>
                                <th scope='col' style='text-align: right;'>Factor</th>
                                <th scope='col'>Unidad Almacen</th>
                                <th scope='col' style='text-align: center;'>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>";
            
            if (count($lstEquivalencia) > 0) {
                foreach ($lstEquivalencia as $objEquivalencia) {
                    $objUnidadAlmacen = $objEquivalencia->getUnidadBase();
                    $objUnidadCompra = $objEquivalencia->getUnidadAlternativa();
                    
                    $cadena .= "
                                    <tr>
                                        <td style='text-align: left;'>".$item."</td>
                                        <td style='text-align: center;'>".number_format(1, 2)."</td>
                                        <td style='text-align: left;'>".$objUnidadCompra->nombre."</td>
                                        <td style='text-align: center;'>=</td>
                                        <td style='text-align: right;'>".number_format($objEquivalencia->factor, 2)."</td>
                                        <td style='text-align: left;'>".$objUnidadAlmacen->nombre."</td>
                                        <td style='text-align: center;'>
                                            <a id='eliminar".$item.$objEquivalencia->id."' title='Eliminar' class='btn btn-danger btn-xs'><em class='fa fa-trash'></em></button>
                                            <script type='text/javascript'>
                                            	$('#eliminar".$item.$objEquivalencia->id."').click(function() {
                                                    $('#eliminar".$item.$objEquivalencia->id."').attr('disabled','disabled');
                                                    $.blockUI();
                                                	$.post('./?action=listequivalence', {
                                                        id: ".$objEquivalencia->id.",
                                                        item: ".$item.",
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
                    $item++;
                }
                $cadena .= "
                                </tbody>
                            </table>";
            } else {
                $cadena .= "
    
                                </tbody>
                            </table>";
            }
            echo $cadena;            
        }
    } else if ($_POST["accion"] == 3) {
        $objEquivalencia = EquivalenciaData::getById($_POST["id"]);
        $objEquivalencia->factor = str_replace(",", "", $_POST["factor"]);
        $resultado = $objEquivalencia->update();
        if (isset($resultado[0]) && $resultado[0] == 1) {
            echo $resultado[0];
        } else {
            echo 0;
        }
    }
}