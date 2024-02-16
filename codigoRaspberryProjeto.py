import requests
import time
import RPi.GPIO as GPIO
import datetime

print("Prima CTRL+C para terminar")

GPIO.setmode(GPIO.BOARD)
mode = GPIO.getmode()


# Atenção: Antes de executar o programa ter em atenção o IP do computador

# Configuração dos pinos: GND - 7º pino lado direito

channel = 3                      #pino GPIO 2 - 2º pino lado esquerdo
GPIO.setup(channel, GPIO.OUT)

channelAlarme = 5                #pino GPIO 3 - 3º pino lado esquerdo
GPIO.setup(channelAlarme, GPIO.OUT)

pin_LDR = 12                     # 6º pino lado direito
GPIO.setup(pin_LDR, GPIO.IN)



def post2API(nome, valor):
	agora=datetime.datetime.now()
	payload = {'nome': nome, 'valor': valor, 'hora': agora.strftime("%Y-%m-%d %H:%M:%S")}
	r = requests.post('http://10.79.12.21/ProjetoTi/api/api.php', data=payload)  # mudar o endereço para o http://iot.dei.estg.ipleiria.pt/ti/g081/api/api.php


while True:
    try:
    	# Realizar um pedido HTTP com método GET para a temperatura
        responseTemp = requests.get('http://10.79.12.21/ProjetoTi/api/api.php?nome=temperatura')

    	# Realizar um pedido HTTP com método GET para humidade
        responseHum = requests.get('http://10.79.12.21/ProjetoTi/api/api.php?nome=humidade')

        # Realizar um pedido HTTP com método GET para atuador luminosidade
        responseIluminacao = requests.get('http://10.79.12.21/ProjetoTi/api/api.php?nome=atuadorLuminosidade')


        # Lê o valor do sensor 
        luminosidade = GPIO.input(pin_LDR)
        post2API("luminosidade", luminosidade)
	

        # Armazenar o valor do HTTP status code e as leituras em variáveis
        status_code_temp = responseTemp.status_code
        temperatura = float(responseTemp.text)
      
        status_code_hum = responseHum.status_code
        humidade = float(responseHum.text)

        status_code_ilu = responseIluminacao.status_code
        atuadorLuminosidade = float(responseIluminacao.text)


        # Relação Botão Dashboard com Raspaberry
        if status_code_ilu == 200:
                
            if atuadorLuminosidade == 1:
                
                # Ligar LED Luminosidade
                GPIO.output(channel, GPIO.HIGH)

            else:
                # Desligar LED Luminosidade
                GPIO.output(channel, GPIO.LOW)



        if status_code_temp == 200 and status_code_hum == 200:
            
            # Imprimir o valor da temperatura, humidade e luminosidade
            print("Temperatura: ", temperatura)
            print("Humidade: ", humidade)
            print("Luminosidade: ", luminosidade)


            # Relação sensor Temperatura e Humidade com Raspaberry
            if (temperatura > 35 or temperatura < 15) and (humidade < 50 or humidade > 70) and (luminosidade == 0):
                GPIO.output(channelAlarme, GPIO.HIGH)
                post2API("alarme", 1)

            else:
                GPIO.output(channelAlarme, GPIO.LOW)
                post2API("alarme", 0)

                

        else:
            # Imprimir informação sobre o erro
            print("Erro ao realizar pedido HTTP")

    	# Aguardar 5 segundos antes de executar novamente
        time.sleep(5)


    except KeyboardInterrupt:
    # captura excecao CTRL + C
        print('\n O programa foi interrompido pelo utilizador.')
        break # sai do ciclo while
    except Exception as e:
        # captura todos os erros
        print('Erro inesperado:', e)
    finally:
        #GPIO.cleanup()
        print('')
        time.sleep(2)