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
  <title>Início - Datavenci</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <h2>Bem-vindo, <?= $nome; ?>!</h2>
  <p>Esta é a página inicial do seu painel Datavenci.</p>
</body>
</html>
