<?php
session_start();
require_once('../Conexion/conexion.php');
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Modelo de Gobernanza</title>
  <!-- BOOTSTRAP PRIMERO -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- GOOGLE FONTS -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- TU CSS DESPUÉS -->
  <link rel="stylesheet" href="../Css/Style.css?v=<?php echo time(); ?>">
</head>

<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg fixed-top custom-navbar">
    <div class="container">

      <!-- LOGO -->
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img class="LogoNav" src="../Img/Logo.png" alt="Logo">
      </a>

      <!-- BOTÓN MOBILE -->
      <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- MENÚ -->
      <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav align-items-lg-center">
          <li class="nav-item"><a class="nav-link" href="#">Inicio</a></li>
          <li class="nav-item"><a class="nav-link" href="#">¿Qué es Gobernanza?</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Actores Culturales</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Estadísticas</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Participa</a></li>

          <!-- BOTÓN EN MOBILE -->
          <li class="nav-item d-lg-none mt-3">
            <a href="InicioSesion.php" class="btn btn-login w-100">Iniciar Sesión</a>
          </li>
        </ul>
      </div>

      <!-- BOTÓN DESKTOP -->
      <a href="InicioSesion.php" class="btn btn-login d-none d-lg-inline-block">
        Iniciar Sesión
      </a>

    </div>
  </nav>

  <!-- HERO SECTION -->
  <section class="hero-section">
    <img src="../Img/Mapa.png" class="hero-mapa" alt="">
    <div class="container hero-content">
      <div class="row align-items-center min-vh-100">

        <!-- TEXTO -->
        <div class="col-lg-6 hero-left">

          <div class="logo-titulo d-flex align-items-start gap-3 mb-4">
            <img src="../Img/Logo.png" class="hero-logo" alt="Logo">
            <h2 class="hero-title mb-0">
              Modelo de Gobernanza <br>
              para el Turismo y la <br>
              Cultura
            </h2>
          </div>

          <div class="hero-line"></div>

          <p class="hero-text">
            Una plataforma para conectar, gestionar y promover nuestro patrimonio
          </p>

          <a href="InicioSesion.php" class="btn btn-explorar">
            Explora el proyecto
          </a>
        </div>

        <!-- IMÁGENES -->
        <div class="col-lg-6">
          <div class="imagenes-container">

            <div class="img-box grande">
              <img src="../Img/ImgPlaza.jpg" alt="">
            </div>

            <div class="img-box pequena top">
              <img src="../Img/Plazadebolivar2.png" alt="">
            </div>

            <div class="img-box pequena bottom">
              <img src="../Img/ImgPlaza2.jpg" alt="">
            </div>

          </div>
        </div>

      </div>
    </div>

  </section>







  <!-- ================= BENEFICIOS ================= -->
  <section class="beneficios-section py-5">
    <div class="container text-center">

      <h2 class="beneficios-title">
        Beneficios de la Plataforma de Gobernanza
      </h2>
      <p class="beneficios-subtitle mb-5">
        Una herramienta al servicio de la cultura, el turismo y la participación ciudadana.
      </p>

      <div class="row g-5 justify-content-center">

        <div class="col-md-3 col-lg-3">
          <div class="beneficio-card">
            <div class="icon-circle">
              <i class="bi bi-graph-up-arrow"></i>
            </div>
            <h5>Visualización de Datos Inteligente</h5>
            <div class="divider"></div>
            <p>
              Gráficas e informes que permiten entender y tomar decisiones sobre el turismo y el patrimonio.
            </p>
          </div>
        </div>

        <div class="col-md-3 col-lg-3">
          <div class="beneficio-card">
            <div class="icon-circle">
              <i class="bi bi-shield-check"></i>
            </div>
            <h5>Acceso por Roles Seguro</h5>
            <div class="divider"></div>
            <p>
              Cada actor accede solo a lo que necesita, con seguridad y control.
            </p>
          </div>
        </div>

        <div class="col-md-3 col-lg-3">
          <div class="beneficio-card">
            <div class="icon-circle">
              <i class="bi bi-building"></i>
            </div>
            <h5>Gestión Eficiente del Patrimonio</h5>
            <div class="divider"></div>
            <p>
              Información ordenada de actores culturales y actividades de forma centralizada.
            </p>
          </div>
        </div>
      </div>
      <div class="row g-5 mt-1 justify-content-center">

        <div class="col-md-3 col-lg-3">
          <div class="beneficio-card">
            <div class="icon-circle">
              <i class="bi bi-file-earmark-text"></i>
            </div>
            <h5>Automatización de Reportes</h5>
            <div class="divider"></div>
            <p>
              Generación rápida de estadísticas e informes desde el sistema.
            </p>
          </div>
        </div>

        <div class="col-md-3 col-lg-3">
          <div class="beneficio-card">
            <div class="icon-circle">
              <i class="bi bi-chat-dots"></i>
            </div>
            <h5>Participación Ciudadana Activa</h5>
            <div class="divider"></div>
            <p>
              Los ciudadanos pueden opinar y ser parte de la toma de decisiones.
            </p>
          </div>
        </div>

        <div class="col-md-3 col-lg-3">
          <div class="beneficio-card">
            <div class="icon-circle">
              <i class="bi bi-globe-americas"></i>
            </div>
            <h5>Accesibilidad desde cualquier lugar</h5>
            <div class="divider"></div>
            <p>
              Plataforma disponible 24/7 para consultar y monitorear información.
            </p>
          </div>
        </div>

      </div>
    </div>
  </section>




  <!-- ================= ACTORES DESTACADOS ================= -->
  <section class="actores-section py-5">
    <div class="container">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
          <h2 class="actores-title">Actores destacados</h2>
          <p class="actores-subtitle">
            Conoce a las personas, lugares y organizaciones que fortalecen la identidad patrimonial de Tunja.
          </p>
        </div>

        <a href="#" class="btn btn-vermas">Ver más</a>
      </div>

      <!-- Cards -->
      <div class="row justify-content-center">

        <!-- Card 1 -->
        <div class="col-12 col-sm-6 col-lg-4 mb-4">
          <div class="actor-card">
            <img src="../Img/CasaDelFundador.jpg" class="actor-img" alt="Casa Fundador">

            <div class="actor-content">
              <h5>Casa del Fundador Gonzalo <br> Suárez Rendón</h5>

              <p><strong>Tipo:</strong> Museo / Patrimonio colonial</p>
              <p><strong>Ubicación:</strong> Centro histórico de Tunja</p>

              <a href="#" class="actor-link">
                Más Información <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="col-12 col-sm-6 col-lg-4 mb-4">
          <div class="actor-card">
            <img src="../Img/IglesiaSantoDomingo.jpg" class="actor-img" alt="Iglesia">

            <div class="actor-content">
              <h5>Iglesia de Santo <br> Domingo</h5>

              <p><strong>Tipo:</strong> Iglesia / Templo histórico</p>
              <p><strong>Ubicación:</strong> Plaza de Bolívar, Tunja</p>

              <a href="#" class="actor-link">
                Más Información <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="col-12 col-sm-6 col-lg-4 mb-4">
          <div class="actor-card">
            <img src="../Img/PozodeHunzahua.jpg" class="actor-img" alt="Pozo Hunzahúa">

            <div class="actor-content">
              <h5>Pozo de Hunzahúa <br> (Pozo de Donato)</h5>

              <p><strong>Tipo:</strong> Sitio arqueológico / Monumento Muisca</p>
              <p><strong>Ubicación:</strong> Predios de la UPTC, Tunja</p>

              <a href="#" class="actor-link">
                Más Información <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>









  <section class="container py-5">

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
      <div>
        <h2 class="section-title">¿Sabías que...?</h2>
        <p class="section-subtitle">
          Descubre datos sorprendentes sobre el patrimonio y la cultura de Tunja.
        </p>
      </div>
      <a href="#" class="btn btn-vermas">Ver más</a>
    </div>

    <div class="row mt-4">

      <!-- TARJETA PRINCIPAL -->
      <div class="col-lg-7 mb-4">
        <div class="card main-card border-0 shadow-sm">

          <img id="mainImage" src="../Img/CapilladelRosario.jpg" class="card-img-top" height="350">

          <div class="card-body">
            <h5 id="mainTitle" class="card-title fw-semibold">
              ¿Sabías que la Capilla del Rosario es conocida como la "Capilla Sixtina de América"?
            </h5>

            <p id="mainText" class="text-muted">
              Ubicada en la Iglesia de Santo Domingo, su decoración dorada, tallas y pinturas
              la convierten en una de las obras barrocas más valiosas del continente.
            </p>

            <div id="mainTags" class="mt-3">
              <span class="tag tag-red">Patrimonio Colonial</span>
              <span class="tag tag-orange">Arte y Cultura</span>
            </div>
          </div>
        </div>
      </div>

      <!-- TARJETAS PEQUEÑAS -->
      <div class="col-lg-5">

        <!-- Card 1 -->
        <div class="card small-card border-0 shadow-sm mb-3 p-2 selectable" data-img="../Img/PozodeHunzahua.jpg"
          data-title="¿Sabías que El Pozo de Hunzahúa guarda una leyenda de amor prohibido?"
          data-text="Según la tradición muisca, Hunzahúa y su hermana se enamoraron y fueron castigados, dando origen al pozo."
          data-tags='<span class="tag tag-green">Historia Muisca</span>'>

          <div class="row g-2 align-items-center">
            <div class="col-4">
              <img src="../Img/PozodeHunzahua.jpg" class="img-fluid rounded">
            </div>
            <div class="col-8">
              <h6 class="fw-semibold mb-1">
                ¿Sabías que El Pozo de Hunzahúa guarda una leyenda de amor prohibido?
              </h6>
            </div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="card small-card border-0 shadow-sm mb-3 p-2 selectable" data-img="../Img/CapilladelRosario.jpg"
          data-title="¿Sabías que la Capilla del Rosario es conocida como la 'Capilla Sixtina de América'?"
          data-text="Su interior está completamente decorado en pan de oro, convirtiéndola en una joya del barroco colonial."
          data-tags='<span class="tag tag-red">Patrimonio Colonial</span>
                   <span class="tag tag-orange">Arte y Cultura</span>'>

          <div class="row g-2 align-items-center">
            <div class="col-4">
              <img src="../Img/CapilladelRosario.jpg" class="img-fluid rounded">
            </div>
            <div class="col-8">
              <h6 class="fw-semibold mb-1">
                ¿Sabías que la Capilla del Rosario es conocida como la "Capilla Sixtina de América"?
              </h6>
            </div>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="card small-card border-0 shadow-sm p-2 selectable" data-img="../Img/TunjaPlaza.jpg"
          data-title="¿Sabías que Tunja significa 'varón poderoso' en lengua muisca?"
          data-text="El nombre proviene de 'Hunza', antigua capital del pueblo muisca."
          data-tags='<span class="tag tag-yellow">Fundación / Historia</span>'>

          <div class="row g-2 align-items-center">
            <div class="col-4">
              <img src="../Img/TunjaPlaza.jpg" class="img-fluid rounded">
            </div>
            <div class="col-8">
              <h6 class="fw-semibold mb-1">
                ¿Sabías que Tunja significa "varón poderoso" en lengua muisca?
              </h6>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>



  <!-- SECCIÓN CTA ROJA -->
  <section class="cta-section d-flex align-items-center text-white">
    <div class="container text-center py-5">

      <h2 class="cta-title mb-3">
        Sé parte del cambio cultural de Tunja
      </h2>

      <p class="cta-text mb-4">
        Conocer nuestra historia es el primer paso para transformarla.
        Explora, aprende y comparte el valor de nuestro patrimonio.
      </p>

      <a href="#" class="btn btn-cta">
        Descarga Nuestra Guía Cultural
      </a>

    </div>
  </section>



  <footer class="footer-section text-white">
    <div class="container py-4 mt-5">

      <div class="row align-items-center text-center text-lg-start">

        <!-- COLUMNA 1 -->
        <div class="col-lg-4 mb-4 mb-lg-0">
          <div class="d-flex align-items-center justify-content-center justify-content-lg-start mb-2">
            <img src="../Img/footer.png" alt="Logo" class="footer-logo">
          </div>

          <div class="footer-line my-3"></div>

          <p class="footer-description">
            Plataforma para la gestión, visualización y apropiación
            del patrimonio cultural y turístico de Tunja.
          </p>
        </div>

        <!-- COLUMNA 2 -->
        <div class="col-lg-4 d-flex flex-column justify-content-center align-items-center text-center mb-4 mb-lg-0">

          <h6 class="footer-title mb-3">Estamos para escucharte</h6>

          <div class="social-icons">
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-instagram"></i></a>
            <a href="#"><i class="bi bi-tiktok"></i></a>
            <a href="#"><i class="bi bi-youtube"></i></a>
            <a href="#"><i class="bi bi-twitter-x"></i></a>
          </div>

        </div>

        <!-- COLUMNA 3 -->
        <div class="col-lg-4 p-3 colcontactos">
          <h6 class="footer-title mb-3 text-center text-lg-start">
            Comunícate con el equipo
          </h6>

          <div class="contact-item">
            <div class="icon-box">
              <i class="bi bi-envelope"></i>
            </div>
            <span>ModeloGobernanza@gmail.com</span>
          </div>

          <div class="contact-item">
            <div class="icon-box">
              <i class="bi bi-whatsapp"></i>
            </div>
            <span>+57 324 3619521</span>
          </div>

          <div class="contact-item">
            <div class="icon-box">
              <i class="bi bi-geo-alt"></i>
            </div>
            <span>Carrera 10 No.19-17, Tunja - Boyacá, Colombia</span>
          </div>
        </div>

      </div>

      <hr class="footer-divider my-4">

      <div class="text-center footer-copy">
        © 2025 | Proyecto académico — Todos los derechos reservados.
      </div>

    </div>
  </footer>


  <!-- BOOTSTRAP JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


  <script>
    const cards = document.querySelectorAll('.selectable');

    const mainImage = document.getElementById('mainImage');
    const mainTitle = document.getElementById('mainTitle');
    const mainText = document.getElementById('mainText');
    const mainTags = document.getElementById('mainTags');

    cards.forEach(card => {
      card.addEventListener('click', () => {

        // 🔹 Quitar active a todas
        cards.forEach(c => c.classList.remove('active-card'));

        // 🔹 Agregar active a la seleccionada
        card.classList.add('active-card');

        // 🔹 Cambiar contenido principal
        mainImage.src = card.getAttribute('data-img');
        mainTitle.textContent = card.getAttribute('data-title');
        mainText.textContent = card.getAttribute('data-text');
        mainTags.innerHTML = card.getAttribute('data-tags');

      });
    });
  </script>
</body>

</html>