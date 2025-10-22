<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Percentual de desconto para produtos próximos da validade
$percentualDesconto = 20;

$stmt = $pdo->prepare("
    SELECT DISTINCT p.id, p.nome, p.preco, p.imagem, p.quantidade AS estoque, p.vencimento, u.nome AS mercado, u.id AS mercado_id
    FROM produtos p
    JOIN usuarios u ON p.usuario_id = u.id
    WHERE p.vencimento <= DATE_ADD(CURDATE(), INTERVAL 5 DAY)
    ORDER BY p.vencimento ASC
");

$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcula preço com desconto
foreach ($produtos as &$p) {
    $p['preco_desconto'] = $p['preco'] * (1 - $percentualDesconto / 100);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Promoções | Datavenci</title>
<style>
* {margin:0; padding:0; box-sizing:border-box; font-family:'Arial',sans-serif;}
body {background:#f5f5f5; color:#333;}

header {background:#176d2d; color:white; padding:15px 20px; display:flex; align-items:center; justify-content:space-between;}
header h1 {font-size:1.3rem;}
.back-btn {background:none; border:none; color:white; font-size:1.5rem; cursor:pointer;}

main {padding:20px; margin-bottom:80px;}

.product-card {display:flex; justify-content:space-between; align-items:center; background:white; border-radius:12px; padding:15px; margin-bottom:15px; box-shadow:0 4px 8px rgba(0,0,0,0.08); transition:transform 0.2s;}
.product-card:hover {transform:translateY(-3px);}
.product-left {display:flex; gap:15px; align-items:center;}
.product-left img {width:120px; height:120px; object-fit:cover; border-radius:12px;}
.product-info {display:flex; flex-direction:column; gap:6px;}
.product-info h3 {color:#176d2d; font-size:1.2rem; font-weight:bold;}
.product-info span {font-size:0.9rem; color:#555;}
.vence-proximo {color:#dc3545; font-weight:bold;}
.preco-original {text-decoration:line-through; color:#999;}
.preco-desconto {color:#28a745; font-weight:bold;}

.product-actions {display:flex; flex-direction:column; gap:10px;}
.product-actions button {padding:10px 15px; border:none; border-radius:8px; cursor:pointer; font-weight:bold; font-size:0.9rem; transition:.3s; width:160px;}
.btn-carrinho {background:#007bff; color:white;}
.btn-carrinho:hover {background:#0056b3;}
.btn-pedido {background:#28a745; color:white;}
.btn-pedido:hover {background:#1e7e34;}

.bottom-menu {position: fixed; bottom:0; left:0; width:100%; background:#176d2d; display:flex; justify-content:space-around; padding:10px 0; border-top:2px solid #144e21;}
.bottom-menu a {color:white; text-decoration:none; font-size:0.9rem; display:flex; flex-direction:column; align-items:center;}
.bottom-menu a:hover, .bottom-menu a.active {color:#b2ffb2; font-weight:bold;}
.bottom-menu i {font-size:20px; margin-bottom:3px;}

@media(max-width:768px){
  .product-card {flex-direction:column; align-items:flex-start;}
  .product-left {flex-direction:row; gap:15px;}
  .product-actions {flex-direction:row; width:100%; justify-content:flex-start; gap:10px; margin-top:10px;}
  .product-actions button {width:48%;}
  .product-left img {width:100px; height:100px;}
}
</style>
</head>
<body>

<header>
  <button class="back-btn" onclick="window.location.href='restaurante_dashboard.php'">&#8592;</button>
  <h1>Promoções</h1>
</header>

<main>
  <?php if(empty($produtos)): ?>
    <p>Nenhum produto em promoção no momento.</p>
  <?php else: ?>
    <?php foreach($produtos as $p): ?>
      <div class="product-card">
        <div class="product-left">
          <img src="<?= htmlspecialchars($p['imagem'] ?? '../imagens/sem_imagem.png') ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
          <div class="product-info">
            <h3><?= htmlspecialchars($p['nome']) ?></h3>
            <span>Mercado: <?= htmlspecialchars($p['mercado']) ?></span>
            <span>Quantidade: <?= $p['estoque'] ?></span>
            <span>
              Preço: 
              <span class="preco-original">R$ <?= number_format($p['preco'],2,',','.') ?></span>
              <span class="preco-desconto">R$ <?= number_format($p['preco_desconto'],2,',','.') ?></span>
            </span>
            <span class="vence-proximo">Validade: <?= $p['vencimento'] ? date('d/m/Y', strtotime($p['vencimento'])) : '-' ?></span>
          </div>
        </div>
        <div class="product-actions">
          <button class="btn btn-carrinho" onclick="adicionarCarrinho(<?= $p['id'] ?>)">Adicionar ao Carrinho</button>
          <button class="btn btn-pedido" onclick="fazerPedido(<?= $p['id'] ?>)">Fazer Pedido</button>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</main>

<script>
function adicionarCarrinho(id){
  window.location.href='adicionar_carrinho.php?id='+id;
}
function fazerPedido(id){
  window.location.href='fazer_pedido.php?id='+id;
}
</script>

</body>
</html>
