<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$mesSelecionado = $_GET['mes'] ?? date('m');
$anoSelecionado = $_GET['ano'] ?? date('Y');

$stmt = $pdo->prepare("SELECT * FROM lancamentos WHERE MONTH(data_lancamento) = ? AND YEAR(data_lancamento) = ? ORDER BY data_lancamento ASC");
$stmt->execute([$mesSelecionado, $anoSelecionado]);
$lancamentos = $stmt->fetchAll();

$receitas = array_sum(array_map(function($l) { return $l['tipo'] == 'receita' ? $l['valor'] : 0; }, $lancamentos));
$despesas = array_sum(array_map(function($l) { return $l['tipo'] == 'despesa' ? $l['valor'] : 0; }, $lancamentos));
$saldo = $receitas - $despesas;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório Financeiro - <?= htmlspecialchars($empresaNome) ?></title>
    <style>
        @page { size: A4; margin: 15mm; }
        body { font-family: Arial, sans-serif; background: #fff; color: #000; margin: 0; padding: 0; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; color: #333; }
        .header p { margin: 0; color: #666; font-size: 14px; }
        .resumo-grid { display: flex; gap: 20px; margin-bottom: 30px; }
        .resumo-card { flex: 1; padding: 15px; border: 1px solid #ddd; background: #f9f9f9; text-align: center; border-radius: 4px; }
        .resumo-card h3 { margin: 0 0 10px 0; font-size: 14px; color: #555; text-transform: uppercase; }
        .resumo-card .valor { font-size: 22px; font-weight: bold; }
        .val-positivo { color: #2e7d32; }
        .val-negativo { color: #c62828; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; font-size: 13px; }
        th { background: #eee; font-weight: bold; text-transform: uppercase; }
        .text-right { text-align: right; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <div>
            <h1><?= htmlspecialchars($empresaNome) ?></h1>
            <p>CNPJ/Documento: (Uso Interno)</p>
        </div>
        <div style="text-align: right;">
            <p><strong>Competência:</strong> <?= $mesSelecionado ?>/<?= $anoSelecionado ?></p>
            <p><strong>Gerado em:</strong> <?= date('d/m/Y H:i') ?></p>
        </div>
    </div>

    <div class="resumo-grid">
        <div class="resumo-card">
            <h3>Total Receitas</h3>
            <div class="valor val-positivo"><?= APP_CURRENCY_SYMBOL ?> <?= number_format($receitas, 2, ',', '.') ?></div>
        </div>
        <div class="resumo-card">
            <h3>Total Despesas</h3>
            <div class="valor val-negativo"><?= APP_CURRENCY_SYMBOL ?> <?= number_format($despesas, 2, ',', '.') ?></div>
        </div>
        <div class="resumo-card">
            <h3>Saldo Líquido</h3>
            <div class="valor <?= $saldo >= 0 ? 'val-positivo' : 'val-negativo' ?>"><?= APP_CURRENCY_SYMBOL ?> <?= number_format($saldo, 2, ',', '.') ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Status</th>
                <th class="text-right">Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($lancamentos as $l): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($l['data_lancamento'])) ?></td>
                <td>
                    <?= htmlspecialchars($l['descricao']) ?>
                    <?= $l['tipo'] == 'receita' ? '<small style="color:#2e7d32;">(Entrada)</small>' : '<small style="color:#c62828;">(Saída)</small>' ?>
                </td>
                <td><?= ucfirst($l['status']) ?></td>
                <td class="text-right <?= $l['tipo'] == 'receita' ? 'val-positivo' : 'val-negativo' ?>">
                    <?= APP_CURRENCY_SYMBOL ?> <?= number_format($l['valor'], 2, ',', '.') ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>