<?php
require_once __DIR__ . '/../includes/functions.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}

$nome = htmlspecialchars($_SESSION['user']['nome']);
$tipo = htmlspecialchars($_SESSION['user']['tipo']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mercados Próximos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    body {
      display: flex;
      min-height: 100vh;
      background-color: #f4f6f5;
    }

    /* 🟢 Menu lateral verde */
    #sidebar {
      width: 250px;
      background-color: #145a32;
      color: white;
      transition: 0.3s;
      padding: 20px 0;
      position: fixed;
      height: 100%;
    }

    #sidebar h4 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: bold;
    }

    #sidebar a {
      display: flex;
      align-items: center;
      color: white;
      text-decoration: none;
      padding: 12px 20px;
      transition: 0.3s;
    }

    #sidebar a:hover {
      background-color: #0e4024;
    }

    #sidebar i {
      margin-right: 10px;
    }

    #menuBtn {
      background: none;
      border: none;
      color: #145a32;
      font-size: 22px;
      margin: 10px;
      cursor: pointer;
    }

    #mainContent {
      flex-grow: 1;
      margin-left: 250px;
      padding: 20px;
      transition: 0.3s;
    }

    #sidebar.active {
      transform: translateX(-250px);
    }

    #mainContent.shifted {
      margin-left: 0;
    }

    .card {
      border-radius: 12px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* 🟢 Botão verde */
    .btn-custom {
      background-color: #145a32;
      color: white;
      border: none;
    }

    .btn-custom:hover {
      background-color: #0e4024;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div id="sidebar">
    <h4>Menu</h4>
    <a href="painel.php"><i class="fas fa-store"></i>Painel Mercado</a>
    <a href="mercados.php"><i class="fas fa-shopping-cart"></i>Mercados</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Sair</a>
  </div>

  <!-- Conteúdo -->
  <div id="mainContent">
    <button id="menuBtn"><i class="fas fa-bars"></i> Menu</button>

    <div class="container mt-3">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title mb-3">Mercados Próximos</h4>
          <p class="card-text">Veja os mercados próximos e visualize seus produtos disponíveis.</p>

          <div class="mt-4">
            <div class="card p-3 mb-3">
              <h5>🛒 Mercado - Datavenci</h5>
              <p>Endereço: Rua Central, nº 100 — Cidade Recife</p>
              <a href="todos_produtos.php" class="btn btn-custom">Ver produtos disponíveis</a>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <script>
    const menuBtn = document.getElementById('menuBtn');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    menuBtn.addEventListener('click', () => {
      sidebar.classList.toggle('active');
      mainContent.classList.toggle('shifted');
    });
  </script>

</body>
</html>

