<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Demostración PHP</title>
   <link rel="stylesheet" href="styles.css">
</head>

<body>

    <p>Esto es texto escrito fuera de las marcas de php. Es entregado en la respuesta http sin pasar por el preprocesador php</p>

    <hr>

    <?php
    echo "<p>Texto y/o HTML entregado por el procesador php usando la sentencia echo.</p>";
    ?>

    <hr>

    <?php
    $variableA = "valor1";
    $variableB = 2;
    $variableC = 3;

    echo "<h3>Para comentar una variable en php es \ \$NombreVariable</h3>";

    echo "<p>El valor de <strong>\$variableA</strong> es: $variableA</p>";
    echo "<p>El tipo de <strong>\$variableA</strong> es: " . gettype($variableA) . "</p>";

    echo "<p>El valor de <strong>\$variableB</strong> es: $variableB</p>";
    echo "<p>El tipo de <strong>\$variableB</strong> es: " . gettype($variableB) . "</p>";

    echo "<p>El valor de <strong>\$variableC</strong> es: $variableC</p>";
    echo "<p>El tipo de <strong>\$variableC</strong> es: " . gettype($variableC) . "</p>";
    ?>

    <div class="resultado">
        <?php
        $variableD = $variableB + $variableC;
        echo "variableD es la suma de variableB y variableC";
        ?>
    </div>

    <div class="error">
        Si los tipos fueran diferentes Php devolvería error.
    </div>

    <?php
    echo "<p>El valor de <strong>\$variableD</strong> es: $variableD</p>";
    echo "<p>El tipo de <strong>\$variableD</strong> es: " . gettype($variableD) . "</p>";
    ?>

    <hr>

    <?php
    $variableE = true;
    $variableF = false;

    echo "<p>variable tipo booleanas o logicas (verdadero) <strong>\$variableE</strong> : $variableE</p>";
    echo "<p>El tipo de <strong>\$variableE</strong> es: " . gettype($variableE) . "</p>";

    echo "<p>variable tipo booleanas o logicas (falso) <strong>\$variableF</strong> :</p>";
    echo "<p>El tipo de <strong>\$variableF</strong> es: " . gettype($variableF) . "</p>";
    ?>

    <hr>

    <?php
    define("MICONSTANTE", "valorConstante");

    echo "<p><strong>MICONSTANTE</strong> : " . MICONSTANTE . "</p>";
    echo "<p>Tipo de <strong>MICONSTANTE</strong>: " . gettype(MICONSTANTE) . "</p>";
    ?>

    <hr>

    <h3>Arreglos:</h3>

    <?php
    $aSaludo = array("hola", "hello");

    echo "<p><strong>\$aSaludo[0]:</strong> " . $aSaludo[0] . "</p>";
    echo "<p><strong>\$aSaludo[1]:</strong> " . $aSaludo[1] . "</p>";
    echo "<p>Tipo de <strong>\$aSaludo</strong> : " . gettype($aSaludo) . "</p>";
    ?>

    <hr>

    <p>Se agregan por programa dos elementos nuevos</p>

    <?php
    $aSaludo[] = "ciao";
    $aSaludo[] = "bonjour";
    ?>

    <h4>Todos los elementos originales y agregados:</h4>
    <ul>
        <?php
        foreach ($aSaludo as $saludo) {
            echo "<li>$saludo</li>";
        }
        ?>
    </ul>

    <hr>

    <h3>Arreglo de dos dimensiones (diccionario)</h3>

    <?php
    $aDiccionarioBasico = array(
        "Español" => array("hola", "adios", "casa"),
        "Ingles" => array("hello", "good by", "house"),
        "Italiano" => array("ciao", "arrivederci", "casa"),
        "Frances" => array("bonjour", "au revoir", "maison")
    );

    echo "<p>La variable <strong>\$aDiccionarioBasico</strong> tiene el siguiente tipo: " . gettype($aDiccionarioBasico) . "</p>";
    ?>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Español</th>
            <th>Ingles</th>
            <th>Italiano</th>
            <th>Frances</th>
        </tr>
        <?php
        for ($i = 0; $i < 3; $i++) {
            echo "<tr>";
            echo "<td>" . $aDiccionarioBasico["Español"][$i] . "</td>";
            echo "<td>" . $aDiccionarioBasico["Ingles"][$i] . "</td>";
            echo "<td>" . $aDiccionarioBasico["Italiano"][$i] . "</td>";
            echo "<td>" . $aDiccionarioBasico["Frances"][$i] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <p>Tambien asi se puede expresar el valor de <strong>\$aDiccionarioBasico[1][3]</strong>: <?php echo $aDiccionarioBasico["Frances"][1]; ?></p>

    <p>Cantidad de elementos de diccionario: <?php echo count($aDiccionarioBasico); ?></p>

    <hr>

    <h3>Variables tipo arreglo asociativo</h3>

    <div class="resultado">
        Cargar por programa una variable de tipo arreglo asociativo y mostrar sus atributos, la cantidad de elementos y los tipos de datos
    </div>

    <?php
    $articulo = array(
        "codigo" => "cp001",
        "descripcion" => "jagger",
        "precio" => 2018,
        "cantidad" => 912
    );
    echo "<h4>Datos del articulo:</h4>";
    echo "<p>Codigo de articulo: " . $articulo["codigo"] . "</p>";
    echo "<p style='color:blue;'>tipo del elemento: " . gettype($articulo["codigo"]) . "</p>";

    echo "<p>Descripcion del articulo: " . $articulo["descripcion"] . "</p>";
    echo "<p style='color:blue;'>tipo del elemento: " . gettype($articulo["descripcion"]) . "</p>";

    echo "<p>Precio del articulo: " . $articulo["precio"] . "</p>";
    echo "<p style='color:blue;'>tipo del elemento: " . gettype($articulo["precio"]) . "</p>";

    echo "<p>Cantidad: " . $articulo["cantidad"] . "</p>";
    echo "<p style='color:blue;'>tipo del elemento: " . gettype($articulo["cantidad"]) . "</p>";

    echo "<p>Cantidad de elementos del arreglo: " . count($articulo) . "</p>";
    echo "<p style='color:blue;'>Tipo de dato del arreglo: " . gettype($articulo) . "</p>";
    ?>

    <hr>

    <h3>Expresiones aritmeticas</h3>

    <?php
    $x = 3;
    $y = 4;

    echo "<p>La variable \$x tiene el siguiente valor: $x</p>";
    echo "<p>La variable \$y tiene el siguiente valor: $y</p>";
    echo "<p>La variable \$x tiene el siguiente tipo: " . gettype($x) . "</p>";
    echo "<p>La variable \$y tiene el siguiente tipo: " . gettype($y) . "</p>";

    $suma = $x + $y;
    $multiplicacion = $x * $y;
    $division = $x / $y;

    echo "<p>Asi se invrime una expresión aritmetica por ejemplo de Suma: (\$x + \$y) = $suma</p>";
    echo "<p>Asi se invrime una expresión aritmetica por ejemplo de Multiplicacion: \$x * \$y = $multiplicacion</p>";
    echo "<p>Asi se invrime una expresión aritmetica por ejemplo de División: \$x / \$y = $division</p>";
    ?>

    <hr>

    <h3>Alcances de las variables:</h3>

    <?php
    $n1 = 40;
    $n2 = 50;

    echo "<p><strong>Variables gobales:</strong> Las variables definidas fuera de toda función tienen alcance global y se encuentran referenciadas en un array asociativo global <strong>\$GLOBALS[]</strong> donde el indice se corresponde con el nombre de la variable global</p>";

    echo "<p>El valor de <strong>\$n1</strong> es: $n1</p>";
    echo "<p>El valor de <strong>\$n2</strong> es: $n2</p>";

    echo "<p>Suma de variables en el ambito global: \$GLOBALS['n1'] + \$GLOBALS['n2'] = " . ($GLOBALS['n1'] + $GLOBALS['n2']) . "</p>";

    echo "<p>Las declaraciones de variables dentro de una funcion solo tienen alcance dentro de la misma.</p>";
    ?>
    <button class="volver-atras">
        <a href="../index.php">Volver atras</a>
    </button>
</body>

</html>