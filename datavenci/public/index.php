<?php
require_once __DIR__ . '/includes/functions.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$nome = htmlspecialchars($_SESSION['user']['nome']);
$tipo = htmlspecialchars($_SESSION['user']['tipo']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel - Datavenci</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
  body { background-color: #e6f0e9; color: #333; }

  header { background-color: #176d2d; color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 15px; }
  .menu-btn { background: none; border: none; color: #fff; font-size: 22px; cursor: pointer; }
  .logo { font-weight: bold; font-size: 1.5rem; letter-spacing: 1px; }

  .sidebar { position: fixed; left: -270px; top: 0; width: 250px; height: 100%; background: #333; color: white; padding-top: 60px; transition: left 0.3s; z-index: 100; }
  .sidebar.active { left: 0; }
  .sidebar ul { list-style: none; }
  .sidebar ul li { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); }
  .sidebar ul li a { color: white; text-decoration: none; display: flex; align-items: center; gap: 10px; }
  .sidebar ul li a:hover, .sidebar ul li a.active { color: #1db954; font-weight: bold; }

  main { padding: 20px; margin-bottom: 80px; margin-left: 0; transition: margin-left 0.3s; }
  main.shifted { margin-left: 250px; }
  .card { background-color: white; border-radius: 15px; padding: 15px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
  .card h2 { color: #176d2d; font-size: 1.2rem; }
  .highlight { color: #176d2d; font-weight: bold; }

  .markets, .products { display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px; }
  .market-card, .product-card { min-width: 150px; background: #fff; border-radius: 15px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); overflow: hidden; flex-shrink: 0; }
  .market-card img, .product-card img { width: 100%; height: 100px; object-fit: cover; border-bottom: 1px solid #ccc; }
  .product-info, .market-info { padding: 10px; font-size: 0.9rem; color: #555; }
  .offer { background-color: rgba(255,255,200,0.7); padding: 5px; border-radius: 5px; font-size: 0.9rem; font-weight: bold; color: #176d2d; margin-top: 5px; display: inline-block; }

  .bottom-menu { position: fixed; bottom: 0; left: 0; width: 100%; background-color: #176d2d; display: flex; justify-content: space-around; padding: 10px 0; border-top: 2px solid #144e21; }
  .bottom-menu a { color: white; text-decoration: none; font-size: 0.9rem; display: flex; flex-direction: column; align-items: center; }
  .bottom-menu a:hover, .bottom-menu a.active { color: #b2ffb2; font-weight: bold; }
  .bottom-menu i { font-size: 20px; margin-bottom: 3px; }

  @media (max-width: 768px) { .sidebar { width: 200px; } main.shifted { margin-left: 200px; } }
</style>
</head>
<body>

<!-- MENU LATERAL -->
<div class="sidebar" id="sidebar">
  <ul>
    <li><a href="#" class="active">🏠 Início</a></li>
    <li><a href="add_produto.php">➕ Novo Produto</a></li>
    <li><a href="#">📦 Meus Anúncios</a></li>
    <li><a href="tabelas.php">📋 Tabela de Validade</a></li>
    <li><a href="#">🏪 Mercados</a></li>
    <li><a href="#">🔔 Notificações</a></li>
    <li><a href="#">⚙️ Configurações</a></li>
    <li><a href="#">❓ Perguntas Frequentes</a></li>
    <li><a href="#">ℹ️ Sobre</a></li>
    <li><a href="backend/logout.php">🚪 Sair</a></li>
  </ul>
</div>

<!-- TOPO -->
<header>
  <button class="menu-btn" id="menuBtn">☰</button>
  <div class="logo">DATAVENCI</div>
</header>

<!-- CONTEÚDO -->
<main id="mainContent">
  <!-- SAUDAÇÃO -->
  <section class="card">
    <h2>Olá, <span class="highlight"><?= $nome; ?></span>!</h2>
    <p>Bem-vindo ao seu painel de controle <strong><?= $tipo; ?></strong>.</p>
  </section>

  <!-- MERCADOS PRÓXIMOS -->
  <section class="card">
    <h2>Mercados Próximos <span style="float:right; font-size:0.8rem; color:#555;">Ver Todos</span></h2>
    <div class="markets">
      <div class="market-card">
        <img src="assets/images/mercado1.jpg" alt="Mercado 1">
        <div class="market-info">Mercado Central - Recife</div>
      </div>
      <div class="market-card">
        <img src="https://maps.googleapis.com/maps/api/staticmap?center=Recife,PE&zoom=12&size=400x200&key=YOUR_API_KEY" alt="Mapa">
        <div class="market-info">Mapa do Mercado</div>
      </div>
    </div>
  </section>

  <!-- PRODUTOS DISPONÍVEIS -->
  <section class="card">
    <h2>Produtos disponíveis <span style="float:right; font-size:0.8rem; color:#555;">Ver Todos</span></h2>
    <div class="products">
      <div class="product-card">
        <img src="assets/images/melao.jpg" alt="Melão">
        <div class="product-info">
          <div>Melão</div>
          <div class="offer">25% off</div>
        </div>
      </div>
      <div class="product-card">
        <img src="assets/images/mamao.jpg" alt="Mamão">
        <div class="product-info">
          <div>Mamão</div>
          <div class="offer">10% off</div>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- MENU INFERIOR -->
<nav class="bottom-menu">
  <a href="#" class="active"><i>🏠</i> Início</a>
  <a href="add_produto.php"><i>➕</i> Adicionar</a>
  <a href="#"><i>🔍</i> Pesquisa</a>
  <a href="tabelas.php"><i>📋</i> Tabela</a>
  <a href="#"><i>👤</i> Perfil</a>
</nav>

<script>
  const menuBtn = document.getElementById('menuBtn');
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');

  menuBtn.addEventListener('click', () => {
    sidebar.classList.toggle('active');
    mainContent.classList.toggle('shifted');
  });
</script>

</body>
</html>
