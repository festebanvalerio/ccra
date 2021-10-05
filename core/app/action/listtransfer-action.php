<?php

$cadena = "";
if (count($_POST) > 0) {
    if (isset($_POST["opcion"]) && isset($_POST["insumo"])) {
        // Obtener la informacion del insumo
        if ($_POST["opcion"] == 0) {
            $idInsumo = $_POST["insumo"];
            $objInsumo = InsumoData::getById($idInsumo);
            $stockActual = 0;
            $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objInsumo->id, $_POST["almacen"]);
            if (count($lstInsumoAlmacen) > 0) {
                $stockActual = $lstInsumoAlmacen[0]->stock;
                $cadena .= $objInsumo->id."|".$objInsumo->getUnidad()->abreviatura."|".$objInsumo->costo."|".number_format($stockActual, 2);
            }            
        } else {
            if (isset($_POST["indicador"])) {
                if ($_POST["indicador"] == 1) {
                    $stockActual = 0;
                    $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $_POST["insumo"], $_POST["almacen"]);
                    if (count($lstInsumoAlmacen) > 0) {
                        $stockActual = $lstInsumoAlmacen[0]->stock;
                    }
                    $tipo = 0;
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
                        <th scope='col' style='text-align: right;'>Cant. Transferida</th>
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
                        $objUnidad = $objInsumo->getUnidad();
                        
                        $cadena .= "
                    <tr>
                        <td style='text-align: left;'>".$item."</td>
                        <td style='text-align: left;'>".$nombre."</td>
                        <td style='text-align: left;'>".$objUnidad->abreviatura."</td>
                        <td style='text-align: right;'>".number_format($data[1], 2)."</td>
                        <td style='text-align: center;'>
                            <a id='eliminar".$item.$objInsumo->id."' title='Eliminar' class='btn btn-danger btn-xs'><em class='fa fa-trash'></em></button>                            
                            <script type='text/javascript'>
                            	$('#eliminar".$item.$objInsumo->id."').click(function() {
                                    $('#eliminar".$item.$objInsumo->id."').attr('disabled','disabled');
                                	var insumo = ".$objInsumo->id.";
                                	$.post('./?action=listtransfer', {
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
                $cadena = "
            <table class='table table-hover'>
                <thead>
                    <tr class='btn-primary'>
                        <th scope='col'>Item</th>
                        <th scope='col'>Insumo</th>
                        <th scope='col'>Unidad</th>
                        <th scope='col' style='text-align: right;'>Cant. Transferida</th>
                        <th scope='col' style='text-align: center;'>Acciones</th>
                    </tr>
                </thead>
            </table>";
                
                unset($_SESSION["insumos"]);
                unset($_SESSION["tmp_insumos"]);
            }
        }
    }
    
}
echo $cadena;