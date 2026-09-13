<?php
/**
 * Ponto de Entrada Principal da Raiz
 * Redireciona o acesso inicial para a pasta pública e sistema de rotas.
 */
require_once __DIR__ . '/config/config.php';

// Se já estiver logado, vai pro dashboard; senão, vai pro login
session_start();
if (!empty($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "/public/index.php");
} else {
    header("Location: " . BASE_URL . "/public/login.php");
}
exit;