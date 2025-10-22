<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/functions.php';
start_secure_session();

$csrf = generate_csrf_token();
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Datavenci</title>
<style>
body {
  margin: 0;
  font-family: "Poppins", Arial, sans-serif;
  background: linear-gradient(135deg, #0c5721, #2a9c44);
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}
.container {
  background-color: #e5f7e5;
  width: 380px;
  padding: 40px 35px;
  border-radius: 20px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
  text-align: center;
  animation: fadeIn 0.6s ease-out;
}
.logo {
  font-size: 52px;
  font-weight: 900;
  color: #0c5721;
  margin-bottom: 10px;
  letter-spacing: -2px;
}
h2 {
  color: #083b0f;
  font-size: 22px;
  margin-bottom: 30px;
}
.input-group {
  position: relative;
  margin-bottom: 20px;
}
.input-group input {
  width: 100%;
  padding: 12px 40px;
  border: none;
  border-bottom: 2px solid #b3e0b3;
  background: transparent;
  font-size: 14px;
  color: #083b0f;
  outline: none;
  transition: border-color 0.3s;
}
.input-group input:focus {
  border-color: #0c5721;
}
.input-group i {
  position: absolute;
  top: 50%;
  left: 10px;
  transform: translateY(-50%);
  color: #0c5721;
  font-size: 18px;
}
.btn {
  width: 100%;
  background-color: #0c5721;
  color: white;
  padding: 12px;
  border: none;
  border-radius: 25px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}
.btn:hover {
  background-color: #0e6d28;
}
.error {
  background-color: #c0392b;
  color: #fff;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 15px;
  font-size: 14px;
}
.success {
  background-color: #27ae60;
  color: white;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 15px;
  font-size: 14px;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-15px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
<div class="container">
  <div class="logo">DV</div>
  <h2>Entrar</h2>

  <?php if($error): ?>
    <div class="error"><?= e($error) ?></div>
  <?php endif; ?>
  
  <?php if($success): ?>
    <div class="success"><?= e($success) ?></div>
  <?php endif; ?>

  <form action="../backend/auth.php" method="post">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">

    <div class="input-group">
      <i>📧</i>
      <input type="email" name="email" placeholder="Digite seu Email" required>
    </div>

    <div class="input-group">
      <i>🔒</i>
      <input type="password" name="password" placeholder="Digite sua Senha" required>
    </div>

    <button class="btn" type="submit">Acessar</button>
  </form>

  <div style="margin-top:12px;color:#083b0f">
    Ainda não possui uma Conta? <a href="cadastro.php">Cadastre-se</a>
  </div>
</div>
</body>
</html>
