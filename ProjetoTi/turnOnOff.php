<?php

session_start();

// Caso o utilizador não tenha sessão iniciada o acesso é restrito
if (!isset($_SESSION['username'])) {
    header("refresh:5;url=index.php");
    die("Acesso restrito.");
} else {
    $username = $_SESSION['username'];

    // Guarda numa variável a data e hora atuais
    date_default_timezone_set('Europe/Lisbon');
    $data_Atual = date('Y-m-d H:i:s');

    // Confirma qual o método http que se está a utilizar
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if (isset($_POST['turnOff']) || isset($_POST['turnOn'])){

            if (isset($_POST['turnOff'])) {           
                $valor = '0';
            }else{
                $valor = '1';
            }
            
            // Coloca o valor da iluminação nos ficheiro valor.txt e no log.txt
            file_put_contents("api/files/atuadorLuminosidade/valor.txt", $valor);
            file_put_contents("api/files/atuadorLuminosidade/log.txt", $data_Atual.";".$valor.PHP_EOL, FILE_APPEND);
            header("refresh:0;url=dashboard.php");

        }
        else{
            http_response_code(400);
        }
    }
    else
    {
        http_response_code(403);
        echo("Método não permitido");
    } 
}


?>