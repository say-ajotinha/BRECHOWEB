<?php
include_once __DIR__ . '/Config/config.php'; 
session_start();

// Consulta para selecionar os 12 últimos produtos ordenados por data de cadastro,
// garantindo que apenas produtos não comprados sejam exibidos
$query = "SELECT * FROM produtos 
          WHERE id_comprador IS NULL OR id_comprador = '' 
          ORDER BY data_postagem DESC 
          LIMIT 12";

$produtos = $conn->query($query);
?> 

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moda como Brechó Sustentável</title>
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

/* Mantém a página estruturada corretamente */
.container {
    flex: 1; /* Faz o conteúdo ocupar o espaço antes do rodapé */
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
            }

/* Evita conflitos globais isolando os estilos */
.container-categories .categorias {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    max-width: 4000px; /* Ajuste conforme necessário */
    padding: 20px;
    background-color: rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    margin-bottom: 50px;
}

/* Mantém o título alinhado no topo */
.container-categories .titles-aj {
    font-size: 24px;
    font-weight: bold;
    text-align: center;
}

/* Contêiner dos botões */
.container-categories .categorias-botoes {
    display: flex;
    flex-direction: row; /* Alinha os botões na horizontal */
    justify-content: center;
    align-items: center;
    gap: 5px;
    width: 100%;
}

/* Botões */
.container-categories .categorias a {
    text-decoration: none; /* Remove o sublinhado do link */
}

.container-categories .categorias-btn {
    background-color: #4c2200;
    color: white;
    border: none;
    padding: 30px 50px;
    border-radius: 25px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s ease;
    white-space: nowrap;
    text-transform: uppercase;
}

/* Efeito hover */
.container-categories .categorias-btn:hover {
    background-color: #a05418;
    transform: scale(1.05);
}


/* Seção dos destaques */
.destaques-container {
    width: 100%; /* Define uma largura adequada */
    max-width: 1000px;
    background: #fff;
    padding: 100px;
    border-radius: 100px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    margin-top: 50px;
}

/* Alinhamento dos produtos */
.destaques-wrapper {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 5px;
}

/* Cartões dos produtos */
.destaque-item {
    width: 200px;
    background: #f9f9f9;
    border-radius: 8px;
    padding: 10px;
    text-align: center;
    transition: transform 0.2s ease-in-out;
}

.destaque-item:hover {
    transform: scale(1.05);
}

/* Ajuste das imagens */
.destaque-img {
    width: 100%;
    height: 150px;
    background: #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    overflow: hidden;
}

.destaque-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Ajuste no texto */
.destaque-item p {
    font-size: 14px;
    font-weight: bold;
    margin-top: 8px;
    color: #321432;
}

@media (max-width: 1024px) {
    .produtos-grid {
        grid-template-columns: repeat(3, 1fr); /* Em telas menores, ajusta para 3 colunas */
    }
}

@media (max-width: 768px) {
    .produtos-grid {
        grid-template-columns: repeat(2, 1fr); /* Em telas menores, ajusta para 2 colunas */
    }
}

@media (max-width: 480px) {
    .produtos-grid {
        grid-template-columns: repeat(1, 1fr); /* Em telas pequenas, apenas 1 produto por linha */
    }
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
    <form action="Vendas/listar.php" method="GET">
        <input type="text" name="q" placeholder="Busque por produtos..." required>
        <button type="submit">➤</button>
    </form>
</div>
        <nav class="nav-links">
            <a href="categorias/fem.php">Mulheres</a>
            <a href="categorias/masc.php">Homens</a>
            <?php if (isset($_SESSION["usuario_id"])): ?>
                <a href="Vendas/dashboard.php">Minha Conta</a>
                <a href="UserCentral/logout.php">Sair</a>
            <?php else: ?>
                <a href="UserCentral/login.php">Entrar</a>
            <?php endif; ?>
            <a href="Vendas/adicionar_venda.php" class="btn-vender">Quero vender</a>
        </nav>
    </header>

<div class="container">
    <div class="destaques-container">
    <h2 class="titles">Brecho que traz QUALIDADE!</h2>
    <div class="destaques-wrapper">
        <?php
        // Caminho das imagens
        $base_path = "sheets/img/static/";

        // Itens de destaque
        $destaques = [
            ["img" => $base_path . "01.jpg", "titulo" => "CAMISETAS BEM CUIDADAS!"],
            ["img" => $base_path . "02.jpg", "titulo" => "CALÇAS QUE FAZEM O SEU ESTILO."],
            ["img" => $base_path . "05.jpg", "titulo" => "MODA REUTILIZAVEL!"],
            ["img" => $base_path . "04.jpg", "titulo" => "VENDAS NO MEU BRECHÓ"]
        ];

        foreach ($destaques as $destaque) {
            echo "
            <div class='destaque-item'>
                <div class='destaque-img'>
                    <img src='{$destaque['img']}' alt='Imagem destaque' onerror=\"this.src='placeholder.jpg';\">
                </div>
                <p>{$destaque['titulo']}</p>
            </div>";
        }
        ?>
    </div>
</div>

<main>
    <section class="produtos">
        <h2 class="titles">Últimos Produtos Anunciados</h2>
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

<div class="container-categories">
    <!-- Menu de Categorias -->
    <div class="categorias">
        <!-- Título Centralizado -->
        <h3 class="titles-aj">CATEGORIAS</h3>
        
        <!-- Wrapper dos botões -->
        <div class="categorias-botoes">
            <?php
            // Busca as categorias no banco de dados
            $categorias = $conn->query("SELECT DISTINCT categoria FROM produtos");
            
            // Verifica se há categorias e exibe como botões
            if ($categorias->num_rows > 0):
                while ($categoria = $categorias->fetch_assoc()):
            ?>
                    <a href="Vendas/categorias.php?categoria=<?= urlencode($categoria['categoria']) ?>">
                        <button class="categorias-btn"><?= strtoupper($categoria['categoria']) ?></button>
                    </a>
            <?php
                endwhile;
            else:
                echo "<p>Nenhuma categoria encontrada.</p>";
            endif;
            ?>
        </div> <!-- Fim do wrapper -->
    </div>
</div>


</body>
    <footer class="footer">
        <p>O Meu Brechó é uma plataforma dedicada à venda de roupas, sapatos e móveis.</p>
        <p>Além de oferecer produtos de alta qualidade, contribuímos para a redução da poluição e combatemos o trabalho análogo à escravidão na moda.</p>
        <p>Além disso, destinamos 20% do lucro a instituições de caridade.</p>
        <a href="#">Exibir mais informações sobre o Meu Brechó</a>
    </footer>
</html>