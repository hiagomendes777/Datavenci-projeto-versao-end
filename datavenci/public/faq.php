<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$usuario_nome = htmlspecialchars($_SESSION['user']['nome']);

// Perguntas frequentes fixas (você pode puxar do banco)
$perguntas_frequentes = [
    ['pergunta' => 'Como faço para alterar meu endereço?', 'resposta' => 'Você pode alterar seu endereço em Configurações > Perfil.'],
    ['pergunta' => 'Como rastrear meu pedido?', 'resposta' => 'Vá em Meus Pedidos e clique no botão "Rastrear".'],
    ['pergunta' => 'Posso cancelar um pedido?', 'resposta' => 'Pedidos podem ser cancelados antes de serem aceitos pelo mercado.'],
    ['pergunta' => 'Como entrar em contato com o suporte?', 'resposta' => 'Envie uma mensagem pelo chat ou pelo e-mail suporte@datavenci.com.']
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FAQ - Datavenci</title>
<style>
* {margin:0; padding:0; box-sizing:border-box; font-family:'Arial', sans-serif;}
body {background:#e6f0e9; color:#333;}
header {background:#176d2d; color:white; padding:15px 20px; display:flex; align-items:center; justify-content:space-between;}
.logo {font-weight:bold; font-size:1.5rem;}
main {padding:20px; margin-bottom:80px;}
.card {background:white; border-radius:15px; padding:15px; margin-bottom:20px; box-shadow:0 2px 6px rgba(0,0,0,0.1);}
h2 {color:#176d2d; margin-bottom:15px;}
.faq-item {border-bottom:1px solid #ddd; padding:10px 0; cursor:pointer;}
.faq-item:hover {background:#f5f5f5;}
.faq-item h3 {font-size:1rem; color:#176d2d;}
.faq-item p {display:none; margin-top:5px; color:#555; font-size:0.9rem;}
.chat-container {margin-top:20px; border:1px solid #cde3d0; border-radius:10px; background:white; display:flex; flex-direction:column; height:400px; overflow:hidden;}
.chat-messages {flex:1; padding:10px; overflow-y:auto;}
.message {margin-bottom:10px; padding:8px 12px; border-radius:10px; max-width:80%;}
.user {background:#176d2d; color:white; align-self:flex-end;}
.bot {background:#f0f0f0; color:#333; align-self:flex-start;}
.chat-input {display:flex; border-top:1px solid #ddd;}
.chat-input input {flex:1; padding:10px; border:none; outline:none;}
.chat-input button {padding:10px 15px; background:#176d2d; color:white; border:none; cursor:pointer; transition:.3s;}
.chat-input button:hover {background:#145a24;}
.bottom-menu {position:fixed; bottom:0; left:0; width:100%; background:#176d2d; display:flex; justify-content:space-around; padding:10px 0; border-top:2px solid #144e21;}
.bottom-menu a {color:white; text-decoration:none; font-size:0.9rem; display:flex; flex-direction:column; align-items:center;}
.bottom-menu a:hover, .bottom-menu a.active {color:#b2ffb2; font-weight:bold;}
.bottom-menu i {font-size:20px; margin-bottom:3px;}
</style>
</head>
<body>

<header>
    <button class="back-btn" onclick="window.location.href='painel.php'">&#8592;</button>
    <h1>FAQ</h1>
</header>

<main>
    <section class="card">
        <h2>Perguntas Frequentes</h2>
        <?php foreach($perguntas_frequentes as $faq): ?>
        <div class="faq-item">
            <h3><?= htmlspecialchars($faq['pergunta']) ?></h3>
            <p><?= htmlspecialchars($faq['resposta']) ?></p>
        </div>
        <?php endforeach; ?>
    </section>

    <section class="card">
        <h2>Chat de Suporte IA</h2>
        <div class="chat-container">
            <div class="chat-messages" id="chatMessages"></div>
            <div class="chat-input">
                <input type="text" id="chatInput" placeholder="Digite sua pergunta...">
                <button onclick="enviarMensagem()">Enviar</button>
            </div>
        </div>
    </section>
</main>



<script>
// Toggle FAQ respostas
document.querySelectorAll('.faq-item').forEach(item => {
    item.addEventListener('click', () => {
        const p = item.querySelector('p');
        p.style.display = p.style.display === 'block' ? 'none' : 'block';
    });
});

// Chat IA (simulado)
function enviarMensagem(){
    const input = document.getElementById('chatInput');
    const mensagem = input.value.trim();
    if(!mensagem) return;

    const chat = document.getElementById('chatMessages');

    // Adiciona mensagem do usuário
    const userMsg = document.createElement('div');
    userMsg.className = 'message user';
    userMsg.textContent = mensagem;
    chat.appendChild(userMsg);
    chat.scrollTop = chat.scrollHeight;
    input.value = '';

    // Simula resposta IA
    setTimeout(() => {
        const botMsg = document.createElement('div');
        botMsg.className = 'message bot';
        botMsg.textContent = "Resposta automática: Ainda estou aprendendo a responder isso, mas logo você terá uma resposta completa!";
        chat.appendChild(botMsg);
        chat.scrollTop = chat.scrollHeight;
    }, 1000);
}
</script>

</body>
</html>
