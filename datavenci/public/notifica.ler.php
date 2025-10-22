<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$mercado_id = $_SESSION['user']['id'];

// Marcar todas as notificações do mercado como lidas
$stmt = $pdo->prepare("UPDATE notificacoes SET lida = 1 WHERE mercado_id = ?");
$stmt->execute([$mercado_id]);

header('Location: notificacoes.php');
exit;
?>

