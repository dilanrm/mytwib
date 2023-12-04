<?php
$method = $_SERVER['REQUEST_METHOD'];

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$listHeader = [];

// print_r($route);


if (!isset($route) || $method !== 'POST') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

extract($_POST);
// print_r($_ENV);


$headers = apache_request_headers();

foreach ($headers as $header => $value) {
    
    $listHeader[$header] = $value;
    
    // echo "$header: $value <br />\n";
}
// print_r($listHeader);

// echo $listHeader['Api-Token'];
$listHeader['Api-Token'] = (array_key_exists('Api-Token',$listHeader) ? $listHeader['Api-Token'] : '');

// echo $listHeader['Api-Token'];

if (($listHeader['Api-Token'] !== $_ENV['API_KEY'])) {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(`{
        message: "wrong api token"
    }`);
    exit();
}

