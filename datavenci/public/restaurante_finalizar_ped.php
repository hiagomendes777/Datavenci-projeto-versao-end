<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
start_secure_session();

if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'restaurante') {
    header('Location: login.php');
    exit;
}

$restaurante_id = $_SESSION['user']['id'];
$mercado_id = $_POST['mercado_id'];
$produto_id = $_POST['produto_id'];
$quantidade = $_POST['quantidade'];
$valor_total = $_POST['valor_total'];

try {
    $pdo->beginTransaction();

    // 1️⃣ Inserir pedido
    $stmt = $pdo->prepare("INSERT INTO pedidos (restaurante_id, mercado_id, produto_id, quantidade, valor_total) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$restaurante_id, $mercado_id, $produto_id, $quantidade, $valor_total]);
    $pedido_id = $pdo->lastInsertId();

    // 2️⃣ Criar notificação para o mercado
    $titulo = "Novo pedido recebido";
    $mensagem = "Um novo pedido foi feito pelo restaurante ID #$restaurante_id para o produto #$produto_id.";
    $stmtNotif = $pdo->prepare("INSERT INTO notificacoes (mercado_id, pedido_id, titulo, mensagem) VALUES (?, ?, ?, ?)");
    $stmtNotif->execute([$mercado_id, $pedido_id, $titulo, $mensagem]);

    $pdo->commit();

    $_SESSION['success'] = "Pedido realizado com sucesso!";
    header("Location: pedidos_restaurante.php");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Erro ao finalizar pedido: " . $e->getMessage();
    header("Location: carrinho.php");
    exit;
}
