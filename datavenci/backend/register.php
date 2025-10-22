<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

start_secure_session();

// CSRF
if (!isset($_POST['csrf']) || !verify_csrf_token($_POST['csrf'])) {
    $_SESSION['error'] = "Erro de segurança.";
    header("Location: ../public/cadastro.php");
    exit;
}

// Recebe os dados do formulário
$nome  = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$tipo  = $_POST['tipo'] ?? '';

if (!$nome || !$email || !$senha || !$tipo) {
    $_SESSION['error'] = "Preencha todos os campos.";
    header("Location: ../public/cadastro.php");
    exit;
}

// Verifica se o e-mail já existe
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $_SESSION['error'] = "E-mail já cadastrado.";
    header("Location: ../public/cadastro.php");
    exit;
}

// Criptografa senha
$hash = password_hash($senha, PASSWORD_DEFAULT);

// Insere no banco
$stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
$stmt->execute([$nome, $email, $hash, $tipo]);

$_SESSION['success'] = "Cadastro realizado com sucesso!";
header("Location: ../public/login.php");
exit;
