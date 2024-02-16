<?php

session_start();

// Caso o utilizador não tenha sessão iniciada ou não seja o administrador o acesso é restrito
if (!isset($_SESSION['username'])) {
    header("refresh:5;url=index.php");
    die("Acesso restrito.");
} else {

    // Verifica se existe a variável que indica as estatísticas a mostrar
    if (isset($_GET['estatisticas'])) {

        $admin = $_SESSION['admin'];
        $gerente = $_SESSION['gerente'];
        $user = $_SESSION['user'];

        // Obtém uma lista de todas as pastas dentro da pasta files através da função scandir
        $pastas = scandir("api/files");

        // Percorre a lista de pastas 
        foreach ($pastas as $pasta) {

            // Verifica se o nome da pasta é igual ao nome das estatísticas pedidas
            if ($pasta == $_GET['estatisticas']) {
                $estatisticas = $_GET['estatisticas'];
                $nome = file_get_contents("api/files/" . $pasta . "/nome.txt");
            }
        }

        // Se a variável estatisticas não tiver sido definida, não são mostradas estatísticas
        if (!isset($estatisticas)) {
            header("refresh:3;url=dashboard.php");
            die("Estatísticas não encontradas!");
        }
    } else {
        // Se a variável não corresponder a nenhuma variável existente, mostra uma mensagem de erro  
        echo "Estas estatísticas não existem!";
        header("refresh:0;url=dashboard.php");
    }
}

?>

<?php

$path = "api/files/" . $estatisticas . "/log.txt";

// Guarda o conteúdo do ficheiro de texto
$linhas = file($path);

// Arrays para armazenar os dados do gráfico
$datas = array();
$valores = array();

switch ($estatisticas) {
    case 'temperatura':
    case 'humidade':
    case 'luminosidade':

        foreach ($linhas as $linha) {
            $dados = explode(";", $linha);
            $datas[] = $dados[0];
            $valores[] = $dados[1];
        }
        break;
}
?>



<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estatísticas <?php echo $nome ?></title>
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

                            <h1>Estatísticas <?php echo $nome ?></h1>
                            <br>

                            <?php

                            // Mostra a descrição do histórico correspondente à variável 

                            switch ($estatisticas) {
                                case 'temperatura':
                                case 'humidade':
                                case 'luminosidade':
                                    echo '<p>Aqui consegue visualizar estatísticas sobre os últimos valores de ' . $estatisticas . ' medidos pelo Sensor de ' . $nome . '.</p>';
                                    break;
                                default:
                                    // Se a variável não corresponder a nenhuma variável existente, mostra uma mensagem de erro 
                                    echo 'Estas estatísticas não existem!!';
                                    break;
                            }
                            ?>
                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                        </div>
                    </div>
                </div>

                <br>
                <br>

                <?php

                // Verifica se o ficheiro está vazio ou não
                if (filesize($path) == 0) {
                ?>
                    <div class="card">

                        <div class="card-header">
                            <b><?php echo $nome ?></b>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <?php
                                switch ($estatisticas) {
                                    case 'temperatura':
                                    case 'humidade':
                                    case 'luminosidade':
                                        echo "<p>Ainda não foram registados valores de " . $nome . "!</p>";
                                        break;
                                    default:
                                        echo "Estas estatísticas não existem!";
                                        break;
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                    <?php
                } else {
                    switch ($estatisticas) {
                        case 'temperatura':
                        case 'humidade':
                        case 'luminosidade':
                    ?>
                        <div class="card">

                            <div class="card-header">
                                <b><?php echo $nome ?></b>
                            </div>
                            <div class="card-body">
                                <canvas id="grafico"></canvas>
                                <script>
                                    // Dados para o gráfico
                                    var datas = <?php echo json_encode($datas); ?>;
                                    var valores = <?php echo json_encode($valores); ?>;

                                    // Configurações do gráfico
                                    var config = {
                                        type: 'line',
                                        data: {
                                            labels: datas,
                                            datasets: [{
                                                label: 'Valor',
                                                data: valores,
                                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                                borderColor: 'rgba(75, 192, 192, 1)',
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                }
                                            }
                                        }
                                    }
                                    // Criação do gráfico
                                    var grafico = new Chart(document.getElementById('grafico'), config);
                                </script>
                            </div>
                        </div>
                <?php
                    break;
                    }
                }
                ?>

            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>