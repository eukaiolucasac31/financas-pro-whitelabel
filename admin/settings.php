<?php
require_once __DIR__ . '/../includes/auth.php';

// Restringe a página apenas para administradores
if (function_exists('require_admin')) { 
    require_admin(); 
}

require_once __DIR__ . '/../includes/header.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('Requisição inválida (CSRF).');
    }

    $novoNome = trim($_POST['empresa_nome'] ?? '');
    
    if (!empty($novoNome)) {
        $stmt = $pdo->prepare("INSERT INTO configuracoes (chave, valor) VALUES ('empresa_nome', ?) ON DUPLICATE KEY UPDATE valor = ?");
        $stmt->execute([$novoNome, $novoNome]);
        
        $permitidosImagens = ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp'];
        $permitidosIcones = ['image/x-icon', 'image/png', 'image/svg+xml'];

        // Processa Logo
        if (!empty($_FILES['empresa_logo']['tmp_name'])) {
            $mime = mime_content_type($_FILES['empresa_logo']['tmp_name']);
            if (in_array($mime, $permitidosImagens)) {
                $ext = pathinfo($_FILES['empresa_logo']['name'], PATHINFO_EXTENSION);
                $nomeLogo = 'logo_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['empresa_logo']['tmp_name'], UPLOADS_LOGOS_DIR . $nomeLogo)) {
                    $caminho = '/uploads/logos/' . $nomeLogo;
                    $pdo->prepare("INSERT INTO configuracoes (chave, valor) VALUES ('empresa_logo', ?) ON DUPLICATE KEY UPDATE valor = ?")->execute([$caminho, $caminho]);
                }
            } else {
                $mensagem = "Formato de logo inválido.";
                $tipoMensagem = "erro";
            }
        }

        // Processa Favicon
        if (!empty($_FILES['empresa_favicon']['tmp_name'])) {
            $mime = mime_content_type($_FILES['empresa_favicon']['tmp_name']);
            if (in_array($mime, $permitidosIcones)) {
                $ext = pathinfo($_FILES['empresa_favicon']['name'], PATHINFO_EXTENSION);
                $nomeIcone = 'favicon_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['empresa_favicon']['tmp_name'], UPLOADS_ICONS_DIR . $nomeIcone)) {
                    $caminho = '/uploads/icons/' . $nomeIcone;
                    $pdo->prepare("INSERT INTO configuracoes (chave, valor) VALUES ('empresa_favicon', ?) ON DUPLICATE KEY UPDATE valor = ?")->execute([$caminho, $caminho]);
                }
            }
        }

        if (empty($mensagem)) {
            $mensagem = "Configurações atualizadas com sucesso!";
            $tipoMensagem = "sucesso";
            
            // Recarrega os dados dinâmicos atualizados
            $empresaNome = get_config('empresa_nome', APP_NAME);
            $empresaLogo = get_config('empresa_logo', APP_LOGO_DEFAULT);
            $empresaFavicon = get_config('empresa_favicon', APP_FAVICON_DEFAULT);
        }
    } else {
        $mensagem = "O nome da empresa é obrigatório.";
        $tipoMensagem = "erro";
    }
}

$nomeAtual = get_config('empresa_nome', 'Gestão Pro');
$logoAtual = get_config('empresa_logo');
$faviconAtual = get_config('empresa_favicon');
?>

<div class="page-header">
    <h2>Identidade Visual da Empresa</h2>
</div>

<div class="settings-card">
    <?php if ($mensagem): ?>
        <div class="alert <?= $tipoMensagem ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <div class="form-group">
            <label for="empresa_nome">Nome da Empresa</label>
            <input type="text" id="empresa_nome" name="empresa_nome" value="<?= htmlspecialchars($nomeAtual) ?>" required>
        </div>

        <div class="form-group">
            <label for="empresa_logo">Logo (PNG, JPG, SVG, WebP)</label>
            <input type="file" id="empresa_logo" name="empresa_logo" accept="image/png, image/jpeg, image/svg+xml, image/webp">
            <?php if ($logoAtual && $logoAtual !== APP_LOGO_DEFAULT): ?>
                <img src="<?= BASE_URL . htmlspecialchars($logoAtual) ?>" alt="Logo Atual" class="preview-img">
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="empresa_favicon">Favicon / Ícone (.ico, .png, .svg)</label>
            <input type="file" id="empresa_favicon" name="empresa_favicon" accept="image/x-icon, image/png, image/svg+xml">
            <?php if ($faviconAtual && $faviconAtual !== APP_FAVICON_DEFAULT): ?>
                <img src="<?= BASE_URL . htmlspecialchars($faviconAtual) ?>" alt="Favicon Atual" class="preview-img" style="max-height: 32px;">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn" style="width: 100%;">Salvar Configurações</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>