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

	<div class="container">

		<!-- HEADER MODULO USUARIOS -->
		<div class="card border-0 shadow-sm rounded-4 mb-4">
			<div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3 px-4 py-3">

				<!-- IZQUIERDA -->
				<div class="d-flex align-items-center gap-3">

					<!-- Icono suave -->
					<div class="icon-soft bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center"
						style="width:42px;height:42px;">
						<i class="fas fa-user-shield"></i>
					</div>

					<div>
						<h5 class="fw-semibold mb-0">Gestión de Usuarios</h5>
						<small class="text-muted">Administración y control de accesos del sistema</small>
					</div>

				</div>

				<!-- DERECHA -->
				<div class="d-flex align-items-center gap-3">

					<!-- Botón principal -->
					<a href="NuevoUsuario.php" class="btn btn-danger rounded-pill px-4 shadow-sm">
						<i class="fas fa-user-plus me-1"></i> Nuevo usuario
					</a>

				</div>

			</div>
		</div>

	</div>

	<div class="container">
		<div class="card border-0 shadow-sm rounded-4 mb-4">
			<div class="card-body">

				<?php
				/* ======================================
				   CONFIGURACIÓN PAGINACIÓN
				====================================== */

				$registrosPorPagina = 5;
				$paginaActual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
				if ($paginaActual < 1)
					$paginaActual = 1;

				$offset = ($paginaActual - 1) * $registrosPorPagina;


				/* ======================================
				   FILTROS
				====================================== */

				$filtroNombre = $_GET['nombre'] ?? '';
				$filtroRol = $_GET['rol'] ?? '';

				$where = "WHERE r.Nombre != 'Administrador'";

				if (!empty($filtroNombre)) {
					$filtroNombre = mysqli_real_escape_string($conexion, $filtroNombre);
					$where .= " AND (u.Nombres LIKE '%$filtroNombre%' OR u.Apellidos LIKE '%$filtroNombre%')";
				}

				if (!empty($filtroRol)) {
					$filtroRol = intval($filtroRol);
					$where .= " AND u.IdRol = $filtroRol";
				}


				/* ======================================
				   TOTAL REGISTROS
				====================================== */

				$totalQuery = mysqli_query($conexion, "
    SELECT COUNT(*) total
    FROM usuario u
    INNER JOIN rol r ON u.IdRol = r.Id
    $where
");

				$totalRegistros = mysqli_fetch_assoc($totalQuery)['total'];
				$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

				if ($paginaActual > $totalPaginas && $totalPaginas > 0) {
					$paginaActual = $totalPaginas;
				}


				/* ======================================
				   CONSULTA PRINCIPAL
				====================================== */

				$usuarios = mysqli_query($conexion, "
    SELECT u.*, r.Nombre AS NombreRol
    FROM usuario u
    INNER JOIN rol r ON u.IdRol = r.Id
    $where
    ORDER BY u.Id DESC
    LIMIT $offset, $registrosPorPagina
");
				?>

				<!-- ================= FILTROS ================= -->

				<form method="GET" class="row mb-3">

					<div class="col-md-4">
						<input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre o apellido"
							value="<?= htmlspecialchars($filtroNombre) ?>">
					</div>

					<div class="col-md-3">
						<select name="rol" class="form-select">
							<option value="">Todos los roles</option>
							<?php
							$roles = mysqli_query($conexion, "
                SELECT * FROM rol 
                WHERE Nombre != 'Administrador'
            ");
							while ($r = mysqli_fetch_assoc($roles)) {
								$selected = ($filtroRol == $r['Id']) ? "selected" : "";
								echo "<option value='{$r['Id']}' $selected>{$r['Nombre']}</option>";
							}
							?>
						</select>
					</div>

					<div class="col-md-2">
						<button class="btn btn-primary w-100">Filtrar</button>
					</div>

					<div class="col-md-2">
						<a href="UsuarioSelect.php" class="btn btn-secondary w-100">Limpiar</a>
					</div>

				</form>


				<!-- ================= TABLA ================= -->

				<div class="table-responsive">
					<table class="table table-hover table-bordered align-middle">

						<thead class="table-dark text-center">
							<tr>
								<th>ID</th>
								<th>Nombre Completo</th>
								<th>Documento</th>
								<th>Email</th>
								<th>Teléfono</th>
								<th>Rol</th>
								<th width="150">Acciones</th>
							</tr>
						</thead>

						<tbody>

							<?php if (mysqli_num_rows($usuarios) > 0): ?>
								<?php while ($u = mysqli_fetch_assoc($usuarios)): ?>
									<tr>
										<td class="text-center">
											<a href="VerUsuario.php?id=<?= $u['Id'] ?>" class="btn btn-sm btn-info">
												Ver datos
											</a>
										</td>

										<td><?= $u['Nombres'] . " " . $u['Apellidos'] ?></td>
										<td><?= $u['Documento'] ?></td>
										<td><?= $u['Email'] ?></td>
										<td><?= $u['Telefono'] ?></td>
										<td class="text-center">
											<span class="badge bg-info">
												<?= $u['NombreRol'] ?>
											</span>
										</td>
										<td class="text-center">
											<a href="UpdateUsuario.php?id=<?= $u['Id'] ?>" class="btn btn-sm btn-warning">
												Editar
											</a>

											<a href="DeleteUsuario.php?id=<?= $u['Id'] ?>" class="btn btn-sm btn-danger"
												onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">
												Eliminar
											</a>
										</td>
									</tr>
								<?php endwhile; ?>
							<?php else: ?>
								<tr>
									<td colspan="7" class="text-center">
										No hay usuarios registrados
									</td>
								</tr>
							<?php endif; ?>

						</tbody>
					</table>
				</div>


				<!-- ================= PAGINACIÓN PRO ================= -->

				<?php if ($totalPaginas > 1): ?>

					<nav>
						<ul class="pagination justify-content-center">

							<!-- Primera -->
							<li class="page-item <?= ($paginaActual == 1) ? 'disabled' : '' ?>">
								<a class="page-link"
									href="?pagina=1&nombre=<?= urlencode($filtroNombre) ?>&rol=<?= $filtroRol ?>">
									« Primera
								</a>
							</li>

							<!-- Anterior -->
							<li class="page-item <?= ($paginaActual == 1) ? 'disabled' : '' ?>">
								<a class="page-link"
									href="?pagina=<?= $paginaActual - 1 ?>&nombre=<?= urlencode($filtroNombre) ?>&rol=<?= $filtroRol ?>">
									‹
								</a>
							</li>

							<?php
							$inicio = max(1, $paginaActual - 2);
							$fin = min($totalPaginas, $paginaActual + 2);

							for ($i = $inicio; $i <= $fin; $i++):
								?>

								<li class="page-item <?= ($i == $paginaActual) ? 'active' : '' ?>">
									<a class="page-link"
										href="?pagina=<?= $i ?>&nombre=<?= urlencode($filtroNombre) ?>&rol=<?= $filtroRol ?>">
										<?= $i ?>
									</a>
								</li>

							<?php endfor; ?>

							<!-- Siguiente -->
							<li class="page-item <?= ($paginaActual == $totalPaginas) ? 'disabled' : '' ?>">
								<a class="page-link"
									href="?pagina=<?= $paginaActual + 1 ?>&nombre=<?= urlencode($filtroNombre) ?>&rol=<?= $filtroRol ?>">
									›
								</a>
							</li>

							<!-- Última -->
							<li class="page-item <?= ($paginaActual == $totalPaginas) ? 'disabled' : '' ?>">
								<a class="page-link"
									href="?pagina=<?= $totalPaginas ?>&nombre=<?= urlencode($filtroNombre) ?>&rol=<?= $filtroRol ?>">
									Última »
								</a>
							</li>

						</ul>
					</nav>

				<?php endif; ?>




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

	<script src="../Js/wizard.js"></script>


</body>

</html>