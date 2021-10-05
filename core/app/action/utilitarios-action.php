<?php
$cadena = "";
if (count($_POST) > 0) {
    // Obtener productos asociados a una categoria
    if (isset($_POST["categoria"]) && isset($_POST["sede"]) && isset($_POST["vista"])) {
        $nomProducto = "";
        if (isset($_POST["campo"])) {
            $nomProducto = $_POST["campo"];
        }
        $indicadorVista = $_POST["vista"];
        $idPedido = 0;
        if (isset($_POST["idPedido"])) {
            $idPedido = $_POST["idPedido"];
        }
        if ($indicadorVista == 0) {
            $lstProducto = ProductoData::getAll(1, $_POST["sede"], $_POST["categoria"], "", $nomProducto);
            if (count($lstProducto) > 0) {
                $cadena .= '<option value="">SELECCIONE</option>';
                foreach ($lstProducto as $objProducto) {
                    $cadena .= '<option value="' . $objProducto->id . '">' . $objProducto->nombre . '</option>';
                }
            } else {
                $cadena .= '<option value="">SELECCIONE</option>';
            }
        } else if ($indicadorVista == 1) {
            if ($nomProducto == "") {
                $lstProducto = ProductoData::getAll(1, $_POST["sede"], $_POST["categoria"], "", $nomProducto);
            } else {
                $lstProducto = ProductoData::getAll(1, $_POST["sede"], "", "", $nomProducto);
            }
            if (count($lstProducto) > 0) {
                foreach ($lstProducto as $objProducto) {
                    $cadena .= '
                        <div class="col-md-2 col-sm-12">
        				    <div class="info-box">
                                <span id="producto' . $objProducto->id . '" class="info-box-icon bg-green" style="cursor: pointer;">
                                    <em class="fa fa-cloud"></em>
        					    </span>
                                <div class="info-box-content">					
        							<span class="info-box-number" style="font-size: 11px;">' . str_replace(" ", "<br/>", $objProducto->nombre) . '</span>
        						</div>
        						<script type="text/javascript">
        							$("#producto' . $objProducto->id . '").click(function(){
                                        var idPedido = ' . $idPedido . ';
                                        var categoria = $("#categoria").val();
        								var producto = ' . $objProducto->id . ';
        								var cantidad = $("#cantidad").val();
                                        var comentario = $("#comentario").val();
                                        var opcion = $("#opcion").val();

        								if (categoria === "") {
        									Swal.fire({
        										icon: "warning",
        										title: "Seleccione una categoría"
        									})
        									return false;
        								}
        								if (producto === "") {
        									Swal.fire({
        										icon: "warning",
        										title: "Seleccione un producto"
        									})
        									return false;
        								}
        								if (cantidad === "") {
        									Swal.fire({
        										icon: "warning",
        										title: "Ingrese cantidad"
        									})
        									return false;
        								} else if (isNaN(cantidad)) {
        									Swal.fire({
        										icon: "warning",
        										title: "Ingrese cantidad válida"
        									})
        									return false;
        								}
        								$("#btnAgregar").attr("disabled","disabled");
        
        								$.blockUI();
        								$.post("./?action=getdetailssale", {
        									categoria: categoria,
        									producto: producto,
        									cantidad: cantidad,
                                            comentario: comentario,
        									indicador: 1,
                                            idPedido: idPedido
        	                           	}, function (data) {
                                            if (idPedido == 0) {
            									$("#tabla").html(data);
            									$("#btnAgregar").removeAttr("disabled");
                                                $("#cantidad").val("1");
                                                $("#comentario").val("");
                                                setTimeout(function(){$("#comentario").trigger("focus")},1);
                                            } else {
                                                var usuario = $("#idusuario").val();
    						    				var piso = $("#idpisosede").val();
    						    				var mesa = $("#idmesapisosede").val();
    						    				
                                                if (opcion === "0") {
		    	                           			window.location.href = "./index.php?view=salestableitem&usuario="+usuario+"&piso="+piso+"&mesa="+mesa;
    						    				} else {
    						    					window.location.href = "./index.php?view=salestableitem&usuario="+usuario+"&piso="+piso+"&mesa="+mesa+"&opcion="+opcion;
    						    				}		    	                           		
                                            } 
        									$.unblockUI();
        	                            });
        							});
        						</script>
        					</div>
        				</div>';
                }
            } else {
                $cadena .= '
                        <div class="col-md-12 col-sm-12">
    					   <label for="mensajes">No hay productos asociados a esa categoría</label>
    					</div>';
            }
        }
    } else if (isset($_POST["tipodoc"]) && isset($_POST["numdoc"])) {
        $objCliente = ClienteData::getByNumDoc(1, $_POST["numdoc"]);
        if (!$objCliente) {
            $objParametro = ParametroData::getById($_POST["tipodoc"]);
            if ($objParametro->valor2 == 3) {
                // Boleta
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.apirest.pe/api/getDniPremium",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\r\n\t\"dni\": \"" . $_POST["numdoc"] . "\"\r\n}",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                        "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImE4Mzg1M2QzNDA1NjM0MjAwZmE0NGY5ZjM0ZjIxZWNjOTNkMDE1ZDVhNzQxYjExMDhmOWM0YjRhYzJiYWU0OGVlNTg2NDI2Mjc2YWQ1ZTA5In0.eyJhdWQiOiIxIiwianRpIjoiYTgzODUzZDM0MDU2MzQyMDBmYTQ0ZjlmMzRmMjFlY2M5M2QwMTVkNWE3NDFiMTEwOGY5YzRiNGFjMmJhZTQ4ZWU1ODY0MjYyNzZhZDVlMDkiLCJpYXQiOjE1OTIyNDY4MDYsIm5iZiI6MTU5MjI0NjgwNiwiZXhwIjoxOTA3Nzc5NjA2LCJzdWIiOiIxMTkxIiwic2NvcGVzIjpbIioiXX0.mfBjK1VTmYKwROEUZ1yCNTDA2-77Rr9_-InShyKbciPdH03XBqh4ZxnOw1KuhWHr3WRI4_SQZ4hBeaS9e5NjYKTrFE9s8O1wzOoRFo2woed1VuVDV9uHXhbxmQIPFbTIz6xJ0LI4XZbyhodZavarjSNnyFnmGDYtzkivzJmwGjQ_q4Ktks-mm5XjN0yLYR8RUyNWYoWQZThUwkJHJI0cdQ97sc4pVPNirLth4tuG0JzdHjjOkfNhfDujOjr1jKkupMr-peDzcCNjmRrOxZwYNZquw5I_hZr-7Q3uQkdGUsnHWeTas-c_ZG_R3lExxXguYPQmmVsrfe05jZEI0NHNX_SLESS18ghxVtJx-e3CEVAtPKEUXLM-T3_F5BQfBdwS3j5dKw9ORT__SHB1EiTHu3nhwrSjosqvLPYjo-MUo_AVtBow1TV3AHjx__f3gtHgiu8KlHWWM9eJg1fNRverbo88TfJjLOUEbfyGjQF2RHz_yyGvAM65H5D9nMl5yf6MVKHrFpXRmlSp9tWEBFJ3nIoJ91zG9bQ3qQqloo52PAZES2I5SqAl0wtJRy5s5CqR7ALdItbAhIn-x3fkTGXqqLO1GbcXNzflG2T5gqqa1VG4lfdUX5JfBH3Tkp4FB-x12tok83ovUu_qX6WHBCJYNkxG209s6oSpgqCGhOWKHz8",
                        "Cookie: apirest_session=eyJpdiI6IlcxS0k3dTN1OXBaNzNOVGlHakxTRWc9PSIsInZhbHVlIjoiUFN3MVV0VHQ4YW1ya09tV0ZFY2tzZ2IyTXFHaDlIVHJCU1o1NlpreTBMXC9VMjk2eDlYQjhBM1A2U3V1M2RhdUsiLCJtYWMiOiJlM2U5OGY3NWM4MTY2ZWU2ODEwYTAwZDA4MzRhODMxMmRlMTI0ZTliZWI4ZDU1Zjg4YTUyOWMwOWNhNjEwMmFmIn0%3D"
                    )
                ));
                $response = curl_exec($curl);
                curl_close($curl);

                $data = json_decode($response);
                if ($data->success) {
                    $resultado = $data->result;
                    $cadena .= str_replace("&Ntilde;", "Ñ", $resultado->Nombre) . " " . str_replace("&Ntilde;", "Ñ", $resultado->Paterno) . " " . str_replace("&Ntilde;", "Ñ", $resultado->Materno) . "@";
                } else {
                    $cadena .= "@";
                }
                $cadena .= "@0@0";
            } else if ($objParametro->valor2 == 1) {
                // Factura
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://servicio.apirest.pe/api/getRuc",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\r\n\t\"ruc\": \"" . $_POST["numdoc"] . "\"\r\n}",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                        "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImE4Mzg1M2QzNDA1NjM0MjAwZmE0NGY5ZjM0ZjIxZWNjOTNkMDE1ZDVhNzQxYjExMDhmOWM0YjRhYzJiYWU0OGVlNTg2NDI2Mjc2YWQ1ZTA5In0.eyJhdWQiOiIxIiwianRpIjoiYTgzODUzZDM0MDU2MzQyMDBmYTQ0ZjlmMzRmMjFlY2M5M2QwMTVkNWE3NDFiMTEwOGY5YzRiNGFjMmJhZTQ4ZWU1ODY0MjYyNzZhZDVlMDkiLCJpYXQiOjE1OTIyNDY4MDYsIm5iZiI6MTU5MjI0NjgwNiwiZXhwIjoxOTA3Nzc5NjA2LCJzdWIiOiIxMTkxIiwic2NvcGVzIjpbIioiXX0.mfBjK1VTmYKwROEUZ1yCNTDA2-77Rr9_-InShyKbciPdH03XBqh4ZxnOw1KuhWHr3WRI4_SQZ4hBeaS9e5NjYKTrFE9s8O1wzOoRFo2woed1VuVDV9uHXhbxmQIPFbTIz6xJ0LI4XZbyhodZavarjSNnyFnmGDYtzkivzJmwGjQ_q4Ktks-mm5XjN0yLYR8RUyNWYoWQZThUwkJHJI0cdQ97sc4pVPNirLth4tuG0JzdHjjOkfNhfDujOjr1jKkupMr-peDzcCNjmRrOxZwYNZquw5I_hZr-7Q3uQkdGUsnHWeTas-c_ZG_R3lExxXguYPQmmVsrfe05jZEI0NHNX_SLESS18ghxVtJx-e3CEVAtPKEUXLM-T3_F5BQfBdwS3j5dKw9ORT__SHB1EiTHu3nhwrSjosqvLPYjo-MUo_AVtBow1TV3AHjx__f3gtHgiu8KlHWWM9eJg1fNRverbo88TfJjLOUEbfyGjQF2RHz_yyGvAM65H5D9nMl5yf6MVKHrFpXRmlSp9tWEBFJ3nIoJ91zG9bQ3qQqloo52PAZES2I5SqAl0wtJRy5s5CqR7ALdItbAhIn-x3fkTGXqqLO1GbcXNzflG2T5gqqa1VG4lfdUX5JfBH3Tkp4FB-x12tok83ovUu_qX6WHBCJYNkxG209s6oSpgqCGhOWKHz8",
                        "Cookie: apirest_session=eyJpdiI6IlcxS0k3dTN1OXBaNzNOVGlHakxTRWc9PSIsInZhbHVlIjoiUFN3MVV0VHQ4YW1ya09tV0ZFY2tzZ2IyTXFHaDlIVHJCU1o1NlpreTBMXC9VMjk2eDlYQjhBM1A2U3V1M2RhdUsiLCJtYWMiOiJlM2U5OGY3NWM4MTY2ZWU2ODEwYTAwZDA4MzRhODMxMmRlMTI0ZTliZWI4ZDU1Zjg4YTUyOWMwOWNhNjEwMmFmIn0%3D"
                    )
                ));
                $response = curl_exec($curl);
                curl_close($curl);

                $data = json_decode($response);
                if ($data->success) {
                    $resultado = $data->result;
                    $cadena .= str_replace("&Ntilde;", "Ñ", $resultado->RazonSocial) . "@" . str_replace("&Ntilde;", "Ñ", $resultado->Direccion);
                }
                $cadena .= "@1@0";
            }
        } else {
            $objParametro = ParametroData::getById($_POST["tipodoc"]);            
            if ($objParametro->valor2 == 3) {
                // Boleta
                $cadena .= $objCliente->datos . "@" . $objCliente->direccion . "@0@" . $objCliente->id;
            } else if ($objParametro->valor2 == 1) {
                // RUC
                $cadena .= $objCliente->datos . "@" . $objCliente->direccion . "@1@" . $objCliente->id;
            }
        }
    } else if (isset($_POST["tipodoc"])) {
        $objParametro = ParametroData::getById($_POST["tipodoc"]);
        if ($objParametro) {
            // Boleta
            if ($objParametro->valor2 == 3) {
                $cadena .= "DNI :|8|" . $objParametro->valor2;
            } else if ($objParametro->valor2 == 1) {
                $cadena .= "RUC :*|11|" . $objParametro->valor2;
            }
        }
    } else if (isset($_POST["formapago"])) {
        $objParametro = ParametroData::getById($_POST["formapago"]);
        if ($objParametro) {
            $cadena .= $objParametro->valor1;
            /* Efectivo: 0
             * Tarjeta: 1
             * Mixta: 2
             * Credito: 3
             */
        }
    } else if (isset($_POST["sede"]) && isset($_POST["piso"]) && isset($_POST["mesa"]) && isset($_POST["usuario"])) {
        $idSede = $_POST["sede"];
        $idPiso = $_POST["piso"];
        $idMesa = $_POST["mesa"];
        $idUsuario = $_POST["usuario"];
        
        $lstPedido = PedidoData::getMesaOcupadaXMozo(1, $idSede, $idPiso, $idMesa, $idUsuario, 1);
        if (count($lstPedido) > 0) {
            $cadena .= $lstPedido[0]->getUsuario()->nombres." ".$lstPedido[0]->getUsuario()->apellidos;            
        } else {
            $cadena .= "0";
        }
    } else if (isset($_POST["almacen"])) {
        $objAlmacen = AlmacenData::getById($_POST["almacen"]);
        $cadena .= $objAlmacen->getSede()->id;
    } else if (isset($_POST["tiporeporte"])) {
        $objParametro = ParametroData::getById($_POST["tiporeporte"]);
        $cadena .= $objParametro->valor1;
    } else if (isset($_POST["idpisosede"]) && isset($_POST["sede"])) {
        $cadena .= '<option value="">SELECCIONE</option>';
        $lstMesaXPiso = MesaPisoSedeData::getMesaDisponibleXPiso($_POST["idpisosede"], $_POST["sede"]);        
        foreach ($lstMesaXPiso as $objMesaXPiso) {
            $cadena .= '<option value="'.$objMesaXPiso->getMesa()->id.'">'.$objMesaXPiso->getMesa()->nombre.'</option>';    
        }        
        $objPisoXSede = PisoSedeData::getById($_POST["idpisosede"]);
        $cadena = $cadena . "|" . $objPisoXSede->piso;
    } else if (isset($_POST["idpedido"]) && isset($_POST["idpiso"]) && isset($_POST["idmesa"])) {
        $idPedido = $_POST["idpedido"];
        $idPiso = $_POST["idpiso"];
        $idMesa = $_POST["idmesa"];
        
        $objPedido = PedidoData::getById($idPedido);
        $objPedido->piso = $idPiso;
        $objPedido->mesa = $idMesa;
        $objPedido->fecha_actualizacion = date("Y-m-d H:i:s");
        $objPedido->usuario_actualizacion = $_SESSION["user"];
        $resultado = $objPedido->update();
        if (isset($resultado[0]) && $resultado[0] == 1) {
            $cadena .= "1";
        }
    } else if (isset($_POST["pedido"]) && isset($_POST["impresion"])) {
        $idPedido = $_POST["pedido"];
        $objPedido = PedidoData::getById($idPedido);
        $objEmpresa = EmpresaData::getById($_SESSION["empresa"]);
        $impresion = $_POST["impresion"];
        if ($impresion == 0) {
            $lstArea = AreaData::getAllAreaXProducto($idPedido, 0);
            if (count($lstArea) > 0) {
                $lstArea = AreaData::getAllAreaXProducto($idPedido, 1);
                if (count($lstArea) > 0) {
                    $area = "";            
                    foreach ($lstArea as $objArea) {                
                        $area .= $objArea->id.",";
                    }
                    $cadena .= substr($area, 0, strlen($area) - 1);
                }
            } else {
                $cadena .= "-1";
            }
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "PEDIDO PARA LLEVAR O DELIVERY (". $idPedido . ") - AREA: " . $cadena. "\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);           
        } else if ($impresion == 1) {
            $datos = array();
            $objArea = AreaData::getById($_POST["area"]);
            $lstProducto = DetallePedidoData::getAllProductoXArea($idPedido, $objArea->id, 0);
            if (count($lstProducto) > 0) {
                $datos["empresa"]["razon_social"] = $objEmpresa->razon_social;
                $datos["empresa"]["nombre_comercial"] = $objEmpresa->nombre_comercial;
                $datos["empresa"]["ruc"] = $objEmpresa->ruc;
                $datos["empresa"]["direccion"] = $objEmpresa->direccion;
                $datos["empresa"]["telefono"] = $objEmpresa->telefono;                    
                $datos["empresa"]["sede"] = $objPedido->getSede()->nombre;
                $datos["empresa"]["impresora"] = $objArea->impresora;
                
                $datos["datacomp"]["id"] = str_pad($idPedido, 8, "0", STR_PAD_LEFT);
                $datos["datacomp"]["fecha"] = date("d/m/Y H:i", strtotime($objPedido->fecha));
                $datos["datacomp"]["piso"] = $objPedido->getPiso()->nombre;
                $datos["datacomp"]["hora"] = $objPedido->hora;
                if ($objPedido->getMesa()) {
                    $datos["datacomp"]["mesa"] = $objPedido->getMesa()->nombre;
                } else {
                    $datos["datacomp"]["mesa"] = $objPedido->getTipo()->nombre;
                }
                $datos["datacomp"]["mesero"] = $objPedido->getUsuario()->nombres." ".$objPedido->getUsuario()->apellidos;
                
                foreach ($lstProducto as $objProducto) {
                    $datos["detalle"][] = $objProducto;
                    
                    $objDetallePedido = DetallePedidoData::getById($objProducto->id);
                    $objDetallePedido->fecha_comanda = date("Y-m-d H:i:s");
                    $objDetallePedido->update();
                }
            }
            if (!empty($datos)) {
                $cadena .= json_encode($datos);
            }
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "PEDIDO PARA LLEVAR O DELIVERY (". $idPedido . ") - MENSAJE: " . $cadena. "\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
        } else if ($impresion == 2) {
            $datos = array();
            $lstParametro = ParametroData::getAll(1, "OPCIONES GENERALES", "IMPRESORA LOCAL");
            $impresora = $lstParametro[0]->valor1;
            
            $datos["empresa"]["razon_social"] = $objEmpresa->razon_social;
            $datos["empresa"]["nombre_comercial"] = $objEmpresa->nombre_comercial;
            $datos["empresa"]["ruc"] = $objEmpresa->ruc;
            $datos["empresa"]["direccion"] = $objEmpresa->direccion;
            $datos["empresa"]["telefono"] = $objEmpresa->telefono;
            $datos["empresa"]["impresora"] = $impresora;
            $datos["empresa"]["sede"] = $objPedido->getSede()->nombre;
            
            $datos["datacomp"]["id"] = str_pad($idPedido, 8, "0", STR_PAD_LEFT);
            $datos["datacomp"]["fecha"] = date("d/m/Y H:i", strtotime($objPedido->fecha));
            $datos["datacomp"]["piso"] = $objPedido->getPiso()->nombre;
            $datos["datacomp"]["mesa"] = $objPedido->getMesa()->nombre;
            $datos["datacomp"]["mesero"] = $objPedido->getUsuario()->nombres." ".$objPedido->getUsuario()->apellidos;            
            $datos["datacomp"]["total"] = number_format($objPedido->total, 2);
            
            $lstProducto = DetallePedidoData::getAllProductoXArea($idPedido, "", 1);
            foreach ($lstProducto as $objProducto) {
                $datos["detalle"][] = $objProducto;
            }
            if (!empty($datos)) {
                $cadena .= json_encode($datos);
            }
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "IMPRIMIR DIRECTAMENTE (". $idPedido . ") - MENSAJE: " . $cadena. "\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
        } else if ($impresion == 3) {
            $datos = array();
            $lstParametro = ParametroData::getAll(1, "OPCIONES GENERALES", "IMPRESORA LOCAL");
            $impresora = $lstParametro[0]->valor1;
            
            $datos["empresa"]["razon_social"] = $objEmpresa->razon_social;
            $datos["empresa"]["nombre_comercial"] = $objEmpresa->nombre_comercial;
            $datos["empresa"]["ruc"] = $objEmpresa->ruc;
            $datos["empresa"]["direccion"] = $objEmpresa->direccion;
            $datos["empresa"]["telefono"] = $objEmpresa->telefono;
            $datos["empresa"]["impresora"] = $impresora;
            $datos["empresa"]["sede"] = $objPedido->getSede()->nombre;
            
            $datos["datacomp"]["id"] = str_pad($idPedido, 8, "0", STR_PAD_LEFT);
            $datos["datacomp"]["fecha"] = date("d/m/Y H:i", strtotime($objPedido->fecha));
            $datos["datacomp"]["piso"] = $objPedido->getPiso()->nombre;
            $datos["datacomp"]["hora"] = $objPedido->hora;
            if ($objPedido->getMesa()) {
                $datos["datacomp"]["mesa"] = $objPedido->getMesa()->nombre;
            } else {
                $datos["datacomp"]["mesa"] = $objPedido->getTipo()->nombre;
            }
            $datos["datacomp"]["mesero"] = $objPedido->getUsuario()->nombres." ".$objPedido->getUsuario()->apellidos;
            
            $lstProducto = DetallePedidoData::getAllProductoXArea($idPedido, "", 1);
            foreach ($lstProducto as $objProducto) {
                $datos["detalle"][] = $objProducto;
            }
            
            if (!empty($datos)) {
                $cadena .= json_encode($datos);
            }
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "IMPRIMIR DIRECTAMENTE COPIA (". $idPedido . ") - MENSAJE: " . $cadena. "\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
        }
    } else if (isset($_POST["numdocumento"])) {
        $objCredito = CreditoData::getByNumDoc(trim($_POST["numdocumento"]));
        if ($objCredito) {
            $cadena .= $objCredito->datos;
        }
    } else if (isset($_POST["perfil"])) {
        $objPerfil = PerfilData::getById($_POST["perfil"]);
        $cadena .= $objPerfil->indicador;
    }
}
echo $cadena;