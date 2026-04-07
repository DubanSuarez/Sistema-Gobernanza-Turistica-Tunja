<?php
session_start();
require_once('../Conexion/conexion.php');
?>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title> M.Gobernanza - Inicio de sesión </title>
    <!-- BOOTSTRAP PRIMERO -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- TU CSS DESPUÉS -->
    <link rel="stylesheet" href="../Css/StyleLogin.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="container login-container">
        <div class="row h-100">

            <!-- LEFT SIDE -->
            <div class="col-md-6 d-none d-md-block">
                <div class="left-side h-100">
                    <div class="left-content">
                        <div class="hero-overlay">
                            <div class="hero-content">
                                <h1 class="hero-title">Modelo de Gobernanza</h1>
                                <h5 class="hero-subtitle">Turismo, Cultura y Desarrollo Territorial</h5>
                                <p class="hero-text">
                                    Plataforma digital para el análisis, gestión y toma de decisiones
                                    basadas en datos del sector turístico y cultural de Tunja.
                                </p>
                            </div>

                            <div class="profile text-center">

                                <a href="paginaInicio.php" class="btn btn-outline-light btn-volver">
                                    <i class="bi bi-arrow-left-circle me-2"></i>
                                    Volver al inicio
                                </a>

                            </div>
                        </div>



                    </div>
                </div>
            </div>


            <!-- RIGHT SIDE -->
            <div class="col-md-6 right-side">
                <div class="login-box">

                    <h1>Bienvenido</h1>
                    <p class="subtitle">Sistema de Gestión para el Turismo y la Cultura</p>

                    <form method='post' action='ValidaLogin.php'>
                        <div class="mb-3">
                            <input type="email" id="inputEmail3" class="form-control" placeholder="Correo electrónico"
                                name="txtusr" aria-describedby="emailHelp" class="white-text form-control" required>
                        </div>

                        <div class="mb-2">
                            <input type="password" id="contrasena" name="txtpwd" class="form-control"
                                placeholder="Contraseña">
                            <div class="forgot">
                                <a href="RecuperarContrasena.php" class="text-danger text-decoration-none">¿Olvidaste tu
                                    contraseña?</a>
                            </div>
                        </div>

                        <div class="divider">o</div>

                        <button type="submit" class="btn login-btn w-100 text-white">
                            Iniciar sesión
                        </button>

                        <?php
                        if (isset($_SESSION["Error"])) {
                            $error = $_SESSION["Error"];
                            echo "<span>$error</span>";
                        }
                        ?>

                    </form>

                    <div class="social-icons">
                        <i class="bi bi-facebook"></i>
                        <i class="bi bi-twitter"></i>
                        <i class="bi bi-linkedin"></i>
                        <i class="bi bi-instagram"></i>
                    </div>

                </div>
            </div>
        </div>
    </div>

</body>

<script src="../Js/jquery-3.6.0.min.js"></script>
<script type="../Js/popper.min.js"></script>
<script src="../Js/bootstrap.min.js"></script>

</html>
<?php
unset($_SESSION["Error"]);
?>