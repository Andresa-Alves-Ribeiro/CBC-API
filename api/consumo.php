<?php
require('../config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);

if ($method === 'post') {
    $data = json_decode(file_get_contents("php://input"), true);

    $clubeId = $data['clube_id'];
    $recursoId = $data['recurso_id'];
    $valorConsumo = $data['valor_consumo'];

    // Verificar se o clube existe
    $clubeStmt = $pdo->prepare("SELECT * FROM clube WHERE id = :clube_id");
    $clubeStmt->bindParam(':clube_id', $clubeId);
    $clubeStmt->execute();

    if ($clubeStmt->rowCount() > 0) {
        // Se o clube existe, prosseguir com o consumo do recurso

        // Obter o saldo atual do clube
        $saldoAnteriorStmt = $pdo->prepare("SELECT saldo_disponivel FROM clube WHERE id = :clube_id");
        $saldoAnteriorStmt->bindParam(':clube_id', $clubeId);
        $saldoAnteriorStmt->execute();
        $saldoAnterior = floatval($saldoAnteriorStmt->fetchColumn());

        // Converter o valor de consumo para formato float
        $valorConsumo = str_replace(',', '.', $valorConsumo);
        $valorConsumo = floatval($valorConsumo);

        // Verificar se o saldo disponível é suficiente para o consumo
        if ($saldoAnterior >= $valorConsumo) {
            // Atualizar o saldo disponível do clube após o consumo
            $novoSaldoClube = $saldoAnterior - $valorConsumo;

            $atualizarSaldoClubeStmt = $pdo->prepare("UPDATE clube SET saldo_disponivel = :novo_saldo WHERE id = :clube_id");
            $atualizarSaldoClubeStmt->bindParam(':novo_saldo', $novoSaldoClube);
            $atualizarSaldoClubeStmt->bindParam(':clube_id', $clubeId);
            $atualizarSaldoClubeStmt->execute();

            // Obter o saldo atual do recurso
            $saldoRecursoStmt = $pdo->prepare("SELECT saldo FROM recurso WHERE id = :recurso_id");
            $saldoRecursoStmt->bindParam(':recurso_id', $recursoId);
            $saldoRecursoStmt->execute();
            $saldoRecurso = floatval($saldoRecursoStmt->fetchColumn());

            // Verificar se o saldo disponível do recurso é suficiente para o consumo
            if ($saldoRecurso >= $valorConsumo) {
                // Atualizar o saldo disponível do recurso após o consumo
                $novoSaldoRecurso = $saldoRecurso - $valorConsumo;

                $atualizarSaldoRecursoStmt = $pdo->prepare("UPDATE recurso SET saldo = :novo_saldo WHERE id = :recurso_id");
                $atualizarSaldoRecursoStmt->bindParam(':novo_saldo', $novoSaldoRecurso);
                $atualizarSaldoRecursoStmt->bindParam(':recurso_id', $recursoId);
                $atualizarSaldoRecursoStmt->execute();

                // Buscar nome do clube
                $nomeClubeStmt = $pdo->prepare("SELECT clube FROM clube WHERE id = :clube_id");
                $nomeClubeStmt->bindParam(':clube_id', $clubeId);
                $nomeClubeStmt->execute();
                $nomeClube = $nomeClubeStmt->fetchColumn();

                $array = [
                    'clube' => $nomeClube,
                    'saldo_anterior' => $saldoAnterior,
                    'saldo_atual' => $novoSaldoClube
                ];
            } else {
                http_response_code(400);
                $array = ['error' => 'O saldo disponível do recurso é insuficiente.'];
            }
        } else {
            http_response_code(400);
            $array = ['error' => 'O saldo disponível do clube é insuficiente.'];
        }
    } else {
        http_response_code(400);
        $array = ['error' => 'Clube não encontrado.'];
    }
} else {
    http_response_code(400);
    $array = ['error' => 'Método não permitido'];
}

require('../return.php');
