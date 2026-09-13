<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensagem = '';
$tipoMensagem = '';

// Processa novo lançamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'novo') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('Requisição inválida (CSRF).');
    }

    $tipo = in_array($_POST['tipo'], ['receita', 'despesa']) ? $_POST['tipo'] : null;
    $descricao = trim($_POST['descricao'] ?? '');
    $valor = str_replace(['.', ','], ['', '.'], $_POST['valor'] ?? '0'); // Converte para Decimal
    $data_lancamento = $_POST['data_lancamento'] ?? date('Y-m-d');
    $status = in_array($_POST['status'], ['pendente', 'pago']) ? $_POST['status'] : 'pago';

    if ($tipo && $descricao && is_numeric($valor) && $valor > 0) {
        $stmt = $pdo->prepare("INSERT INTO lancamentos (tipo, descricao, valor, data_lancamento, status, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$tipo, $descricao, $valor, $data_lancamento, $status, $_SESSION['usuario_id']])) {
            $mensagem = "Lançamento registrado com sucesso!";
            $tipoMensagem = "sucesso";
        }
    } else {
        $mensagem = "Preencha todos os campos corretamente.";
        $tipoMensagem = "erro";
    }
}

// Processa exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'excluir') {
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $id_excluir = (int) $_POST['id_excluir'];
        $stmt = $pdo->prepare("DELETE FROM lancamentos WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$id_excluir, $_SESSION['usuario_id']]);
        header("Location: lancamentos.php");
        exit;
    }
}

$mesAtual = date('m');
$anoAtual = date('Y');
$stmt = $pdo->prepare("SELECT * FROM lancamentos WHERE MONTH(data_lancamento) = ? AND YEAR(data_lancamento) = ? AND usuario_id = ? ORDER BY data_lancamento DESC, id DESC");
$stmt->execute([$mesAtual, $anoAtual, $_SESSION['usuario_id']]);
$lancamentos = $stmt->fetchAll();
?>

<div class="page-header">
    <h2>Lançamentos Financeiros</h2>
</div>

<?php if ($mensagem): ?>
    <div class="alert <?= $tipoMensagem ?>"><?= htmlspecialchars($mensagem) ?></div>
<?php endif; ?>

<div class="card-form">
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="acao" value="novo">
        
        <div class="form-row">
            <div class="form-group">
                <label>Tipo</label>
                <select name="tipo" required>
                    <option value="receita">Receita (Entrada)</option>
                    <option value="despesa">Despesa (Saída)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Descrição</label>
                <input type="text" name="descricao" placeholder="Ex: Venda de Produto" required>
            </div>
            <div class="form-group">
                <label>Valor (<?= APP_CURRENCY_SYMBOL ?>)</label>
                <input type="text" name="valor" class="mask-currency" placeholder="0,00" required>
            </div>
            <div class="form-group">
                <label>Data</label>
                <input type="date" name="data_lancamento" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="pago">Pago / Recebido</option>
                    <option value="pendente">Pendente</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn">Registrar Lançamento</button>
    </form>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Valor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($lancamentos) > 0): ?>
                <?php foreach ($lancamentos as $lanc): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($lanc['data_lancamento'])) ?></td>
                        <td><?= htmlspecialchars($lanc['descricao']) ?></td>
                        <td class="<?= $lanc['tipo'] === 'receita' ? 'badge-receita' : 'badge-despesa' ?>">
                            <?= ucfirst($lanc['tipo']) ?>
                        </td>
                        <td><?= ucfirst($lanc['status']) ?></td>
                        <td><?= APP_CURRENCY_SYMBOL ?> <?= number_format($lanc['valor'], 2, ',', '.') ?></td>
                        <td>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="acao" value="excluir">
                                <input type="hidden" name="id_excluir" value="<?= $lanc['id'] ?>">
                                <button type="submit" class="btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:var(--text-muted);">Nenhum lançamento encontrado neste mês.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>