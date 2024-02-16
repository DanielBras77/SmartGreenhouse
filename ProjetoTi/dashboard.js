$(document).ready(function() {

    // Função para atualizar os conteudos da página
    function atualizarValores() {
        $.ajax({
            url: "atualizar_valores.php",
            method: "POST",
            dataType: "json",
            success: function(data) {

                // Atualizar os valores na página com os dados atualizados do atualizar_valores.php
                $("#valor_temperatura").text(data.valor_temperatura + " °C").addClass("valor-atualizado");
                $("#valor_humidade").text(data.valor_humidade + " %").addClass("valor-atualizado");
                $("#valor_luminosidade").text(data.valor_luminosidade).addClass("valor-atualizado");

                $("#valor_temperaturaTabela").text(data.valor_temperatura + " °C");
                $("#valor_humidadeTabela").text(data.valor_humidade + " %");
                $("#valor_luminosidadeTabela").text(data.valor_luminosidade);

                // Atualizar as datas de atualização
                $("#hora_temperatura").text(data.hora_temperatura);
                $("#hora_humidade").text(data.hora_humidade);
                $("#hora_luminosidade").text(data.hora_luminosidade);
                

                // Atualizar os estados de alerta
                $("#estado_temperatura").html(data.estado_temperatura);
                $("#estado_humidade").html(data.estado_humidade);
                $("#estado_luminosidade").html(data.estado_luminosidade);

                // Atualizar os dados do alarme
                $("#imagem_alarme").html(data.dados_alarme.imagem_alarme);
                $("#mensagem_alarme").text(data.dados_alarme.mensagem_alarme);
                $("#card_alarme").removeClass().addClass("card-footer " + data.dados_alarme.classe_estilo);

    
                // Aplica temporariamente outro estilo durante 1 segundo
                $(".valor-atualizado").addClass("valor-brilhante");

                setTimeout(function() {
                    $(".valor-atualizado").removeClass("valor-brilhante");
                }, 1000);

                // Atualizar o status geral
                atualizarStatusGeral(data.status_geral);

            },
            error: function() {
                console.log("Erro ao atualizar os valores.");
            }
        });
    }

    
    function atualizarStatusGeral(status) {
        var elementoStatusGeral = $("#status-geral");
    
        // Remove todas as classes de estado anteriores
        elementoStatusGeral.removeClass("verde amarelo vermelho");
    
        // Aplica a classe de estado adequada com base no valor do status
        if (status == 0) {
            elementoStatusGeral.addClass("verde");
        } else if (status == 1 || status == 2) {
            elementoStatusGeral.addClass("amarelo");
        } else if (status == 3) {
            elementoStatusGeral.addClass("vermelho");
        }
    }



    // Chamar a função de atualização inicialmente
    atualizarValores();


    // Define um intervalo de tempo de 5 segundos para atualizar os valores
    setInterval(atualizarValores, 5000); //  
});


                //$("#dados_alarme_imagem").html(data.dados_alarme.imagem_alarme);
                //$("#dados_alarme_mensagem").html(data.dados_alarme.mensagem_alarme);