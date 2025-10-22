<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// ID do mercado logado
$mercado_id = $_SESSION['user']['id'];
$nome_usuario = htmlspecialchars($_SESSION['user']['nome']);

// Buscar notificações do mercado
$stmt = $pdo->prepare("
    SELECT n.*, p.quantidade, p.valor_total, p.status AS status_pedido
    FROM notificacoes n
    LEFT JOIN pedidos p ON n.pedido_id = p.id
    WHERE n.mercado_id = ?
    ORDER BY n.data_criacao DESC
");
$stmt->execute([$mercado_id]);
$notificacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notificações | Datavenci</title>
<style>
body {
  font-family: Arial, sans-serif;
  background-color: #f0f9f2;
  margin: 0;
  padding: 0;
  color: #333;
}
header {
  background-color: #176d2d;
  color: white;
  padding: 15px 25px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
header h1 {
  font-size: 1.3rem;
}
.back-btn {
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
}
main {
  padding: 20px;
}
.notificacao {
  background: white;
  border-left: 5px solid #176d2d;
  border-radius: 10px;
  padding: 15px;
  margin-bottom: 12px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  transition: background 0.3s;
}
.notificacao.nova {
  background: #e7fbe7;
}
.notificacao small {
  color: #666;
}
.status {
  font-weight: bold;
  text-transform: capitalize;
}
.status.pendente { color: #f0ad4e; }
.status.aceito { color: #0275d8; }
.status.enviado { color: #5bc0de; }
.status.entregue { color: #5cb85c; }
.status.cancelado { color: #d9534f; }

.btn-limpar {
  margin-top: 20px;
  background: #dc3545;
  color: white;
  border: none;
  padding: 10px 14px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
}
.btn-limpar:hover {
  background: #a71d2a;
}
</style>
</head>
<body>

<header>
  <button class="back-btn" onclick="window.location.href='painel.php'">&#8592;</button>
  <h1>Notificações</h1>
</header>

<main>
  <?php if (empty($notificacoes)): ?>
      <p>📭 Nenhuma notificação no momento.</p>
  <?php else: ?>
      <?php foreach ($notificacoes as $n): ?>
          <div class="notificacao <?= $n['lida'] ? '' : 'nova' ?>">
              <h3><?= htmlspecialchars($n['titulo']) ?></h3>
              <p><?= htmlspecialchars($n['mensagem']) ?></p>
              <?php if (!empty($n['quantidade'])): ?>
                  <p>📦 Quantidade: <?= $n['quantidade'] ?> | 💰 Valor: R$ <?= number_format($n['valor_total'], 2, ',', '.') ?></p>
              <?php endif; ?>
              <p class="status <?= htmlspecialchars($n['status']) ?>">Status: <?= htmlspecialchars($n['status_pedido']) ?></p>
              <small>📅 <?= date('d/m/Y H:i', strtotime($n['data_criacao'])) ?></small>
          </div>
      <?php endforeach; ?>
      <form method="post" action="notificacoes_ler.php">
        <button type="submit" class="btn-limpar">Marcar todas como lidas</button>
      </form>
  <?php endif; ?>
</main>

</body>
</html>
