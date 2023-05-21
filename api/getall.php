<?php
require('../config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);

if ($method === 'get') {
    $sql = $pdo->query("SELECT * FROM clube");
    $data = $sql->fetchAll(PDO::FETCH_ASSOC);

    $array = ['result' => $data];
} else {
    http_response_code(400);
    $array = ['error' => 'Método não permitido'];
}

require('../return.php');