<?php
require('config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);
$path = $_SERVER['REQUEST_URI'];

$url_base = "/cbc-api/api/";

if (strpos($path, $url_base) === 0) {
    $path = substr($path, strlen($url_base));
}

// Router
switch ($path) {
    case 'cadastro':
        require('api/cadastro.php');
        break;
    case 'getall':
        require('api/getall.php');
        break;
    case 'consumo':
        require('api/consumo.php');
        break;
    default:
        http_response_code(404);
        $array = ['error' => 'Endpoint not found'];
}

require('return.php');
?>
