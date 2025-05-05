<?php 
session_start();

if(!isset($_SESSION['rate_limited']) || $_SESSION['rate_limited'] !== true) {
  header('Location: index.html');
  exit();
}

unset($_SESSION['rate_limited']);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Prea Multe Solicitări</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .message-container {
      min-height: 100vh;
    }
  </style>
</head>
<body>
  <div class="container d-flex align-items-center justify-content-center message-container">
    <div class="text-center border p-5 rounded shadow-sm bg-white">
      <h1 class="mb-4 text-danger">Prea multe solicitări</h1>
      <p class="lead">Ai trimis deja formularul de două ori în ultima oră.</p>
      <p class="mb-4">Din motive de securitate, limităm numărul de încercări pentru a preveni abuzurile. Te rugăm să revii mai târziu sau să ne contactezi telefonic dacă este urgent. Mulțumim pentru înțelegere!</p>
      <a href="index.html" class="btn btn-primary">Înapoi la pagina principală</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>