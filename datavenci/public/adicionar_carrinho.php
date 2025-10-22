<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: todos_produtos.php');
    exit;
}

$produto_id = (int)$_GET['id'];

// Buscar produto
$stmt = $pdo->prepare("SELECT id, nome, preco, imagem FROM produtos WHERE id = ?");
$stmt->execute([$produto_id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if ($produto) {
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
    if (isset($_SESSION['carrinho'][$produto_id])) {
        $_SESSION['carrinho'][$produto_id]['quantidade'] += 1;
    } else {
        $_SESSION['carrinho'][$produto_id] = [
            'nome' => $produto['nome'],
            'preco' => $produto['preco'],
            'imagem' => $produto['imagem'],
            'quantidade' => 1
        ];
    }
}

header('Location: res_carrinho.php');
exit;
?>
