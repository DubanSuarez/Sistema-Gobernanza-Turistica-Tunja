<?php
session_start();
require_once('../Conexion/conexion.php');

// Verificar que exista sesión
if (!isset($_SESSION['rol'])) {
	$_SESSION['Error'] = "Debe iniciar sesión";
	header('location: ../Usuario/InicioSesion.php');
	exit();
}

// Permitir únicamente rol 1 (Administrador)
if ($_SESSION['rol'] != 1) {
	$_SESSION['Error'] = "No tiene permisos para acceder a esta sección";
	header('location: ../Usuario/InicioSesion.php');
	exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Dashboard Admin | Gobernanza Turística</title>
	<link rel="icon" href="../Img/Icono.png">
	<link rel="stylesheet" href="../Css/Estilo.css?v=<?php echo time(); ?>">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
	<link href="../css/dashboard.css" rel="stylesheet">
</head>

<body>
	<!-- NAVBAR -->
	<nav class="navbar navbar-expand-lg navbar-pro fixed-top">
		<div class="container">
			<!-- LOGO -->
			<a class="navbar-brand fw-bold d-flex align-items-center" href="#">
				<div class="logo-icon me-2">
					<img src="../Img/IconoNav.png" alt="" style="width: 36px; height: 22px;">
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
						<a class="nav-link active" href="../Usuario/dashboard.php">
							<i class="fa-solid fa-chart-pie me-1"></i> Dashboard
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="../Crepib/UsuarioSelect.php">
							<i class="fa-solid fa-users me-1"></i> Usuarios
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="../DatosTuristas/SelectDatosTuristas.php">
							<i class="fa-solid fa-database me-1"></i> Base datos
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
						<a class="btn-logout" href="Salir.php">
							<i class="fa-solid fa-right-from-bracket"></i>
							<span>Cerrar sesión</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>


	<br><br><br>


	<div class="container mt-4">

		<!-- HEADER MODULO REPORTES -->
		<div class="card border-0 shadow-sm rounded-4 mb-4">
			<div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3 px-4 py-3">

				<!-- IZQUIERDA -->
				<div class="d-flex align-items-center gap-3">

					<!-- Icono suave -->
					<div class="icon-soft bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center"
						style="width:42px;height:42px;">
						<i class="fa fa-chart-pie"></i>
					</div>

					<div>
						<h5 class="fw-semibold mb-0">Panel de Reportes</h5>
						<small class="text-muted">
							Consulta, análisis y generación de reportes estadísticos del sistema de Gobernanza Turística
						</small>
					</div>

				</div>

			</div>
		</div>

	</div>



	<div class="container mt-4">
		<div class="row mb-4">
			<div class="col-12">
				<div class="section-card p-4 shadow-sm">

					<div class="row g-4 justify-content-center">

						<!-- 🔴 INFORMACIÓN -->
						<div class="col-md-3">
							<a href="ReporteProcedencia.php" class="report-box cat-info">
								<i class="fa fa-earth-americas report-icon"></i>
								<h6>Procedencia</h6>
								<small>Origen de los visitantes</small>
							</a>
						</div>

						<div class="col-md-3">
							<a href="ReporteTemporada.php" class="report-box cat-info">
								<i class="fa fa-calendar-days report-icon"></i>
								<h6>Temporada</h6>
								<small>Comportamiento por fechas</small>
							</a>
						</div>

						<div class="col-md-3">
							<a href="ReportePercepción.php" class="report-box cat-info">
								<i class="fa fa-repeat report-icon"></i>
								<h6>Frecuencia de Visita</h6>
								<small>Periodicidad de los turistas</small>
							</a>
						</div>

						<div class="col-md-3">
							<a href="ReporteMotivos.php" class="report-box cat-info">
								<i class="fa fa-lightbulb report-icon"></i>
								<h6>Motivos</h6>
								<small>Razones del viaje</small>
							</a>
						</div>

						<div class="col-md-3">
							<a href="ReporteAcompañantes.php" class="report-box cat-info">
								<i class="fa fa-users report-icon"></i>
								<h6>Acompañantes</h6>
								<small>Tipo de grupo</small>
							</a>
						</div>

						<!-- 🟠 ECONÓMICO -->
						<div class="col-md-3">
							<a href="ReporteGeneralG.php" class="report-box cat-economico">
								<i class="fa fa-money-bill-wave report-icon"></i>
								<h6>Gastos</h6>
								<small>Distribución y comportamiento del gasto turístico</small>
							</a>
						</div>

						<div class="col-md-3">
							<a href="ReporteAgencia.php" class="report-box cat-economico">
								<i class="fa fa-building report-icon"></i>
								<h6>Agencias</h6>
								<small>Intermediarios turísticos</small>
							</a>
						</div>

						<!-- 🔵 PROMOCIÓN -->
						<div class="col-md-3">
							<a href="ReporteTunja.php" class="report-box cat-promocion">
								<i class="fa fa-bullhorn report-icon"></i>
								<h6>¿Cómo se enteró?</h6>
								<small>Canales de información</small>
							</a>
						</div>

						<div class="col-md-3">
							<a href="ReporteRedesSociales.php" class="report-box cat-promocion">
								<i class="fa fa-share-nodes report-icon"></i>
								<h6>¿Dónde compartió?</h6>
								<small>Redes sociales</small>
							</a>
						</div>

						<!-- 🟡 CULTURA -->
						<div class="col-md-3">
							<a href="ReporteCultura.php" class="report-box cat-cultura">
								<i class="fa fa-landmark report-icon"></i>
								<h6>Cultura</h6>
								<small>Actividades culturales</small>
							</a>
						</div>

						<!-- ⚫ DESTINOS -->
						<div class="col-md-3">
							<a href="ReporteMunicipios.php" class="report-box cat-destino">
								<i class="fa fa-location-dot report-icon"></i>
								<h6>Municipios</h6>
								<small>Destinos visitados</small>
							</a>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<br><br>

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

	<script src="../Js/wizard.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script>
		// Actualizar el valor de los sliders al moverlos
		document.querySelectorAll('.slider-percepcion').forEach(slider => {
			slider.addEventListener('input', function () {
				document.getElementById(this.dataset.target).textContent = this.value;
			});
		});
	</script>
</body>

</html>