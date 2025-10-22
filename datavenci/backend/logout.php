<?php
// Caminho para funções
require_once __DIR__ . '/../includes/functions.php';

// Inicia sessão segura (ou comum se não existir função)
if (function_exists('start_secure_session')) {
    start_secure_session();
} else {
    session_start();
}

// Limpa todas as variáveis de sessão
$_SESSION = [];

// Remove cookies de sessão se existirem
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destrói a sessão
session_destroy();

// Redireciona para o login
header("Location: ../public/login.php");
exit;
