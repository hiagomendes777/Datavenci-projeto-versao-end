<?php
// menu.php — Menu lateral dinâmico do painel

// Detecta página atual para marcar como "ativa"
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
  <div class="logo">DATAVENCI</div>
  <ul>
    <li><a href="home.php" class="<?= $paginaAtual === 'home.php' ? 'active' : '' ?>">🏠 Início</a></li>
    <li><a href="add_produto.php" class="<?= $paginaAtual === 'add_produto.php' ? 'active' : '' ?>">➕ Novo Produto</a></li>
    <li><a href="meus_anuncios.php" class="<?= $paginaAtual === 'meus_anuncios.php' ? 'active' : '' ?>">📢 Meus Anúncios</a></li>
    <li><a href="tabela_produtos.php" class="<?= $paginaAtual === 'tabela_produtos.php' ? 'active' : '' ?>">📋 Tabela de Produtos</a></li>
    <li><a href="categorias.php" class="<?= $paginaAtual === 'categorias.php' ? 'active' : '' ?>">📂 Categorias</a></li>
    <li><a href="mercados.php" class="<?= $paginaAtual === 'mercados.php' ? 'active' : '' ?>">🏪 Mercados</a></li>
    <li><a href="notificacoes.php" class="<?= $paginaAtual === 'notificacoes.php' ? 'active' : '' ?>">🔔 Notificações</a></li>
    <li><a href="configuracoes.php" class="<?= $paginaAtual === 'configuracoes.php' ? 'active' : '' ?>">⚙️ Configurações</a></li>
    <li><a href="faq.php" class="<?= $paginaAtual === 'faq.php' ? 'active' : '' ?>">💬 FAQ</a></li>
    <li><a href="sobre.php" class="<?= $paginaAtual === 'sobre.php' ? 'active' : '' ?>">ℹ️ Sobre</a></li>
    <li><a href="../backend/logout.php">🚪 Sair</a></li>
  </ul>
</div>

<style>
  .sidebar { width: 220px; background: #176d2d; color: white; padding: 20px 10px; height: 100vh; position: fixed; top: 0; left: 0; overflow-y: auto; }
  .logo { font-size: 22px; font-weight: bold; margin-bottom: 30px; text-align: center; }
  .sidebar ul { list-style: none; padding: 0; }
  .sidebar ul li { margin-bottom: 5px; }
  .sidebar ul li a {
    display: flex; align-items: center; gap: 8px;
    padding: 12px;
    border-radius: 6px;
    text-decoration: none;
    color: white;
    font-size: 14px;
    transition: background 0.2s, font-weight 0.2s;
  }
  .sidebar ul li a:hover { background: rgba(255, 255, 255, 0.2); }
  .sidebar ul li a.active {
    background: white;
    color: #176d2d;
    font-weight: bold;
  }

  @media (max-width: 768px) {
    .sidebar { width: 60px; padding: 20px 5px; }
    .logo { font-size: 16px; }
    .sidebar ul li a { font-size: 12px; justify-content: center; }
  }
</style>
