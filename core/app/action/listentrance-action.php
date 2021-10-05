<?php

$cadena = "";
if (count($_POST) > 0) {
    if (isset($_POST["opcion"]) && isset($_POST["insumo"])) {
        // Obtener la informacion del insumo
        if ($_POST["opcion"] == 0) {
            $idInsumo = $_POST["insumo"];
            $objInsumo = InsumoData::getById($idInsumo);
            $cadena .= $objInsumo->id."|".$objInsumo->getUnidad()->abreviatura."|".$objInsumo->costo."|".$objInsumo->getUnidad()->id;
        } else {
            if (isset($_POST["indicador"])) {
                if ($_POST["indicador"] == 1) {
                    if (!isset($_SESSION["insumos"])) {
                        $_SESSION["insumos"] = array();
                        $_SESSION["insumos"][] = $_POST["insumo"]."|".$_POST["cantidad"]."|".$_POST["unidad"]."|".$_POST["almacen"];
                    } else {
                        if (count($_SESSION["insumos"]) > 0) {
                            $_SESSION["tmp_insumos_ingreso"] = array();
                            foreach ($_SESSION["insumos"] as $valor) {
                                $data = explode("|", $valor);
                                if ($data[0] == $_POST["insumo"]) {                                
                                    $data[1] = $_POST["cantidad"] + $data[1];                        
                                    $_SESSION["tmp_insumos_ingreso"][] = $data[0]."|".$data[1]."|".$data[2]."|".$data[3];
                                } else {
                                    $_SESSION["tmp_insumos_ingreso"][] = $data[0]."|".$data[1]."|".$data[2]."|".$data[3];
                                }
                            }
                            $_SESSION["tmp_insumos_ingreso"][] = $_POST["insumo"]."|".$_POST["cantidad"]."|".$_POST["unidad"]."|".$_POST["almacen"];
                        }
                        $_SESSION["insumos"] = array();
                        $_SESSION["insumos"] = $_SESSION["tmp_insumos_ingreso"];
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
                        <th scope='col' style='text-align: right;'>Stock</th>
                        <th scope='col' style='text-align: right;'>Costo</th>
                        <th scope='col' style='text-align: right; width: 10%;'>Cantidad</th>                        
                        <th scope='col'>Acciones</th>
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
                        
                        $stock = 0;
                        $lstInsumoxAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objInsumo->id, $data[3]);
                        if (count($lstInsumoxAlmacen) > 0) {
                            $stock = $lstInsumoxAlmacen[0]->stock;
                        }
                        
                        $cadena .= "
                    <tr>
                        <td style='text-align: left;'>".$item."</td>
                        <td style='text-align: left;'>".$nombre."</td>
                        <td style='text-align: left;'>".$objUnidad->nombre." (".$objUnidad->abreviatura.")</td>
                        <td style='text-align: right;'>".number_format($stock,2)."</td>
                        <td style='text-align: right;'>".number_format($objInsumo->costo,2)."</td>
                        <td style='text-align: right;'>
                            <input type='text' id='cant".$item.$objInsumo->id."' name='cant".$item.$objInsumo->id."' value='".number_format($data[1], 2)."' class='form-control' maxlength='5' dir='rtl'/>
                            <script type='text/javascript'>
                                $('#cant".$item.$objInsumo->id."').click(function() {
                                    $('#cant".$item.$objInsumo->id."').val('');
                                });
                                $('#cant".$item.$objInsumo->id."').blur(function() {
                                    var cantidad = $('#cant".$item.$objInsumo->id."').val();
                                    if (cantidad === '') {
                                        $('#cant".$item.$objInsumo->id."').val('".number_format($data[1], 2)."');
                                    } else if (isNaN(cantidad)) {
                                        $('#cant".$item.$objInsumo->id."').val('');
                                        document.getElementById('cant".$item.$objInsumo->id."').focus();
                						Swal.fire({
                		    				icon: 'warning',
                		    				title: 'Sólo valores numéricos (cantidad)'
                		    			})
                                    }
                                });
                            </script>
                        </td>
                        <td style='text-align: left;'>
                            <a id='eliminar".$item.$objInsumo->id."' title='Eliminar' class='btn btn-danger btn-xs'><em class='fa fa-trash'></em></button>
                            <script type='text/javascript'>
                            	$('#eliminar".$item.$objInsumo->id."').click(function() {
                                    $('#eliminar".$item.$objInsumo->id."').attr('disabled','disabled');
                                    $.blockUI();
                                	var insumo = ".$objInsumo->id.";
                                	$.post('./?action=listentrance', {
                                        opcion: 1,
                                        item: ".$item.",
                                        almacen: ".$_POST["almacen"].",
                                        insumo: insumo,
                                        indicador: 2
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
                        <th scope='col' style='text-align: right;'>Cantidad</th>                        
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