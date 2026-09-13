<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Requer o arquivo de banco e configurações para conhecer a BASE_URL
require_once __DIR__ . '/db.php';

if (empty($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "/public/login.php");
    exit;
}

function require_admin() {
    if ($_SESSION['usuario_nivel'] !== 'admin') {
        header("HTTP/1.1 403 Forbidden");
        die("Acesso negado: Requer privilégios de administrador.");
    }
}