<?php

use function PHPSTORM_META\type;

$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$base_url .= "://".$_SERVER['HTTP_HOST'];
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);

// echo $base_url;
$frames = json_decode(file_get_contents($base_url.'../data/frame/'));
$settings = json_decode(file_get_contents($base_url.'../data/settings/'));
$categories = json_decode(file_get_contents($base_url.'../data/categories/'));

// echo $categories->name;
// print_r($categories);