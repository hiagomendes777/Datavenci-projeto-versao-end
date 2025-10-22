<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
start_secure_session();

if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'mercado') {
    header('Location: login.php');
    exit;
}

$pedido_id = $_POST['pedido_id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$pedido_id || !$status) {
    header('Location: notificacoes_mercado.php');
    exit;
}

$stmt = $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
$stmt->execute([$status, $pedido_id]);

// Atualiza também o status da notificação
$stmtNotif = $pdo->prepare("UPDATE notificacoes SET status = ? WHERE pedido_id = ?");
$stmtNotif->execute([$status, $pedido_id]);

$_SESSION['success'] = "Status atualizado com sucesso!";
header('Location: notificacoes_mercado.php');
exit;
