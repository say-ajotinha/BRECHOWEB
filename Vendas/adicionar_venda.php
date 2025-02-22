<?php
session_start();
include dirname(__DIR__) . '/Config/config.php';

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../UserCentral/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $descricao = trim($_POST["descricao"]);
    $preco = floatval($_POST["preco"]);
    $categoria = trim($_POST["categoria"]);
    $genero = trim($_POST["genero"]);
    $usuario_id = $_SESSION["usuario_id"];
    $data_postagem = date("Y-m-d H:i:s");

    // Upload da imagem
    $target_dir = "../sheets/img/vendas/";
    $image_extension = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION); // Pega a extensão do arquivo

    // Insere o produto no banco de dados para obter o ID gerado
    $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, categoria, id_usuario, data_postagem) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsis", $nome, $descricao, $preco, $categoria, $usuario_id, $data_postagem);

    if ($stmt->execute()) {
        $produto_id = $stmt->insert_id; // Pega o ID do produto recém-inserido

        // Gera o nome único da imagem usando o ID do produto e a data de postagem
        $image_name = $produto_id . "_" . str_replace([" ", ":"], "-", $data_postagem) . "." . $image_extension;
        $target_file = $target_dir . $image_name;

        // Move a imagem para o diretório de uploads
        if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
            // Atualiza o produto com o nome da imagem
            $stmt_update = $conn->prepare("UPDATE produtos SET imagem = ? WHERE id = ?");
            $stmt_update->bind_param("si", $image_name, $produto_id);
            $stmt_update->execute();

            echo "<script>alert('Produto cadastrado com sucesso!'); window.location.href='../Vendas/dashboard.php';</script>";
        } else {
            echo "Erro ao enviar a imagem.";
        }
    } else {
        echo "Erro ao registrar venda: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #f3e0c2, rgb(250, 224, 200));
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            font-weight: bold;
            font-size: 14px;
            display: block;
            margin-bottom: 5px;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            background: #f9f9f9;
            transition: 0.3s;
            box-sizing: border-box;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #4c2200;
            outline: none;
            background: white;
        }
        .form-actions button {
            width: 100%;
            padding: 12px;
            background:rgb(177, 125, 83);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .form-actions button:hover {
            background:rgb(250, 153, 74);
        }

        .formabtn {
            display: flex;
            justify-content: center;
            margin-top: 15px;
        }

        .formabtn a {
            display: inline-block;
            width: 80%;
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

        .Buy {
    font-family: 'Helvetica Neue', Arial, sans-serif; /* Fonte moderna e limpa */
    font-size: 26px; /* Tamanho do texto */
    font-weight: 600; /* Peso da fonte (semibold) */
    color: #333; /* Cor escura para contraste */
    text-align: center; /* Centraliza o texto */
    text-transform: uppercase; /* Transforma o texto em maiúsculas */
    letter-spacing: 2px; /* Espaçamento entre letras para um toque sofisticado */
    margin: 20px 0; /* Margem superior e inferior para espaçamento */
    padding-bottom: 10px; /* Espaçamento abaixo do texto */
    position: relative; /* Para adicionar um efeito de linha abaixo */
}

/* Efeito de linha abaixo do título */
.Buy::after {
    content: ''; /* Cria um pseudo-elemento */
    display: block;
    width: 50px; /* Largura da linha */
    height: 2px; /* Altura da linha */
    background-color: #4c2200; /* Cor da linha (marrom escuro) */
    position: absolute;
    bottom: 0;
    left: 50%; /* Centraliza a linha */
    transform: translateX(-50%); /* Ajusta a centralização */
}

/* Efeito hover para o título */
.Buy:hover {
    color: #a05418; /* Cor marrom claro ao passar o mouse */
    transition: color 0.3s ease; /* Transição suave */
}

/* Efeito hover na linha */
.Buy:hover::after {
    background-color: #a05418; /* Cor marrom claro ao passar o mouse */
    transition: background-color 0.3s ease; /* Transição suave */
}

    </style>
</head>
<body>
    
    <div class="form-container">
        <h2 class="Buy">Adicionar Produto</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nome do Produto:</label>
                <input type="text" name="nome" required>
            </div>
            <div class="form-group">
                <label>Descrição:</label>
                <textarea name="descricao" required></textarea>
            </div>
            <div class="form-group">
                <label>Preço (R$):</label>
                <input type="number" name="preco" step="0.01" required>
            </div>
            <div class="form-group">
                <label>Categoria:</label>
                <select name="categoria" required>
                    <option value="CAMISA">Camisa</option>
                    <option value="CALÇA">Calça</option>
                    <option value="BERMUDA">Bermuda</option>
                    <option value="SHORTS">Shorts</option>
                    <option value="SAPATO">Sapato</option>
                    <option value="VESTIDO">Vestido</option>
                    <option value="TOP">Top</option>
                    <option value="BLUSINHA">Blusinha</option>
                    <option value="OUTROS">Outros</option>

                </select>
            </div>
            <div class="form-group">
                <label>Gênero:</label>
                <select name="genero" required>
                    <option value="MASCULINO">Masculino</option>
                    <option value="FEMININO">Feminino</option>
                </select>
            </div>
            <div class="form-group">
                <label>Imagem do Produto:</label>
                <input type="file" name="imagem" accept="image/*" required>
            </div>
            <div class="form-actions">
                <button type="submit">Cadastrar Produto</button>
            </div>
        </form>
        <div class="formabtn">
            <a href="<?php echo BASE_URL; ?>/index.php"><i>🏠</i> Início</a>
        </div>
    </div>
</body>
</html>