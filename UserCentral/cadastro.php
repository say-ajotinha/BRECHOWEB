<?php
include dirname(__DIR__) . '/Config/config.php';

$erro = ""; // Variável para armazenar mensagens de erro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    
    // Remove caracteres não numéricos e adiciona código do país (55)
    $telefone = preg_replace('/\D/', '', $_POST["telefone"]);
    if (strlen($telefone) == 10 || strlen($telefone) == 11) {
        $telefone = "55" . $telefone; // Adiciona o código do Brasil
    }

    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT); // Criptografa a senha

    // Verifica se o email já está cadastrado
    $sql = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $erro = "Este email já está cadastrado!";
    } else {
        // Insere o novo usuário com telefone
        $sql = "INSERT INTO usuarios (nome, email, telefone, senha) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nome, $email, $telefone, $senha);

        if ($stmt->execute()) {
            echo "<script>alert('Cadastro realizado com sucesso! Faça login.'); window.location.href='../UserCentral/login.php';</script>";
            exit();
        } else {
            $erro = "Erro ao cadastrar: " . $conn->error;
        }
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
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
            justify-content: space-between;
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
        <h2>Cadastro</h2>

        <?php if (!empty($erro)) { ?>
            <div class="error-box" id="error-box">
                <span><strong><?php echo $erro; ?></strong><br>Este e-mail já está em uso. Verifique ou <a href="<?php echo BASE_URL; ?>/UserCentral/recuperar_senha.php">recupere sua conta</a>.</span>
                <button class="close-btn" onclick="fecharErro()">✖</button>
            </div>
        <?php } ?>

        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="tel" id="telefone" name="telefone" pattern="\(\d{2}\) \d{5}-\d{4}" placeholder="(51) 91234-5678" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <div class="form-actions">
                <button type="submit">Cadastrar</button>
            </div>
        </form>
        <div class="extra-buttons">
            <a href="<?php echo BASE_URL; ?>/index.php">🏠 Início</a>
            <a href="<?php echo BASE_URL; ?>/UserCentral/login.php">🔑 Login</a>
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