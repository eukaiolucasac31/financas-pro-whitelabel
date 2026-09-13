<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

$mensagem = "";
$erro = "";

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    // 1. Atualizar Configurações Whitelabel (Marca)
    if ($acao === 'whitelabel') {
        $novoNome = trim($_POST['app_name'] ?? '');
        $novaMoeda = trim($_POST['app_currency'] ?? 'R$');

        if (!empty($novoNome)) {
            // Atualiza nome e moeda no banco ou arquivo de config
            // Para simplificar, atualizamos a sessão e gravamos nas configurações globais se houver tabela
            $_SESSION['app_name'] = $novoNome;
            $_SESSION['app_currency'] = $novaMoeda;
            
            // Exemplo de upload de Logo
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['logo']['tmp_name'];
                $fileName = $_FILES['logo']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'ico'];
                if (in_array($fileExtension, $allowedExtensions)) {
                    $uploadFileDir = __DIR__ . '/../uploads/logos/';
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }
                    $newFileName = 'logo_' . time() . '.' . $fileExtension;
                    $dest_path = $uploadFileDir . $newFileName;
                    
                    if(move_uploaded_file($fileTmpPath, $dest_path)) {
                        $_SESSION['app_logo'] = '../uploads/logos/' . $newFileName;
                    }
                }
            }
            $mensagem = "Configurações visuais atualizadas com sucesso!";
        } else {
            $erro = "O nome da empresa não pode ficar vazio.";
        }
    }

    // 2. Atualizar Dados de Acesso (E-mail e Senha do Usuário)
    if ($acao === 'credenciais') {
        $novoEmail = trim($_POST['email'] ?? '');
        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';

        if (!filter_var($novoEmail, FILTER_VALIDATE_EMAIL)) {
            $erro = "E-mail inválido.";
        } else {
            // Busca dados atuais do usuário no banco
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$_SESSION['usuario_id']]);
            $user = $stmt->fetch();

            if ($user) {
                // Se o usuário quer alterar a senha, valida a atual
                if (!empty($novaSenha)) {
                    if (empty($senhaAtual) || !password_verify($senhaAtual, $user['senha'])) {
                        $erro = "A senha atual está incorreta.";
                    } else {
                        $hashNovaSenha = password_hash($novaSenha, PASSWORD_DEFAULT);
                        $update = $pdo->prepare("UPDATE usuarios SET email = ?, senha = ? WHERE id = ?");
                        $update->execute([$novoEmail, $hashNovaSenha, $_SESSION['usuario_id']]);
                        $_SESSION['usuario_email'] = $novoEmail;
                        $mensagem = "E-mail e senha atualizados com sucesso!";
                    }
                } else {
                    // Atualiza apenas o e-mail
                    $update = $pdo->prepare("UPDATE usuarios SET email = ? WHERE id = ?");
                    $update->execute([$novoEmail, $_SESSION['usuario_id']]);
                    $_SESSION['usuario_email'] = $novoEmail;
                    $mensagem = "E-mail atualizado com sucesso!";
                }
            }
        }
    }
}

// Busca dados atuais do usuário para preencher os inputs
$stmtUser = $pdo->prepare("SELECT email FROM usuarios WHERE id = ?");
$stmtUser->execute([$_SESSION['usuario_id']]);
$dadosUsuario = $stmtUser->fetch();
?>

<div class="page-header">
    <h2>Configurações do Sistema</h2>
</div>

<?php if (!empty($mensagem)): ?>
    <div style="background: #238636; color: #white; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
        <?= htmlspecialchars($mensagem) ?>
    </div>
<?php endif; ?>

<?php if (!empty($erro)): ?>
    <div style="background: #da3633; color: white; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
        <?= htmlspecialchars($erro) ?>
    </div>
<?php endif; ?>

<!-- Formulário de Identidade Visual (Whitelabel) -->
<div class="card" style="background: #1e222b; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #30363d;">
    <h3 style="margin-bottom: 15px; color: #fff;">Identidade Visual (Whitelabel)</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="acao" value="whitelabel">
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; color: #c9d1d9;">Nome da Empresa / Sistema:</label>
            <input type="text" name="app_name" value="<?= htmlspecialchars($_SESSION['app_name'] ?? 'Gestão Pro') ?>" required style="width: 100%; padding: 8px; background: #0d1117; border: 1px solid #30363d; color: #fff; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; color: #c9d1d9;">Símbolo da Moeda:</label>
            <input type="text" name="app_currency" value="<?= htmlspecialchars($_SESSION['app_currency'] ?? 'R$') ?>" required style="width: 100%; padding: 8px; background: #0d1117; border: 1px solid #30363d; color: #fff; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; color: #c9d1d9;">Logotipo do Sistema:</label>
            <input type="file" name="logo" accept="image/*" style="color: #c9d1d9;">
        </div>

        <button type="submit" class="btn" style="background: #238636; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">Salvar Alterações Visuais</button>
    </form>
</div>

<!-- Formulário de Alteração de E-mail e Senha -->
<div class="card" style="background: #1e222b; padding: 20px; border-radius: 8px; border: 1px solid #30363d;">
    <h3 style="margin-bottom: 15px; color: #fff;">Meus Dados de Acesso (Perfil)</h3>
    <form method="POST">
        <input type="hidden" name="acao" value="credenciais">
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; color: #c9d1d9;">E-mail de Login:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($dadosUsuario['email'] ?? '') ?>" required style="width: 100%; padding: 8px; background: #0d1117; border: 1px solid #30363d; color: #fff; border-radius: 4px;">
        </div>

        <hr style="border: 0; border-top: 1px solid #30363d; margin: 20px 0;">

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; color: #c9d1d9;">Senha Atual (Necessária apenas se for alterar a senha):</label>
            <input type="password" name="senha_atual" placeholder="Digite sua senha atual" style="width: 100%; padding: 8px; background: #0d1117; border: 1px solid #30363d; color: #fff; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; color: #c9d1d9;">Nova Senha (Deixe em branco para não alterar):</label>
            <input type="password" name="nova_senha" placeholder="Digite a nova senha" style="width: 100%; padding: 8px; background: #0d1117; border: 1px solid #30363d; color: #fff; border-radius: 4px;">
        </div>

        <button type="submit" class="btn" style="background: #1f6feb; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">Atualizar Credenciais</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>