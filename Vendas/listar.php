<?php
include dirname(__DIR__) . '/Config/config.php';
session_start();

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$sql = "SELECT * FROM produtos WHERE nome LIKE ? OR descricao LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $q . "%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$produtos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da Pesquisa</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/sheets/styles/style.css">
    <style>

.produtos-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr); /* 6 colunas */
    gap: 50px; /* Espaçamento entre os itens */
    padding: 20px;
    max-width: 1600px; /* Ajuste para suportar 6 itens confortavelmente */
    margin: 0 auto;
}

.produto {
    border: 1px solid #ddd;
    border-radius: 12px;
    overflow: hidden;
    text-align: center;
    background: white;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    width: 100%;
}

.produto img {
    width: 100%;
    height: 180px; 
    object-fit: contain;
    padding: 10px;
}

.comprar-btn {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.2s ease;
        }

        .comprar-btn:hover {
            background-color: #218838;
        }

        .mensagem {
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    color: #777; /* Cor neutra */
    margin-top: 50px;
    grid-column: span 6; /* Faz com que ocupe todas as colunas */
    justify-self: center; /* Alinha dentro do grid */
}

    </style>
</head>
<body>

<header class="header">
    <div class="logo">Moda Sustentável</div>

    <div class="search-bar">
    <form action="<?php echo BASE_URL; ?>/Vendas/listar.php" method="GET">
        <input type="text" name="q" placeholder="Busque por produtos..." required>
        <button type="submit">➤</button>
    </form>
    </div>

    <nav class="nav-links">
        <a href="<?php echo BASE_URL; ?>/index.php">Início</a>
        <a href="<?php echo BASE_URL; ?>/Categorias/fem.php">Mulheres</a>
        <a href="<?php echo BASE_URL; ?>/Categorias/masc.php">Homens</a>
        <?php if (isset($_SESSION["usuario_id"])): ?>
                <a href="<?php echo BASE_URL; ?>/Vendas/dashboard.php">Minha Conta</a>
                <a href="<?php echo BASE_URL; ?>/UserCentral/logout.php">Sair</a>
        <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/UserCentral/login.php">Entrar</a>
        <?php endif; ?>
        <a href="<?php echo BASE_URL; ?>/Vendas/adicionar_venda.php" class="btn-vender">Quero vender</a>
    </nav>
</header>

<main>
    <section class="produtos">
        <h2>Resultados para: "<?= htmlspecialchars($q) ?>"</h2>
        <div class="produtos-grid">
            <?php if ($produtos->num_rows > 0): ?>
                <?php while ($produto = $produtos->fetch_assoc()): ?>
                    <div class="produto">
                        <img src="<?= BASE_URL ?>/sheets/img/vendas/<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>">
                        <h3><?= $produto['nome'] ?></h3>
                        <p>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                        <a href="<?php echo BASE_URL; ?>/Vendas/adicionar_venda.php?produto_id=<?= $produto['id'] ?>" class="comprar-btn">Comprar</a>
                    </div>
                <?php endwhile; ?>
                <?php else: ?>
    <p class="mensagem">Nenhum produto encontrado.</p>
                <?php endif; ?>
        </div>
    </section>
</main>

</body>
</html>
