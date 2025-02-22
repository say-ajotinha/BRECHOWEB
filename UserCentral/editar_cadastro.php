<?php
include dirname(__DIR__) . '/Config/config.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$erro = "";
$usuario_id = $_SESSION['usuario_id'];

// Obtém os dados do usuário
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $telefone = preg_replace('/\D/', '', $_POST["telefone"]);
    $senha_atual = $_POST["senha_atual"];
    $nova_senha = $_POST["nova_senha"];
    $confirmar_senha = $_POST["confirmar_senha"];

    if (!password_verify($senha_atual, $usuario['senha'])) {
        $erro = "Senha atual incorreta!";
    } else {
        if (!empty($nova_senha)) {
            if ($nova_senha !== $confirmar_senha) {
                $erro = "As senhas não coincidem!";
            } else {
                $nova_senha = password_hash($nova_senha, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nome = ?, email = ?, telefone = ?, senha = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $nome, $email, $telefone, $nova_senha, $usuario_id);
            }
        } else {
            $sql = "UPDATE usuarios SET nome = ?, email = ?, telefone = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nome, $email, $telefone, $usuario_id);
        }

        if ($stmt->execute()) {
            echo "<script>alert('Cadastro atualizado com sucesso!'); window.location.href='../Vendas/dashboard.php';</script>";
            exit();
        } else {
            $erro = "Erro ao atualizar cadastro: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Cadastro</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/sheets/styles/style.css">
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
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            background:rgb(255, 255, 255);
            transition: 0.3s ease-in-out;
            box-sizing: border-box;
        }
        input:focus {
            border-color:rgb(0, 0, 0);
            outline: none;
            background: white;
        }
        .form-actions button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #4c2200,rgb(19, 8, 0));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .form-actions button:hover {
            background: linear-gradient(135deg,rgb(131, 59, 0),rgb(19, 8, 0));
        }
        .error-message {
            background: #ffdddd;
            color: #d8000c;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: <?php echo empty($erro) ? 'none' : 'block'; ?>;
            transition: opacity 0.5s ease-in-out;
        }
        .extra-buttons {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        .extra-buttons a {
            text-decoration: none;
            background: #ddd;
            padding: 10px;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45%;
            transition: 0.3s;
        }
        .extra-buttons a:hover {
            background: #bbb;
        }
        .extra-buttons a i {
            margin-right: 5px;
        }
         /* Caixa de erro */
        .error-box {
            background: #2a0000;
            color: #ff5c5c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: <?php echo empty($erro) ? 'none' : 'flex'; ?>;
            align-items: center;
            justify-content: space-between;
            position: relative;
            animation: fadeIn 0.5s ease-in-out;
        }
        .error-box span {
            flex-grow: 1;
        }
        .error-box::before {
            content: "⚠";
            font-size: 18px;
            margin-right: 8px;
            font-weight: bold;
        }
        .error-box a {
            color: rgb(255, 92, 92);
            text-decoration: none;
            font-weight: bold;
        }
        .error-box a:hover {
            text-decoration: underline;
        }
        .close-btn {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            font-weight: bold;
            color: #ff5c5c;
            padding: 5px;
        }
        .close-btn:hover {
            color: #ff0000;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }


    </style>
</head>
<body>
    <div class="form-container">
        <h2>Editar Cadastro</h2>

        <?php if (!empty($erro)) { ?>
            <div class="error-box" id="error-box">
                <span><strong><?php echo $erro; ?></strong></span>
                <button class="close-btn" onclick="fecharErro()">✖</button>
            </div>
        <?php } ?>

        <form method="POST" action="editar_cadastro.php">
            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo $usuario['nome']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $usuario['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="tel" id="telefone" name="telefone" pattern="\(\d{2}\) \d{5}-\d{4}" placeholder="(51) 91234-5678" value="<?php echo $usuario['telefone']; ?>" required>
            </div>
            <div class="form-group">
                <label for="senha_atual">Senha Atual:</label>
                <input type="password" id="senha_atual" name="senha_atual" required>
            </div>
            <div class="form-group">
                <label for="nova_senha">Nova Senha (opcional):</label>
                <input type="password" id="nova_senha" name="nova_senha">
            </div>
            <div class="form-group">
                <label for="confirmar_senha">Confirmar Nova Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha">
            </div>
            <div class="form-actions">
                <button type="submit">Salvar Alterações</button>
            </div>
        </form>
        <div class="extra-buttons">
            <a href="<?php echo BASE_URL; ?>/index.php">🏠 Início</a>
        </div>
    </div>

    <script>
        document.getElementById("telefone").addEventListener("input", function (e) {
            let value = e.target.value.replace(/\D/g, ""); // Remove tudo que não for número
            if (value.length > 11) value = value.substring(0, 11); // Limita a 11 números

            if (value.length >= 2) {
                value = `(${value.substring(0, 2)}) ` + value.substring(2);
            }
            if (value.length >= 10) {
                value = value.substring(0, 10) + "-" + value.substring(10);
            }

            e.target.value = value; // Atualiza o input formatado
        });

        function fecharErro() {
            var errorBox = document.getElementById("error-box");
            if (errorBox) {
                errorBox.style.opacity = "0";
                setTimeout(() => errorBox.style.display = "none", 500);
            }
        }
    </script>
</body>
</html>
