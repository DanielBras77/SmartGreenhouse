#include <WiFi101.h>
#include <ArduinoHttpClient.h>
#include <DHT.h>
#include <NTPClient.h>
#include <WiFiUdp.h> 
#include <TimeLib.h>


#define PINO_LED 8 // Pino digital ao qual o LED da iluminação está conectado
#define PINO_ALARME 9// Pino digital ao qual o LED do alarme está conectado
#define DHTPIN 7 // Pin Digital onde está ligado o sensor
#define DHTTYPE DHT11 // Tipo de sensor DHT
DHT dht(DHTPIN, DHTTYPE); // Instanciar e declarar a class DHT


char SSID[] = "labs_lrsc"; // Nome da rede labs
char PASS_WIFI[] = "robot1cA!ESTG"; // Pass da rede labs


// Servidor Iot
//char URL[] = "iot.dei.estg.ipleiria.pt";
char URL[] = "10.79.12.21"; // Ip do pc


char datahora[20];

int PORTO = 80; // ou outro porto que esteja definido no servidor

WiFiClient clienteWifi;
HttpClient clienteHTTP = HttpClient(clienteWifi, URL, PORTO);


WiFiUDP clienteUDP;


//Servidor de NTP do IPLeiria: ntp.ipleiria.pt
//Fora do IPLeiria servidor: 0.pool.ntp.org

char NTP_SERVER[] = "ntp.ipleiria.pt";
NTPClient clienteNTP(clienteUDP, NTP_SERVER, 3600);


void setup() {

  Serial.begin(115200);

  while (!Serial);

  // Inicia a conexão Wi-fi 
  WiFi.begin(SSID,PASS_WIFI);


  // Fica em loop enquanto a conexão não for estabelecida
  while(WiFi.status() != WL_CONNECTED){
    Serial.println(".");
    delay(500);
  }


  // Inicia o sensor de temperatura e humidade
  dht.begin();

  // Configura o pino do LED como saída
  pinMode(PINO_LED, OUTPUT);  


  // Liga o LED para indicar conclusão da inicialização
  digitalWrite(PINO_LED, HIGH);
  delay(500);
  digitalWrite(PINO_LED, LOW);
  delay(500);
  digitalWrite(PINO_LED, LOW);
  delay(500);

}


void loop() {

  float temperatura = dht.readTemperature();
  float humidade = dht.readHumidity();

  update_time(datahora);

  String enviaNome = "temperatura";
  float enviaValor = temperatura;
  String enviaHora = datahora;
  
  post2API(enviaNome, enviaValor, enviaHora);

  update_time(datahora);

  enviaNome = "humidade";
  enviaValor = humidade;
  enviaHora = datahora;
  
  post2API(enviaNome, enviaValor, enviaHora);


  int iluminacao = getFromAPI("atuadorLuminosidade");

  int alarme = getFromAPI("alarme");


  
  if (iluminacao == 1){
    digitalWrite(PINO_LED, HIGH);
    Serial.println("O Led da Iluminação foi ligado");
  }
  else if (iluminacao == 0){
    digitalWrite(PINO_LED, LOW);
  }


  if (alarme == 1){
    digitalWrite(PINO_ALARME, HIGH);
    Serial.println("O Led do Alarme foi foi ligado");
  }
  else if (alarme == 0){
    digitalWrite(PINO_ALARME, LOW);
  }



  delay(2000);

}


void post2API(String enviaNome, float enviaValor, String enviaHora){

  String URLPath = "/ProjetoTi/api/api.php";
  String contentType = "application/x-www-form-urlencoded";


  String body = "nome="+enviaNome+"&valor="+enviaValor+"&hora="+enviaHora;

  Serial.println(body);

  clienteHTTP.post(URLPath, contentType, body);


  //Enquanto a comunicação estiver ativa (connected), aguarda dados ficarem disponíveis(available)
  while(clienteHTTP.connected()){
    if (clienteHTTP.available()){
      int responseStatusCode = clienteHTTP.responseStatusCode();
      String responseBody = clienteHTTP.responseBody();
      Serial.println("Status Code: "+String(responseStatusCode)+" Resposta: "+responseBody);
    }
  }
  
}


int getFromAPI(String enviaNome) {
  
  int valor=-1;

  String URLPath = "http://10.79.12.21/ProjetoTi/api/api.php?";
  URLPath += "nome=" + enviaNome;

  clienteHTTP.get(URLPath);

  if (clienteHTTP.responseStatusCode() == 200){

    String aux= clienteHTTP.responseBody();

    valor = aux.toInt();
    Serial.println(valor);

  }
  else{
    Serial.println("Erro ao fazer o pedido GET!");
    Serial.println("Status Code: "+String(clienteHTTP.responseStatusCode()));
  }

  return valor;
}


void update_time(char *datahora){
  clienteNTP.update();
  unsigned long epochTime = clienteNTP.getEpochTime();
  sprintf(datahora, "%02d-%02d-%02d %02d:%02d:%02d", year(epochTime), month(epochTime), day(epochTime), hour(epochTime), minute(epochTime), second(epochTime));
}
