<?php
require_once __DIR__ . '/../includes/functions.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$nome = htmlspecialchars($_SESSION['user']['nome']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sobre o Datavenci</title>
<style>
* {margin:0; padding:0; box-sizing:border-box; font-family:'Arial', sans-serif;}
body {background:#e6f0e9; color:#333;}

header {background:#176d2d; color:white; padding:15px 20px; display:flex; align-items:center; justify-content:space-between;}
header h1 {font-size:1.3rem;}
.back-btn {background:none; border:none; color:white; font-size:1.5rem; cursor:pointer;}

main {padding:20px; margin-bottom:80px;}
.card {background:white; border-radius:15px; padding:20px; box-shadow:0 2px 6px rgba(0,0,0,0.1); margin-bottom:20px;}
.card h2 {color:#176d2d; margin-bottom:15px;}
.card p {color:#555; line-height:1.5rem;}

.bottom-menu {position:fixed; bottom:0; left:0; width:100%; background:#176d2d; display:flex; justify-content:space-around; padding:10px 0; border-top:2px solid #144e21;}
.bottom-menu a {color:white; text-decoration:none; font-size:0.9rem; display:flex; flex-direction:column; align-items:center;}
.bottom-menu a:hover, .bottom-menu a.active {color:#b2ffb2; font-weight:bold;}
.bottom-menu i {font-size:20px; margin-bottom:3px;}

@media(max-width:768px){
    main {padding:15px;}
    .card {padding:15px;}
}
</style>
</head>
<body>

<header>
    <button class="back-btn" onclick="window.location.href='painel.php'">&#8592;</button>
    <h1>Sobre o Datavenci</h1>
</header>

<main>
    <section class="card">
        <h2>O que é o Datavenci?</h2>
        <p>O <strong>Datavenci</strong> é uma aplicação desenvolvida para gerenciar produtos e seus prazos de validade de forma simples e intuitiva. A plataforma permite que usuários e mercados acompanhem facilmente os produtos disponíveis, garantindo uma organização eficiente e evitando desperdícios.</p>
    </section>

    <section class="card">
        <h2>Funcionalidades principais</h2>
        <ul style="margin-left:20px; color:#555;">
            <li>Gerenciamento de produtos com detalhes de quantidade e validade.</li>
            <li>Controle de estoque e notificações de produtos próximos da validade.</li>
            <li>Visualização de mercados e produtos disponíveis.</li>
            <li>Carrinho de compras com opção de pedidos individuais ou em massa.</li>
            <li>Notificações em tempo real sobre pedidos e status de entrega.</li>
            <li>Chat de suporte com respostas automáticas usando IA.</li>
        </ul>
    </section>

    <section class="card">
        <h2>Nosso objetivo</h2>
        <p>O objetivo do <strong>Datavenci</strong> é tornar a gestão de produtos e prazos de validade mais eficiente e prática, tanto para os clientes quanto para os mercados. Buscamos facilitar o controle, reduzir desperdícios e melhorar a experiência do usuário na gestão de produtos perecíveis.</p>
    </section>
</main>


</body>
</html>
