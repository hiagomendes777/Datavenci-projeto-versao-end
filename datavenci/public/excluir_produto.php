<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

start_secure_session();

// 🔒 Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Verifica se o id foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: todos_produtos.php');
    exit;
}

$id = (int)$_GET['id'];

try {
    // Busca o produto para pegar a imagem
    $stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        // Produto não encontrado
        header('Location: todos_produtos.php');
        exit;
    }

    // Exclui a imagem do servidor, se existir
    if (!empty($produto['imagem']) && file_exists(__DIR__ . '/../' . $produto['imagem'])) {
        unlink(__DIR__ . '/../' . $produto['imagem']);
    }

    // Exclui o produto do banco
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->execute([$id]);

    // Redireciona de volta
    header('Location: tabelas.php');
    exit;

} catch (PDOException $e) {
    echo "Erro ao excluir o produto: " . $e->getMessage();
}
