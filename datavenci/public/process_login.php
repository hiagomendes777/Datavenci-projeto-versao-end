<?php
require_once __DIR__ . '/../includes/functions.php';
start_secure_session();
require_once __DIR__ . '/../includes/db_connect.php'; // se usa conexão com BD

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nome' => $user['nome'],
            'tipo' => $user['tipo']
        ];

        // 🔹 Apaga o cookie de consentimento sempre que fizer login
        setcookie('cookiesAccepted', '', time() - 3600, '/');

        header('Location: ../frontend/painel.php');
        exit;
    } else {
        $_SESSION['error'] = 'Email ou senha incorretos.';
        header('Location: ../frontend/login.php');
        exit;
    }
}
