<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

try {
    $stmt = $pdo->query("
        SELECT p.id, p.nome, c.nome AS categoria, p.vencimento, p.preco, p.quantidade
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        ORDER BY p.id ASC
    ");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$produtos) {
        die("Nenhum produto encontrado para exportar.");
    }

    // Define cabeçalhos e adiciona BOM para Excel
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=relatorio_produtos.csv');

    $output = fopen('php://output', 'w');

    // Escreve BOM para corrigir acentuação no Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Cabeçalho das colunas
    fputcsv($output, ['ID', 'Produto', 'Categoria', 'Data de Validade', 'Preço (R$)', 'Quantidade'], ';');

    // Linhas de produtos
    foreach ($produtos as $produto) {
        fputcsv($output, [
            $produto['id'],
            $produto['nome'],
            $produto['categoria'] ?? '-',
            date('d/m/Y', strtotime($produto['vencimento'])),
            'R$ ' . number_format($produto['preco'], 2, ',', '.'),
            $produto['quantidade']
        ], ';'); // usa ponto e vírgula como separador — o Excel lê corretamente
    }

    fclose($output);
    exit;

} catch (PDOException $e) {
    die("Erro ao exportar produtos: " . $e->getMessage());
}
