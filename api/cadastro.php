<?php
require('../config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);

if ($method === 'post') {
    // Obter os dados do clube a partir da requisição
    $data = json_decode(file_get_contents("php://input"), true);

    // Extrair os campos relevantes
    $clube = $data['clube'];
    $saldoDisponivel = $data['saldo_disponivel'];

    // Inserir o clube no banco de dados
    $stmt = $pdo->prepare("INSERT INTO clube (clube, saldo_disponivel) VALUES (:clube, :saldo_disponivel)");
    $stmt->bindParam(':clube', $clube);
    $stmt->bindParam(':saldo_disponivel', $saldoDisponivel);
    $stmt->execute();

    $array = ['message' => 'Clube cadastrado com sucesso'];
} else {
    // Responder com erro para métodos HTTP não permitidos
    http_response_code(400);
    $array = ['error' => 'Método não permitido'];
}

require('../return.php');
