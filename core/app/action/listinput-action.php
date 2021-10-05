<?php

$cadena = "";
if (count($_POST) > 0) {
    if (isset($_POST["opcion"]) && isset($_POST["insumo"])) {
        // Obtener la informacion del insumo
        if ($_POST["opcion"] == 0) {
            $idInsumo = $_POST["insumo"];
            $objInsumo = InsumoData::getById($idInsumo);
            if ($objInsumo->id == 0) {
                $cadena .= "0||0.00";
            } else {
                $cadena .= $objInsumo->id."|".$objInsumo->getUnidad()->abreviatura."|".$objInsumo->costo;
            }
        } else {
            if (isset($_POST["indicador"])) {
                if ($_POST["indicador"] == 1) {
                    if (!isset($_SESSION["insumos"])) {
                        $_SESSION["insumos"] = array();
                    }
                    $_SESSION["insumos"][] = $_POST["insumo"]."|".$_POST["cantidad"]."|".$_POST["precio"]."|0";
                }
                if ($_POST["indicador"] == 2) {
                    $item = 1;
                    $_SESSION["tmp_insumos"] = array();
                    
                    foreach ($_SESSION["insumos"] as $valor) {
                        $data = explode("|", $valor);
                        if ($item != $_POST["item"]) {
                            $_SESSION["tmp_insumos"][] = $data[0]."|".$data[1]."|".$data[2]."|".$data[3];
                        } else {
                            if (isset($_POST["id"]) && $_POST["id"] > 0) {
                                $objDetalleReceta = DetalleRecetaData::getById($_POST["id"]);
                                $objDetalleReceta->estado = 0;
                                $objDetalleReceta->delete();
                            }
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
                        <th scope='col' style='text-align: right;'>Cantidad</th>
                        <th scope='col' style='text-align: right;'>Costo</th>                                                    
                        <th scope='col' style='text-align: right;'>Total</th>
                        <th scope='col' style='text-align: center;'>Acciones</th>
                    </tr>
                </thead>
                <tbody>";
                
                $item = 1;
                $totalGeneral = $totalCantidad = 0;
                foreach ($_SESSION["insumos"] as $valor) {
                    $data = explode("|", $valor);
                    $objInsumo = InsumoData::getById($data[0]);
                    if ($objInsumo) {
                        $nombre = $objInsumo->nombre;
                        $total = $data[1] * $data[2];
                        
                        $objUnidad = $objInsumo->getUnidad();
                        
                        $cadena .= "
                    <tr>
                        <td style='text-align: left;'>".$item."</td>
                        <td style='text-align: left;'>".$nombre."</td>
                        <td style='text-align: left;'>".$objUnidad->abreviatura."</td>
                        <td style='text-align: right;'>".number_format($data[1], 2)."</td>
                        <td style='text-align: right;'>".number_format($data[2], 2)."</td>
                        <td style='text-align: right;'>".number_format($total, 2)."</td>
                        <td style='text-align: center;'>
                            <a id='eliminar".$item.$objInsumo->id."' title='Eliminar' class='btn btn-danger btn-xs'><em class='fa fa-trash'></em></button>                            
                            <script type='text/javascript'>
                            	$('#eliminar".$item.$objInsumo->id."').click(function() {
                                    $('#eliminar".$item.$objInsumo->id."').attr('disabled','disabled');
                                	var insumo = ".$objInsumo->id.";
                                	$.post('./?action=listinput', {
                                        opcion: 1,
                                        item: ".$item.",
                                        insumo: insumo,
                                        indicador: 2,
                                        id: ".$data[3]."
                                    }, function (data) {
                                        $('#tabla').html('');
                                        $('#tabla').append(data);
                                    });
                            	});
                            </script>
                        </td>
                    </tr>";
                        $totalCantidad += $data[1];
                        $totalGeneral += $total;
                        $item++;
                    }
                }
                $cadena .= "
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan='3' style='text-align: left;'><strong>TOTAL</strong></td>
                        <td style='text-align: right;'><strong>".number_format($totalCantidad,2)."</strong></td>
                        <td></td>
                        <td id='totalReceta' style='text-align: right;'><strong>".number_format($totalGeneral,2)."</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
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