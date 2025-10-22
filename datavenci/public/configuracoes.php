<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

// 🔒 Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];

// 🔽 Buscar dados do usuário no banco
try {
    $stmt = $pdo->prepare("SELECT nome, email, tipo, tema, notificacoes_email FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        throw new Exception("Usuário não encontrado no banco de dados.");
    }
} catch (Exception $e) {
    die("Erro ao buscar dados do usuário: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Configurações - Datavenci</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
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

  main { padding: 20px; margin-left: 0; transition: margin-left 0.3s; }
  main.shifted { margin-left: 250px; }

  .card { background-color: white; border-radius: 15px; padding: 15px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
  .card h2 { color: #176d2d; font-size: 1.2rem; margin-bottom: 10px; }

  .form-group { margin-bottom: 15px; }
  label { font-weight: bold; color: #176d2d; display: block; margin-bottom: 5px; }
  input[type="text"], input[type="email"], input[type="password"], select {
    width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;
  }
  button {
    background-color: #176d2d; color: white; border: none;
    padding: 10px 15px; border-radius: 8px; cursor: pointer;
    transition: 0.3s;
  }
  button:hover { background-color: #145a24; }

  .bottom-menu { position: fixed; bottom: 0; left: 0; width: 100%; background-color: #176d2d; display: flex; justify-content: space-around; padding: 10px 0; border-top: 2px solid #144e21; }
  .bottom-menu a { color: white; text-decoration: none; font-size: 0.9rem; display: flex; flex-direction: column; align-items: center; }
  .bottom-menu a:hover, .bottom-menu a.active { color: #b2ffb2; font-weight: bold; }
  .bottom-menu i { font-size: 20px; margin-bottom: 3px; }

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
    <li><a href="painel.php"><span>🏠</span> Início</a></li>
    <li><a href="add_produto.php"><span>➕</span> Novo Produto</a></li>
    <li><a href="meus_anuncios.php"><span>📢</span> Meus Anúncios</a></li>
    <li><a href="tabelas.php"><span>📋</span> Tabela</a></li>
    <li><a href="notificacoes.php"><span>🔔</span> Notificações</a></li>
    <li><a href="configuracoes.php" class="active"><span>⚙️</span> Configurações</a></li>
    <li><a href="faq.php"><span>❓</span> FAQ</a></li>
    <li><a href="sobre.php"><span>ℹ️</span> Sobre</a></li>
    <li><a href="../backend/logout.php"><span>🚪</span> Sair</a></li>
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
    <h2>Configurações da Conta</h2>

    <form method="POST" action="salvar_config.php">
      <div class="form-group">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
      </div>

      <div class="form-group">
        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
      </div>

      <div class="form-group">
        <label for="senha">Nova Senha:</label>
        <input type="password" name="senha" id="senha" placeholder="Digite nova senha (opcional)">
      </div>

      <div class="form-group">
        <label for="tema">Tema do Painel:</label>
        <select name="tema" id="tema">
          <option value="claro" <?= $usuario['tema'] === 'claro' ? 'selected' : '' ?>>Claro</option>
          <option value="escuro" <?= $usuario['tema'] === 'escuro' ? 'selected' : '' ?>>Escuro</option>
        </select>
      </div>

      <div class="form-group">
        <label for="notificacoes_email">Notificações por E-mail:</label>
        <select name="notificacoes_email" id="notificacoes_email">
          <option value="1" <?= $usuario['notificacoes_email'] ? 'selected' : '' ?>>Ativar</option>
          <option value="0" <?= !$usuario['notificacoes_email'] ? 'selected' : '' ?>>Desativar</option>
        </select>
      </div>

      <button type="submit">💾 Salvar Alterações</button>
    </form>
  </section>

  <section class="card">
    <h2>Outras Ações</h2>
    <button style="background:#d9534f;">🗑 Excluir Conta</button>
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
