<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$nome = htmlspecialchars($_SESSION['user']['nome']);
$email = htmlspecialchars($_SESSION['user']['email']);
$tipo = htmlspecialchars($_SESSION['user']['tipo']);

if ($tipo === 'restaurante') {
    $tituloPainel = "Restaurante - Datavenci";
    $descricaoPainel = "Bem-vindo ao seu painel de controle Restaurante.";
} elseif ($tipo === 'mercado') {
    $tituloPainel = "Mercado - Datavenci";
    $descricaoPainel = "Bem-vindo ao seu painel de controle Mercado.";
} else {
    $tituloPainel = "Mercado - Datavenci";
    $descricaoPainel = "Bem-vindo ao seu painel de controle!";
}

if(!isset($_SESSION['show_cookie_banner'])){
    $_SESSION['show_cookie_banner'] = true;
}

// 🔹 Buscar todos os produtos do banco (forçado)
try {
    $stmt = $pdo->query("
        SELECT p.*, u.nome AS mercado_nome, c.nome AS categoria_nome
        FROM produtos p
        JOIN usuarios u ON p.usuario_id = u.id
        LEFT JOIN categorias c ON p.categoria_id = c.id
        ORDER BY p.created_at DESC
        LIMIT 50
    ");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtos = [];
  
}

// Restaurantes próximos (exemplo estático)
$restaurantes = [
    ['nome' => 'Mercado - Datavenci', 'distancia' => '1.2 km'],
];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel - Datavenci</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
  body { background-color: #e6f0e9; color: #333; }

  header { background-color: #176d2d; color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 15px; }
  .menu-btn { background: none; border: none; color: #fff; font-size: 22px; cursor: pointer; }
  .logo { font-weight: bold; font-size: 1.5rem; }

  .sidebar { position: fixed; left: -270px; top: 0; width: 250px; height: 100%; background: #333; color: white; padding-top: 60px; transition: left 0.3s; z-index: 100; }
  .sidebar.active { left: 0; }
  .sidebar ul { list-style: none; }
  .sidebar ul li { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); }
  .sidebar ul li a { color: white; text-decoration: none; display: flex; align-items: center; gap: 10px; }
  .sidebar ul li a:hover, .sidebar ul li a.active { color: #1db954; font-weight: bold; }

  main { padding: 20px; margin-bottom: 80px; transition: margin-left 0.3s; }
  main.shifted { margin-left: 250px; }

  .card { background-color: white; border-radius: 15px; padding: 15px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
  .card h2 { color: #176d2d; font-size: 1.2rem; }

  /* ===== Restaurantes ===== */
  .restaurantes { display: flex; flex-direction: column; gap: 10px; }
  .rest-card { background: #f7fff8; border: 1px solid #cde3d0; padding: 10px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; }
  .rest-card span { color: #555; }
  .btn-conectar { background: #176d2d; color: #fff; border: none; padding: 6px 10px; border-radius: 8px; cursor: pointer; }
  .btn-conectar:hover { background: #145a24; }

  /* ===== Produtos ===== */
  .produtos { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 10px; }
  .produto-card { background: #f7fff8; border: 1px solid #cde3d0; border-radius: 10px; padding: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
  .produto-card h3 { color: #176d2d; margin-bottom: 5px; font-size: 1.05rem; }
  .produto-card p { font-size: 0.9rem; color: #333; margin-bottom: 3px; }
  .ver-todos { display: block; text-align: right; margin-top: 10px; }
  .ver-todos a { color: #176d2d; text-decoration: none; font-weight: bold; }
  .ver-todos a:hover { text-decoration: underline; }

  /* ===== Dicas ===== */
  .tips ul { list-style: disc; margin-left: 20px; color: #333; }
  .tips li { margin: 5px 0; }

  /* ===== Bottom Menu ===== */
  .bottom-menu { position: fixed; bottom: 0; left: 0; width: 100%; background-color: #176d2d; display: flex; justify-content: space-around; padding: 10px 0; border-top: 2px solid #144e21; }
  .bottom-menu a { color: white; text-decoration: none; font-size: 0.9rem; display: flex; flex-direction: column; align-items: center; }
  .bottom-menu a:hover, .bottom-menu a.active { color: #b2ffb2; }


  /* ===== BANNER DE COOKIES ===== */
  #cookie-banner { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 500px; background-color: #176d2d; color: white; padding: 15px 20px; border-radius: 12px; display: <?= isset($_SESSION['show_cookie_banner']) && $_SESSION['show_cookie_banner'] ? 'flex' : 'none'; ?>; flex-direction: column; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999; }
  #cookie-banner p { text-align: center; font-size: 0.95rem; }
  #cookie-banner button { margin: 5px; padding: 8px 15px; border-radius: 8px; border: none; font-weight: bold; cursor: pointer; transition: all 0.3s; }
  #acceptCookies { background-color: #b2ffb2; color: #176d2d; }
  #rejectCookies { background-color: #fff; color: #176d2d; }
  #acceptCookies:hover { background-color: #9cf39c; }
  #rejectCookies:hover { background-color: #e0e0e0; }



  @media (max-width: 768px) {
    .sidebar { width: 200px; }
    main.shifted { margin-left: 200px; }
  }
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
  <ul>
    <li><a href="add_produto.php">➕ Novo Produto</a></li>
    <li><a href="meus_anuncios.php">📢 Meus Anúncios</a></li>
    <li><a href="tabelas.php">📋 Tabela de Produtos</a></li>
    <li><a href="notificacoes.php">🔔 Notificações</a></li>
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
    <h2>Olá, <?= $tituloPainel ?>!</h2>
    <p><?= $descricaoPainel ?></p>
  </section>

  <section class="card">
    <h2> Meus Mercados </h2>
    <div class="restaurantes">
      <?php foreach ($restaurantes as $r): ?>
        <div class="rest-card">
          <div><strong><?= $r['nome'] ?></strong><br><span><?= $r['distancia'] ?></span></div>
          <button class="btn-conectar">Conectado</button>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

<section class="card">
  <h2>📦 Meus Produtos Cadastrados</h2>
  <?php if ($produtos): ?>
    <div class="produtos">
      <?php foreach ($produtos as $p): ?>
        <div class="produto-card">
          <h3><?= htmlspecialchars($p['nome']) ?></h3>
          <p><strong>Categoria:</strong> <?= htmlspecialchars($p['categoria_nome'] ?? 'Não definida') ?></p>
          <p><strong>Quantidade:</strong> <?= htmlspecialchars($p['quantidade']) ?></p>
          <p><strong>Preço:</strong> R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
          <p><strong>Cadastrado em:</strong> <?= date('d/m/Y', strtotime($p['created_at'])) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="ver-todos">
      <a href="tabelas.php">➡️ Ver Todos</a>
    </div>
  <?php else: ?>
    <p style="color:#555;">Nenhum produto cadastrado ainda.</p>
  <?php endif; ?>
</section>


  <section class="card tips">
    <h2>💡 Dicas de Vendas</h2>
    <ul>
      <li>Mantenha seus produtos sempre atualizados e com imagens claras.</li>
      <li>Ofereça descontos em produtos próximos do vencimento.</li>
      <li>Conecte-se com restaurantes para escoar estoque mais rápido.</li>
    </ul>
  </section>
</main>

<!-- BANNER DE COOKIES -->
<div id="cookie-banner">
  <p>🍪 Este site utiliza cookies para melhorar sua experiência.</p>
  <div>
    <button id="acceptCookies">Aceitar</button>
    <button id="rejectCookies">Rejeitar</button>
  </div>
</div>


<nav class="bottom-menu">
  <a href="#" class="active">🏠 Início</a>
  <a href="add_produto.php">➕ Adicionar</a>
  <a href="meus_anuncios.php">📢 Meus Anúncios</a>
  <a href="tabelas.php">📋 Tabela</a>
  <a href="perfil.php">👤 Perfil</a>
</nav>
   
<script>
  const menuBtn = document.getElementById('menuBtn');
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');

  menuBtn.addEventListener('click', () => {
    sidebar.classList.toggle('active');
    mainContent.classList.toggle('shifted');
  });

// Cookies
const banner = document.getElementById('cookie-banner');
document.getElementById('acceptCookies').onclick = () => { fetch('../backend/cookies.php?accept=1'); banner.style.display = 'none'; }
document.getElementById('rejectCookies').onclick = () => { fetch('../backend/cookies.php?reject=1'); banner.style.display = 'none'; }
</script>

</script>
</body>
</html>
