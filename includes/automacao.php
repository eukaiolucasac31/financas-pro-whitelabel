<?php
require_once __DIR__ . '/db.php';

function processar_recorrencias($pdo, $usuario_id) {
    $mesAtualStr = date('Y-m'); 
    $diaAtual = (int) date('d');

    $stmt = $pdo->prepare("
        SELECT id, tipo, descricao, valor, dia_vencimento 
        FROM contas_recorrentes 
        WHERE usuario_id = ? 
        AND dia_vencimento <= ? 
        AND (ultimo_mes_gerado IS NULL OR ultimo_mes_gerado != ?)
    ");
    $stmt->execute([$usuario_id, $diaAtual, $mesAtualStr]);
    $recorrencias = $stmt->fetchAll();

    foreach ($recorrencias as $rec) {
        $dataLancamento = date('Y-m-') . str_pad($rec['dia_vencimento'], 2, '0', STR_PAD_LEFT);
        
        $insert = $pdo->prepare("INSERT INTO lancamentos (tipo, descricao, valor, data_lancamento, status, usuario_id) VALUES (?, ?, ?, ?, 'pendente', ?)");
        if ($insert->execute([$rec['tipo'], $rec['descricao'] . ' (Recorrente)', $rec['valor'], $dataLancamento, $usuario_id])) {
            
            $update = $pdo->prepare("UPDATE contas_recorrentes SET ultimo_mes_gerado = ? WHERE id = ?");
            $update->execute([$mesAtualStr, $rec['id']]);
        }
    }
}