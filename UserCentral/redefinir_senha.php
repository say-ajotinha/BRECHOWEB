<?php
include dirname(__DIR__) . '/Config/config.php';

$erro = "";
$sucesso = "";
$email = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verifica se o token é válido na tabela correta
    $sql = "SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $dados = $result->fetch_assoc();
        $email = $dados['email'];
    } else {
        $erro = "Token inválido ou expirado.";
    }
} else {
    $erro = "Token não fornecido.";
}

// Processa a redefinição de senha
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nova_senha'])) {
    if (!empty($email)) {
        $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);

        // Atualiza a senha do usuário
        $sql = "UPDATE usuarios SET senha = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nova_senha, $email);

        if ($stmt->execute()) {
            // Remove o token usado para evitar reutilização
            $sql = "DELETE FROM password_resets WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();

            $sucesso = "Senha redefinida com sucesso! Você pode fazer login agora.";
        } else {
            $erro = "Erro ao redefinir a senha. Tente novamente.";
        }
    } else {
        $erro = "Nenhum e-mail encontrado para redefinição de senha.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
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
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            background:rgb(255, 255, 255);
            transition: 0.3s;
        }
        input:focus {
            border-color:rgb(0, 0, 0);
            outline: none;
            background: white;
        }
        button {
            width: 100%;
            padding: 12px;
            color: #4c2200;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #833b00;
        }
        .error-box, .success-box {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }
        .error-box {
            background: #2a0000;
            color: #ff5c5c;
        }
        .success-box {
            background: #004d00;
            color: #00ff00;
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
    background: linear-gradient(135deg, #4c2200,rgb(19, 8, 0));
    color: white;
    font-weight: bold;
    text-decoration: none;
    border-radius: 10px;
    font-size: 16px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease-in-out;
    }

    .formabtn a:hover {
        background: linear-gradient(135deg,#833b00,rgb(19, 8, 0));
        transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    </style>
</head>
<body>
    <div class="form-container">
        <h2>Redefinir Senha</h2>
        
        <?php if (!empty($erro)) { ?>
            <div class="error-box"> <?php echo $erro; ?> </div>
        <?php } ?>
        
        <?php if (!empty($sucesso)) { ?>
            <div class="success-box"> <?php echo $sucesso; ?> <br> <a href="<?php echo BASE_URL; ?>/UserCentral/login.php">Fazer Login</a></div>
        <?php } ?>
        
        <?php if (empty($erro) && empty($sucesso)) { ?>
            <form method="POST">
                <label for="nova_senha">Nova Senha:</label>
                <input type="password" id="nova_senha" name="nova_senha" required>
                <button type="submit">Redefinir Senha</button>
            </form>
        <?php } ?>
        <div class="formabtn">
        <a href="<?php echo BASE_URL; ?>/index.php"><i>🏠</i> Início</a>
    </div>
</body>
</html>
