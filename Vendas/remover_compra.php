<?php
session_start();
include dirname(__DIR__) . '/Config/config.php';

if (!isset($_SESSION['usuario_id'])) {
    echo "<script>
        alert('Faça login para acessar esta funcionalidade.');
        window.location.href = '../login.php';
    </script>";
    exit();
}

$produto_id = $_GET['produto_id'];
$usuario_id = $_SESSION['usuario_id'];

// Verifica se o produto realmente pertence ao usuário logado
$query_verifica = "SELECT id FROM produtos WHERE id = ? AND id_comprador = ?";
$stmt = $conn->prepare($query_verifica);
$stmt->bind_param("ii", $produto_id, $usuario_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo "<script>
        alert('Você não pode remover este produto.');
        window.location.href = '../dashboard.php';
    </script>";
    exit();
}

// Remove a compra do produto
$query_remover = "UPDATE produtos SET id_comprador = NULL WHERE id = ?";
$stmt = $conn->prepare($query_remover);
$stmt->bind_param("i", $produto_id);

if ($stmt->execute()) {
    echo "<script>
        alert('Compra removida com sucesso.');
        window.location.href = '../dashboard.php';
    </script>";
} else {
    echo "<script>
        alert('Erro ao remover a compra.');
        window.location.href = '../dashboard.php';
    </script>";
}
?>
