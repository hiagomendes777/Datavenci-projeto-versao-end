<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
start_secure_session();

// 🔒 Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Verifica se o id foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: todos_produtos.php');
    exit;
}

$id = (int)$_GET['id'];
$success = $error = '';

// Categorias fixas
$categoriasFixas = [
    'Hortifruti','Mercearia','Padaria','Açougue','Frios e Laticínios',
    'Bebidas','Higiene Pessoal','Limpeza','Utilidades Domésticas'
];

// Busca produto existente
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header('Location: todos_produtos.php');
    exit;
}

// Quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $codigo = trim($_POST['codigo'] ?? '');
    $lote = trim($_POST['lote'] ?? '');
    $quantidade = (int)($_POST['quantidade'] ?? 0);
    $preco_raw = trim($_POST['preco'] ?? '');
    $vencimento = $_POST['vencimento'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $imagem = $produto['imagem'];

    // Corrige o preço
    $preco_num = str_replace(['R$', ' '], '', $preco_raw);
    $preco_num = str_replace(',', '.', $preco_num);
    $preco_final = floatval($preco_num);

    // Upload de nova imagem, se enviado
    if (!empty($_FILES['imagem']['name'])) {
        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $permitidos = ['jpg','jpeg','png','gif'];

        if (in_array($extensao, $permitidos)) {
            $nomeArquivo = uniqid() . '.' . $extensao;
            $destino = __DIR__ . '/../uploads/' . $nomeArquivo;

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                // Remove imagem antiga
                if (!empty($imagem) && file_exists(__DIR__ . '/../' . $imagem)) {
                    unlink(__DIR__ . '/../' . $imagem);
                }
                $imagem = 'uploads/' . $nomeArquivo;
            } else {
                $error = "Erro ao enviar a nova imagem.";
            }
        } else {
            $error = "Formato de imagem não permitido (jpg, jpeg, png, gif).";
        }
    }

    // Atualiza banco
    if (!$error && $nome && $codigo && $quantidade && $vencimento && $preco_final > 0) {
        $stmt = $pdo->prepare("
            UPDATE produtos SET
                nome = :nome,
                codigo = :codigo,
                lote = :lote,
                quantidade = :quantidade,
                preco = :preco,
                vencimento = :vencimento,
                categoria_id = :categoria,
                imagem = :imagem
            WHERE id = :id
        ");
        $stmt->execute([
            ':nome' => $nome,
            ':codigo' => $codigo,
            ':lote' => $lote,
            ':quantidade' => $quantidade,
            ':preco' => $preco_final,
            ':vencimento' => $vencimento,
            ':categoria' => array_search($categoria, $categoriasFixas) + 1, // ajusta id da categoria
            ':imagem' => $imagem,
            ':id' => $id
        ]);

        $success = "Produto atualizado com sucesso!";
        // Atualiza objeto produto
        $produto = array_merge($produto, $_POST, ['imagem' => $imagem, 'preco' => $preco_final]);
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
<title>Editar Produto - Datavenci</title>
<style>
body { margin:0; font-family: Arial,sans-serif; background-color:#1e1e1e; color:white; }
.topo { background-color:#176d2d; padding:15px; display:flex; align-items:center; justify-content:space-between; color:white; }
.topo a { color:white; text-decoration:none; font-size:20px; }
.titulo { font-size:18px; font-weight:bold; }
.form { padding:15px; max-width:500px; margin:auto; }
.campo { display:flex; align-items:center; background-color:#ccc; border-radius:8px; margin-bottom:12px; padding:10px; color:#000; }
.campo input, .campo select { border:none; outline:none; background:none; font-size:14px; flex:1; color:#000; }
.btn { width:100%; background-color:#176d2d; color:white; border:none; padding:12px; border-radius:8px; font-size:15px; cursor:pointer; transition:0.3s; }
.btn:hover { background-color:#145a24; }
.msg { text-align:center; padding:10px; border-radius:6px; margin-bottom:12px; }
.success { background:#28a745; color:white; }
.error { background:#dc3545; color:white; }
label { display:block; margin:8px 0 5px; color:#ccc; }
img.preview { width:100%; max-height:150px; object-fit:cover; margin-bottom:10px; border-radius:6px; }
</style>
</head>
<body>

<div class="topo">
    <a href="tabelas.php">&#8592;</a>
    <div class="titulo">Editar Produto</div>
    <span>📦</span>
</div>

<div class="form">
    <?php if ($success): ?><div class="msg success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="msg error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="campo"><input type="text" name="nome" placeholder="Nome do Produto" value="<?= htmlspecialchars($produto['nome']) ?>" required></div>
        <div class="campo"><input type="text" name="codigo" placeholder="Código do Produto" value="<?= htmlspecialchars($produto['codigo']) ?>" required></div>
        <div class="campo"><input type="text" name="lote" placeholder="Lote" value="<?= htmlspecialchars($produto['lote']) ?>"></div>
        <div class="campo"><input type="number" name="quantidade" placeholder="Quantidade" min="1" value="<?= $produto['quantidade'] ?>" required></div>
        <div class="campo"><input type="text" name="preco" placeholder="Preço unitário ex: 5.50" value="<?= number_format($produto['preco'],2,',','.') ?>" required></div>
        <label>Data de Vencimento:</label>
        <div class="campo"><input type="date" name="vencimento" value="<?= htmlspecialchars($produto['vencimento']) ?>" required></div>
        <label>Categoria:</label>
        <div class="campo">
            <select name="categoria" required>
                <?php foreach ($categoriasFixas as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $produto['categoria'] === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <label>Imagem do Produto:</label>
        <?php if (!empty($produto['imagem'])): ?>
            <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="Imagem do produto" class="preview">
        <?php endif; ?>
        <div class="campo"><input type="file" name="imagem" accept="image/*"></div>
        <button type="submit" class="btn">Salvar Alterações</button>
    </form>
</div>

</body>
</html>
