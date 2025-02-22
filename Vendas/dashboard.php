<?php
session_start();
include dirname(__DIR__) . '/Config/config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../UserCentral/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Produtos anunciados e disponíveis para venda (id_comprador = 0)
$query_disponiveis = "SELECT * FROM produtos WHERE id_usuario = ? AND (id_comprador = 0 OR id_comprador IS NULL)";
$stmt = $conn->prepare($query_disponiveis);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result_disponiveis = $stmt->get_result();

// Produtos vendidos, aguardando finalização (id_comprador preenchido)
$query_aguardando = "
    SELECT p.*, u.nome AS nome_comprador
    FROM produtos p
    JOIN usuarios u ON p.id_comprador = u.id
    WHERE p.id_usuario = ? AND p.id_comprador IS NOT NULL AND p.id_comprador != 0
";
$stmt = $conn->prepare($query_aguardando);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result_aguardando = $stmt->get_result();

// Produtos já vendidos (tabela `vendidos`)
$query_vendidos = "SELECT * FROM vendidos WHERE id_vendedor = ?";
$stmt = $conn->prepare($query_vendidos);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result_vendidos = $stmt->get_result();

// Produtos comprados (ativos na tabela `produtos`)
$query_comprados = "
    SELECT p.*, u.nome AS nome_vendedor
    FROM produtos p
    JOIN usuarios u ON p.id_usuario = u.id
    WHERE p.id_comprador = ?
";
$stmt = $conn->prepare($query_comprados);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result_comprados = $stmt->get_result();

// Produtos comprados e baixados (tabela `vendidos`)
$query_comprados_vendidos = "
    SELECT v.*, u.nome AS nome_vendedor
    FROM vendidos v
    JOIN usuarios u ON v.id_vendedor = u.id
    WHERE v.id_comprador = ?
";
$stmt = $conn->prepare($query_comprados_vendidos);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result_comprados_vendidos = $stmt->get_result();

$usuario_id = $_SESSION['usuario_id'];

// Produtos anunciados e disponíveis para venda
$query_disponiveis = "SELECT * FROM produtos WHERE id_usuario = ? AND (id_comprador = 0 OR id_comprador IS NULL)";
$stmt = $conn->prepare($query_disponiveis);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result_disponiveis = $stmt->get_result();

// Produtos vendidos, aguardando finalização
$query_aguardando = "
    SELECT p.*, u.nome AS nome_comprador
    FROM produtos p
    JOIN usuarios u ON p.id_comprador = u.id
    WHERE p.id_usuario = ? AND p.id_comprador IS NOT NULL AND p.id_comprador != 0
";
$stmt = $conn->prepare($query_aguardando);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result_aguardando = $stmt->get_result();

// Produtos já vendidos (tabela `vendidos`) com nome do comprador
$query_vendidos = "
    SELECT v.*, u.nome AS nome_comprador
    FROM vendidos v
    JOIN usuarios u ON v.id_comprador = u.id
    WHERE v.id_vendedor = ?
";
$stmt = $conn->prepare($query_vendidos);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result_vendidos = $stmt->get_result();


?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/sheets/styles/style.css">
    <style>
        /* Estilos específicos do Dashboard */
        .container {
            max-width: 2500px; /* Define a largura máxima */
            margin: 0 auto; /* Centraliza a página */
            padding: 20px;
        }

        .center {
            text-align: center;
            align-items: center;
        }

        .header {
            background-color: #fdcfa9;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-bottom: 2px solid #411a00; /* Detalhe sutil */
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
        }

        .header a {
            text-decoration: none;
            color: white;
            background:rgb(255, 255, 255);
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .header a:hover {
            background:rgb(255, 255, 255);
        }

        /* Estilo para as seções */
        section {
            margin-top: 30px;
        }

        h2 {
            color:#4c2200;
            border-bottom: 2px solidrgb(236, 187, 147);
            padding-bottom: 5px;
        }

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

        .produto:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .produto img {
            width: 100%;
            height: 180px; 
            object-fit: contain;
            padding: 10px;
        }

        .produto h3 {
            font-size: 1.2em;
            margin: 10px 0;
        }

        .produto p {
            font-size: 1em;
            color: #8f4000;
            margin: 5px 0;
        }

        .comprar-btn {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 15px;
            background-color:rgb(233, 183, 142);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.2s ease;
        }

        .comprar-btn:hover {
            background-color:#8f4000;
        }

        .baixar-btn {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 15px;
            background-color:rgb(206, 233, 142);
            color: black;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.2s ease;
        }

        .baixar-btn:hover {
            background-color:rgb(69, 143, 0);
        }

        .titles-aj1 {
    font-family: "Playfair Display", serif;
    font-size: 30px; /* Aumentei o tamanho para dar mais destaque */
    font-weight: normal; /* Menos agressivo, mantendo elegância */
    color: #4c2200; /* Cor mais suave, sem ser totalmente preta */
    text-transform: uppercase; /* Mantém a capitalização */
    letter-spacing: 1.5px; /* Menos espaçamento para um visual mais refinado */
    text-align: center; /* Centraliza o título */
    transition: all 0.3s ease-in-out; /* Suaviza qualquer transição */
}

        .nav-links1 {
            display: flex;
            gap: 20px;
        }

        .nav-links1 a {
            text-decoration: none;
            color:rgb(255, 255, 255);
            font-weight: bold;
            transition: 0.3s;
            background:#4c2200;
        }

        .nav-links1 a:hover {
            color:#8f4000;
        }

        /* Estilo para títulos h4 minimalistas e estilosos */
.Buy {
    font-family: 'Helvetica Neue', Arial, sans-serif; /* Fonte moderna e limpa */
    font-size: 26px; /* Tamanho do texto */
    font-weight: 600; /* Peso da fonte (semibold) */
    color: #333; /* Cor escura para contraste */
    text-align: center; /* Centraliza o texto */
    text-transform: uppercase; /* Transforma o texto em maiúsculas */
    letter-spacing: 2px; /* Espaçamento entre letras para um toque sofisticado */
    margin: 20px 0; /* Margem superior e inferior para espaçamento */
    padding-bottom: 10px; /* Espaçamento abaixo do texto */
    position: relative; /* Para adicionar um efeito de linha abaixo */
}

/* Efeito de linha abaixo do título */
.Buy::after {
    content: ''; /* Cria um pseudo-elemento */
    display: block;
    width: 50px; /* Largura da linha */
    height: 2px; /* Altura da linha */
    background-color: #4c2200; /* Cor da linha (marrom escuro) */
    position: absolute;
    bottom: 0;
    left: 50%; /* Centraliza a linha */
    transform: translateX(-50%); /* Ajusta a centralização */
}

/* Efeito hover para o título */
.Buy:hover {
    color: #a05418; /* Cor marrom claro ao passar o mouse */
    transition: color 0.3s ease; /* Transição suave */
}

/* Efeito hover na linha */
.Buy:hover::after {
    background-color: #a05418; /* Cor marrom claro ao passar o mouse */
    transition: background-color 0.3s ease; /* Transição suave */
}

    </style>
</head>
<body>

<div class="header">
    <h2>Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?></h2>
    <div class="titles-aj1">Moda Sustentável</div>
        <nav class="nav-links1">
            <a href="<?php echo BASE_URL; ?>/index.php">Início</a>
            <a href="<?php echo BASE_URL; ?>/Vendas/adicionar_venda.php">Anunciar</a>
            <a href="<?php echo BASE_URL; ?>/UserCentral/editar_cadastro.php">Editar Usuario</a>
            <a href="<?php echo BASE_URL; ?>/UserCentral/logout.php">Sair</a>
        </nav>
</div>

<section>
    <div class="container">
        <h4 class="Buy">Seus Produtos Anunciados para Venda</h4>
        <div class="produtos-grid">
            <?php while ($produto = $result_disponiveis->fetch_assoc()) { ?>
                <div class="produto">
                    <img src="../sheets/img/vendas/<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>">
                    <h3><?php echo $produto['nome']; ?></h3>
                    <p>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                    <p><strong>Status:</strong> Disponível</p>
                    <a href="../Vendas/editar_produto.php?id=<?= $produto['id'] ?>" class="comprar-btn">Editar</a>
                </div>
            <?php } ?>
        </div>
    </div>
</section>


<section>
    <div class="container">
        <h4 class="Buy">Produtos Vendidos - Aguardando Finalização</h4>
        <div class="produtos-grid">
            <?php while ($produto = $result_aguardando->fetch_assoc()) { ?>
                <div class="produto">
                    <img src="../sheets/img/vendas/<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>">
                    <h3><?php echo $produto['nome']; ?></h3>
                    <p>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                    <p><strong>Status:</strong> Aguardando Venda</p>
                    <p><strong>Comprador:</strong> <?php echo htmlspecialchars($produto['nome_comprador']); ?></p>
                    <a href="../Vendas/dar_baixa.php?produto_id=<?= $produto['id'] ?>" class="baixar-btn">Dar Baixa</a>
                </div>
            <?php } ?>
        </div>
    </div>
</section>


<section>
    <div class="container">
        <h4 class="Buy">Produtos Vendidos</h4>
        <div class="produtos-grid">
            <?php while ($produto = $result_vendidos->fetch_assoc()) { ?>
                <div class="produto">
                    <img src="../sheets/img/vendas/<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>">
                    <h3><?php echo $produto['nome']; ?></h3>
                    <p>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                    <p><strong>Comprador:</strong> <?php echo htmlspecialchars($produto['nome_comprador']); ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <h4 class="Buy">Seus Produtos Comprados</h4>
        <div class="produtos-grid">
            <?php while ($produto = $result_comprados->fetch_assoc()) { ?>
                <div class="produto">
                    <img src="../sheets/img/vendas/<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>">
                    <h3><?php echo $produto['nome']; ?></h3>
                    <p>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                    <p><strong>Status:</strong> Aguardando Baixa</p>
                    <p><strong>Vendedor:</strong> <?php echo htmlspecialchars($produto['nome_vendedor']); ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <h4 class="Buy">Seus Produtos Comprados (Confirmado)</h4>
        <div class="produtos-grid">
            <?php while ($produto = $result_comprados_vendidos->fetch_assoc()) { ?>
                <div class="produto">
                    <img src="../sheets/img/vendas/<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>">
                    <h3><?php echo $produto['nome']; ?></h3>
                    <p>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                    <p><strong>Status:</strong> Compra Finalizada</p>
                    <p><strong>Vendedor:</strong> <?php echo htmlspecialchars($produto['nome_vendedor']); ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

</body>
</html>