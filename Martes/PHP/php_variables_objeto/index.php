<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Variables tipo objeto en PHP</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Variables tipo objeto en PHP. Objeto renglon de pedido</h1>

<?php
$objRenglonPedido = new stdClass();
$objRenglonPedido->codArt = "cp001";
$objRenglonPedido->descripcion = "jagger 500 ml";
$objRenglonPedido->precioUnitario = 2018;
$objRenglonPedido->cantidad = 912;
?>

<h2 class="objeto-nombre">$objRenglonPedido</h2>

<div class="info">
    <p>Codigo de articulo: <?php echo $objRenglonPedido->codArt; ?></p>
    <p>Descripcion del articulo: <?php echo $objRenglonPedido->descripcion; ?></p>
    <p>Precio unitario: <?php echo $objRenglonPedido->precioUnitario; ?></p>
    <p>Cantidad: <?php echo $objRenglonPedido->cantidad; ?></p>
</div>

<h2>Tipo de <span class="objeto-nombre">$objRenglonPedido</span>: <?php echo gettype($objRenglonPedido); ?></h2>

<hr>

<h2>Definir arreglo de pedidos:</h2>

<?php
$renglonesPedido = array();

$renglon1 = new stdClass();
$renglon1->codArt = "cp001";
$renglon1->descripcion = "jagger 500 ml";
$renglon1->precioUnitario = 2018;
$renglon1->cantidad = 912;
$renglonesPedido[] = $renglon1;

$renglon2 = new stdClass();
$renglon2->codArt = "cp002";
$renglon2->descripcion = "atun 800 gr";
$renglon2->precioUnitario = 24;
$renglon2->cantidad = 3;
$renglonesPedido[] = $renglon2;
?>

<h2 class="objeto-nombre">$renglonesPedido</h2>

<h2>Tipo de <span class="objeto-nombre">$renglonesPedido</span>: <?php echo gettype($renglonesPedido); ?></h2>

<h2>Tabula <span class="objeto-nombre">$renglonesPedido</span>. Recorrer el arreglo de renglones y tabularlos con html:</h2>

<table>
    <tr>
        <th>Código</th>
        <th>Descripción</th>
        <th>Precio Unitario</th>
        <th>Cantidad</th>
    </tr>
    <?php
    foreach ($renglonesPedido as $renglon) {
        echo "<tr>";
        echo "<td>" . $renglon->codArt . "</td>";
        echo "<td>" . $renglon->descripcion . "</td>";
        echo "<td>" . $renglon->precioUnitario . "</td>";
        echo "<td>" . $renglon->cantidad . "</td>";
        echo "</tr>";
    }
    ?>
</table>

<h3>Cantidad de renglones: <?php echo count($renglonesPedido); ?></h3>

<hr>

<h2>Produccion de un objeto <span class="objeto-nombre">$objRenglonesPedido</span> con dos atributos array renglonesPedido y cantidadDeRenglones</h2>

<?php
$objRenglonesPedido = new stdClass();
$objRenglonesPedido->renglonesPedido = $renglonesPedido;
$objRenglonesPedido->cantidadDeRenglones = count($renglonesPedido);
?>

<div class="info">
    <p>Cantidad de renglones: <?php echo $objRenglonesPedido->cantidadDeRenglones; ?></p>
</div>

<hr>

<h2>Produccion de un JSON jsonRenglones:</h2>

<?php
$jsonRenglones = json_encode($objRenglonesPedido);
?>

<div><?php echo $jsonRenglones; ?></div>

<hr>
<button class="volver-atras"><a href="../index.php">Volver atras</a></button>
</body>
</html>