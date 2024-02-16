import cv2
import requests
import time
import os

# Atenção: Antes de executar o programa ter atenção ao IP do computador


def capture_image():

    # Liga a câmara do computador
    cap = cv2.VideoCapture(0)
    
    # Verifica se conseguiu abrir a câmera corretamente
    if not cap.isOpened():
        print("Unable to open the camera.")
        return
    
    # Captura a imagem
    ret, frame = cap.read()
    
    # Verifica se a imagem foi capturada com sucesso
    if not ret:
        print("Failed to capture frame.")
        return
    
    # Guarda a imagem na pasta da última imagem capturada
    cv2.imwrite("api/imagem/webcam.jpg", frame)


    # Gera um nome dinâmico para a imagem baseado no timestamp atual
    timestamp = int(time.time())
    filename = f"webcam{timestamp}.jpg"


    # Cria o caminho completo para a pasta de histórico de imagens e a imagem capturada
    folder_path = "api/historicoImagens"
    file_path = os.path.join(folder_path, filename)

    # Verifica se a pasta de histórico de imagens existe, se não, cria-a
    if not os.path.exists(folder_path):
        os.makedirs(folder_path)


    # Guarda a imagem na pasta de histórico com o nome dinâmico
    cv2.imwrite(file_path, frame)

    
    # Desliga a câmera
    cap.release()
    
    print("Image captured successfully.")



while True:
   
    # Realizar um pedido HTTP com método GET para a temperatura
    responseTemp = requests.get('http://10.79.12.21/ProjetoTi/api/api.php?nome=temperatura')

    # Realizar um pedido HTTP com método GET para humidade
    responseHum = requests.get('http://10.79.12.21/ProjetoTi/api/api.php?nome=humidade')

    # Realizar um pedido HTTP com método GET para luminosidade
    responseLum = requests.get('http://10.79.12.21/ProjetoTi/api/api.php?nome=luminosidade')


    # Armazenar o valor do HTTP status code e as leituras em variáveis
    status_code_temp = responseTemp.status_code
    temperatura = float(responseTemp.text)
    
    status_code_hum = responseHum.status_code
    humidade = float(responseHum.text)

    status_code_lum = responseHum.status_code
    luminosidade = float(responseLum.text)



    if status_code_temp == 200 and status_code_hum == 200 and status_code_lum == 200:

        if (temperatura > 35 or temperatura < 15) and (humidade < 50 or humidade > 70) and (luminosidade == 0):

            # Chamar a função de capturar imagem
            capture_image()

        else:
            print("")
            print("Sensores com valores corretos!")

    else:
        # Imprimir informação sobre o erro
        print("Erro ao realizar pedido HTTP")
    

    # Faz um intervalo de 1 segundo antes da próxima iteração
    time.sleep(1)



    





