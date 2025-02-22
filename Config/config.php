<?php

// Configurações do banco de dados
$servidor = "localhost";
$usuario = "root"; // Altere se necessário
$senha = ""; // Altere se necessário
$banco = "BRECHO_DADOS";

// Criar conexão com o banco de dados
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Definir charset UTF-8 para evitar problemas com acentos
$conn->set_charset("utf8");

// Definir a URL base para facilitar inclusão de arquivos
define('BASE_URL', 'http://localhost/BRECHOWEB'); 
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/BRECHOWEB');


?>