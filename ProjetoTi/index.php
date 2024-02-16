<?php

session_start();


if (isset($_POST['username'])) {

  $credenciais = file('hash.txt');
  $autenticado = false;

  foreach ($credenciais as $credencial) {

    // Numa linha com o utilizador e password é divido o username e a password pelos ":" num array com duas posições
    $dados = explode(":", $credencial);

    /* As variáveis recebem o username e a hash da password e é utilizada a função trim 
    para retirar espaços indesejados no ficheiro.txt */
    $username = trim($dados[0]);
    $password = trim($dados[1]);

    /* Se as credenciais recebidas por POST coincidirem com as guardadas,
    a sessão inicia com estas e o utilizador é direcionado para o dashboard */
    if ($username == $_POST['username'] && password_verify($_POST['password'], $password)) {

      $_SESSION['username'] = $_POST['username'];
      $autenticado = true;
      break;
    }
  }

  if ($autenticado) {
    header('Location: dashboard.php');
    exit;
  } else {
    // Mostrar mensagem de erro se o utilizador digitar o username ou a password incorretamente
    echo '<div class="alert alert-danger" role="alert">
              Autenticação Falhada! Credenciais Incorretas!
            </div>';
  }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

  <link rel="stylesheet" href="css/mystyle.css">

  <style>
    body {
      background-image: url('img/estufa.jpg');
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-size: cover;
    }
  </style>
</head>

<body>

 <!-- Formulário de Login -->
  <div class="container">
    <div class="row justify-content-center">
      <form class="TIform" method="POST">
        <img src="img/estg_h.png" alt="imagem" class="center" width="320">
        <br>
        <br>
        <br>
        <div class="mb-3">
          <label for="username" class="form-label">Username:</label>
          <input type="text" class="form-control" id="username" name="username" placeholder="Insira o seu username" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password:</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Insira a sua password" required>
        </div>
        <button type="submit" class="btn btn-primary">Submeter</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>