<?php

require '../../vendor/autoload.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
$route = $uri[sizeof($uri) - 1];

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
$listHeader['Api-Token'] = (array_key_exists('Api-Token', $listHeader) ? $listHeader['Api-Token'] : '');

// echo $listHeader['Api-Token'];

if (($listHeader['Api-Token'] !== $_ENV['API_KEY'])) {
    header("HTTP/1.1 404 Not Found");
    echo json_encode([
        'message'=> "wrong api token"
    ]);
    exit();
}

require './conn.php';

switch ($route) {
    case 'settings':
        $query = "SELECT * FROM settings";
        $result = mysqli_query($conn, $query);

        echo json_encode(mysqli_fetch_assoc($result));
        break;


    case 'generate_key':
        if(!isset($_POST['api_pass_key'])){
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                'message'=> "pass key not found"
            ]);
            exit();
        }
        if ($api_pass_key !== '0Kuunibyou@') {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                'message'=> "wrong pass key"
            ]);
            exit();
        }
        $key = implode('-', str_split(substr(strtolower(md5(microtime() . rand(1000, 9999))), 0, 30), 6));

        echo $key;
        break;

    default:
        header("HTTP/1.1 404 Not Found");
        echo "<h2>404 Not Found</h2>";
        exit();
    // break;
}
