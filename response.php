<?php
require_once 'DatabaseGateway.php';

if (isset($_GET['query'])) {
    $sql = $_GET['query'];
}

$database_gateway = new DatabaseGateway();

$response = $database_gateway->query($sql, $data = NULL, $fetchType = 2);
$responseRowCount = $database_gateway->rowCount($sql);

$json_data = array(
    "draw" => 1,
    "recordsTotal" => $responseRowCount,
    "recordsFiltered" => $responseRowCount,
    "data" => $response   // total data array
);

echo json_encode($json_data);