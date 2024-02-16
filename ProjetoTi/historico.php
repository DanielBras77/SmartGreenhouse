<?php

session_start();

// Caso o utilizador não tenha sessão iniciada ou não seja o administrador o acesso é restrito
if (!isset($_SESSION['username'])) {
    header("refresh:5;url=index.php");
    die("Acesso restrito.");
} else {

    // Verifica se existe a variável que indica o histórico a mostrar
    if (isset($_GET['historico'])) {

        $admin = $_SESSION['admin'];
        $gerente = $_SESSION['gerente'];
        $user = $_SESSION['user'];

        // Obtém uma lista de todas as pastas dentro da pasta files através da função scandir
        $pastas = scandir("api/files");

        // Percorre a lista de pastas 
        foreach ($pastas as $pasta) {

            // Verificamos se o nome da pasta é igual ao nome do histórico pedido 
            if ($pasta == $_GET['historico']) {
                $historico = $_GET['historico'];
                $nome = file_get_contents("api/files/" . $pasta . "/nome.txt");
            }
        }

        // Confima se a variável histórico não tiver sido definida, não é mostrado histórico
        if (!isset($historico)) {
            header("refresh:3;url=dashboard.php");
            die("Histórico não encontrado!");
        }
    } else {
        // Se a variável não corresponder a nenhum histórico, mostra uma mensagem de erro  
        echo "Este histórico não existe!";
        header("refresh:0;url=dashboard.php");
    }
}

?>



<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Histórico <?php echo $nome ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="css/mystyle.css">

</head>

<body class="corBody">

    <!-- Menu Lateral -->
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-success">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                    <a href="dashboard.php" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="sidebar-brand">EI-TI</span>
                    </a>

                    <!-- Neste menu lateral podemos escolher o histórico pretendido enviando um pedido GET à variável historico -->
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                        <?php
                        if ($admin || $gerente || $user) {
                            echo '
                                <li class="nav-item">
                                <a href="dashboard.php" class="nav-link align-middle px-0">
                                    <span class="sidebar-item">Dashboard</span>
                                </a>
                                </li>
                                <li>
                                    <a href="#submenu3" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                    <span class="sidebar-item">Histórico</span> </a>
                                    <ul class="collapse nav flex-column ms-1" id="submenu3" data-bs-parent="#menu">
                                        <li class="w-100">
                                            <a href="historico.php?historico=temperatura" class="nav-link px-0"><span class="sidebar-item">Temperatura</span></a>
                                        </li>
                                        <li>
                                            <a href="historico.php?historico=humidade" class="nav-link px-0"><span class="sidebar-item">Humidade</span></a>
                                        </li>
                                        <li>
                                            <a href="historico.php?historico=luminosidade" class="nav-link px-0"><span class="sidebar-item">Luminosidade</span></a>
                                        </li>
                                        <li>
                                            <a href="historico.php?historico=arCondicionado" class="nav-link px-0"><span class="sidebar-item">Ar Condicionado</span></a>
                                        </li>
                                        <li>
                                            <a href="historico.php?historico=atuadorHumidade" class="nav-link px-0"><span class="sidebar-item">Atuador Humidade</span></a>
                                        </li>
                                        <li>
                                            <a href="historico.php?historico=atuadorLuminosidade" class="nav-link px-0"><span class="sidebar-item">Iluminação</span></a>
                                        </li>
                                    </ul>
                                </li>
                                ';
                        }

                        if ($admin || $gerente) {
                            echo '
                                <li>
                                    <a href="#submenu4" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                    <span class="sidebar-item">Estatísticas</span> </a>
                                    <ul class="collapse nav flex-column ms-1" id="submenu4" data-bs-parent="#menu">
                                        <li class="w-100">
                                            <a href="estatisticas.php?estatisticas=temperatura" class="nav-link px-0"><span class="sidebar-item">Temperatura</span></a>
                                        </li>
                                        <li>
                                            <a href="estatisticas.php?estatisticas=humidade" class="nav-link px-0"><span class="sidebar-item">Humidade</span></a>
                                        </li>
                                        <li>
                                            <a href="estatisticas.php?estatisticas=luminosidade" class="nav-link px-0"><span class="sidebar-item">Luminosidade</span></a>
                                        </li>
                                    </ul>
                                </li>
                                ';
                        }

                        if ($admin) {
                            echo '
                                    <li class="nav-item">
                                    <a href="camera.php" class="nav-link align-middle px-0">
                                        <span class="sidebar-item">Câmara</span>
                                    </a>
                                    </li>
                            ';
                        }
                        echo '<li><br></li>';
                        ?>
                        <li>
                            <a href="logout.php" class="btn btn-outline-light">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Conteúdo da página -->
            <div class="col py-3">
                <br>
                <div class="container">
                    <div class="card">
                        <div class="card-body">
                            <div class="float-end">
                                <img src="img/estg.png" alt="imagem" width="300" height="120">
                            </div>

                            <h1>Histórico <?php echo $nome ?></h1>
                            <br>

                            <?php

                            // Mostra a descrição do histórico correspondente à variável 

                            switch ($historico) {
                                case 'temperatura':
                                case 'humidade':
                                case 'luminosidade':
                                    echo '<p>Aqui consegue visualizar quais foram os últimos valores de ' . $historico . ' medidos pelo Sensor de ' . $nome . '.</p>';
                                    break;
                                case 'arCondicionado':
                                    echo '<p>Aqui consegue visualizar quando o Ar Condicionado esteve ligado (aquecimento ou arrefecimento) ou desligado.</p>';
                                    break;
                                case 'atuadorHumidade':
                                    echo '<p>Aqui consegue visualizar quando o Atuador de Humidade esteve ligado (humidificação ou desumidificação) ou desligado.</p>';
                                    break;
                                case 'atuadorLuminosidade':
                                    echo '<p>Aqui consegue visualizar quando a Iluminação esteve ou não ligada.</p>';
                                    break;
                                default:
                                    // Se a variável não corresponder a nenhum histórico, mostra uma mensagem de erro 
                                    echo 'Este histórico não existe!';
                                    break;
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <br>
                <br>
                <?php
                switch ($historico) {
                    case 'temperatura':
                    case 'humidade':
                    case 'luminosidade':
                    case 'arCondicionado':
                    case 'atuadorHumidade':
                    case 'atuadorLuminosidade':
                ?>
                        <div class="card">

                            <div class="card-header">
                                <b><?php echo $nome ?></b>
                            </div>
                            <div class="card-body">
                                <table class="table">
                            <?php

                            $path = "api/files/" . $historico . "/log.txt";

                            // Verifica se o ficheiro está vazio
                            if (filesize($path) == 0) {
                                switch ($historico) {
                                    case 'temperatura':
                                    case 'humidade':
                                    case 'luminosidade':
                                        echo "<p>Ainda não foram registados valores de " . $nome . "!</p>";
                                        break;
                                    case 'arCondicionado':
                                    case 'atuadorHumidade':
                                        echo "<p>Ainda não foram registados dados sobre o estado do " . $nome . "!</p>";
                                        break;
                                    case 'atuadorLuminosidade':
                                        echo "<p>Ainda não foram registados dados sobre a Iluminação!</p>";
                                        break;
                                    default:
                                        echo "Este histórico não existe";
                                }
                            } else {

                                echo '
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Data de Atualização</th>
                                                    <th scope="col">Valor</th>
                                                </tr>
                                            </thead>
                                            <tbody>';

                                // Lê o conteúdo do ficheiro de texto
                                $linhas = file($path);

                                // Inverte o conteúdo do ficheiro para aparecerem os valores +recentes em primeiro lugar:
                                $linhasNovas = array_reverse($linhas);

                                /*  Mostra linha por linha com um delimitador consoante o sensor ou atuador:
                                            função explode(delimitador, string dividida, número máximo de colunas) */


                                switch ($historico) {
                                    case 'temperatura':
                                        foreach ($linhasNovas as $linha) {
                                            $dados = explode(";", $linha);
                                            echo "<tr><td>" . $dados[0] . "</td><td>" . $dados[1] . "°C</td></tr>";
                                        }
                                        break;
                                    case 'humidade':
                                        foreach ($linhasNovas as $linha) {
                                            $dados = explode(";", $linha);
                                            echo "<tr><td>" . $dados[0] . "</td><td>" . $dados[1] . "%</td></tr>";
                                        }
                                        break;
                                    case 'luminosidade':
                                        foreach ($linhasNovas as $linha) {
                                            $dados = explode(";", $linha);
                                            echo '<tr><td>' . $dados[0] . '</td>';
                                            if ($dados[1] <= 10) {
                                                echo '<td>Iluminado</td></tr>';
                                            } else {
                                                echo '<td>Escuro</td></tr>';
                                            }
                                        }
                                        break;
                                    case 'arCondicionado':
                                        foreach ($linhasNovas as $linha) {
                                            $dados = explode(";", $linha);
                                            echo '<tr><td>' . $dados[0] . '</td>';
                                            if ($dados[1] == 1) {
                                                echo '<td>Aquecimento Ligado</td></tr>';
                                            } elseif ($dados[1] == 2) {
                                                echo '<td>Arrefecimento Ligado</td></tr>';
                                            } else {
                                                echo '<td>Desligado</td></tr>';
                                            }
                                        }
                                        break;
                                    case 'atuadorHumidade':
                                        foreach ($linhasNovas as $linha) {
                                            $dados = explode(";", $linha);
                                            echo '<tr><td>' . $dados[0] . '</td>';
                                            if ($dados[1] == 1) {
                                                echo '<td>Humidificação Ligada</td></tr>';
                                            } elseif ($dados[1] == 2) {
                                                echo '<td>Desumidificação Ligada</td></tr>';
                                            } else {
                                                echo '<td>Desligado</td></tr>';
                                            }
                                        }
                                        break;
                                    case 'atuadorLuminosidade':
                                        foreach ($linhasNovas as $linha) {
                                            $dados = explode(";", $linha);
                                            echo '<tr><td>' . $dados[0] . '</td>';
                                            if ($dados[1] == 1) {
                                                echo '<td>Ligada</td></tr>';
                                            } else {
                                                echo '<td>Desligada</td></tr>';
                                            }
                                        }
                                        break;
                                    default:
                                        echo "Este histórico não existe";
                                }
                                echo '</tbody>';
                            }
                    }
                            ?>
                                </table>
                            </div>
                        </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

</body>

</html>