<?php

require '../../../vendor/autoload.php';
require '../conn.php';

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
$listHeader['Api-Token'] = (array_key_exists('Api-Token', $listHeader) ? $listHeader['Api-Token'] : '');

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
    case 'getFrames':
        $query = "SELECT `frames`.*,`category`.`name` as kategori,`category`.`slug` as cat FROM `frames` INNER JOIN `category` ON `frames`.`category` = `category`.`id` ORDER BY `frames`.`id` DESC";
        $result = mysqli_query($conn, $query);
        $rows = array();
        while ($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }

        echo json_encode($rows);
        break;

    case 'addFrame':
        require_once('../middleware/function.php');
        $statusMsg = '';
        $success = false;

        // File upload path
        extract($_POST);
        $slug = slugify($name);
        $deskrip = mysqli_real_escape_string($conn, $deskrip);
        $deskrip = str_replace("'", "&#39;", htmlspecialchars($deskrip));
        $targetDir = "../assets/frames/";
        $fileName = date('dmYHis') . "_" . str_replace(" ", "", basename($_FILES["file"]["name"]));
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // echo $slug;
        // print_r($_POST);

        if (!empty($_FILES["file"]["name"])) {
            // Allow certain file formats
            $allowTypes = ['jpg', 'png', 'jpeg', 'gif', 'pdf', 'webp'];
            if (in_array($fileType, $allowTypes)) {
                // Upload file to server
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                    // Insert image file name into database
                    $query = "INSERT into `frames` (`name`, `deskrip`, `file`, `keywords`, `slug`, `category`) VALUES ('$name','$deskrip','" . $fileName . "', '$keywords', '$slug', '$category')";
                    $result = mysqli_query($conn, $query);
                    if ($result) {
                        $statusMsg = "The file " . $fileName . " has been uploaded successfully.";
                        $success = true;
                    } else {
                        $statusMsg = "File upload failed, please try again. $query " . mysqli_error($conn);
                    }
                } else {
                    $statusMsg = "Sorry, there was an error uploading your file. $targetFilePath ";
                }
            } else {
                $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, WEBP, & PDF files are allowed to upload.';
            }
        } else {
            $statusMsg = 'Please select a file to upload.';
        }

        // Display status message
        // echo $statusMsg;
        echo json_encode([
            'message' => $statusMsg,
            'success' => $success
        ]);
        break;

    default:
        header("HTTP/1.1 404 Not Found");
        echo "<h2>404 Not Found</h2>";
        exit();
    // break;
}
