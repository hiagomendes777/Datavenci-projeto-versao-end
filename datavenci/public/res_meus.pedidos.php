<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['user']['id'];
$nome = htmlspecialchars($_SESSION['user']['nome']);

// Busca pedidos do usuário logado
try {
    $stmt = $pdo->prepare("
        SELECT p.id, p.data_pedido, p.status, m.nome AS mercado_nome, p.valor_total
        FROM pedidos p
        JOIN mercados m ON p.mercado_id = m.id
        WHERE p.usuario_id = ?
        ORDER BY p.data_pedido DESC
    ");
    $stmt->execute([$usuario_id]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $pedidos = [];
    $erro = "Erro ao carregar pedidos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meus Pedidos | Datavenci</title>
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
.card h2 {color: #176d2d; font-size: 1.2rem; margin-bottom: 10px;}
.status {padding: 5px 10px; border-radius: 8px; color: white; font-weight: bold; display: inline-block;}
.status.pendente {background: #b8860b;}
.status.aprovado {background: #176d2d;}
.status.entregue {background: #1b5e20;}
.status.cancelado {background: #b71c1c;}

.bottom-menu {position: fixed; bottom: 0; left: 0; width: 100%; background-color: #176d2d; display: flex; justify-content: space-around; padding: 10px 0; border-top: 2px solid #144e21;}
.bottom-menu a {color: white; text-decoration: none; font-size: 0.9rem; display: flex; flex-direction: column; align-items: center;}
.bottom-menu a:hover, .bottom-menu a.active {color: #b2ffb2; font-weight: bold;}
.bottom-menu i {font-size: 20px; margin-bottom: 3px;}

@media (max-width: 768px) {
  .sidebar {width: 200px;}
  main.shifted {margin-left: 200px;}
}


</style>
</head>
<body>

<div class="sidebar" id="sidebar">
  <ul>
    <li><a href="mercados.php">🏪 Mercados</a></li>
    <li><a href="#" class="active">📦 Meus Pedidos</a></li>
    <li><a href="carrinho.php">🛒 Carrinho</a></li>
    <li><a href="promocoes.php">🔥 Promoções</a></li>
    <li><a href="configuracoes.php">⚙️ Configurações</a></li>
    <li><a href="faq.php">❓ FAQ</a></li>
    <li><a href="sobre.php">ℹ️ Sobre</a></li>
    <li><a href="../backend/logout.php">🚪 Sair</a></li>
  </ul>
</div>

<header>
  <button class="back-btn" onclick="window.location.href='restaurante_dashboard.php'">&#8592;</button>
  <h1>Meus Pedidos</h1>
</header>

<main id="mainContent">
  <section class="card">
    <h2>📦 Meus Pedidos</h2>
    <?php if (!empty($pedidos)): ?>
      <?php foreach ($pedidos as $pedido): ?>
        <div class="card">
          <p><strong>Pedido #<?= htmlspecialchars($pedido['id']) ?></strong></p>
          <p>Mercado: <?= htmlspecialchars($pedido['mercado_nome']) ?></p>
          <p>Data: <?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></p>
          <p>Valor Total: <span class="highlight">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></span></p>
          <p>Status: 
            <span class="status <?= strtolower($pedido['status']) ?>">
              <?= ucfirst($pedido['status']) ?>
            </span>
          </p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Você ainda não possui pedidos.</p>
    <?php endif; ?>
  </section>
</main>



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
