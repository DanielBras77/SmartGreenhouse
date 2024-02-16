<?php

session_start();

// Caso o utilizador não tenha sessão iniciada o acesso é restrito
if (!isset($_SESSION['username'])) {
    header("refresh:5;url=index.php");
    die("Acesso restrito.");
} else {

    if ($_SESSION['admin']) {

        $admin = $_SESSION['admin'];
        $gerente = $_SESSION['gerente'];
        $user = $_SESSION['user'];
    } else {
        header("refresh:5;url=index.php");
        die("Acesso restrito.");
    }
}
?>


<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="65">
    <title>Imagens de Câmara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
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
                            <h1>Imagens de Câmara</h1>
                            <br>
                            <p>Aqui pode ver as últimas imagens capturadas pela câmara de segurança.</p>
                        </div>
                    </div>
                </div>
                <br>
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card text-center">
                                <div class="card-header">
                                    <p><b>Última Imagem Capturada</b></p>
                                </div>
                                <?php
                                $imagemPath = 'api/imagem/webcam.jpg';

                                if (file_exists($imagemPath)) {
                                    echo "<div class='card-body'>
                                                  <img src='$imagemPath?id=" . time() . "' style='width:100%'>
                                              </div>";

                                    $dataHora = date("Y-m-d H:i:s", filemtime("api/imagem/webcam.jpg"));
                                    echo "<div class='card-footer'>
                                                  <b>Atualização:</b>
                                                  <div class='image-info'>$dataHora</div>
                                              </div>";
                                } else {
                                    echo "<div class='card-body'>Ainda não foi capturada nenhuma imagem!</div>";
                                }
                                ?>


                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card text-center">
                                <div class="card-header">
                                    <p><b>Histórico de Imagens</b></p>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Pasta que tem o histórico de imagens
                                    $historicoImagens = "api/historicoImagens";

                                    // Obtém a lista de ficheiros com extensão .jpg na pasta do histórico
                                    $imagens = glob("$historicoImagens/*.jpg");

                                    if (!empty($imagens)) {
                                        // Ordena a lista por ordem decrescente com base na data de modificação 
                                        usort($imagens, function ($a, $b) {
                                            return filemtime($b) - filemtime($a);
                                        });

                                        // Faz 3 iterações para mostrar as 3 imagens mais recentes (ou menos se houver menos de 3)
                                        $count = min(count($imagens), 3);
                                        for ($i = 0; $i < $count; $i++) {
                                            $imagem = $imagens[$i];
                                            $dataHora = date("Y-m-d H:i:s", filemtime($imagem));
                                    ?>

                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <div class="image-container">
                                                        <img src="<?php echo $imagem; ?>" style="width: 161px;">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="dataHora text-center"><?php echo $dataHora; ?></div>
                                                </div>
                                            </div>
                                            <br>

                                    <?php
                                        }
                                    } else {
                                        echo "Não existem imagens para mostrar!";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

</body>

</html>