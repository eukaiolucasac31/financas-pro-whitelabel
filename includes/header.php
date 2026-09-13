<?php
/**
 * Cabeçalho Principal (Header) - Whitelabel
 */

if (!isset($pdo)) {
    require_once __DIR__ . '/db.php';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($empresaNome) ?> - Painel Financeiro</title>
    
    <?php if (!empty($empresaFavicon)): ?>
        <link rel="icon" href="<?= BASE_URL . htmlspecialchars($empresaFavicon) ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

    <nav class="navbar">
        <a href="<?= BASE_URL ?>/public/index.php" class="navbar-brand">
            <?php if (!empty($empresaLogo) && file_exists(__DIR__ . '/..' . $empresaLogo)): ?>
                <img src="<?= BASE_URL . htmlspecialchars($empresaLogo) ?>" alt="Logo da Empresa">
            <?php else: ?>
                <div class="logo-fallback">
                    <?= strtoupper(substr($empresaNome, 0, 1)) ?>
                </div>
            <?php endif; ?>
            <?= htmlspecialchars($empresaNome) ?>
        </a>

        <div class="nav-links">
            <a href="<?= BASE_URL ?>/public/index.php">Dashboard</a>
            <a href="<?= BASE_URL ?>/public/lancamentos.php">Lançamentos</a>
            <a href="<?= BASE_URL ?>/public/relatorios.php">Relatórios</a>
            <a href="<?= BASE_URL ?>/admin/settings.php">Configurações</a>
            <a href="<?= BASE_URL ?>/logout.php" class="logout">Sair</a>
        </div>
    </nav>

    <main class="container">