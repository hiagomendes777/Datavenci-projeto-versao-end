<?php
require_once __DIR__ . '/../includes/functions.php';
start_secure_session();
$csrf = generate_csrf_token();
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cadastro - Datavenci</title>
<style>
/* ======== Fundo ======== */
body {
  margin: 0;
  font-family: "Poppins", Arial, sans-serif;
  background: linear-gradient(135deg, #0c5721, #2a9c44);
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* ======== Cartão ======== */
.container {
  background-color: #e5f7e5;
  width: 380px;
  padding: 40px 35px;
  border-radius: 20px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  text-align: center;
  animation: fadeIn 0.6s ease-out;
}

/* ======== Logo ======== */
.logo {
  font-size: 52px;
  font-weight: 900;
  color: #0c5721;
  margin-bottom: 10px;
  letter-spacing: -2px;
}

/* ======== Título ======== */
h2 {
  color: #083b0f;
  font-size: 22px;
  margin-bottom: 30px;
}

/* ======== Inputs ======== */
.input-group {
  position: relative;
  margin-bottom: 20px;
}

.input-group input {
  width: 100%;
  padding: 12px 40px 12px 40px;
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

/* ======== Radio buttons ======== */
.radio-group {
  display: flex;
  justify-content: space-around;
  margin-bottom: 20px;
  color: #083b0f;
  font-weight: 500;
}

/* ======== Botão ======== */
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

/* ======== Login com Google ======== */
.btn-google {
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #fff;
  border: 2px solid #b3e0b3;
  color: #0c5721;
  border-radius: 25px;
  padding: 10px;
  font-weight: 600;
  gap: 8px;
  width: 100%;
  transition: background 0.3s;
  margin-top: 15px;
}

.btn-google:hover {
  background-color: #f3fff3;
}

.btn-google img {
  width: 20px;
}

/* ======== Links ======== */
.footer {
  margin-top: 20px;
  font-size: 14px;
  color: #083b0f;
}

.footer a {
  color: #0c5721;
  font-weight: bold;
  text-decoration: none;
}

.footer a:hover {
  text-decoration: underline;
}

/* ======== Erro ======== */
.error {
  background-color: #c0392b;
  color: #fff;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 15px;
  font-size: 14px;
}

/* ======== Animação ======== */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-15px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
  <div class="container">
    <div class="logo">DV</div>
    <h2>Crie sua conta</h2>

    <?php if ($error): ?>
      <div class="error"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="../backend/register.php" method="post">
      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">

      <div class="input-group">
        <i>👤</i>
        <input type="text" name="nome" placeholder="Nome completo" required>
      </div>

      <div class="input-group">
        <i>📧</i>
        <input type="email" name="email" placeholder="Digite seu Email" required>
      </div>

      <div class="input-group">
        <i>🔒</i>
        <input type="password" name="senha" placeholder="Crie uma senha" required>
      </div>

      <div class="radio-group">
        <label><input type="radio" name="tipo" value="Mercado" checked> Mercado</label>
        <label><input type="radio" name="tipo" value="Restaurante"> Restaurante</label>
      </div>

      <button class="btn" type="submit">Registrar-se</button>
    </form>

    <div style="margin:15px 0;color:#083b0f;">Ou</div>

    <button class="btn-google" type="button">
      <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" alt="">
      Entrar com Google
    </button>

    <div class="footer">
      Já possui uma conta? <a href="login.php">Faça o Login</a>
    </div>
  </div>
</body>
</html>
