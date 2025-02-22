<?php
session_start();
include dirname(__DIR__) . '/Config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST["token"];
    $nova_senha = $_POST["nova_senha"];
    $confirmar_senha = $_POST["confirmar_senha"];

    if ($nova_senha !== $confirmar_senha) {
        $_SESSION["erro"] = "As senhas não coincidem.";
        header("Location: ../UserCentral/redefinir_senha.php?token=$token");
        exit();
    }

    // Buscar usuário pelo token e verificar validade
    $sql = "SELECT email FROM recuperacao_senha WHERE token = ? AND expira_em > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION["erro"] = "Token inválido ou expirado.";
        header("Location: ../UserCentral/recuperar_senha.php");
        exit();
    }

    $row = $result->fetch_assoc();
    $email = $row["email"];
    $stmt->close();

    // Atualizar a senha no banco de dados
    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    $sql = "UPDATE usuarios SET senha = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $senha_hash, $email);

    if ($stmt->execute()) {
        // Remover o token usado
        $sql = "DELETE FROM recuperacao_senha WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $_SESSION["sucesso"] = "Senha redefinida com sucesso! Faça login.";
        header("Location: ../UserCentral/login.php");
        exit();
    } else {
        $_SESSION["erro"] = "Erro ao redefinir senha. Tente novamente.";
        header("Location: ../UserCentral/redefinir_senha.php?token=$token");
        exit();
    }
}
?>
