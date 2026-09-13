<?php
if (!defined('DB_HOST')) {
    $configFile = dirname(__DIR__) . '/config/config.php';
    if (file_exists($configFile)) {
        require_once $configFile;
    } else {
        die('Erro: Configure o arquivo config/config.php a partir do config.example.php.');
    }
}

// Garante a existência das pastas de upload
$diretorios = [UPLOADS_DIR, UPLOADS_ICONS_DIR, UPLOADS_LOGOS_DIR];
foreach ($diretorios as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

try {
    $dsn = sprintf("mysql:host=%s;dbname=%s;charset=%s", DB_HOST, DB_NAME, DB_CHARSET);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (\PDOException $e) {
    error_log("Falha no banco: " . $e->getMessage());
    die("Erro interno de conexão. Contate o suporte.");
}

function get_config(string $chave, $padrao = '') {
    global $pdo;
    static $configCache = null;

    if ($configCache === null) {
        try {
            $stmt = $pdo->query("SELECT chave, valor FROM configuracoes");
            $configCache = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
        } catch (\PDOException $e) {
            $configCache = [];
        }
    }
    return (!empty($configCache[$chave])) ? $configCache[$chave] : $padrao;
}

$empresaNome = get_config('empresa_nome', APP_NAME);
$empresaLogo = get_config('empresa_logo', APP_LOGO_DEFAULT);
$empresaFavicon = get_config('empresa_favicon', APP_FAVICON_DEFAULT);