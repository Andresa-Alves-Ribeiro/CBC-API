<?php
require('../config.php');


$method = strtolower($_SERVER['REQUEST_METHOD']);

if ($method === 'post') {
    $data = json_decode(file_get_contents("php://input"), true);

    $clube = $data['clube'];
    $saldoDisponivel = $data['saldo_disponivel'];

    $stmt = $pdo->prepare("INSERT INTO clube (clube, saldo_disponivel) VALUES (:clube, :saldo_disponivel)");
    $stmt->bindParam(':clube', $clube);
    $stmt->bindParam(':saldo_disponivel', $saldoDisponivel);
    $stmt->execute();

    $array = ['message' => 'Clube cadastrado com sucesso'];
} else {
    http_response_code(400);
    $array = ['error' => 'Método não permitido'];
}

require('../return.php');