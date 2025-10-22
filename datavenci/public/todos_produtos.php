<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Categorias fixas
$categoriasFixas = [
    'Hortifruti','Mercearia','Padaria','Açougue','Frios e Laticínios',
    'Bebidas','Higiene Pessoal','Limpeza','Utilidades Domésticas'
];

// Filtro atual
$filtro = $_GET['categoria'] ?? 'todas';

// Consulta produtos do banco
if ($filtro === 'todas') {
    $stmt = $pdo->query("
        SELECT p.id, p.nome, p.quantidade, p.imagem, p.vencimento, p.preco, c.nome AS categoria
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        ORDER BY c.nome ASC, p.nome ASC
    ");
} else {
    $stmt = $pdo->prepare("
        SELECT p.id, p.nome, p.quantidade, p.imagem, p.vencimento, p.preco, c.nome AS categoria
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE c.nome = ?
        ORDER BY p.nome ASC
    ");
    $stmt->execute([$filtro]);
}

$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Todos os Produtos Disponíveis - Datavenci</title>
<style>
body { margin:0; font-family: Arial,sans-serif; background:#eaf3ea; color:#222; }
header { background:#155b26; color:white; padding:15px 25px; display:flex; align-items:center; justify-content:space-between; }
header h1 { margin:0; font-size:1.3rem; }
.back-btn { background:none; border:none; color:white; font-size:1.5rem; cursor:pointer; transition:.2s; }
.back-btn:hover { transform:scale(1.2); }

.container { padding:30px 40px; }
.top-controls { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; }
select { padding:8px 10px; border-radius:6px; border:1px solid #ccc; font-size:0.9rem; }
.btn { background:#155b26; color:white; border:none; padding:10px 15px; border-radius:8px; text-decoration:none; transition:.3s; }
.btn:hover { background:#1e7031; }

.tabela-produtos { width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden; box-shadow:0 2px 5px rgba(0,0,0,0.1); }
.tabela-produtos th { background:#155b26; color:white; text-align:left; padding:10px; }
.tabela-produtos td { padding:10px; border-bottom:1px solid #ddd; }
.tabela-produtos tr:nth-child(even) { background:#f7f7f7; }

td img { width:60px; height:60px; object-fit:cover; border-radius:6px; background:#f0f0f0; }

.acoes { display:flex; gap:8px; }
.btn-small { padding:6px 10px; font-size:0.8rem; border:none; border-radius:5px; cursor:pointer; color:white; }
.carrinho { background:#28a745; }
.carrinho:hover { background:#1e7e34; }

.sem-produtos { text-align:center; color:#666; font-style:italic; margin-top:40px; }
</style>
</head>
<body>

<header>
  <button class="back-btn" onclick="window.location.href='restaurante_dashboard.php'">&#8592;</button>
  <h1>Todos os Produtos Disponíveis</h1>
</header>

<div class="container">
  <div class="top-controls">
    <div>
      <label for="filtroCategoria">Filtrar por categoria: </label>
      <select id="filtroCategoria" onchange="filtrarCategoria()">
        <option value="todas" <?= $filtro === 'todas' ? 'selected' : '' ?>>Todas</option>
        <?php foreach ($categoriasFixas as $cat): ?>
          <option value="<?= htmlspecialchars($cat) ?>" <?= $filtro === $cat ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <a href="res_carrinho.php" class="btn">Meu Carrinho</a>
  </div>

  <?php if (empty($produtos)): ?>
    <p class="sem-produtos">Nenhum produto encontrado nesta categoria.</p>
  <?php else: ?>
  <table class="tabela-produtos">
    <thead>
      <tr>
        <th>ID</th>
        <th>Imagem</th>
        <th>Nome</th>
        <th>Categoria</th>
        <th>Quantidade</th>
        <th>Validade</th>
        <th>Preço</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($produtos as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><img src="<?= $p['imagem'] ?: '../imagens/sem_imagem.png' ?>" alt=""></td>
        <td><?= htmlspecialchars($p['nome']) ?></td>
        <td><?= htmlspecialchars($p['categoria'] ?? 'Sem Categoria') ?></td>
        <td><?= htmlspecialchars($p['quantidade']) ?></td>
        <td><?= $p['vencimento'] ? date('d/m/Y', strtotime($p['vencimento'])) : '-' ?></td>
        <td>R$ <?= number_format($p['preco'] ?? 0, 2, ',', '.') ?></td>
        <td>
          <div class="acoes">
            <button class="btn-small carrinho" onclick="window.location.href='adicionar_carrinho.php?id=<?= $p['id'] ?>'">Adicionar ao Carrinho</button>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<script>
function filtrarCategoria() {
  const categoria = document.getElementById('filtroCategoria').value;
  const url = new URL(window.location.href);
  url.searchParams.set('categoria', categoria);
  window.location.href = url.toString();
}
</script>

</body>
</html>
