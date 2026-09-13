<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

$mesSelecionado = $_GET['mes'] ?? date('m');
$anoSelecionado = $_GET['ano'] ?? date('Y');

// Busca o resumo financeiro apenas do usuário logado
$stmt = $pdo->prepare("
    SELECT 
        SUM(CASE WHEN tipo = 'receita' AND status = 'pago' THEN valor ELSE 0 END) as total_receitas,
        SUM(CASE WHEN tipo = 'despesa' AND status = 'pago' THEN valor ELSE 0 END) as total_despesas
    FROM lancamentos 
    WHERE MONTH(data_lancamento) = ? AND YEAR(data_lancamento) = ? AND usuario_id = ?
");
$stmt->execute([$mesSelecionado, $anoSelecionado, $_SESSION['usuario_id']]);
$resumo = $stmt->fetch();

$totalReceitas = $resumo['total_receitas'] ?? 0;
$totalDespesas = $resumo['total_despesas'] ?? 0;
$saldoLiquido = $totalReceitas - $totalDespesas;
?>

<div class="page-header">
    <h2>Relatório Mensal - DRE Simplificado</h2>
</div>

<form method="GET" class="filter-box" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
    <div style="display: flex; gap: 1rem;">
        <label for="mes" style="align-self: center; font-weight: bold;">Período:</label>
        <select name="mes" id="mes">
            <?php for($m=1; $m<=12; $m++): $mesF = str_pad($m, 2, "0", STR_PAD_LEFT); ?>
                <option value="<?= $mesF ?>" <?= $mesSelecionado == $mesF ? 'selected' : '' ?>>Mês <?= $mesF ?></option>
            <?php endfor; ?>
        </select>
        
        <select name="ano">
            <?php for($a = date('Y') - 2; $a <= date('Y') + 1; $a++): ?>
                <option value="<?= $a ?>" <?= $anoSelecionado == $a ? 'selected' : '' ?>><?= $a ?></option>
            <?php endfor; ?>
        </select>
        
        <button type="submit" class="btn">Filtrar</button>
    </div>
    
    <a href="<?= BASE_URL ?>/public/imprimir_relatorio.php?mes=<?= $mesSelecionado ?>&ano=<?= $anoSelecionado ?>" target="_blank" class="btn" style="background: #e2b714; color: #171a21; text-decoration: none;">📄 Exportar PDF</a>
</form>

<div class="report-grid">
    <div class="report-card">
        <h3>Total de Receitas</h3>
        <div class="report-value val-positivo">
            <?= APP_CURRENCY_SYMBOL ?> <?= number_format($totalReceitas, 2, ',', '.') ?>
        </div>
    </div>
    
    <div class="report-card">
        <h3>Total de Despesas</h3>
        <div class="report-value val-negativo">
            <?= APP_CURRENCY_SYMBOL ?> <?= number_format($totalDespesas, 2, ',', '.') ?>
        </div>
    </div>
    
    <div class="report-card">
        <h3>Resultado Líquido</h3>
        <div class="report-value <?= $saldoLiquido >= 0 ? 'val-neutro' : 'val-negativo' ?>">
            <?= APP_CURRENCY_SYMBOL ?> <?= number_format($saldoLiquido, 2, ',', '.') ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>