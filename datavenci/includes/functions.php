<?php
function start_secure_session() {
    $secure = true; // true se estiver usando HTTPS
    $httponly = true; // impede acesso via JavaScript

    // Define opções da sessão
    session_set_cookie_params([
        'lifetime' => 0,          // sessão até fechar navegador
        'path' => '/',
        'domain' => '',           // domínio atual
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => 'Strict',   // protege contra CSRF
    ]);

    if(session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}


// Gera token CSRF
function generate_csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

// Verifica token CSRF
function verify_csrf_token($token) {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

// Escapa saídas HTML
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
