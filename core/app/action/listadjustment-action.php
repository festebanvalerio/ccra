<?php

$cadena = "";
if (count($_POST) > 0) {
    if (isset($_POST["opcion"]) && isset($_POST["insumo"])) {
        // Obtener la informacion del insumo
        if ($_POST["opcion"] == 0) {
            $idInsumo = $_POST["insumo"];
            $objInsumo = InsumoData::getById($idInsumo);
            if ($objInsumo) {
                $stockActual = 0;
                $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objInsumo->id, $_POST["almacen"]);
                if (count($lstInsumoAlmacen) > 0) {
                    $stockActual = $lstInsumoAlmacen[0]->stock;
                    $cadena .= $objInsumo->id."|".$objInsumo->getUnidad()->abreviatura."|".$objInsumo->costo."|".$stockActual;
                }
            }
        } else {
            if (isset($_POST["indicador"])) {
                if ($_POST["indicador"] == 1) {
                    $stockActual = 0;
                    $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $_POST["insumo"], $_POST["almacen"]);
                    if (count($lstInsumoAlmacen) > 0) {
                        $stockActual = $lstInsumoAlmacen[0]->stock;
                    }
                    $tipo = "";
                    if ($stockActual < $_POST["cantidad"]) {
                        $tipo = 1;
                    } else {
                        $tipo = 0;
                    }
                    
                    if (!isset($_SESSION["insumos"])) {
                        $_SESSION["insumos"] = array();
                        $_SESSION["insumos"][] = $_POST["insumo"]."|".$_POST["cantidad"]."|".$tipo."|".$stockActual;
                    } else {
                        $_SESSION["insumos"][] = $_POST["insumo"]."|".$_POST["cantidad"]."|".$tipo."|".$stockActual;
                    }
                }
                if ($_POST["indicador"] == 2) {
                    $item = 1;
                    $_SESSION["tmp_insumos"] = array();
                    
                    foreach ($_SESSION["insumos"] as $valor) {
                        $data = explode("|", $valor);
                        if ($item != $_POST["item"]) {
                            $_SESSION["tmp_insumos"][] = $data[0]."|".$data[1]."|".$data[2]."|".$data[3];
                        }
                        $item++;
                    }
                    if (count($_SESSION["tmp_insumos"]) > 0) {
                        $_SESSION["insumos"] = array();
                        $_SESSION["insumos"] = $_SESSION["tmp_insumos"];
                    } else {
                        $_SESSION["insumos"] = array();
                    }
                }
            }
            
            if (count($_SESSION["insumos"]) > 0) {
                $cadena = "
            <table class='table table-hover'>
                <thead>
                    <tr class='btn-primary'>
                        <th scope='col'>Item</th>
                        <th scope='col'>Insumo</th>
                        <th scope='col'>Unidad</th>
                        <th scope='col'>Tipo</th>
                        <th scope='col' style='text-align: right;'>Stock</th>
                        <th scope='col' style='text-align: right;'>Ajustar A</th>
                        <th scope='col' style='text-align: right;'>Diferencia</th>
                        <th scope='col' style='text-align: center;'>Acciones</th>
                    </tr>
                </thead>
                <tbody>";
                
                $item = 1;
                foreach ($_SESSION["insumos"] as $valor) {
                    $data = explode("|", $valor);
                    $objInsumo = InsumoData::getById($data[0]);
                    if ($objInsumo) {
                        $nombre = $objInsumo->nombre;
                        
                        $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objInsumo->id, $_POST["almacen"]);
                        if (count($lstInsumoAlmacen) > 0) {
                            $stockActual = $lstInsumoAlmacen[0]->stock;
                        } else {
                            $stockActual = 0;
                        }
                        
                        $diferencia = 0;
                        $tipo = $data[2];
                        if ($tipo == 0) {
                            $tipo = "SALIDA";
                        } else if ($tipo == 1) {
                            $tipo = "ENTRADA";
                            
                        }
                        $diferencia = $data[1] - $stockActual;
                        $objUnidad = $objInsumo->getUnidad();
                        
                        $cadena .= "
                    <tr>
                        <td style='text-align: left;'>".$item."</td>
                        <td style='text-align: left;'>".$nombre."</td>
                        <td style='text-align: left;'>".$objUnidad->abreviatura."</td>
                        <td style='text-align: left;'>".$tipo."</td>        
                        <td style='text-align: right;'>".number_format($stockActual, 2)."</td>
                        <td style='text-align: right;'>".number_format($data[1], 2)."</td>
                        <td style='text-align: right;'>".number_format($diferencia, 2)."</td>
                        <td style='text-align: center;'>
                            <a id='eliminar".$item.$objInsumo->id."' title='Eliminar' class='btn btn-danger btn-xs'>X</button>                            
                            <script type='text/javascript'>
                            	$('#eliminar".$item.$objInsumo->id."').click(function() {
                                    $('#eliminar".$item.$objInsumo->id."').attr('disabled','disabled');
                                	var insumo = ".$objInsumo->id.";
                                	$.post('./?action=listadjustment', {
                                        opcion: 1,
                                        item: ".$item.",
                                        almacen: ".$_POST["almacen"].",
                                        insumo: insumo,
                                        indicador: 2
                                    }, function (data) {
                                        $('#tabla').html('');
                                        $('#tabla').append(data);
                                    });
                            	});
                            </script>
                        </td>
                    </tr>";
                        $item++;
                    }
                }
                $cadena .= "
                </tbody>
            </table>";
            } else {
                $cadena = "";
                unset($_SESSION["insumos"]);
                unset($_SESSION["tmp_insumos"]);
            }
        }
    }
    
}
echo $cadena;