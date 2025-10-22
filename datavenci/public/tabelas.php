<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php'; // Arquivo com $pdo
start_secure_session();

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Dados do usuário
$nome = htmlspecialchars($_SESSION['user']['nome']);
$tipo = htmlspecialchars($_SESSION['user']['tipo']);

// Busca os produtos com a categoria
try {
    $stmt = $pdo->query("
        SELECT p.id, p.nome, c.nome AS categoria, p.vencimento, p.preco, p.quantidade
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        ORDER BY p.id ASC
    ");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar produtos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tabela de Produtos - Datavenci</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
body { background-color: #e6f0e9; color: #333; }
header { background-color: #176d2d; color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 15px; }
.logo { font-weight: bold; font-size: 1.5rem; letter-spacing: 1px; }
.back-btn { background: none; border: none; color: #fff; font-size: 1.2rem; cursor: pointer; }
main { padding: 20px; margin-bottom: 80px; }
.card { background-color: white; border-radius: 15px; padding: 15px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
.card h2 { color: #176d2d; font-size: 1.4rem; margin-bottom: 10px; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
table th, table td { border: 1px solid #ccc; padding: 10px; text-align: left; vertical-align: middle; }
table th { background-color: #176d2d; color: white; }
table tr:nth-child(even) { background-color: #f5f5f5; }
.bottom-menu { position: fixed; bottom: 0; left: 0; width: 100%; background-color: #176d2d; display: flex; justify-content: space-around; padding: 10px 0; border-top: 2px solid #144e21; }
.bottom-menu a { color: white; text-decoration: none; font-size: 0.9rem; display: flex; flex-direction: column; align-items: center; }
.bottom-menu a:hover, .bottom-menu a.active { color: #b2ffb2; font-weight: bold; }
/* Estilos para os botões de Ações */
.actions-cell { white-space: nowrap; text-align: center !important; }
.action-btn { padding: 5px 8px; margin: 0 2px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 0.9rem; }
.edit-btn { background-color: #ffc107; color: #333; }
.delete-btn { background-color: #dc3545; color: white; }
/* Botão exportar CSV */
.export-btn { margin-bottom: 15px; padding: 8px 12px; background-color: #176d2d; color: white; border: none; border-radius: 5px; cursor: pointer; }
</style>
</head>
<body>
<header>
    <button class="back-btn" onclick="window.location.href='painel.php'">⬅ Voltar</button>
    <div class="logo">DATAVENCI</div>
</header>
<main>
<section class="card">
    <h2>Tabela de Produtos</h2>
    <p>Visualize abaixo todos os produtos cadastrados:</p>

    <!-- Botão Exportar CSV -->
    <form method="post" action="exportar_csv.php">
        <button type="submit" class="export-btn">📄 Exportar CSV</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Validade</th>
                <th>Preço</th>
                <th>Quantidade</th>
                <th style="width: 120px; text-align: center;">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($produtos): ?>
            <?php foreach ($produtos as $produto): ?>
            <tr>
                <td><?= htmlspecialchars($produto['id']) ?></td>
                <td><?= htmlspecialchars($produto['nome']) ?></td>
                <td><?= htmlspecialchars($produto['categoria'] ?? '-') ?></td>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($produto['vencimento']))) ?></td>
                <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                <td><?= htmlspecialchars($produto['quantidade']) ?></td>
                <td class="actions-cell">
                    <a href="editar_produto.php?id=<?= $produto['id'] ?>" class="action-btn edit-btn" title="Editar"> ✏️ Editar </a>
                    <a href="excluir_produto.php?id=<?= $produto['id'] ?>" class="action-btn delete-btn" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir o produto <?= htmlspecialchars($produto['nome']) ?>?');"> 🗑️ Excluir </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">Nenhum produto cadastrado.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
</main>
</body>
</html>
