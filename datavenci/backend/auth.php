<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

start_secure_session();

if (!isset($_POST['csrf']) || !verify_csrf_token($_POST['csrf'])) {
    $_SESSION['error'] = "Erro de segurança.";
    header("Location: ../public/login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($senha, $user['senha'])) {

    // 🔹 Salva dados do usuário na sessão
    $_SESSION['user'] = [
        'id' => $user['id'],
        'nome' => $user['nome'],
        'tipo' => $user['tipo']
    ];

$tipo = strtolower(trim($user['tipo']));

if ($tipo === 'mercado') {
    header("Location: ../public/painel.php");
    exit;
} elseif ($tipo === 'restaurante') {
    header("Location: ../public/restaurante_dashboard.php");
    exit;
} else {
    $_SESSION['error'] = "Tipo de usuário inválido: " . htmlspecialchars($user['tipo']);
    header("Location: ../public/login.php");
    exit;
}


} else {
    $_SESSION['error'] = "Email ou senha incorretos.";
    header("Location: ../public/login.php");
    exit;
}
