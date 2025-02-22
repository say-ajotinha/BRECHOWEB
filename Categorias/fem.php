<?php
include dirname(__DIR__) . '/Config/config.php';
session_start();

// Corrigindo a consulta SQL para remover espaços extras do campo 'genero'
$query = "SELECT * FROM produtos WHERE genero = 'Feminino' AND (id_comprador IS NULL OR id_comprador = '')";
$produtos = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moda Feminina - Brechó Sustentável</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/sheets/styles/style.css">
    <style>

.produtos-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr); /* 6 colunas padrão */
    gap: 40px; /* Espaçamento reduzido para melhor equilíbrio */
    padding: 20px;
    max-width: 1600px; /* Mantém o limite máximo */
    margin: 0 auto;
}

/* Responsividade */
@media (max-width: 1400px) {
    .produtos-grid {
        grid-template-columns: repeat(4, 1fr); /* 4 colunas em telas menores */
    }
}

@media (max-width: 900px) {
    .produtos-grid {
        grid-template-columns: repeat(2, 1fr); /* 2 colunas em tablets */
    }
}

@media (max-width: 600px) {
    .produtos-grid {
        grid-template-columns: repeat(1, 1fr); /* 1 coluna no celular */
    }
}

.produto {
    border: 1px solid #ddd;
    border-radius: 12px;
    overflow: hidden;
    text-align: center;
    background: white;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    width: 100%;
    padding-bottom: 10px;
}

/* Efeito hover para destaque */
.produto:hover {
    transform: scale(1.05);
    box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.1);
}

.produto img {
    width: 100%;
    height: 200px; /* Altura fixa para padronização */
    object-fit: cover; /* Corta a imagem para cobrir o espaço */
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.produto h3 {
    font-size: 18px;
    margin: 10px 0;
    color: #333;
}

.produto p {
    font-size: 16px;
    font-weight: bold;
    color: #27ae60; /* Verde para realçar o preço */
}

.comprar-btn {
    display: inline-block;
    background: #27ae60;
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: background 0.3s;
}

.comprar-btn:hover {
    background: #219150;
}

.banner {
    position: relative;
    background: linear-gradient(to right,rgb(53, 31, 13), #53361e); /* Fundo base */
    padding: 100px 20px;
    text-align: center;
    border-radius: 12px;
    box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
    max-width: 1200px;
    margin: 20px auto;
    overflow: hidden;
}

/* Criamos um container interno para mais bolhas */
.banner .blobs {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
}

/* Blobs orgânicos para efeito de lâmpada de lava */
.banner::before, 
.banner::after, 
.banner .blobs span {
    content: "";
    position: absolute;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255, 150, 50, 0.7), rgba(255, 50, 50, 0.5));
    border-radius: 50%;
    filter: blur(50px);
    opacity: 0.7;
    animation: lava-move 8s infinite alternate ease-in-out;
}

/* Ajustando posições para mais bolhas */
.banner::before {
    top: 15%;
    left: 10%;
}

.banner::after {
    bottom: 15%;
    right: 10%;
    animation-delay: 4s;
}

/* Criamos 3 bolhas extras */
.banner .blobs span:nth-child(1) {
    top: 30%;
    left: 25%;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(255, 200, 50, 0.6), rgba(255, 100, 50, 0.4));
    animation-duration: 10s;
}

.banner .blobs span:nth-child(2) {
    top: 50%;
    left: 60%;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(255, 80, 50, 0.7), rgba(255, 30, 50, 0.5));
    animation-duration: 7s;
    animation-delay: 2s;
}

.banner .blobs span:nth-child(3) {
    bottom: 10%;
    left: 40%;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(255, 180, 80, 0.6), rgba(255, 70, 50, 0.4));
    animation-duration: 9s;
    animation-delay: 3s;
}

/* Animação das blobs */
@keyframes lava-move {
    0% {
        transform: translateX(0) translateY(0) scale(1);
    }
    50% {
        transform: translateX(30px) translateY(-30px) scale(1.2);
    }
    100% {
        transform: translateX(-30px) translateY(30px) scale(1);
    }
}

/* Conteúdo dentro do banner */
.banner h1 {
    font-family: 'Poppins', sans-serif;
    font-size: 42px;
    font-weight: 700;
    color: white; /* Cor do texto */
    -webkit-text-stroke: 1px #fdcfa9; /* Traçado ao redor do texto */
    text-align: center;
    position: relative;
    z-index: 1;
}

.banner p {
    font-family: 'Lato', sans-serif;
    font-size: 18px;
    color: #ffffff;
    max-width: 700px;
    margin: 10px auto;
    position: relative;
    z-index: 1;
}

.banner a {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    background: #1a1a2e;
    color: white;
    border-radius: 25px;
    transition: background 0.3s ease-in-out;
    position: relative;
    z-index: 1;
}

.banner a:hover {
    background: #0d0d1a;
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
        <a href="masc.php">Homens</a>
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
    <section class="banner">
        <h1>Moda Feminina</h1>
        <p>Encontre as melhores peças femininas sustentáveis!</p>
    </section>

    <section class="produtos">
        <h2>Produtos Disponíveis</h2>
        <div class="produtos-grid">
            <?php while ($produto = $produtos->fetch_assoc()): ?>
                <div class="produto">
                    <img src="<?= BASE_URL ?>/sheets/img/vendas/<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>">
                    <h3><?= $produto['nome'] ?></h3>
                    <p>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                    <a href="<?= BASE_URL ?>/Vendas/comprar.php?produto_id=<?= $produto['id'] ?>" class="comprar-btn">Comprar</a>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
</main>

</body>
</html>
