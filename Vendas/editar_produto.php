<?php
session_start();
include dirname(__DIR__) . '/Config/config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../UserCentral/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$produto_id = $_GET['id'] ?? null;

if (!$produto_id) {
    die("Produto não encontrado.");
}

// Buscar informações do produto
$query = "SELECT * FROM produtos WHERE id = ? AND id_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $produto_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$produto = $result->fetch_assoc();

if (!$produto) {
    die("Produto não encontrado ou não pertence a este usuário.");
}

// Se o formulário for enviado, atualiza os dados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $descricao = $_POST['descricao'];

    $update_query = "UPDATE produtos SET nome = ?, preco = ?, descricao = ? WHERE id = ? AND id_usuario = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param("sdsii", $nome, $preco, $descricao, $produto_id, $usuario_id);

    if ($stmt_update->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Erro ao atualizar produto.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <style>

          body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        background: linear-gradient(135deg, #f3e0c2, rgb(250, 224, 200));
    }

    .container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
        width: 350px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    h2 {
        color: #321432;
        margin-bottom: 15px;
    }

    label {
        font-weight: bold;
        font-size: 14px;
        display: block;
        margin-bottom: 5px;
        text-align: left;
        width: 100%;
    }

    input, textarea {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid rgba(0, 0, 0, 0.4);
        border-radius: 5px;
        box-sizing: border-box;
    }

    .botao {
        width: 100%;
        background:rgb(177, 125, 83);
        color: white;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 15px;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
    }

    .botao:hover {
        background:rgb(250, 153, 74);
    }

    .formabtn {
            display: flex;
            justify-content: center;
            margin-top: 15px;
            gap: 10px;
        }

        .formabtn a {
            display: inline-block;
            align-content: center;
            width: 50%;
            padding: 12px;
            text-align: center;
            background:rgb(177, 125, 83);
            color: white;
            font-weight: bold;
            text-decoration: none;
            border-radius: 10px;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        .formabtn a:hover {
            background:#4c2200;
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }


    </style>
    </head>
<body>

<div class="container">
    <h2>Editar Produto</h2>
    <form method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo $produto['nome']; ?>" required>

        <label>Preço:</label>
        <input type="number" step="0.01" name="preco" value="<?php echo $produto['preco']; ?>" required>

        <label>Descrição:</label>
        <textarea name="descricao" required><?php echo $produto['descricao']; ?></textarea>

        <button type="submit" class="botao">Salvar Alterações</button>
    </form>
    <div class="formabtn">
            <a href="<?php echo BASE_URL; ?>/index.php">Início</a>
            <a href="<?php echo BASE_URL; ?>/Vendas/dashboard.php">Minha Conta</a>
    </div>
</div>

</body>
</html>
