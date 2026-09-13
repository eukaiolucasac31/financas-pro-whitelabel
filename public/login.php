<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!empty($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "/public/index.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('Requisição inválida.');
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if ($email && $senha) {
        $stmt = $pdo->prepare("SELECT id, nome, senha, nivel_acesso, status FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            if ($usuario['status'] === 'ativo') {
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_nivel'] = $usuario['nivel_acesso'];
                
                header("Location: " . BASE_URL . "/public/index.php");
                exit;
            } else {
                $erro = "Conta inativa. Contate o administrador.";
            }
        } else {
            usleep(500000); 
            $erro = "E-mail ou senha incorretos.";
        }
    } else {
        $erro = "Preencha todos os campos corretamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= htmlspecialchars($empresaNome) ?></title>
    
    <?php if ($empresaFavicon): ?>
        <link rel="icon" href="<?= BASE_URL . htmlspecialchars($empresaFavicon) ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="login-body">

<div class="login-box">
    <div class="brand-logo">
        <?php if ($empresaLogo && $empresaLogo !== APP_LOGO_DEFAULT): ?>
            <img src="<?= BASE_URL . htmlspecialchars($empresaLogo) ?>" alt="Logo">
        <?php else: ?>
            <h1><?= htmlspecialchars($empresaNome) ?></h1>
        <?php endif; ?>
    </div>

    <?php if ($erro): ?>
        <div class="alert erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <div class="form-group">
            <label for="email">Endereço de E-mail</label>
            <input type="email" id="email" name="email" required autofocus>
        </div>

        <div class="form-group">
            <label for="senha">Senha de Acesso</label>
            <input type="password" id="senha" name="senha" required>
        </div>

        <button type="submit" class="btn-login">Acessar Sistema</button>
    </form>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>