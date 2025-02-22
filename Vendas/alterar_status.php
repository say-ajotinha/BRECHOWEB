<?php
session_start();
require '../config.php';

if (!isset($_SESSION['usuario_id'])) {
    die("Acesso negado.");
}

$usuario_id = $_SESSION['usuario_id'];
$venda_id = $_GET['venda_id'] ?? null;
$acao = $_GET['acao'] ?? '';

if (!$venda_id || !in_array($acao, ['cancelar', 'confirmar'])) {
    die("Requisição inválida.");
}

// Verifica se o usuário tem permissão
$sql = "SELECT * FROM vendas WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $venda_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Ação não permitida.");
}

// Atualiza o status da compra
$status = ($acao == 'cancelar') ? 'Disponível' : 'Vendido';
$sql = "UPDATE vendas SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $venda_id);

if ($stmt->execute()) {
    echo "<script>alert('Status atualizado!'); window.location.href='../dashboard.php';</script>";
} else {
    echo "<script>alert('Erro ao atualizar status.');</script>";
}
?>
