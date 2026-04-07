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

	<br><br><br><br>
	<?php
	/* ================== FILTRO ================== */

	// Rango de fechas
	$fecha_inicio = $_GET['fecha_inicio'] ?? '';
	$fecha_fin = $_GET['fecha_fin'] ?? '';

	// Construcción dinámica del WHERE
	$where = "";

	// Solo aplicar filtro si hay fechas
	if (!empty($fecha_inicio) || !empty($fecha_fin)) {

		$where = "WHERE 1=1";

		if (!empty($fecha_inicio)) {
			$fecha_inicio = mysqli_real_escape_string($conexion, $fecha_inicio);
			$where .= " AND fecha_registro >= '$fecha_inicio'";
		}

		if (!empty($fecha_fin)) {
			$fecha_fin = mysqli_real_escape_string($conexion, $fecha_fin);
			$where .= " AND fecha_registro <= '$fecha_fin'";
		}
	}


	/* ================== KPIs ================== */

	// Total turistas (general o filtrado)
	$sql1 = mysqli_query($conexion, "
    SELECT COUNT(*) total 
    FROM datosturista
    $where
");
	$totalTuristas = mysqli_fetch_assoc($sql1)['total'] ?? 0;


	// Total encuestas (igual lógica)
	$sql2 = mysqli_query($conexion, "
    SELECT COUNT(*) total 
    FROM datosturista 
    $where
");
	$totalEncuestas = mysqli_fetch_assoc($sql2)['total'] ?? 0;


	// Último año registrado (SIEMPRE general)
	$sql3 = mysqli_query($conexion, "
    SELECT MAX(YEAR(fecha_registro)) ultimo 
    FROM datosturista
");
	$ultima = mysqli_fetch_assoc($sql3)['ultimo'] ?? "Sin datos";


	/* ==== KPI PRO: mes con más turistas ==== */

	$sqlTopMes = mysqli_query($conexion, "
    SELECT 
        MONTH(fecha_registro) num_mes,
        MONTHNAME(fecha_registro) Mes,
        COUNT(*) total
    FROM datosturista
    $where
    GROUP BY MONTH(fecha_registro)
    ORDER BY total DESC
    LIMIT 1
");

	$topMes = mysqli_fetch_assoc($sqlTopMes);

	$mesTop = $topMes['Mes'] ?? 'Sin datos';
	$cantTop = $topMes['total'] ?? 0;


	/* ================== GRAFICA ================== */

	$mesesOrden = [
		1 => "Enero",
		2 => "Febrero",
		3 => "Marzo",
		4 => "Abril",
		5 => "Mayo",
		6 => "Junio",
		7 => "Julio",
		8 => "Agosto",
		9 => "Septiembre",
		10 => "Octubre",
		11 => "Noviembre",
		12 => "Diciembre"
	];

	$datosMes = array_fill(1, 12, 0);

	$grafica = mysqli_query($conexion, "
    SELECT 
        MONTH(fecha_registro) num_mes,
        COUNT(*) total
    FROM datosturista
    $where
    GROUP BY MONTH(fecha_registro)
");

	while ($row = mysqli_fetch_assoc($grafica)) {
		$mesNumero = $row['num_mes'];
		$datosMes[$mesNumero] = $row['total'];
	}

	$meses = array_values($mesesOrden);
	$cantidades = array_values($datosMes);


	/* ================== AÑOS DISPONIBLES ================== */

	$anios = mysqli_query($conexion, "
    SELECT DISTINCT YEAR(fecha_registro) AS Ano
    FROM datosturista
    ORDER BY Ano DESC
");
	?>


	<div class="container mb-5">

		<!-- ================= BIENVENIDA ================= -->
		<div class="welcome-box mb-4">
			<div class="row align-items-center">
				<div class="col-md-8">
					<h3 class="fw-bold">Dashboard de Gobernanza Turística</h3>
					<p class="mb-0">
						Bienvenido,
						<b><?php echo $_SESSION['nombres'] . " " . $_SESSION['apellidos']; ?></b><br>
						Panel analítico turístico con visualización estadística en tiempo real.
					</p>
				</div>

				<div class="col-md-4 text-end d-none d-md-block">
					<i class="fa-solid fa-chart-pie" style="font-size:70px; opacity:0.15;"></i>
				</div>
			</div>
		</div>

		<!-- ================= KPIs ================= -->
		<div class="row g-4 mb-4">

			<div class="col-md-3">
				<div class="stat-card d-flex align-items-center">
					<div class="stat-icon bg1 me-3"><i class="fa fa-users"></i></div>
					<div>
						<h4 class="fw-bold"><?php echo $totalTuristas; ?></h4>
						<small class="text-muted">Turistas totales</small>
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="stat-card d-flex align-items-center">
					<div class="stat-icon bg2 me-3"><i class="fa fa-chart-column"></i></div>
					<div>
						<h4 class="fw-bold"><?php echo $totalEncuestas; ?></h4>
						<small class="text-muted">
							<?php
							if (!empty($fecha_inicio) || !empty($fecha_fin)) {
								echo "Registros filtrados";
							} else {
								echo "Registros generales";
							}
							?>
						</small>
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="stat-card d-flex align-items-center">
					<div class="stat-icon bg3 me-3"><i class="fa fa-calendar"></i></div>
					<div>
						<h5 class="fw-bold"><?php echo $ultima; ?></h5>
						<small class="text-muted">Último año registrado</small>
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="stat-card d-flex align-items-center">
					<div class="stat-icon bg4 me-3"><i class="fa fa-star"></i></div>
					<div>
						<h5 class="fw-bold"><?php echo $mesTop; ?></h5>
						<small class="text-muted">Mes top (<?php echo $cantTop; ?> turistas)</small>
					</div>
				</div>
			</div>

		</div>

		<!-- ================= FILTRO ================= -->
		<!-- ================= FILTRO POR RANGO ================= -->
		<div class="card border-0 shadow-sm p-3 mb-4" style="border-radius:18px;">
			<form method="GET" id="filtroForm" class="row g-3 align-items-end">

				<div class="col-md-4">
					<label class="small text-muted">Fecha inicio</label>
					<input type="date" name="fecha_inicio" value="<?php echo $_GET['fecha_inicio'] ?? ''; ?>"
						class="form-control auto-submit">
				</div>

				<div class="col-md-4">
					<label class="small text-muted">Fecha fin</label>
					<input type="date" name="fecha_fin" value="<?php echo $_GET['fecha_fin'] ?? ''; ?>"
						class="form-control auto-submit">
				</div>

				<div class="col-md-2">
					<a href="Dashboard.php" class="btn btn-outline-secondary w-100">
						<i class="fa fa-eraser"></i>
					</a>
				</div>

			</form>
		</div>



		<!-- ================= GRAFICA ================= -->
		<div class="card p-4 border-0 shadow-sm" style="border-radius:22px;">

			<div class="d-flex justify-content-between align-items-center mb-3">
				<h5 class="fw-bold mb-0">
					<i class="fa fa-chart-line"></i> Flujo de turistas por mes
				</h5>
				<span class="badge bg-dark">
					<?php
					if (!empty($fecha_inicio) || !empty($fecha_fin)) {
						echo "Reporte filtrado";
					} else {
						echo "Reporte general";
					}
					?>
				</span>
			</div>

			<!-- selector tipo grafica -->
			<div class="mb-3 text-end">
				<label class="me-2 fw-bold">Tipo gráfico:</label>

				<button class="btn btn-sm btn-dark" onclick="cambiarGrafico('bar')">
					<i class="fa fa-chart-column"></i>
				</button>

				<button class="btn btn-sm btn-dark" onclick="cambiarGrafico('line')">
					<i class="fa fa-chart-line"></i>
				</button>

				<button class="btn btn-sm btn-dark" onclick="cambiarGrafico('pie')">
					<i class="fa fa-chart-pie"></i>
				</button>

				<button class="btn btn-sm btn-dark" onclick="cambiarGrafico('doughnut')">
					<i class="fa fa-circle"></i>
				</button>
			</div>

			<canvas id="grafica" height="80"></canvas>
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


	<!-- ================= CHART JS PRO ================= -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	<script>
		document.querySelectorAll('.auto-submit').forEach(element => {
			element.addEventListener('change', function () {
				document.getElementById('filtroForm').submit();
			});
		});
	</script>

	<script>
		let chart;

		function crearGrafico(tipo) {

			const ctx = document.getElementById('grafica').getContext('2d');

			if (chart) {
				chart.destroy();
			}

			/* tamaño dinámico */
			let aspectRatio = 2; // normal barras

			if (tipo === 'pie' || tipo === 'doughnut') {
				aspectRatio = 2; // mas pequeño circular
			}

			chart = new Chart(ctx, {
				type: tipo,
				data: {
					labels: <?php echo json_encode($meses); ?>,
					datasets: [{
						label: 'Turistas registrados',
						data: <?php echo json_encode($cantidades); ?>,
						borderWidth: 2,
						borderRadius: 8,
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: true,
					aspectRatio: aspectRatio,

					plugins: {
						legend: {
							display: true,
							position: 'bottom'
						}
					},

					scales: (tipo === 'pie' || tipo === 'doughnut') ? {} : {
						y: { beginAtZero: true }
					}
				}
			});
		}

		crearGrafico('bar');

		function cambiarGrafico(tipo) {
			crearGrafico(tipo);
		}
	</script>


</body>

</html>