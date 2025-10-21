<?php
sleep(5);

if (empty($_POST)) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$data_array = $_POST;

$objProveedor = new stdclass;

foreach ($data_array as $key => $value) {
    $objProveedor->$key = $value;
}

$jsonProveedor = json_encode($objProveedor);

header('Content-Type: application/json');
echo $jsonProveedor;
?>