<?php
session_start();
include dirname(__DIR__) . '/Config/config.php';


// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo "<script>
        alert('Faça login para acessar esta funcionalidade.');
        window.location.href = '../UserCentral/login.php';
    </script>";
    exit();
}

$produto_id = $_GET['produto_id'];
$usuario_id = $_SESSION['usuario_id'];

// Verifica se o produto pertence ao usuário logado (quem anunciou) e já foi comprado
$query_verifica = "SELECT * FROM produtos WHERE id = ? AND id_usuario = ? AND id_comprador IS NOT NULL";
$stmt = $conn->prepare($query_verifica);
$stmt->bind_param("ii", $produto_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

// Se não encontrar nenhum produto com essas condições, bloqueia a ação
if ($result->num_rows == 0) {
    echo "<script>
        alert('Você não tem permissão para dar baixa neste produto.');
        window.location.href = '../Vendas/dashboard.php';
    </script>";
    exit();
}

$produto = $result->fetch_assoc();

// Insere o produto na tabela `vendidos`
$query_insert = "INSERT INTO vendidos (nome, descricao, preco, imagem, id_vendedor, id_comprador, categoria, genero) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query_insert);
$stmt->bind_param("ssdsiiss", 
    $produto['nome'], 
    $produto['descricao'], 
    $produto['preco'], 
    $produto['imagem'], 
    $produto['id_usuario'],  // Quem vendeu
    $produto['id_comprador'],  // Quem comprou
    $produto['categoria'], 
    $produto['genero']
);

if ($stmt->execute()) {
    // Remove o produto da tabela `produtos`
    $query_delete = "DELETE FROM produtos WHERE id = ?";
    $stmt = $conn->prepare($query_delete);
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();

    echo "<script>
        alert('Produto baixado com sucesso e movido para vendidos.');
        window.location.href = '../Vendas/dashboard.php';
    </script>";
} else {
    echo "<script>
        alert('Erro ao dar baixa no produto.');
        window.location.href = '../Vendas/dashboard.php';
    </script>";
}

$stmt->close();
?>
