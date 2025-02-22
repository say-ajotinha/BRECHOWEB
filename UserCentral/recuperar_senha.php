<?php
include dirname(__DIR__) . '/Config/config.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    
    // Verifica se o e-mail está cadastrado
    $sql = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // Gera um token seguro
        $token = bin2hex(random_bytes(32));
        $expira = date("Y-m-d H:i:s", strtotime("+30 minutes"));
        
        // Salva o token no banco
        $sql = "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $token, $expira);
        $stmt->execute();
        
        // Link para redefinição de senha
        $link = "http://localhost/BRECHOWEB/UserCentral/redefinir_senha.php?token=$token";
        
        // Enviar e-mail com PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Servidor SMTP do Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'adrianogamer.oficial@gmail.com'; // Seu e-mail do Gmail
            $mail->Password = 'mgxo afkd tmih psox'; // Sua senha do Gmail ou senha de app
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS
            $mail->Port = 587; // Porta do Gmail

            // Remetente e destinatário
            $mail->setFrom('ajdevoficial@gmail.com', 'AJ DEV');
            $mail->addAddress($email);

            // Conteúdo do e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Recuperar Senha do Cadastro';
            $mail->Body    = "Clique no link para redefinir sua senha: <a href='$link'>$link</a>";

            $mail->send();
            echo "<script>alert('Se este e-mail estiver cadastrado, um link será enviado para você!'); window.location.href='../UserCentral/login.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Erro ao enviar o e-mail: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('E-mail não encontrado!');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha</title>
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
        .back-to-login {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .back-to-login a {
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
        .back-to-login a:hover {
            background: linear-gradient(135deg,#833b00,rgb(19, 8, 0));
            color: white;
        }
        .back-to-login a i {
            margin-right: 5px;
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
        <h2>Recuperação de Senha</h2>
        <form method="POST">
            <div class="form-group">
                <label for="email">Digite seu e-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-actions">
                <button type="submit">Enviar Link</button>
            </div>
        </form>
        <div class="back-to-login">
        <a href="<?php echo BASE_URL; ?>/UserCentral/login.php"><i>🔑</i> Voltar ao Login</a>
        <a href="<?php echo BASE_URL; ?>/UserCentral/cadastro.php"><i>➕</i> Cadastrar-se </a>
        </div>
        <div class="formabtn">
        <a href="<?php echo BASE_URL; ?>/index.php"><i>🏠</i> Início</a>
        </div>
    </div>
</body>
</html>