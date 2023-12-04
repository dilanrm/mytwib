<?php
require '../conn.php';
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header('Location: ./');
}

extract($_GET);
$query = "DELETE FROM `frames` where `id` = $id";
unlink("../assets/frames/$file");

if(mysqli_query($conn, $query)){
    header('Location: ./');
}else{
    echo("Error description: " . mysqli_error($conn));
}

?>