<?php

$ruta = "/var/www/html/ccra/";

require_once ($ruta . "core/app/model/SedeData.php");
require_once ($ruta . "core/app/model/AlmacenData.php");
require_once ($ruta . "core/app/model/HistoricoStockData.php");
require_once ($ruta . "core/app/model/DetalleHistoricoStockData.php");
require_once ($ruta . "core/controller/Executor.php");
require_once ($ruta . "core/controller/Database.php");
require_once ($ruta . "core/controller/Core.php");
require_once ($ruta . "core/controller/Model.php");

file_put_contents("historico_stock" . date("Ymd") . ".log", "----------------------------------------------------------------\n", FILE_APPEND);
file_put_contents("historico_stock" . date("Ymd") . ".log", "Historico Stock - Inicio\n", FILE_APPEND);

$fechaActual = date("d-m-Y");
$fechaStock = date("Y-m-d", strtotime($fechaActual."- 1 days")); 

$lstAlmacen = AlmacenData::getAll(1);
foreach ($lstAlmacen as $objAlmacen) {
    file_put_contents("historico_stock" . date("Ymd") . ".log", "Almacen : " . $objAlmacen->nombre . "\n", FILE_APPEND);
    
    $objHistoricoStock = new HistoricoStockData();
    $objHistoricoStock->fecha_stock = $fechaStock;
    $objHistoricoStock->sede = $objAlmacen->getSede()->id;
    $objHistoricoStock->nom_sede = $objAlmacen->getSede()->nombre;
    $objHistoricoStock->almacen = $objAlmacen->id;
    $objHistoricoStock->nom_almacen = $objAlmacen->nombre;
    $objHistoricoStock->fecha_actualizacion = date("Y-m-d H:i:s");
    $resultado = $objHistoricoStock->add();
    if (isset($resultado[1]) && $resultado[1] > 0) {
        $idHistoricoStock = $resultado[1];
        
        file_put_contents("historico_stock" . date("Ymd") . ".log", "Registro en tabla historicos_stock : " . $idHistoricoStock . "\n", FILE_APPEND);
        
        $resultado = DetalleHistoricoStockData::add($idHistoricoStock);
        if (isset($resultado[1]) && $resultado[1] > 0) {
            file_put_contents("historico_stock" . date("Ymd") . ".log", "Registro en tabla detalle_historicos_stock : " . $resultado[1] . "\n", FILE_APPEND);
        } else {
            file_put_contents("historico_stock" . date("Ymd") . ".log", "Error registro en tabla detalle_historicos_stock\n", FILE_APPEND);
        }
    } else {
        file_put_contents("historico_stock" . date("Ymd") . ".log", "Error registro en tabla historicos_stock\n", FILE_APPEND);
    }
}

file_put_contents("historico_stock" . date("Ymd") . ".log", "Historico Stock - Fin\n", FILE_APPEND);
file_put_contents("historico_stock" . date("Ymd") . ".log", "----------------------------------------------------------------\n", FILE_APPEND);

?>