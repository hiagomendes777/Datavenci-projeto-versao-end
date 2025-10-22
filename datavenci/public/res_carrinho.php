<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['user']['id'] ?? null;
$carrinhoSessao = $_SESSION['carrinho'] ?? [];
$carrinho = [];

// Buscar dados completos do produto no banco
if (!empty($carrinhoSessao)) {
    $ids = implode(',', array_keys($carrinhoSessao));
    $stmt = $pdo->query("
        SELECT p.id, p.nome, p.quantidade AS estoque, p.preco, p.imagem, p.vencimento, 
               u.nome AS mercado, u.id AS mercado_id
        FROM produtos p
        LEFT JOIN usuarios u ON p.usuario_id = u.id
        WHERE p.id IN ($ids)
    ");
    $produtosBanco = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($produtosBanco as $p) {
        $id = $p['id'];
        $carrinho[$id] = [
            'id' => $id,
            'nome' => $p['nome'],
            'quantidade' => $carrinhoSessao[$id]['quantidade'] ?? 1,
            'preco' => $p['preco'],
            'imagem' => $p['imagem'],
            'vencimento' => $p['vencimento'],
            'mercado' => $p['mercado'],
            'mercado_id' => $p['mercado_id']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meu Carrinho | Datavenci</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Arial',sans-serif; }
body { background:#e6f0e9; color:#333; }

header { background:#176d2d; color:white; padding:15px 25px; display:flex; align-items:center; justify-content:space-between; }
header h1 { font-size:1.3rem; }
.back-btn { background:none; border:none; color:white; font-size:1.5rem; cursor:pointer; }

main { padding:20px; margin-bottom:80px; }

.product-item { display:flex; align-items:center; gap:15px; border:1px solid #cde3d0; border-radius:12px; padding:10px; margin-bottom:12px; background:white; width:100%; }
.product-item img { width:120px; height:100px; object-fit:cover; border-radius:8px; }
.product-details { flex:1; display:flex; justify-content:space-between; align-items:center; gap:10px; }
.info { display:flex; flex-direction:column; gap:4px; flex:2; }
.info h3 { color:#176d2d; margin:0; font-size:1.1rem; }
.info span { font-size:0.9rem; color:#555; }

.actions { display:flex; flex-direction:column; gap:5px; flex:1; align-items:flex-end; }
.btn { padding:6px 10px; border:none; border-radius:8px; cursor:pointer; font-weight:bold; transition:.3s; font-size:0.9rem; }
.btn-fazer-pedido { background:#007bff; color:white; }
.btn-fazer-pedido:hover { background:#0056b3; }
.btn-remover { background:#dc3545; color:white; }
.btn-remover:hover { background:#a71d2a; }

.checkbox-select { width:20px; height:20px; }

#fazerPedidoSelecionados { margin-top:10px; padding:12px 15px; border:none; border-radius:10px; background:#176d2d; color:white; font-weight:bold; cursor:pointer; transition:.3s; }
#fazerPedidoSelecionados:hover { background:#145a24; }

.bottom-menu {position: fixed; bottom:0; left:0; width:100%; background-color:#176d2d; display:flex; justify-content:space-around; padding:10px 0; border-top:2px solid #144e21;}
.bottom-menu a {color:white; text-decoration:none; font-size:0.9rem; display:flex; flex-direction:column; align-items:center;}
.bottom-menu a:hover, .bottom-menu a.active {color:#b2ffb2; font-weight:bold;}
.bottom-menu i {font-size:20px; margin-bottom:3px;}

@media(max-width:768px){
  .product-item { flex-direction: column; align-items:flex-start; }
  .actions { flex-direction:row; width:100%; justify-content:flex-start; gap:10px; margin-top:10px; }
  .product-item img { width:100%; height:auto; }
}
</style>
</head>
<body>

<header>
  <button class="back-btn" onclick="window.location.href='restaurante_dashboard.php'">&#8592;</button>
  <h1>Meu Carrinho</h1>
</header>

<main>
  <?php if(empty($carrinho)): ?>
    <p>Seu carrinho está vazio 🛒</p>
  <?php else: ?>
    <div id="carrinhoContainer">
      <?php foreach($carrinho as $id => $item): ?>
        <div class="product-item" data-id="<?= $id ?>">
          <input type="checkbox" class="checkbox-select" name="produtos[]" value="<?= $id ?>">
          <img src="<?= htmlspecialchars($item['imagem'] ?? '../imagens/sem_imagem.png') ?>" alt="<?= htmlspecialchars($item['nome']) ?>">
          <div class="product-details">
            <div class="info">
              <h3><?= htmlspecialchars($item['nome']) ?></h3>
              <span>Mercado: <?= htmlspecialchars($item['mercado']) ?></span>
              <span>Quantidade: <input type="number" class="quantidade-produto" data-id="<?= $id ?>" min="1" value="<?= $item['quantidade'] ?>"></span>
              <span>Preço: R$ <?= number_format($item['preco'],2,',','.') ?></span>
              <span>Validade: <?= $item['vencimento'] ? date('d/m/Y', strtotime($item['vencimento'])) : '-' ?></span>
            </div>
            <div class="actions">
              <button type="button" class="btn btn-fazer-pedido" onclick="fazerPedidoUnico(<?= $id ?>)">Fazer Pedido</button>
              <button type="button" class="btn btn-remover" onclick="removerProduto(<?= $id ?>)">Remover</button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <button type="button" id="fazerPedidoSelecionados">🧾 Fazer Pedido Selecionados</button>
    </div>
  <?php endif; ?>
</main>

<script>
// Pedido único via AJAX
function fazerPedidoUnico(id){
    if(!confirm('Deseja realmente fazer o pedido deste produto?')) return;

    let quantidade = parseInt(document.querySelector('.quantidade-produto[data-id="'+id+'"]').value) || 1;

    fetch('ajax_fazer_pedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ produtos: [id], quantidades: { [id]: quantidade } })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            alert('✅ Pedido realizado com sucesso!');
            location.reload();
        } else {
            alert('❌ Erro ao enviar pedido: ' + data.msg);
        }
    })
    .catch(err => alert('Erro na comunicação com o servidor.'));
}

// Pedido em massa via AJAX
document.getElementById('fazerPedidoSelecionados').addEventListener('click', function(){
    let checkboxes = document.querySelectorAll('input[name="produtos[]"]:checked');
    if(checkboxes.length === 0){
        alert('Selecione pelo menos um produto!');
        return;
    }

    let produtos = Array.from(checkboxes).map(cb => cb.value);
    let quantidades = {};
    produtos.forEach(id => {
        quantidades[id] = parseInt(document.querySelector('.quantidade-produto[data-id="'+id+'"]').value) || 1;
    });

    fetch('ajax_fazer_pedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ produtos, quantidades })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            alert('✅ Pedido em massa realizado com sucesso!');
            location.reload();
        } else {
            alert('❌ Erro ao enviar pedido: ' + data.msg);
        }
    })
    .catch(err => alert('Erro ao enviar pedido.'));
});

function removerProduto(id){
    if(confirm('Deseja remover este produto do carrinho?')){
        window.location.href = 'remover_carrinho.php?id=' + id;
    }
}
