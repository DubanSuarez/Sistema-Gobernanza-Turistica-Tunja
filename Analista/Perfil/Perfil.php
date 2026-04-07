<?php
session_start();
require_once('../../Conexion/conexion.php');

// Verificar que exista sesión
if (!isset($_SESSION['rol'])) {
    $_SESSION['Error'] = "Debe iniciar sesión";
    header('location: ../../Usuario/InicioSesion.php');
    exit();
}

// Permitir únicamente rol 1 (Administrador)
if ($_SESSION['rol'] != 2) {
    $_SESSION['Error'] = "No tiene permisos para acceder a esta sección";
    header('location: ../../Usuario/InicioSesion.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Dashboard Admin | Gobernanza Turística</title>
	<link rel="icon" href="../../Img/Icono.png">
	<link rel="stylesheet" href="../../Css/Estilo.css?v=<?php echo time(); ?>">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
	<link href="../../css/dashboard.css" rel="stylesheet">
</head>

<body>
	<!-- NAVBAR -->
	<nav class="navbar navbar-expand-lg navbar-pro fixed-top">
		<div class="container">
			<!-- LOGO -->
			<a class="navbar-brand fw-bold d-flex align-items-center" href="#">
				<div class="logo-icon me-2">
					<img src="../../Img/IconoNav.png" alt="" style="width: 36px; height: 22px;">
				</div>
				<span class="brand-text">Gobernanza Turística</span>
			</a>
			<!-- BOTON MOVIL -->
			<button class="navbar-toggler border-0 shadow-none" data-bs-toggle="collapse" data-bs-target="#menu">
				<i class="fa-solid fa-bars"></i>
			</button>
			<!-- MENU -->
			<div class="collapse navbar-collapse" id="menu">
				<ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
					<li class="nav-item">
						<a class="nav-link active" href="../DashboardAnalista.php">
							<i class="fa-solid fa-chart-pie me-1"></i> Dashboard
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="../ReGraficas/VerReportes.php">
							<i class="fa-solid fa-chart-column me-1"></i> Reportes
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="../Perfil/Perfil.php">
							<i class="fa-solid fa-user me-1"></i> Perfil
						</a>
					</li>
					<!-- BOTON SALIR PRO -->
					<li class="nav-item ms-lg-3">
						<a class="btn-logout" href="../../Usuario/Salir.php">
							<i class="fa-solid fa-right-from-bracket"></i>
							<span>Cerrar sesión</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	<br><br><br><br>


	<div class="container">
		<div class="card border-0 shadow-sm rounded-4 mb-4">
			<div class="card-body">
				



<?php
$id_usuario = (int) $_SESSION['id'];

$sql = "SELECT u.*, r.Nombre AS RolNombre 
        FROM usuario u
        INNER JOIN rol r ON u.IdRol = r.Id
        WHERE u.Id = $id_usuario";

$resultado = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_assoc($resultado);
?>

<div class="row">

    <!-- FOTO / INFO GENERAL -->
    <div class="col-md-4 text-center border-end">
        <div class="mb-3">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto"
                 style="width:120px; height:120px; font-size:40px; font-weight:bold; color:#6c757d;">
                <?php 
                    echo strtoupper(substr($datos['Nombres'],0,1) . substr($datos['Apellidos'],0,1));
                ?>
            </div>
        </div>

        <h5 class="fw-bold mb-1">
            <?php echo $datos['Nombres']." ".$datos['Apellidos']; ?>
        </h5>

        <span class="badge bg-primary">
            <?php echo $datos['RolNombre']; ?>
        </span>

        <hr>

        <p class="text-muted mb-1">
            <i class="bi bi-envelope"></i> <?php echo $datos['Email']; ?>
        </p>

        <p class="text-muted">
            <i class="bi bi-telephone"></i> <?php echo $datos['Telefono']; ?>
        </p>
    </div>


    <!-- INFORMACIÓN DETALLADA -->
    <div class="col-md-8">

        <h5 class="fw-bold mb-4">Información Personal</h5>

        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label text-muted">Nombres</label>
                <input type="text" class="form-control" 
                       value="<?php echo $datos['Nombres']; ?>" readonly>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted">Apellidos</label>
                <input type="text" class="form-control" 
                       value="<?php echo $datos['Apellidos']; ?>" readonly>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted">Documento</label>
                <input type="text" class="form-control" 
                       value="<?php echo $datos['Documento']; ?>" readonly>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted">Fecha de Nacimiento</label>
                <input type="text" class="form-control" 
                       value="<?php echo date('d/m/Y', strtotime($datos['FechaNacimiento'])); ?>" readonly>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted">Teléfono</label>
                <input type="text" class="form-control" 
                       value="<?php echo $datos['Telefono']; ?>" readonly>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted">Correo Electrónico</label>
                <input type="email" class="form-control" 
                       value="<?php echo $datos['Email']; ?>" readonly>
            </div>

        </div>

        <div class="mt-4 text-end">
            <a href="ActualizarPerfil.php" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Editar Perfil
            </a>

            <a href="UpdateContrasena.php" class="btn btn-secondary">
                <i class="bi bi-shield-lock"></i> Cambiar Contraseña
            </a>
        </div>

    </div>
</div>






			</div>
		</div>
	</div>











	<footer class="footer-pro mt-5">
		<div class="container">
			<div class="row align-items-center">
				<!-- IZQUIERDA -->
				<div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
					<h6 class="mb-1 fw-bold brand-footer">
						<i class="fa-solid fa-chart-simple me-2"></i>
						Sistema de Gobernanza Turística
					</h6>
					<small class="text-muted">
						Plataforma de análisis y gestión de datos turísticos
					</small>
				</div>
				<!-- DERECHA -->
				<div class="col-md-6 text-center text-md-end">
					<div class="footer-links mb-2">
						<a href="#">Dashboard</a>
						<a href="../ReGraficas/VerReportes.php">Reportes</a>
						<a href="../Perfil/Perfil.php">Perfil</a>
					</div>
					<small class="text-muted">
						Portafolio Ingeniería Sistemas ·
						<b>Duban Suárez</b> © <?php echo date("Y"); ?>
					</small>
				</div>
			</div>
		</div>
	</footer>

	<script src="../../Js/wizard.js"></script>


</body>

</html>