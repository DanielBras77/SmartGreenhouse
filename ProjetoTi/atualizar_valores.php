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


/* De acordo com os valores dos sensores (se são os valores ideais ou não), 
percebe o estado geral da estufa, o estado alerta de cada valor lido e o estado do alarme*/

$valoresDeStatus = 0;
$novoValorAlarme = 0;



// É necessário passar o valoresDeStatus por referência

function obterEstadoTemperatura($valor_temperatura, &$valoresDeStatus) {

    if ($valor_temperatura > 35) {
        $valoresDeStatus += 1;
        return '<span class="badge rounded-pill text-bg-danger">Elevada</span>';
    } elseif ($valor_temperatura < 15) {
        $valoresDeStatus += 1;
        return '<span class="badge rounded-pill text-bg-primary">Baixa</span>';
    } else {
        return '<span class="badge rounded-pill text-bg-success">Normal</span>';
    }
}

function obterEstadoHumidade($valor_humidade, &$valoresDeStatus) {
    if ($valor_humidade > 70) {
        $valoresDeStatus += 1;
        return '<span class="badge rounded-pill text-bg-danger">Elevada</span>';
    } elseif ($valor_humidade < 50) {
        $valoresDeStatus += 1;
        return '<span class="badge rounded-pill text-bg-primary">Baixa</span>';
    } else {
        return '<span class="badge rounded-pill text-bg-success">Normal</span>';
    }
}

function obterEstadoLuminosidade($valor_luminosidade, &$valoresDeStatus) {
    if ($valor_luminosidade == 0) {
        $valoresDeStatus += 1;
        return '<span class="badge rounded-pill text-bg-danger">Escuro</span>';
    } else {
        return '<span class="badge rounded-pill text-bg-success">Iluminado</span>';
    }
}


function obterEstadoAlarme($valoresDeStatus, &$novoValorAlarme) {
    if ($valoresDeStatus == 3) {
        $novoValorAlarme = 1;
    } else {
        $novoValorAlarme = 0;
    }

    // Guarda o novo valor do alarme de emergência no ficheiro valor.txt
    $caminhoValorAlarme = "api/files/alarme/valor.txt";
    file_put_contents($caminhoValorAlarme, $novoValorAlarme);

    return $novoValorAlarme;
}

function obterDadosAlarme($novoValorAlarme) {
    $dadosAlarme = array();

    if ($novoValorAlarme == 1) {
        $dadosAlarme['imagem_alarme'] = '<img src="img/alarme_emergencia.jpg" alt="imagem" width="150" height="150" class="center">';
        $dadosAlarme['mensagem_alarme'] = 'Ligado!';
        $dadosAlarme['classe_estilo'] = 'bg-danger-subtle';
    } else {
        $dadosAlarme['imagem_alarme'] = '<img src="img/checkIcon.png" alt="imagem" width="200" height="150" class="center">';
        $dadosAlarme['mensagem_alarme'] = 'Desligado!';
        $dadosAlarme['classe_estilo'] = 'bg-success-subtle';
    }

    return $dadosAlarme;
}



// Preparar os valores para retornar em formato JSON
$valores = array(
    "valor_temperatura" => $valor_temperatura,
    "hora_temperatura" => $hora_temperatura,
    "estado_temperatura" => obterEstadoTemperatura($valor_temperatura, $valoresDeStatus),

    "valor_humidade" => $valor_humidade,
    "hora_humidade" => $hora_humidade,
    "estado_humidade" => obterEstadoHumidade($valor_humidade, $valoresDeStatus),

    "valor_luminosidade" => ($valor_luminosidade == 0) ? 'Escuro' : 'Iluminado',
    "hora_luminosidade" => $hora_luminosidade,
    "estado_luminosidade" => obterEstadoLuminosidade($valor_luminosidade, $valoresDeStatus),

    "status_geral" => $valoresDeStatus,
    
    "dados_alarme" => obterDadosAlarme(obterEstadoAlarme($valoresDeStatus, $novoValorAlarme))
);

// Indica que o tipo de retorno é JSON
header("Content-Type: application/json");

// Retorna os valores como JSON
echo json_encode($valores);
?>
