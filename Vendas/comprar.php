<?php
session_start();
include dirname(__DIR__) . '/Config/config.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo "<script>
        alert('Faça login para comprar este produto.');
        window.location.href = '../UserCentral/login.php'; 
    </script>";
    exit();
}

$produto_id = $_GET['produto_id'];
$usuario_id = $_SESSION['usuario_id'];

// Verifica se o produto já foi comprado e obtém o id do vendedor (id_usuario)
$query_verifica = "SELECT id_comprador, id_usuario FROM produtos WHERE id = ?";
$stmt = $conn->prepare($query_verifica);
$stmt->bind_param("i", $produto_id);
$stmt->execute();
$stmt->bind_result($id_comprador, $id_vendedor);
$stmt->fetch();
$stmt->close();

// Se id_comprador for diferente de 0, significa que já foi comprado
if ($id_comprador != 0) {
    echo "<script>
        alert('Este produto já foi comprado.');
        window.location.href = '../index.php';
    </script>";
    exit();
}

// Atualiza o produto definindo o usuário como comprador
$query_compra = "UPDATE produtos SET id_comprador = ? WHERE id = ?";
$stmt_compra = $conn->prepare($query_compra);
$stmt_compra->bind_param("ii", $usuario_id, $produto_id);

if ($stmt_compra->execute() && $stmt_compra->affected_rows > 0) {
    $stmt_compra->close();

    // Exibe a janela modal explicando a compra
    echo "<script>
        alert('Compra realizada!\\n\\nDevido ao sistema de pagamento de produtos estar offline, entre em contato diretamente com o vendedor.');
    </script>";

    // Busca informações do vendedor (id_vendedor é o id_usuario do produto)
    $query_vendedor = "SELECT nome, email, telefone FROM usuarios WHERE id = ?";
    $stmt_vendedor = $conn->prepare($query_vendedor);
    $stmt_vendedor->bind_param("i", $id_vendedor);
    $stmt_vendedor->execute();
    $stmt_vendedor->bind_result($nome_vendedor, $email_vendedor, $telefone_vendedor);
    $stmt_vendedor->fetch();
    $stmt_vendedor->close();

    // Formata o telefone do vendedor
    $telefone_formatado = preg_replace('/\D/', '', $telefone_vendedor);
    if (strlen($telefone_formatado) === 13) {
        $telefone_formatado = "(" . substr($telefone_formatado, 2, 2) . ") " . substr($telefone_formatado, 4, 5) . "-" . substr($telefone_formatado, 9);
    } else {
        $telefone_formatado = $telefone_vendedor; // Mantém o formato original caso não atenda ao padrão
    }
    echo "<div style='width: 50%; margin: 20px auto; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); background: linear-gradient(135deg, #f3e0c2, #fae0c8); text-align: center;'>
    <h2 style='text-align: center;'>Informações do Vendedor</h2>
    <table style='width: 100%; border-collapse: collapse; text-align: left; margin-bottom: 20px;'>
        <tr><th style='padding: 10px'>Nome</th><td style='padding: 10px;'>$nome_vendedor</td></tr>
        <tr><th style='padding: 10px'>Email</th><td style='padding: 10px;'>$email_vendedor</td></tr>
        <tr><th style='padding: 10px'>Telefone</th><td style='padding: 10px;'>$telefone_formatado</td></tr>
    </table>
    <a href='../Vendas/dashboard.php' style='display: inline-block; padding: 12px 20px; font-size: 16px; color: #fff; background: #28a745; text-decoration: none; border-radius: 5px;'>
        Visualizar Compra
    </a>
    </div>";
} else {
echo "<script>
alert('Erro ao registrar a compra. Tente novamente.');
window.location.href = '../index.php';
</script>";
}
?>