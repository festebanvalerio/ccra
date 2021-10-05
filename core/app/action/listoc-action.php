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
                    $igualdad = 0;
                    $unidadAlmacen = $_POST["unidad"];
                    $unidadCompra = $_POST["unidadcompra"];
                    if ($unidadAlmacen == $unidadCompra) {
                        $igualdad = 1;
                    }
                    if ($igualdad == 0) {
                        $lstEquivalencia = EquivalenciaData::getAllByInsumo(1, $_POST["insumo"], $unidadAlmacen, $unidadCompra);
                        if (count($lstEquivalencia) == 0) {
                            echo -1;
                            return;
                        }
                    } else {
                        if (!isset($_SESSION["insumos"])) {
                            $_SESSION["insumos"] = array();
                            $_SESSION["insumos"][] = $_POST["insumo"]."|".$_POST["cantidad"]."|".$_POST["unidad"]."|".$_POST["precio"]."|".$_POST["unidadcompra"]."|".$igualdad;
                        } else {
                            if (count($_SESSION["insumos"]) > 0) {
                                $_SESSION["tmp_insumos_ingreso"] = array();
                                foreach ($_SESSION["insumos"] as $valor) {
                                    $data = explode("|", $valor);
                                    if ($data[0] == $_POST["insumo"]) {
                                        $data[1] = $_POST["cantidad"] + $data[1];
                                        $_SESSION["tmp_insumos_ingreso"][] = $data[0]."|".$data[1]."|".$data[2]."|".$_POST["precio"]."|".$data[4]."|".$data[5];
                                    } else {
                                        $_SESSION["tmp_insumos_ingreso"][] = $data[0]."|".$data[1]."|".$data[2]."|".$data[3]."|".$data[4]."|".$data[5];
                                    }
                                }
                                $_SESSION["tmp_insumos_ingreso"][] = $_POST["insumo"]."|".$_POST["cantidad"]."|".$_POST["unidad"]."|".$_POST["precio"]."|".$_POST["unidadcompra"]."|".$igualdad;
                            }
                            $_SESSION["insumos"] = array();
                            $_SESSION["insumos"] = $_SESSION["tmp_insumos_ingreso"];
                        }                        
                    }
                }
                if ($_POST["indicador"] == 2) {
                    $item = 1;
                    $_SESSION["tmp_insumos"] = array();
                    
                    foreach ($_SESSION["insumos"] as $valor) {
                        $data = explode("|", $valor);
                        if ($item != $_POST["item"]) {
                            $_SESSION["tmp_insumos"][] = $data[0]."|".$data[1]."|".$data[2]."|".$data[3]."|".$data[4]."|".$data[5];
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
                        <th scope='col'>Unidad Almacen</th>
                        <th scope='col'>Unidad Compra</th>
                        <th scope='col' style='text-align: right; width: 10%;'>Cantidad</th>                        
                        <th scope='col' style='text-align: right;'>Costo</th>
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
                        
                        $objUnidadCompra = UnidadData::getById($data[4]);
                        
                        $cadena .= "
                    <tr>
                        <td style='text-align: left;'>".$item."</td>
                        <td style='text-align: left;'>".$nombre."</td>
                        <td style='text-align: left;'>".$objUnidad->nombre." (".$objUnidad->abreviatura.")</td>
                        <td style='text-align: left;'>".$objUnidadCompra->nombre." (".$objUnidadCompra->abreviatura.")</td>
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
                        <td style='text-align: right;'>".number_format($data[3], 2)."</td>
                        <td style='text-align: left;'>
                            <a id='eliminar".$item.$objInsumo->id."' title='Eliminar' class='btn btn-danger btn-xs'><em class='fa fa-trash'></em></button>
                            <script type='text/javascript'>
                            	$('#eliminar".$item.$objInsumo->id."').click(function() {
                                    $('#eliminar".$item.$objInsumo->id."').attr('disabled','disabled');
                                    $.blockUI();
                                	var insumo = ".$objInsumo->id.";
                                	$.post('./?action=listoc', {
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
                        <th scope='col'>Unidad Almacen</th>
                        <th scope='col'>Unidad Compra</th>
                        <th scope='col' style='text-align: right;'>Cantidad</th>                        
                        <th scope='col' style='text-align: right;'>Precio</th>
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