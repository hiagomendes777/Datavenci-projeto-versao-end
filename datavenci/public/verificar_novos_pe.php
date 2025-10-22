<?php
// Arquivo: verificar_notificacoes_mercado.php

// 1. Define o ID do mercado logado (ESTE É CRUCIAL!)
$mercado_logado_id = 456; // **Substitua pela variável de sessão do mercado logado**

// 2. Conexão com o Banco de Dados (Substitua as credenciais)
$conexao = new mysqli("localhost", "usuario", "senha", "seu_banco");

// 3. Consulta por novas notificações (lida = 0) para este mercado
$sql = "SELECT id, pedido_id, titulo, mensagem, data_criacao 
        FROM notificacoes 
        WHERE mercado_id = ? AND lida = 0 
        ORDER BY data_criacao DESC";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $mercado_logado_id);
$stmt->execute();
$resultado = $stmt->get_result();

$notificacoes = [];
if ($resultado->num_rows > 0) {
    while ($linha = $resultado->fetch_assoc()) {
        $notificacoes[] = $linha;
    }
}

// 4. Retorna a resposta em JSON
header('Content-Type: application/json');
echo json_encode(["novas_notificacoes" => $notificacoes, "quantidade" => count($notificacoes)]);

$stmt->close();
$conexao->close();
?>