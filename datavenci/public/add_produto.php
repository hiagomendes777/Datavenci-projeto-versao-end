<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

start_secure_session();

// 🔒 Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}

$success = $error = '';

// 🔽 Buscar categorias do banco
try {
  $stmt = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome ASC");
  $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $categorias = [];
  $error = "Erro ao carregar categorias: " . $e->getMessage();
}

// 📦 Quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome'] ?? '');
  $codigo = trim($_POST['codigo'] ?? '');
  $lote = trim($_POST['lote'] ?? '');
  $quantidade = (int)($_POST['quantidade'] ?? 0);
  $preco_raw = trim($_POST['preco'] ?? '');
  $vencimento = $_POST['vencimento'] ?? null;
  $categoria_id = $_POST['categoria_id'] ?? null;
  $imagem = null;

  // ======= Corrige o preço =======
  $preco_num = str_replace(['R$', ' '], '', $preco_raw);
  $preco_num = str_replace(',', '.', $preco_num);
  $preco_final = floatval($preco_num);

// ======= Upload de imagem =======
if (!empty($_FILES['imagem']['name'])) {
    $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg','jpeg','png','gif'];

    if (in_array($extensao, $permitidos)) {
        $uploadsDir = __DIR__ . '/../uploads/';
        
        // Verifica se a pasta existe, se não cria
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        $nomeArquivo = uniqid('img_') . '.' . $extensao;
        $destino = $uploadsDir . $nomeArquivo;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
            // Salva o caminho relativo para o banco
            $imagem = 'uploads/' . $nomeArquivo;
        } else {
            $error = "Erro ao enviar a imagem. Verifique as permissões da pasta uploads.";
        }
    } else {
        $error = "Formato de imagem não permitido. Use jpg, jpeg, png ou gif.";
    }
}


  // ======= Salvar no banco =======
  if (!$error && $nome && $codigo && $quantidade && $vencimento && $preco_final > 0 && $categoria_id) {
    try {
      $stmt = $pdo->prepare("INSERT INTO produtos 
        (nome, codigo, lote, quantidade, preco, vencimento, imagem, usuario_id, categoria_id)
        VALUES (:nome, :codigo, :lote, :quantidade, :preco, :vencimento, :imagem, :usuario_id, :categoria_id)");
      $stmt->execute([
        ':nome' => $nome,
        ':codigo' => $codigo,
        ':lote' => $lote,
        ':quantidade' => $quantidade,
        ':preco' => $preco_final,
        ':vencimento' => $vencimento,
        ':imagem' => $imagem,
        ':usuario_id' => $_SESSION['user']['id'],
        ':categoria_id' => $categoria_id
      ]);
      $success = "Produto adicionado com sucesso!";
    } catch (PDOException $e) {
      $error = "Erro ao salvar produto: " . $e->getMessage();
    }
  } elseif (!$error) {
      $error = "Preencha todos os campos obrigatórios corretamente.";
  }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Adicionar Produto - Datavenci</title>
<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #1e1e1e;
    color: white;
  }

  .topo {
    background-color: #176d2d;
    padding: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: white;
  }

  .topo a { color: white; text-decoration: none; font-size: 20px; }
  .titulo { font-size: 18px; font-weight: bold; }

  .form { padding: 15px; max-width: 500px; margin: auto; }
  .campo { display: flex; align-items: center; background-color: #ccc; border-radius: 8px; margin-bottom: 12px; padding: 10px; color: #000; }
  .campo input, .campo select { border: none; outline: none; background: none; font-size: 14px; flex: 1; color: #000; }

  .btn { width: 100%; background-color: #176d2d; color: white; border: none; padding: 12px; border-radius: 8px; font-size: 15px; cursor: pointer; transition: 0.3s; }
  .btn:hover { background-color: #145a24; }

  .msg { text-align: center; padding: 10px; border-radius: 6px; margin-bottom: 12px; }
  .success { background: #28a745; color: white; }
  .error { background: #dc3545; color: white; }

  label { display: block; margin: 8px 0 5px; color: #ccc; }
</style>
</head>
<body>
  <div class="topo">
    <a href="painel.php">←</a>
    <div class="titulo">Adicionar Produto</div>
    <span>📦</span>
  </div>

  <div class="form">
    <?php if ($success): ?><div class="msg success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="msg error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="campo">
        <input type="text" name="nome" placeholder="Nome do Produto" required>
      </div>

      <div class="campo">
        <input type="text" name="codigo" placeholder="Código do Produto" required>
      </div>

      <div class="campo">
        <input type="text" name="lote" placeholder="Lote">
      </div>

      <div class="campo">
        <input type="number" name="quantidade" placeholder="Quantidade" min="1" required>
      </div>

      <div class="campo">
        <input type="text" name="preco" placeholder="Preço unitário ex: 5.50" required>
      </div>

      <label>Categoria:</label>
      <div class="campo">
        <select name="categoria_id" required>
          <option value="">Selecione a categoria</option>
          <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <label>Data de Vencimento:</label>
      <div class="campo">
        <input type="date" name="vencimento" required>
      </div>

      <label>Imagem do Produto:</label>
      <div class="campo">
        <input type="file" name="imagem" accept="image/*">
      </div>

      <button type="submit" class="btn">Salvar Produto</button>
    </form>
  </div>
</body>
</html>
