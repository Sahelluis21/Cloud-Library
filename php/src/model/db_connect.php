<?php

$host = getenv('DB_HOST'); 
$dbname = getenv('DB_NAME'); 
$user = getenv('DB_USER'); 
$password = getenv('DB_PASSWORD'); 

try {
    $conn = new PDO(
        "pgsql:host=$host;dbname=$dbname",
         $user,
         $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    // Teste opcional para verificar a conexão
    $conn->exec("SELECT 1");
    error_log("Conexão com PostgreSQL estabelecida com sucesso");
    
} catch (PDOException $e) {
    error_log("ERRO DB: " . $e->getMessage());
    die("Erro de conexão com o banco de dados. Contate o administrador.");
}
?>