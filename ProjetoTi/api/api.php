<?php

    // Confirma qual o método http que se está a utilizar
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if (isset($_POST['nome']) && isset($_POST['valor']) && isset($_POST['hora']))
        {
            if ($_POST['valor'] != ' nan') {
                // Coloca o conteúdo enviado como POST nos ficheiros adequados
                echo file_put_contents("files/".$_POST['nome']."/valor.txt", $_POST['valor']);
                echo file_put_contents("files/".$_POST['nome']."/hora.txt",$_POST['hora']);
                file_put_contents("files/".$_POST['nome']."/log.txt", $_POST['hora'].";".$_POST['valor'].PHP_EOL, FILE_APPEND);
            }
            else{
                // Valor inválido, não realiza as operações e retorna um código de erro
                http_response_code(400);
            }
        }else{
            http_response_code(400);
        }
    }
    elseif($_SERVER["REQUEST_METHOD"] == "GET")
    {
        // Confirma se o parâmetro nome foi passado
        if (isset($_GET["nome"]))
        {
            // Confirma se o ficheiro existe
            if (file_exists("files/".$_GET["nome"]."/valor.txt"))
            {
                // Mostra o conteúdo do ficheiro
                echo file_get_contents("files/".$_GET["nome"]."/valor.txt");
            }
        }
        else
        {
            http_response_code(400);
        }
    }
    else
    {
        http_response_code(403);
        echo("Método não permitido");
    }
    

?>

