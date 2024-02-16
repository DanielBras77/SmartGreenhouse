<?php

session_start();

// Caso o utilizador não tenha sessão iniciada o acesso é restrito
if (!isset($_SESSION['username'])) {
    header("refresh:5;url=index.php");
    die("Acesso restrito.");
} else {
    // Confirma qual o método http que se está a utilizar
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pasta']) && isset($_POST['valor']))
    {
        $username = $_SESSION['username'];

        // Guarda numa variável a data e hora atuais
        date_default_timezone_set('Europe/Lisbon');
        $data_Atual = date('Y-m-d H:i:s');
        
        $valor = $_POST['valor'];
        
        // Coloca o valor do atuador nos ficheiro valor.txt e log.txt correspondentes à sua pasta
        file_put_contents("api/files/".$_POST['pasta']."/valor.txt", $valor);
        file_put_contents("api/files/".$_POST['pasta']."/log.txt", $data_Atual.";".$valor.PHP_EOL, FILE_APPEND);
        header("refresh:0;url=dashboard.php");
    }
    else{
        
        http_response_code(400);
    }
    
}

?>