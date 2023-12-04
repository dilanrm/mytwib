<?php

require '../../../vendor/autoload.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
$route = $uri[sizeof($uri) - 1];
$method = $_SERVER['REQUEST_METHOD'];

$dotenv = Dotenv\Dotenv::createImmutable('../');
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

require '../conn.php';

switch ($route) {
    case 'getCategories':
        $query = "SELECT * FROM category ORDER BY name ASC";
        $result = mysqli_query($conn, $query);
        $rows = array();
        while ($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }

        echo json_encode($rows);
        break;

    default:
        header("HTTP/1.1 404 Not Found");
        echo "<h2>404 Not Found</h2>";
        exit();
    // break;
}
