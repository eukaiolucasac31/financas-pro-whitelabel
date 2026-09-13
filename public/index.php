<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/automacao.php';

// 1. Executa o motor de automação de contas recorrentes (Silencioso)
processar_recorrencias($pdo, $_SESSION['usuario_id']);

// 2. Busca os dados reais do mês atual
$mesAtual = date('m');
$anoAtual = date('Y');
$stmt = $pdo->prepare("SELECT SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END) as receitas, SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END) as despesas FROM lancamentos WHERE MONTH(data_lancamento) = ? AND YEAR(data_lancamento) = ?");
$stmt->execute([$mesAtual, $anoAtual]);
$dadosMes = $stmt->fetch();
$saldo = ($dadosMes['receitas'] ?? 0) - ($dadosMes['despesas'] ?? 0);

// 3. Busca os dados dos últimos 6 meses para o Gráfico
$stmtGrafico = $pdo->prepare("
    SELECT MONTH(data_lancamento) as mes, YEAR(data_lancamento) as ano,
           SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END) as total_receita,
           SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END) as total_despesa
    FROM lancamentos 
    WHERE data_lancamento >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY YEAR(data_lancamento), MONTH(data_lancamento)
    ORDER BY YEAR(data_lancamento) ASC, MONTH(data_lancamento) ASC
");
$stmtGrafico->execute();
$dadosGrafico = $stmtGrafico->fetchAll();

// Prepara os Arrays para o Chart.js
$labelsMeses = [];
$datasetReceitas = [];
$datasetDespesas = [];
$mesesNomes = ['', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

foreach ($dadosGrafico as $dg) {
    $labelsMeses[] = $mesesNomes[(int)$dg['mes']] . '/' . substr($dg['ano'], 2);
    $datasetReceitas[] = (float) $dg['total_receita'];
    $datasetDespesas[] = (float) $dg['total_despesa'];
}
?>

<div class="dashboard-header">
    <h2>Visão Geral</h2>
    <span>Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong>!</span>
</div>

<div class="stats-grid">
    <div class="stat-card receitas">
        <span class="stat-title">Entradas (Mês Atual)</span>
        <span class="stat-value"><?= APP_CURRENCY_SYMBOL ?> <?= number_format($dadosMes['receitas'] ?? 0, 2, ',', '.') ?></span>
    </div>
    <div class="stat-card despesas">
        <span class="stat-title">Saídas (Mês Atual)</span>
        <span class="stat-value"><?= APP_CURRENCY_SYMBOL ?> <?= number_format($dadosMes['despesas'] ?? 0, 2, ',', '.') ?></span>
    </div>
    <div class="stat-card saldo">
        <span class="stat-title">Saldo Líquido</span>
        <span class="stat-value" style="color: <?= $saldo < 0 ? 'var(--danger-color)' : 'var(--success-color)' ?>;">
            <?= APP_CURRENCY_SYMBOL ?> <?= number_format($saldo, 2, ',', '.') ?>
        </span>
    </div>
</div>

<div class="chart-container">
    <canvas id="evolucaoFinanceira"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('evolucaoFinanceira').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelsMeses) ?>,
            datasets: [
                {
                    label: 'Receitas',
                    data: <?= json_encode($datasetReceitas) ?>,
                    backgroundColor: '#8cc414',
                    borderRadius: 4
                },
                {
                    label: 'Despesas',
                    data: <?= json_encode($datasetDespesas) ?>,
                    backgroundColor: '#ff595e',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#c7d5e0' } } },
            scales: {
                y: { grid: { color: '#2a475e' }, ticks: { color: '#8f98a0' } },
                x: { grid: { display: false }, ticks: { color: '#8f98a0' } }
            }
        }
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>