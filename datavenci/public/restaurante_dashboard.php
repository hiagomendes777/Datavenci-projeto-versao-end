<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Dados do usuário
$nome = htmlspecialchars($_SESSION['user']['nome']);
$email = htmlspecialchars($_SESSION['user']['email']);
$tipo = htmlspecialchars($_SESSION['user']['tipo']);

// Define título conforme o tipo de usuário
if ($tipo === 'restaurante') {
    $tituloPainel = "Restaurante - Datavenci";
    $descricaoPainel = "Bem-vindo ao seu painel de controle Restaurante.";
} elseif ($tipo === 'mercado') {
    $tituloPainel = "Mercado - Datavenci";
    $descricaoPainel = "Bem-vindo ao seu painel de controle Mercado.";
} else {
    $tituloPainel = "Restaurante - Datavenci";
    $descricaoPainel = "Bem-vindo ao seu painel de controle!";
}


// Forçar banner de cookies
if(!isset($_SESSION['show_cookie_banner'])){
    $_SESSION['show_cookie_banner'] = true;
}

// 🔹 Buscar produtos (todos os mercados)
try {
    $stmt = $pdo->query("SELECT p.*, u.nome AS mercado_nome 
                         FROM produtos p 
                         JOIN usuarios u ON p.usuario_id = u.id 
                         ORDER BY p.created_at DESC LIMIT 10");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtos = [];
}

// 🔹 Buscar produtos próximos da validade para promoções
$percentualDesconto = 20; // desconto em %
$promocoes = [];

try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.nome AS mercado_nome
        FROM produtos p
        JOIN usuarios u ON p.usuario_id = u.id
        WHERE DATEDIFF(p.vencimento, CURDATE()) <= :dias
        AND DATEDIFF(p.vencimento, CURDATE()) >= 0
        ORDER BY p.vencimento ASC
    ");
    $stmt->execute([
        ':dias' => 3 // produtos com 3 dias ou menos de validade
    ]);
    $promocoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcula preço com desconto
    foreach ($promocoes as &$p) {
        $p['preco_desconto'] = $p['preco'] * (1 - $percentualDesconto/100);
    }
} catch (PDOException $e) {
    $promocoes = [];
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Restaurante - Datavenci</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
body { background-color: #e6f0e9; color: #333; }

/* ===== TOPO ===== */
header { background-color: #176d2d; color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 15px; }
.menu-btn { background: none; border: none; color: #fff; font-size: 22px; cursor: pointer; }
.logo { font-weight: bold; font-size: 1.5rem; letter-spacing: 1px; }

/* ===== MENU LATERAL ===== */
.sidebar { position: fixed; left: -270px; top: 0; width: 250px; height: 100%; background: #333; color: white; padding-top: 60px; transition: left 0.3s; z-index: 100; }
.sidebar.active { left: 0; }
.sidebar ul { list-style: none; }
.sidebar ul li { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.sidebar ul li a { color: white; text-decoration: none; display: flex; align-items: center; gap: 10px; }
.sidebar ul li a:hover, .sidebar ul li a.active { color: #1db954; font-weight: bold; }

/* ===== CONTEÚDO PRINCIPAL ===== */
main { padding: 20px; margin-bottom: 80px; transition: margin-left 0.3s; }
main.shifted { margin-left: 250px; }
.card { background-color: white; border-radius: 15px; padding: 15px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
.card h2 { color: #176d2d; font-size: 1.2rem; }
.highlight { color: #176d2d; font-weight: bold; }

/* ===== LISTAS ===== */
.products, .markets, .promos { display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px; }
.product-card, .market-card, .promo-card { min-width: 150px; background: #fff; border-radius: 15px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); overflow: hidden; flex-shrink: 0; display: flex; flex-direction: column; }
.product-card img, .market-card img, .promo-card img { width: 100%; height: 100px; object-fit: cover; }
.product-info { padding: 10px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
.product-info div { margin-bottom: 5px; font-weight: bold; color: #176d2d; }
.add-btn { text-align: center; padding: 5px 10px; background: #176d2d; color: #fff; border-radius: 8px; cursor: pointer; text-decoration: none; transition: 0.3s; margin-top: 5px; }
.add-btn:hover { background: #145a24; }

/* ===== MENU INFERIOR ===== */
.bottom-menu { position: fixed; bottom: 0; left: 0; width: 100%; background-color: #176d2d; display: flex; justify-content: space-around; padding: 10px 0; border-top: 2px solid #144e21; }
.bottom-menu a { color: white; text-decoration: none; font-size: 0.9rem; display: flex; flex-direction: column; align-items: center; }
.bottom-menu a:hover, .bottom-menu a.active { color: #b2ffb2; font-weight: bold; }
.bottom-menu i { font-size: 20px; margin-bottom: 3px; }

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

<!-- MENU LATERAL -->
<div class="sidebar" id="sidebar">
  <ul>
    <li><a href="mercados.php">🏪 Mercados</a></li>
    <li><a href="res_meus.pedidos.php">📦 Meus Pedidos</a></li>
    <li><a href="res_carrinho.php">🛒 Carrinho</a></li>
    <li><a href="promocoes.php">🔥 Promoções</a></li>
    <li><a href="configuracoes.php">⚙️ Configurações</a></li>
    <li><a href="faq.php">❓ FAQ</a></li>
    <li><a href="sobre.php">ℹ️ Sobre</a></li>
    <li><a href="../backend/logout.php">🚪 Sair</a></li>
  </ul>
</div>

<!-- TOPO -->
<header>
  <button class="menu-btn" id="menuBtn">☰</button>
  <div class="logo">DATAVENCI</div>
</header>

<!-- CONTEÚDO -->
<main id="mainContent">
  <section class="card">
    <h2>Olá, <?= $tituloPainel ?>!</h2>
<p><?= $descricaoPainel ?></p>

  </section>

<section class="card">
  <h2>
    Mercados Próximos
    <span style="float:right; font-size:0.8rem;">
      <a href="mercados.php" class="add-btn">Ver Todos</a>
    </span>
  </h2>
  <div class="markets">
    <?php
    // Exemplo de mercados, você pode puxar do banco de dados se tiver tabela de mercados
    $mercados = [
      ['id' => 1, 'nome' => 'Mercado - Datavenci', 'localizacao' => 'Endereço: Rua Central, nº 100 — Cidade Recife', 'imagem' => 'https://via.placeholder.com/150x100?text=Mercado+A'],
      // Adicione outros mercados aqui...
    ];
    foreach ($mercados as $m): ?>
      <div class="market-card">
        <img src="<?= htmlspecialchars($m['imagem']) ?>" alt="<?= htmlspecialchars($m['nome']) ?>">
        <div class="product-info">
          <div><?= htmlspecialchars($m['nome']) ?></div>
          <div style="font-size:0.85rem; color:#555; margin-top:3px;"><?= htmlspecialchars($m['localizacao']) ?></div>
          <a href="todos_produtos.php?id=<?= $m['id'] ?>" class="add-btn">Ver Produtos</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="card">
  <h2>
    Promoções Atuais do Mercado - Datavenci
    <span style="float:right; font-size:0.8rem;">
      <a href="promocoes.php" class="add-btn">Ver Todos</a>
    </span>
  </h2>
  <div class="promos">
    <?php if($promocoes): ?>
      <?php foreach($promocoes as $p): ?>
        <div class="promo-card">
          <img src="<?= htmlspecialchars($p['imagem'] ?: 'https://via.placeholder.com/150x100?text=Sem+Imagem') ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
          <div class="product-info">
            <div><?= htmlspecialchars($p['nome']) ?></div>
            <div>De: R$ <?= number_format($p['preco'],2,',','.') ?></div>
            <div>Por: R$ <?= number_format($p['preco_desconto'],2,',','.') ?></div>
            <a href="adicionar_carrinho.php?id=<?= $p['id'] ?>" class="add-btn">Adicionar ao Carrinho</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div style="color:#555; padding:10px;">Nenhuma promoção disponível no momento.</div>
    <?php endif; ?>
  </div>
</section>




<section class="card">
    <h2>Produtos em Destaque do Mercado - Datavenci
      <span style="float:right; font-size:0.8rem;">
        <a href="todos_produtos.php" class="add-btn">Ver Todos</a>
      </span>
    </h2>
    <div class="products">
      <?php if($produtos): ?>
        <?php foreach($produtos as $p): ?>
          <div class="product-card">
            <img src="<?= htmlspecialchars($p['imagem'] ?: 'https://via.placeholder.com/150x100?text=Sem+Imagem') ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
            <div class="product-info">
              <div><?= htmlspecialchars($p['nome']) ?></div>
              <a href="adicionar_carrinho.php?id=<?= $p['id'] ?>" class="add-btn">Adicionar ao Carrinho</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div style="color:#555; padding:10px;">Nenhum produto encontrado.</div>
      <?php endif; ?>
    </div>
</section>


<!-- MENU INFERIOR -->
<nav class="bottom-menu">
  <a href="restaurante_dashboard.php" class="active"><i>🏠</i> Início</a>
  <a href="promocoes.php"><i>🔥</i> Promoções</a>
  <a href="res_meus.pedidos.php"><i>📦</i> Pedidos</a>
  <a href="res_carrinho.php"><i>🛒</i> Carrinho</a>
  <a href="perfil.php"><i>👤</i> Perfil</a>
</nav>

<!-- BANNER DE COOKIES -->
<div id="cookie-banner">
  <p>🍪 Este site utiliza cookies para melhorar sua experiência.</p>
  <div>
    <button id="acceptCookies">Aceitar</button>
    <button id="rejectCookies">Rejeitar</button>
  </div>
</div>

<script>
// Menu lateral expandível
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

</body>
</html>
