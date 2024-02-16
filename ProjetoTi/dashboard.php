<?php

session_start();

// Caso o utilizador não tenha sessão iniciada o acesso é restrito
if (!isset($_SESSION['username'])) {
    header("refresh:5;url=index.php");
    die("Acesso restrito.");
} else {
    $username = $_SESSION['username'];

    $admin = false;
    $gerente = false;
    $user = true;

    if ($username == 'admin') {
        $admin = true;

    } else if ($username == 'gerente') {
        $gerente = true;   
    } 

    $_SESSION['admin'] = $admin;
    $_SESSION['gerente'] = $gerente;
    $_SESSION['user'] = $user;
}

?>

<?php

// Coloca nas variáveis o conteúdo dos ficheiros

// Obtém uma lista de todas as pastas dentro da pasta files através da função scandir
$pastas = scandir("api/files");

// Percorre a lista de pastas e concatena o nome da pasta em que está à variável correspondida
foreach ($pastas as $pasta) {

    // Como a função scandir devolve também a pasta atual e a pasta pai do diretório, é necessário fazer este if
    if ($pasta != "." && $pasta != "..") {

        $path = ("api/files/" . $pasta . "/");

        ${"valor_" . $pasta} = file_get_contents($path . "valor.txt");
        ${"hora_" . $pasta} = file_get_contents($path . "hora.txt");
        ${"nome_" . $pasta} = file_get_contents($path . "nome.txt");
    }
}


// De acordo com os valores dos sensores (se são os valores ideais ou não), percebe o estado geral da estufa  

$valoresDeStatus = 0;

if ($valor_temperatura > 35 || $valor_temperatura < 15) {
    $valoresDeStatus += 1;
}

if ($valor_humidade > 70 || $valor_humidade < 50) {
    $valoresDeStatus += 1;
}

if ($valor_luminosidade == 0) {
    $valoresDeStatus += 1;
}

?>


<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="dashboard.js"></script>

    <link rel="stylesheet" href="css/mystyle.css">

    <style>
        h4 {
            font-family: "Monaco", Sans-serif;
            margin-top: 10px;
        }
    </style>
</head>


<body class="corBody">

    <!-- Menu Lateral -->
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-success">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                    <a href="dashboard.php" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="sidebar-brand">Dashboard EI-TI</span>
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
                            <h1>Estufa Inteligente</h1>
                            <p>Bem-vindo <b><?php echo $username; ?></b></p>
                            <small>Tecnologias de Internet - Engenharia Informática</small>
                        </div>
                    </div>
                </div>


                <br>
                <br>
                <br>


                <div class="container text-center">

                    <!-- Status Geral -->
                    <div class="row">
                        <div class='card' id="status-geral">
                            <p class="status"> Status Geral </p>
                        </div>
                    </div>

                    <br>
                    <br>
                    <br>

                    <!-- Cartões de Sensores-->
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="card borda">
                                <h4> <?php echo $nome_temperatura; ?> </h4>
                                <p class="valor" id="valor_temperatura"></p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card borda">
                                <h4> <?php echo $nome_humidade; ?> </h4>
                                <p class="valor" id="valor_humidade"></p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card borda">
                                <h4> <?php echo $nome_luminosidade; ?> </h4>
                                <p class="valor" id="valor_luminosidade"></p>
                            </div>
                        </div>
                    </div>

                    <br>
                    <br>
                    <br>

                    <!-- Cartões de Atuadores-->
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="card">
                                <div class="card-header">
                                    <b>Ar condicionado</b>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Se o atuador de temperatura estiver a 1 signfica que está ligado e a aquecer
                                    if ($valor_arCondicionado == 1) {
                                        echo '<img src="img/aquecimento.png" alt="imagem" width="160" height="150" class="center">';
                                    }
                                    // Se o atuador de temperatura estiver a 2 signfica que está ligado e a arrefecer
                                    elseif ($valor_arCondicionado == 2) {
                                        echo '<img src="img/arrefecimento.png" alt="imagem" width="160" height="150" class="center">';
                                    }
                                    // Se o atuador de temperatura estiver a 0 significa que está desligado
                                    else {
                                        echo '<img src="img/arCondicionado.png" alt="imagem" width="150" height="150" class="center">';
                                    }
                                    ?>
                                </div>

                                <!-- Botôes para ligar aquecimento, arrefecimento e desligar -->
                                <div class="card-footer">
                                    <form method="post" action="mudarEstado.php">
                                        <?php
                                        if ($valor_arCondicionado == 1) {
                                            echo '<input type="hidden" name="pasta" value="arCondicionado"><br>';
                                            echo '<input type="hidden" name="valor" value="0">';
                                            echo '<button type="submit" class="btn btn-outline-danger">Desligar</button>';
                                        } elseif ($valor_arCondicionado == 2) {
                                            echo '<input type="hidden" name="pasta" value="arCondicionado"><br>';
                                            echo '<input type="hidden" name="valor" value="0">';
                                            echo '<button type="submit" class="btn btn-outline-danger">Desligar</button>';
                                        } else {
                                            echo '<input type="hidden" name="pasta" value="arCondicionado">';
                                            echo '<div class="btn-group" role="group">
                                                    <button type="submit" name="valor" value="1" class="btn btn-outline-danger">Ligar Aquecimento</button>
                                                    <button type="submit" name="valor" value="2" class="btn btn-outline-primary">Ligar Arrefecimento</button>
                                                    </div>';
                                        }
                                        ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card">
                                <div class="card-header">
                                    <b>Atuador de Humidade</b>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Se o atuador de humidade estiver a 1 signfica que está ligado e a humidificar
                                    if ($valor_atuadorHumidade == 1) {
                                        echo '<img src="img/humidificador.jpg" alt="imagem" width="160" height="150" class="center">';
                                    }
                                    // Se o atuador de humidade estiver a 2 signfica que está ligado e a desumidificar
                                    elseif ($valor_atuadorHumidade == 2) {
                                        echo '<img src="img/desumidificador.png" alt="imagem" width="160" height="150" class="center">';
                                    }
                                    // Se o atuador de humidade estiver a 0 significa que está desligado
                                    else {
                                        echo '<img src="img/atuadorHumidade.jpg" alt="imagem" width="200" height="150" class="center">';
                                    }
                                    ?>
                                </div>

                                <!-- Botôes para ligar humidificação, desumidificação e desligar -->
                                <div class="card-footer">
                                    <form method="post" action="mudarEstado.php">
                                        <?php
                                        if ($valor_atuadorHumidade == 1) {
                                            echo '<input type="hidden" name="pasta" value="atuadorHumidade"><br>';
                                            echo '<input type="hidden" name="valor" value="0">';
                                            echo '<button type="submit" class="btn btn-outline-danger">Desligar</button>';
                                        } elseif ($valor_atuadorHumidade == 2) {
                                            echo '<input type="hidden" name="pasta" value="atuadorHumidade"><br>';
                                            echo '<input type="hidden" name="valor" value="0">';
                                            echo '<button type="submit" class="btn btn-outline-danger">Desligar</button>';
                                        } else {
                                            echo '<input type="hidden" name="pasta" value="atuadorHumidade">';
                                            echo '<div class="btn-group" role="group">
                                                    <button type="submit" name="valor" value="1" class="btn btn-outline-primary">Ligar Humidificação</button>
                                                    <button type="submit" name="valor" value="2" class="btn btn-outline-warning">Ligar Desumidificação</button>
                                                    </div>';
                                        }
                                        ?>
                                    </form>
                                </div>
                            </div>

                        </div>
                        <div class="col-sm-4">
                            <div class="card">
                                <div class="card-body">
                                    <img src="img/lampadaVerde.jpg" alt="imagem" width="193" height="191" class="center">
                                </div>
                                <div class="card-footer">
                                    <!-- Botão para ligar e desligar a iluminação  -->
                                    <form method="post" action="turnOnOff.php">
                                        <?php
                                        if ($valor_atuadorLuminosidade  == 1) {
                                            echo
                                            '<br><button type="submit" name="turnOff" class="btn btn-outline-danger">Desligar Luzes</button>';
                                        } else {
                                            echo
                                            '<br><button type="submit" name="turnOn" class="btn btn-outline-success">Ligar Luzes</button>';
                                        }
                                        ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <br>
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <b>Alarme de Emergência</b>
                            </div>
                            <div class="card-body" id="imagem_alarme"></div>
                            <div class="card-footer" id="card_alarme"><p id="mensagem_alarme"></p></div>
                        </div>
                    </div>

                    <br>
                    <br>
                    <br>

                    <!--Tabela com Informações dos sensores -->
                    <div class="card">
                        <div class="card-header">
                            <b>Tabela de sensores</b>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Tipo de Dispositivo IoT</th>
                                        <th scope="col">Valor</th>
                                        <th scope="col">Data de Atualização</th>
                                        <th scope="col">Estado Alertas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo $nome_temperatura; ?></td>
                                        <td id="valor_temperaturaTabela"></td>
                                        <td id="hora_temperatura"></td>
                                        <td id="estado_temperatura"></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $nome_humidade; ?></td>
                                        <td id="valor_humidadeTabela"></td>
                                        <td id="hora_humidade"></td>
                                        <td id="estado_humidade"></td>
                                    </tr>
                                    <tr>
                                        <td> <?php echo $nome_luminosidade; ?></td>
                                        <td id="valor_luminosidadeTabela"></td>
                                        <td id="hora_luminosidade"></td>
                                        <td id="estado_luminosidade"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

</body>

</html>