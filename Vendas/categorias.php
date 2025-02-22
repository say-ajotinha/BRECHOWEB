<?php
include dirname(__DIR__) . '/Config/config.php';
session_start();

// Obtém todas as categorias distintas do banco de dados
$categorias = $conn->query("SELECT DISTINCT categoria FROM produtos");

// Obtém a categoria selecionada, se houver
$categoriaSelecionada = isset($_GET['categoria']) ? $_GET['categoria'] : null;

// Obtém os produtos da categoria selecionada que NÃO foram comprados
if ($categoriaSelecionada) {
    $query = "SELECT * FROM produtos WHERE categoria = ? AND (id_comprador = 0 OR id_comprador IS NULL)";
    $produtos = $conn->prepare($query);
    $produtos->bind_param("s", $categoriaSelecionada);
    $produtos->execute();
    $resultado = $produtos->get_result();
} 
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias - Moda Sustentável</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/sheets/styles/style.css">
    <style>
/* Seção de Categorias */
.categorias {
    text-align: center;
    padding: 20px;
}

/* Título das Categorias */
.categorias h2 {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}

/* Grid de Categorias */
.categorias-grid {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
}

/* Estilo dos botões de categoria */
.categorias-grid .categoria {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #4c2200;
    color: white;
    border-radius: 25px;
    padding: 10px 20px;
    font-size: 16px;
    text-transform: uppercase;
    font-weight: bold;
    transition: all 0.3s ease;
}

/* Remove o sublinhado do link */
.categorias-grid .categoria a {
    text-decoration: none;
    color: white;
}

/* Efeito hover */
.categorias-grid .categoria:hover {
    background-color: #a05418;
    transform: scale(1.05);
}

        /* Grid de Produtos */
        .produtos-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .produto {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            background: white;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            width: 200px;
        }

        .produto img {
            width: 100%;
            height: 180px;
            object-fit: cover;
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
            <a href="../categorias/fem.php">Mulheres</a>
            <a href="../categorias/masc.php">Homens</a>
            <?php if (isset($_SESSION["usuario_id"])): ?>
                <a href="../Vendas/dashboard.php">Minha Conta</a>
                <a href="../UserCentral/logout.php">Sair</a>
            <?php else: ?>
                <a href="../UserCentral/login.php">Entrar</a>
            <?php endif; ?>
            <a href="../Vendas/adicionar_venda.php" class="btn-vender">Quero vender</a>
        </nav>
</header>

<!-- Seção de Categorias -->
<section class="categorias">
    <h2>Categorias</h2>
    <div class="categorias-grid">
        <?php while ($categoria = $categorias->fetch_assoc()): ?>
            <div class="categoria">
                <a href="../Vendas/categorias.php?categoria=<?= urlencode($categoria['categoria']) ?>">
                    <?= strtoupper($categoria['categoria']) ?>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Exibição dos Produtos da Categoria Selecionada -->
<?php if ($categoriaSelecionada): ?>
    <section class="produtos">
        <h2 class="titles-aj">Produtos da categoria: <?= strtoupper($categoriaSelecionada) ?></h2>
        
        <?php if ($resultado->num_rows > 0): ?>
            <div class="produtos-grid">
                <?php while ($produto = $resultado->fetch_assoc()): ?>
                    <div class="produto">
                        <img src="<?= BASE_URL ?>/sheets/img/vendas/<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>">
                        <h3><?= $produto['nome'] ?></h3>
                        <p>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                        <a href="<?= BASE_URL ?>/Vendas/comprar.php?produto_id=<?= $produto['id'] ?>" class="comprar-btn">Comprar</a>
                        </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="mensagem">Nenhum produto disponível nesta categoria.</p>
        <?php endif; ?>
    </section>
<?php endif; ?>

</body>
</html>