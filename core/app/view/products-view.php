<?php
    $estado = "1";
    if (count($_POST) > 0) {
        $estado = $_POST["estado"];
        $categoria = $_POST["categoria"];
        $tipo = $_POST["tipo"];
    
    $lstEstado = EstadoData::getAll();
    $lstCategoria = CategoriaData::getAll(1, $sede);
    $lstTipo = ParametroData::getAll(1, "TIPO PRODUCTO");
    $lstProducto = ProductoData::getAll($estado, $sede, $categoria, $tipo);
        						<?php
                                ?>
        							<tr>
        						<?php
                                    }
                                ?>
        					</table>
		</div>