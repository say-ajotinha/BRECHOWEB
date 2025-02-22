<?php
session_start();
include dirname(__DIR__) . '/Config/config.php';

$erro = ""; // Variável para armazenar a mensagem de erro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    // Evitar SQL Injection usando prepared statements
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($senha, $user["senha"])) {
            $_SESSION["usuario_id"] = $user["id"];
            $_SESSION["usuario_nome"] = $user["nome"];
            header("Location: ../Vendas/dashboard.php");
            exit();
        } else {
            $erro = "O e-mail e a senha não correspondem";
        }
    } else {
        $erro = "Usuário não encontrado!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
        .form-actions {
    display: flex;
    justify-content: center;
    margin-top: 15px; /* Espaço entre o botão e os campos acima */
    }

        .form-actions button {
    width: 80%;
    padding: 12px;
    background: linear-gradient(135deg, #4c2200,rgb(19, 8, 0));
    color: #fff; /* Texto branco para melhor contraste */
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra leve para realce */
    }

        .form-actions button:hover {
            background: linear-gradient(135deg,#833b00,rgb(19, 8, 0));
            transform: scale(1.05); /* Leve aumento no tamanho */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
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
        .forgot-password {
            display: block;
            margin-bottom: 10px;
            color: #4c2200;
            text-decoration: none;
            font-size: 14px;
        }
        .forgot-password:hover {
            text-decoration: underline;
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
            display: <?php echo empty($erro) ? 'none' : 'block'; ?>;
            text-align: left;
            position: relative;
            animation: fadeIn 0.5s ease-in-out;
        }
        .error-box::before {
            content: "⚠";
            font-size: 18px;
            margin-right: 8px;
            font-weight: bold;
        }
        .error-box a {
            color:rgb(255, 92, 92);
            text-decoration: none;
            font-weight: bold;
        }
        .error-box a:hover {
            text-decoration: underline;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        
        <?php if (!empty($erro)) { ?>
    <div class="error-box" id="error-message">
        <strong><?php echo $erro; ?></strong>
        <br>
        Se tiver certeza de que as informações inseridas estão corretas, tente recuperar sua conta.
        <br>
        <a href="<?php echo BASE_URL; ?>/UserCentral/recuperar_senha.php">Recuperar conta</a>
    </div>
    <?php } ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <a href="<?php echo BASE_URL; ?>/UserCentral/recuperar_senha.php" class="forgot-password">Esqueceu a senha?</a>
            <div class="form-actions">
                <button type="submit">Entrar</button>
            </div>
        </form>
        <div class="extra-buttons">
            <a href="<?php echo BASE_URL; ?>/index.php"><i>🏠</i> Início</a>
            <a href="<?php echo BASE_URL; ?>/UserCentral/cadastro.php"><i>➕</i> Cadastrar</a>
        </div>
    </div>
    <script>
        // Aguarda 5 segundos e esconde a mensagem de erro
document.addEventListener("DOMContentLoaded", function() {
    var errorMessage = document.getElementById("error-message");
    if (errorMessage) {
        setTimeout(function() {
            errorMessage.style.opacity = "0";
            setTimeout(() => errorMessage.style.display = "none", 500);
            }, 5000);
        }
    });
    </script>
</body>
</html>