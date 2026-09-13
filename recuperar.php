<?php
require_once __DIR__ . '/includes/db.php';

$email = 'admin@empresa.com';
$novaSenhaPura = 'admin123';

// Gera o hash seguro usando a API nativa do PHP do seu XAMPP
$senhaHash = password_hash($novaSenhaPura, PASSWORD_DEFAULT);

// Atualiza ou insere o usuário admin garantindo consistência
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if ($usuario) {
    $update = $pdo->prepare("UPDATE usuarios SET senha = ?, status = 'ativo', nivel_acesso = 'admin' WHERE email = ?");
    $update->execute([$senhaHash, $email]);
    echo "<h1>Sucesso! A senha do admin foi atualizada para: <strong>admin123</strong></h1>";
} else {
    $insert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, nivel_acesso, status) VALUES ('Administrador', ?, ?, 'admin', 'ativo')");
    $insert->execute([$email, $senhaHash]);
    echo "<h1>Sucesso! Usuário admin criado com a senha: <strong>admin123</strong></h1>";
}

echo "<br><br><a href='public/login.php'>Clique aqui para ir para a tela de login</a>";
exit;