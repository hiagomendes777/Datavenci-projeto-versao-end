<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$nome = htmlspecialchars($_SESSION['user']['nome']);

// Buscar produtos
try {
    $stmt = $pdo->query("SELECT p.*, u.nome AS mercado_nome FROM produtos p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.created_at DESC");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtos = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Produtos | Datavenci</title>
<style>
* {margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif;}
body {background-color: #e6f0e9; color: #333;}

header {background-color: #176d2d; color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 15px;}
.menu-btn {background: none; border: none; color: #fff; font-size: 22px; cursor: pointer;}
.logo {font-weight: bold; font-size: 1.5rem; letter-spacing: 1px;}

.sidebar {position: fixed; left: -270px; top: 0; width: 250px; height: 100%; background: #333; color: white; padding-top: 60px; transition: left 0.3s; z-index: 100;}
.sidebar.active {left: 0;}
.sidebar ul {list-style: none;}
.sidebar ul li {padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);}
.sidebar ul li a {color: white; text-decoration: none; display: flex; align-items: center; gap: 10px;}
.sidebar ul li a:hover, .sidebar ul li a.active {color: #1db954; font-weight: bold;}

main {padding: 20px; margin-bottom: 80px; transition: margin-left 0.3s;}
main.shifted {margin-left: 250px;}
.card {background-color: white; border-radius: 15px; padding: 15px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);}
.card h2 {color: #176d2d; font-size: 1.2rem;}
.highlight {color: #176d2d; font-weight: bold;}

.filter {display: flex; justify-content: space-between; margin-bottom: 15px;}
select {padding: 8px; border-radius: 8px; border: 1px solid #ccc;}

.products {display: flex; flex-wrap: wrap; gap: 15px;}
.product-card {width: calc(50% - 10px); background: #fff; border-radius: 15px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column;}
.product-card img {width: 100%; height: 120px; object-fit: cover;}
.product-info {padding: 10px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;}
.product-info div {margin-bottom: 5px; font-weight: bold; color: #176d2d;}
.add-btn {text-align: center; padding: 6px 10px; background: #176d2d; color: #fff; border-radius: 8px; cursor: pointer; text-decoration: none; transition: 0.3s;}
.add-btn:hover {background: #145a24;}

.bottom-menu {position: fixed; bottom: 0; left: 0; width: 100%; background-color: #176d2d; display: flex; justify-content: space-around; padding: 10px 0; border-top: 2px solid #144e21;}
.bottom-menu a {color: white; text-decoration: none; font-size: 0.9rem; display: flex; flex-direction: column; align-items: center;}
.bottom-menu a:hover, .bottom-menu a.active {color: #b2ffb2; font-weight: bold;}
.bottom-menu i {font-size: 20px; margin-bottom: 3px;}

@media (max-width: 768px) {
  .product-card {width: 100%;}
  .sidebar {width: 200px;}
  main.shifted {margin-left: 200px;}
}
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
  <ul>
    <li><a href="mercados.php">🏪 Mercados</a></li>
    <li><a href="meus_pedidos.php">📦 Meus Pedidos</a></li>
    <li><a href="carrinho.php">🛒 Carrinho</a></li>
    <li><a href="promocoes.php">🔥 Promoções</a></li>
    <li><a href="configuracoes.php">⚙️ Configurações</a></li>
    <li><a href="faq.php">❓ FAQ</a></li>
    <li><a href="sobre.php">ℹ️ Sobre</a></li>
    <li><a href="../backend/logout.php">🚪 Sair</a></li>
  </ul>
</div>

<header>
  <button class="menu-btn" id="menuBtn">☰</button>
  <div class="logo">DATAVENCI</div>
</header>

<main id="mainContent">
  <section class="card">
    <h2>Produtos Disponíveis</h2>
    <div class="filter">
      <label for="categoria">Filtrar por categoria:</label>
      <select id="categoria" onchange="filtrarProdutos()">
        <option value="todas">Todas</option>
        <option value="Alimentos">Alimentos</option>
        <option value="Bebidas">Bebidas</option>
        <option value="Higiene">Higiene</option>
      </select>
    </div>
    <div class="products" id="listaProdutos">
      <?php if($produtos): ?>
        <?php foreach($produtos as $p): ?>
          <div class="product-card" data-categoria="<?= htmlspecialchars($p['categoria']) ?>">
            <img src="<?= htmlspecialchars($p['imagem'] ?: 'https://via.placeholder.com/150x100?text=Sem+Imagem') ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
            <div class="product-info">
              <div><?= htmlspecialchars($p['nome']) ?></div>
              <small><?= htmlspecialchars($p['mercado_nome']) ?></small>
              <a href="adicionar_carrinho.php?id=<?= $p['id'] ?>" class="add-btn">Adicionar ao Carrinho</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Nenhum produto encontrado.</p>
      <?php endif; ?>
    </div>
  </section>
</main>

<nav class="bottom-menu">
  <a href="index.php"><i>🏠</i> Início</a>
  <a href="mercados.php"><i>🏪</i> Mercados</a>
  <a href="#" class="active"><i>🧺</i> Produtos</a>
  <a href="carrinho.php"><i>🛒</i> Carrinho</a>
  <a href="perfil.php"><i>👤</i> Perfil</a>
</nav>

<script>
const menuBtn = document.getElementById('menuBtn');
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
menuBtn.addEventListener('click', () => {
  sidebar.classList.toggle('active');
  mainContent.classList.toggle('shifted');
});

function filtrarProdutos() {
  const categoria = document.getElementById('categoria').value.toLowerCase();
  const produtos = document.querySelectorAll('.product-card');
  produtos.forEach(p => {
    const cat = p.dataset.categoria.toLowerCase();
    if (categoria === 'todas' || cat === categoria) {
      p.style.display = 'flex';
    } else {
      p.style.display = 'none';
    }
  });
}
</script>

</body>
</html>
