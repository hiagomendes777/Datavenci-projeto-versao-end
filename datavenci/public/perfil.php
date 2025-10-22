<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

// 🔒 Verifica login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// 🔽 Buscar dados atualizados do usuário
try {
    $stmt = $pdo->prepare("SELECT nome, email, tipo, tema, notificacoes_email 
                           FROM usuarios 
                           WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $_SESSION['user']['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $user = $_SESSION['user']; // fallback
}

$nome = htmlspecialchars($user['nome']);
$email = htmlspecialchars($user['email']);
$tipo = htmlspecialchars($user['tipo']);
$tema = htmlspecialchars($user['tema'] ?? 'claro');
$notif = $user['notificacoes_email'] ? 'Ativadas' : 'Desativadas';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Perfil - Datavenci</title>
  <style>
    body {
      font-family: "Segoe UI", Arial, sans-serif;
      background: linear-gradient(180deg, #e6f0e9, #cde3d2);
      margin: 0;
      padding: 0;
      color: #333;
    }

    header {
      background-color: #176d2d;
      color: #fff;
      padding: 15px;
      text-align: center;
      font-size: 1.4rem;
      font-weight: bold;
      letter-spacing: 1px;
    }

    .profile-container {
      max-width: 450px;
      background: #fff;
      margin: 40px auto 100px auto;
      padding: 25px;
      border-radius: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.2s;
    }

    .profile-container:hover {
      transform: scale(1.02);
    }

    .profile-header img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #176d2d;
      margin-bottom: 10px;
      background-color: #fff;
    }

    h2 {
      margin: 10px 0 5px 0;
      color: #176d2d;
    }

    .profile-info {
      text-align: left;
      margin-top: 20px;
    }

    .profile-info div {
      margin-bottom: 12px;
      font-size: 1rem;
      background: #f5f9f6;
      padding: 10px 15px;
      border-radius: 10px;
    }

    .profile-info span {
      font-weight: bold;
      color: #176d2d;
    }

    .profile-actions {
      margin-top: 25px;
      display: flex;
      justify-content: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn {
      text-decoration: none;
      background-color: #176d2d;
      color: #fff;
      padding: 10px 18px;
      border-radius: 10px;
      transition: background 0.3s;
      font-weight: bold;
    }

    .btn:hover {
      background-color: #145a24;
    }

    .btn-outline {
      background-color: #fff;
      color: #176d2d;
      border: 2px solid #176d2d;
    }

    .btn-outline:hover {
      background-color: #176d2d;
      color: #fff;
    }

    .bottom-menu a {
      color: white;
      text-decoration: none;
      font-size: 0.9rem;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .bottom-menu a:hover, .bottom-menu a.active {
      color: #b2ffb2;
      font-weight: bold;
    }

    .bottom-menu i {
      font-size: 20px;
      margin-bottom: 3px;
    }
  </style>
</head>
<body>

<header>Meu Perfil</header>

<div class="profile-container">
  <div class="profile-header">
    <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Foto de perfil">
    <h2><?= $nome; ?></h2>
  </div>

  <div class="profile-info">
    <div><span>Email:</span> <?= $email; ?></div>
    <div><span>Tipo de Usuário:</span> <?= ucfirst($tipo); ?></div>
    <div><span>Tema:</span> <?= ucfirst($tema); ?></div>
    <div><span>Notificações por Email:</span> <?= $notif; ?></div>
  </div>

  <div class="profile-actions">
    <a href="configuracoes.php" class="btn">⚙️ Editar Perfil</a>
    <a href="../backend/logout.php" class="btn btn-outline">🚪 Sair</a>
  </div>
</div>

</body>
</html>
