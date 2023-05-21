<?php
require('../config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);

if ($method === 'get') {
    // Consultar os dados dos clubes no banco de dados
    $sql = $pdo->query("SELECT * FROM clube");
    $data = $sql->fetchAll(PDO::FETCH_ASSOC);

    $array = ['result' => $data];
} else {
    // Responder com erro para métodos HTTP não permitidos
    http_response_code(400);
    $array = ['error' => 'Método não permitido'];
}

require('../return.php');
