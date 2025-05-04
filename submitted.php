<?php 
session_start();

if(!isset($_SESSION['form_submitted']) || $_SESSION['form_submitted'] !== true) {
  header('Location: index.html');
  exit();
}

unset($_SESSION['form_submitted']);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="Profile PVC Sigplast Galati. Fabrica profil PVC si Productie Ferestre si Usi PVC si Aluminiu."
    />

    <!-- Fontawesome -->
    <script
      src="https://kit.fontawesome.com/654b63e729.js"
      crossorigin="anonymous"
    ></script>

    <!-- Bootstrap icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />

    <!-- Be Vietnam Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />

    <!-- Turnstile script -->
    <script
      src="https://challenges.cloudflare.com/turnstile/v0/api.js"
      async
      defer
    ></script>

    <!-- Bootstrap -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6"
      crossorigin="anonymous"
    />

    <!-- Slick CSS -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"
      integrity="sha512-yHknP1/AwR+yx26cB1y0cjvQUMvEa2PFzt1c9LlS4pRQ5NOTZFWbhBig+X9G9eYW/8m0/4OXNx8pxJ6z57x0dw=="
      crossorigin="anonymous"
    />
    <link rel="icon" href="img/general/favicon.jpg" type="image/gif" />
    <link rel="stylesheet" href="css/style.css" />
    <title>Solicitare înregistrată: Mulțumim!</title>
  </head>
  <body>
    <header>
      <nav
        class="navbar fixed-top navbar-expand-sm navbar-light bg-light navbar-custom"
      >
        <div class="container h-100">
          <a href="index.html" class="navbar-brand">
            <img
              src="img/general/sigplast_logo.svg"
              class="img-fluid"
              alt="sigplast logo"
              width="250"
            />
          </a>
          <button
            class="navbar-toggler"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
          >
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse h-100" id="navbarNav">
            <ul class="navbar-nav ms-auto fw-bold h-100">
              <li
                class="nav-item d-flex justify-content-center align-items-center"
              >
                <a
                  href="index.html"
                  class="nav-link h-100 d-flex justify-content-center align-items-center"
                  >Acasa</a
                >
              </li>
              <li
                class="nav-item d-flex justify-content-center align-items-center"
              >
                <a
                  href="about.html"
                  class="nav-link h-100 d-flex justify-content-center align-items-center"
                  >Despre</a
                >
              </li>
              <li
                class="nav-item d-flex justify-content-center align-items-center"
              >
                <a
                  href="factory.html"
                  class="nav-link h-100 d-flex justify-content-center align-items-center"
                  >Fabrica</a
                >
              </li>
              <li
                class="nav-item d-flex justify-content-center align-items-center"
              >
                <a
                  href="workshop.html"
                  class="nav-link h-100 d-flex justify-content-center align-items-center"
                  >Atelier</a
                >
              </li>
              <li
                class="nav-item d-flex justify-content-center align-items-center"
              >
                <a
                  href="contact.html"
                  class="nav-link h-100 d-flex justify-content-center align-items-center"
                  >Contact</a
                >
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
    <main
      class="d-flex justify-content-center align-items-center submitted-message-main"
    >
      <div class="container">
        <div class="text-center submitted-message-inner">
          <h1>Vă mulțumim pentru solicitarea dumneavoastră!</h1>
          <p class="lead">
            Datele completate au fost primite cu succes. Unul dintre
            consultanții noștri vă va contacta în cel mai scurt timp pentru a
            discuta detaliile comenzii și pentru a vă oferi o ofertă
            personalizată. Pentru orice întrebări suplimentare, ne puteți
            contacta la 0751 328 081 sau ne puteți scrie la adresa de email
            <a
              class="email-thank-you-page text-decoration-none"
              href="mailto:albarim@gmail.com"
              >albarim@gmail.com</a
            >
          </p>
          <div>
            <p>Pentru a reveni la pagina principală folosiți butonul</p>
            <a href="index.html" class="btn btn-primary">Acasă</a>
          </div>
        </div>
      </div>
    </main>

    <!-- FOOTER -->
    <footer id="main-footer" class="text-center p-4">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <h4 class="d-flex justify-content-start align-items-center gap-1">
              Despre
              <img
                src="img/general/sigplast_logo.svg"
                width="125"
                alt="sigplast logo"
                class="img-fluid"
              />
            </h4>
            <p>
              Fabrică și Producție profil PVC Sigplast. Performanțe ridicate la
              prețuri avantajoase.
            </p>
          </div>
          <div class="col-md-4">
            <h4 class="d-flex justify-content-start">Link-uri utile</h4>
            <ul class="useful-links-list">
              <li class="d-flex">
                <a
                  class="text-decoration-none"
                  target="_blank"
                  href="cookies.html"
                >
                  <i class="bi bi-check-lg me-1"></i>
                  Politica de cookies</a
                >
              </li>
              <li class="d-flex">
                <a
                  class="text-decoration-none"
                  target="_blank"
                  href="index.html"
                >
                  <i class="bi bi-check-lg me-1"></i>
                  Acasă</a
                >
              </li>
              <li class="d-flex">
                <a
                  class="text-decoration-none"
                  target="_blank"
                  href="factory.html"
                >
                  <i class="bi bi-check-lg me-1"></i>
                  Fabrică</a
                >
              </li>
              <li class="d-flex">
                <a
                  class="text-decoration-none"
                  target="_blank"
                  href="workshop.html"
                >
                  <i class="bi bi-check-lg me-1"></i>
                  Atelier</a
                >
              </li>
            </ul>
          </div>
          <div class="col-md-4 d-none d-md-block">
            <h4 class="d-flex justify-content-start">Contact</h4>
            <ul class="useful-links-list">
              <li class="d-flex align-items-center">
                <i class="bi bi-telephone-fill me-2"></i>
                Fabrică:
                <a
                  class="ms-1 text-decoration-none"
                  target="_blank"
                  href="tel:+40751328081"
                >
                  0751 328 081</a
                >
              </li>
              <li class="d-flex align-items-center">
                <i class="bi bi-telephone-fill me-2"></i>
                Atelier:
                <a
                  class="ms-1 text-decoration-none"
                  target="_blank"
                  href="tel:+40745628606"
                >
                  0745 628 606</a
                >
              </li>
              <li class="d-flex align-items-center">
                <i class="bi bi-envelope-fill me-2"></i>
                Email:
                <a
                  class="ms-1 text-decoration-none"
                  target="_blank"
                  href="mailto:albarim@gmail.com"
                >
                  albarim@gmail.com</a
                >
              </li>
            </ul>
          </div>
        </div>
      </div>
    </footer>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
