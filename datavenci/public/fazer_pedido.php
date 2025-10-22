<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Usuário não autenticado.']);
    exit;
}

$usuario_id = $_SESSION['user']['id'];
$carrinho = $_SESSION['carrinho'] ?? [];

if (empty($carrinho)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Carrinho vazio.']);
    exit;
}

try {
    $pdo->beginTransaction();

    foreach ($carrinho as $item) {
        $produto_id = $item['id'];
        $quantidade = $item['quantidade'];
        $preco = $item['preco'];
        $valor_total = $quantidade * $preco;
        $mercado_nome = $item['mercado'];

        // Pega o ID do mercado pelo nome
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nome = ?");
        $stmt->execute([$mercado_nome]);
        $mercado_id = $stmt->fetchColumn();

        if (!$mercado_id) {
            throw new Exception("Mercado '$mercado_nome' não encontrado.");
        }

        // Cria o pedido
        $stmtPedido = $pdo->prepare("
            INSERT INTO pedidos (restaurante_id, mercado_id, produto_id, quantidade, valor_total, status)
            VALUES (?, ?, ?, ?, ?, 'pendente')
        ");
        $stmtPedido->execute([$usuario_id, $mercado_id, $produto_id, $quantidade, $valor_total]);
        $pedido_id = $pdo->lastInsertId();

        // Cria notificação para o mercado
        $stmtNotif = $pdo->prepare("
            INSERT INTO notificacoes (usuario_id, mercado_id, pedido_id, titulo, mensagem, status)
            VALUES (?, ?, ?, ?, ?, 'pendente')
        ");
        $titulo = "Novo pedido recebido!";
        $mensagem = "O restaurante fez um pedido do produto {$item['nome']} (Qtd: {$quantidade}).";
        $stmtNotif->execute([$usuario_id, $mercado_id, $pedido_id, $titulo, $mensagem]);
    }

    $pdo->commit();
    unset($_SESSION['carrinho']);

    echo json_encode(['status' => 'sucesso', 'mensagem' => 'Pedido feito e notificação enviada com sucesso!']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
