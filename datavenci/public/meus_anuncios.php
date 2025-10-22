<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

start_secure_session();
if (!isset($_SESSION['user'])) { 
    header('Location: login.php'); 
    exit; 
}

$nome = htmlspecialchars($_SESSION['user']['nome']);

// Limite de dias para produtos próximos da validade
$diasProxVencer = 3;
$percentualDesconto = 20;

try {
    // 🔹 Buscar todos os produtos próximos da validade
    $stmt = $pdo->prepare("
        SELECT p.*, u.nome AS mercado_nome 
        FROM produtos p
        JOIN usuarios u ON p.usuario_id = u.id
        WHERE DATEDIFF(vencimento, CURDATE()) <= :dias
        AND DATEDIFF(vencimento, CURDATE()) >= 0
        ORDER BY vencimento ASC
        LIMIT 50
    ");
    $stmt->execute([
        ':dias' => $diasProxVencer
    ]);
    $anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcula preço com desconto
    foreach ($anuncios as &$p) {
        $p['preco_desconto'] = $p['preco'] * (1 - $percentualDesconto / 100);
    }

} catch (PDOException $e) {
    die("Erro ao buscar produtos: " . $e->getMessage());
}

function getStatus($vencimento) {
    $hoje = date('Y-m-d');
    return ($vencimento >= $hoje) ? 'Ativo' : 'Expirado';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meus Anúncios - Datavenci</title>
<style>
:root {
  --verde-escuro: #0d4d26;
  --verde-medio: #1e8b43;
  --verde-claro: #b5f2c2;
  --branco: #fff;
  --vermelho: #e74c3c;
  --cinza: #f4f4f4;
}
* { box-sizing: border-box; margin:0; padding:0; font-family:"Poppins", sans-serif; }

body {
  background: linear-gradient(135deg, var(--verde-escuro), var(--verde-medio));
  color: var(--branco);
  min-height: 100vh;
  padding: 20px;
  display: flex;
  flex-direction: column;
}

/* Top bar */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: rgba(255,255,255,0.1);
  padding: 15px 20px;
  border-radius: 12px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
  margin-bottom: 25px;
}

.topbar .left a {
  color: var(--branco);
  text-decoration: none;
  font-size: 22px;
  font-weight: bold;
  transition: 0.3s;
}
.topbar .left a:hover { color: var(--verde-claro); }

.topbar .title {
  font-size: 20px;
  font-weight: 600;
}

.add-btn {
  background-color: var(--verde-claro);
  color: var(--verde-escuro);
  padding: 10px 18px;
  border-radius: 25px;
  font-weight: bold;
  text-decoration: none;
  transition: 0.3s;
}
.add-btn:hover { background-color: #9de6af; }

/* Grid moderno */
.container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
}

.card {
  background: var(--branco);
  color: var(--verde-escuro);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  display: flex;
  flex-direction: column;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.25);
}

.card img {
  width: 100%;
  height: 140px;
  object-fit: cover;
}

.card-content {
  padding: 12px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.card h3 {
  font-size: 16px;
  margin-bottom: 6px;
  color: var(--verde-escuro);
}

.card p {
  font-size: 13px;
  margin-bottom: 3px;
  color: #333;
}

.card .preco {
  font-weight: bold;
  color: var(--vermelho);
}

.card .preco-original {
  text-decoration: line-through;
  font-size: 12px;
  color: #555;
}

.status {
  font-weight: bold;
  margin-top: 6px;
}
.status.Ativo { color: #27ae60; }
.status.Expirado { color: var(--vermelho); }

.actions {
  display: flex;
  justify-content: space-between;
  padding: 10px 12px;
}

.actions a {
  text-decoration: none;
  font-weight: bold;
  border-radius: 8px;
  padding: 6px 10px;
  transition: 0.3s;
}

.delete-btn {
  background-color: var(--vermelho);
  color: white;
}
.delete-btn:hover { background-color: #c0392b; }

.empty {
  text-align: center;
  margin-top: 60px;
  font-size: 18px;
  color: #e0e0e0;
}

@media (max-width: 600px) {
  .topbar { flex-direction: column; gap: 10px; }
  .container { grid-template-columns: 1fr; }
}
</style>
<script>
function confirmarExclusao(url) {
    if(confirm("Tem certeza que deseja excluir este anúncio?")) {
        window.location.href = url;
    }
}
</script>
</head>
<body>

<div class="topbar">
  <div class="left"><a href="painel.php">← Voltar</a></div>
  <div class="title">📦 Meus Anúncios Próximos do Vencimento</div>
  <a href="add_produto.php" class="add-btn">+ Novo Produto</a>
</div>

<?php if($anuncios): ?>
<div class="container">
  <?php foreach($anuncios as $a): ?>
    <div class="card">
      <?php $img = $a['imagem'] ?? 'https://via.placeholder.com/250x160?text=Produto'; ?>
      <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($a['nome']) ?>">
      <div class="card-content">
        <h3><?= htmlspecialchars($a['nome']) ?></h3>
        <p><strong>Código:</strong> <?= htmlspecialchars($a['codigo'] ?? '-') ?></p>
        <p><strong>Lote:</strong> <?= htmlspecialchars($a['lote'] ?? '-') ?></p>
        <p><strong>Quantidade:</strong> <?= htmlspecialchars($a['quantidade']) ?></p>
        <p>
          <span class="preco">R$ <?= number_format($a['preco_desconto'],2,',','.') ?></span>
          <span class="preco-original">R$ <?= number_format($a['preco'],2,',','.') ?></span>
        </p>
        <p><strong>Validade:</strong> <?= htmlspecialchars($a['vencimento']) ?></p>
        <p class="status <?= getStatus($a['vencimento']) ?>"><?= getStatus($a['vencimento']) ?></p>
      </div>

      <div class="actions">
        <a href="editar_produto.php?id=<?= $a['id'] ?>" class="add-btn">Editar</a>
        <a href="javascript:void(0);" class="delete-btn" onclick="confirmarExclusao('excluir_produto.php?id=<?= $a['id'] ?>')">Excluir</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
  <div class="empty">Nenhum produto próximo da validade.</div>
<?php endif; ?>

</body>
</html>
