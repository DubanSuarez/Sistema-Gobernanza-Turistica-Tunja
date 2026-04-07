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
if ($_SESSION['rol'] != 3) {
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
                        <a class="nav-link active" href="../DashboardEncuestador.php">
                            <i class="fa-solid fa-chart-pie me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../DatosTuristas/SelectDatosTuristas.php">
                            <i class="fa-solid fa-database me-1"></i> Base datos
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

	<?php
	$rol = $_SESSION['rol'] ?? 0;

	// Para que el mes salga en español
	mysqli_query($conexion, "SET lc_time_names = 'es_ES'");

	$consulta = "
SELECT 
    t.*,
    YEAR(t.fecha_registro) AS Ano,
    MONTHNAME(t.fecha_registro) AS Mes,
    p.Ciudad,
    m.NombreMunicipio 
FROM datosturista t
LEFT JOIN procedencia p ON p.Id = t.IdProcedencia
LEFT JOIN municipios m ON m.Id = t.IdMunicipioVisitado1
ORDER BY t.fecha_registro DESC
";

	$ejecutar = mysqli_query($conexion, $consulta);
	$totalRegistros = mysqli_num_rows($ejecutar);
	?>



	<div class="container">
		<!-- HEADER MODULO MINIMAL -->
		<div class="card border-0 shadow-sm rounded-4 mb-4">
			<div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3 px-4 py-3">

				<!-- IZQUIERDA -->
				<div class="d-flex align-items-center gap-3">

					<!-- icono suave -->
					<div class="icon-soft bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center"
						style="width:42px;height:42px;">
						<i class="fas fa-users"></i>
					</div>

					<div>
						<h5 class="fw-semibold mb-0">Gestión de Turistas</h5>
						<small class="text-muted">Administración y consulta de registros</small>
					</div>

				</div>

				<!-- DERECHA -->
				<div class="d-flex align-items-center gap-3">

					<!-- contador sutil -->
					<span class="badge rounded-pill bg-light text-secondary border px-3 py-2">
						<?php echo $totalRegistros; ?> registros
					</span>

					<!-- botón principal -->
					<a href="RegistrarT.php" class="btn btn-danger rounded-pill px-4 shadow-sm">
						<i class="fas fa-user-plus me-1"></i> Nuevo
					</a>

				</div>

			</div>
		</div>

	</div>

	<div class="container">
		<div class="card border-0 shadow-sm rounded-4 mb-4">

			<div class="card-body">

				<h6 class="mb-3 fw-bold">
					<i class="fas fa-filter"></i> Filtros inteligentes
				</h6>

				<div class="row g-3">

					<!-- ESTADO -->
					<div class="col-md-2">
						<label class="form-label fw-bold">Estado</label>
						<select id="filtroEstado" class="form-select shadow-sm">
							<option value="">Todos</option>
							<option value="1" selected>Activos</option>
							<option value="0">Anulados</option>
						</select>
					</div>

					<!-- AÑO -->
					<div class="col-md-2">
						<label class="form-label fw-bold">Año</label>
						<select id="filtroAno" class="form-select shadow-sm">
							<option value="">Todos los años</option>
							<?php
							$anos = mysqli_query($conexion, "
							SELECT DISTINCT YEAR(fecha_registro) AS Ano 
							FROM datosturista 
							ORDER BY Ano DESC
						");

							while ($a = mysqli_fetch_array($anos)) {
								echo "<option value='{$a['Ano']}'>{$a['Ano']}</option>";
							}
							?>
						</select>
					</div>

					<!-- MES -->
					<div class="col-md-2">
						<label class="form-label fw-bold">Mes</label>
						<select id="filtroMes" class="form-select shadow-sm">
							<option value="">Todos los meses</option>
							<?php
							mysqli_query($conexion, "SET lc_time_names = 'es_ES'");

							$meses = mysqli_query($conexion, "
							SELECT DISTINCT 
								MONTH(fecha_registro) AS NumeroMes,
								MONTHNAME(fecha_registro) AS Mes
							FROM datosturista
							ORDER BY NumeroMes ASC
						");

							while ($m = mysqli_fetch_array($meses)) {
								echo "<option value='{$m['Mes']}'>{$m['Mes']}</option>";

							}
							?>
						</select>
					</div>

					<!-- PROCEDENCIA -->
					<div class="col-md-2">
						<label class="form-label fw-bold">Procedencia</label>
						<select id="filtroProcedencia" class="form-select shadow-sm">
							<option value="">Todas</option>
							<?php
							$pro = mysqli_query($conexion, "
							SELECT DISTINCT Ciudad 
							FROM procedencia
							ORDER BY Ciudad ASC
						");

							while ($p = mysqli_fetch_array($pro)) {
								echo "<option value='{$p['Ciudad']}'>{$p['Ciudad']}</option>";
							}
							?>
						</select>
					</div>

					<!-- MUNICIPIO -->
					<div class="col-md-2">
						<label class="form-label fw-bold">Municipio</label>
						<select id="filtroMunicipio" class="form-select shadow-sm">
							<option value="">Todos</option>
							<?php
							$mu = mysqli_query($conexion, "
							SELECT DISTINCT NombreMunicipio 
							FROM municipios
							ORDER BY NombreMunicipio ASC
						");

							while ($mm = mysqli_fetch_array($mu)) {
								echo "<option value='{$mm['NombreMunicipio']}'>{$mm['NombreMunicipio']}</option>";
							}
							?>
						</select>
					</div>

					<div class="col-md-2">
						<div class="mt-4">
							<button id="limpiarFiltros" class="btn btn-outline-secondary">
								<i class="fas fa-eraser"></i> Limpiar filtros
							</button>
						</div>
					</div>

				</div>



			</div>

		</div>
	</div>




	<div class="container">
		<div class="card shadow-sm border-0 rounded-4">
			<div class="card-body">
				<div class="table-responsive">

					<table id="tablaTuristas" class="table table-hover align-middle mb-0 tabla-soft">

						<thead class="text-center" style="background:#990E08; color:white;">
							<tr>
								<th style="width:60px">Ver</th>
								<th>Año</th>
								<th>Mes</th>
								<th>Procedencia</th>
								<th>Municipio</th>
								<th>Doc.</th>
								<th>Turista</th>
								<th>Estado</th>
								<th style="width:60px">Edit</th>
								<th style="width:60px">Del</th>
							</tr>
						</thead>

						<tbody>

							<?php
							$rol = $_SESSION['rol'] ?? 0;

							mysqli_query($conexion, "SET lc_time_names = 'es_ES'");

							/* ============================
							   CONSTRUIR FILTROS DINÁMICOS
							============================ */


							if (!empty($_GET['estado'])) {
								$estado = (int) $_GET['estado'];
								$where .= " AND t.estado = $estado";
							}

							if (!empty($_GET['ano'])) {
								$ano = (int) $_GET['ano'];
								$where .= " AND YEAR(t.fecha_registro) = $ano";
							}

							if (!empty($_GET['mes'])) {
								$mes = (int) $_GET['mes'];
								$where .= " AND MONTH(t.fecha_registro) = $mes";
							}

							if (!empty($_GET['procedencia'])) {
								$pro = mysqli_real_escape_string($conexion, $_GET['procedencia']);
								$where .= " AND p.Ciudad = '$pro'";
							}

							if (!empty($_GET['municipio'])) {
								$mun = mysqli_real_escape_string($conexion, $_GET['municipio']);
								$where .= " AND m.NombreMunicipio = '$mun'";
							}

							/* ============================
							   CONSULTA FINAL
							============================ */

							$consulta = "
SELECT 
    t.*, 
    YEAR(t.fecha_registro) AS Ano,
    MONTHNAME(t.fecha_registro) AS Mes,
    p.Ciudad, 
    m.NombreMunicipio 
FROM datosturista t
LEFT JOIN procedencia p ON p.Id = t.IdProcedencia
LEFT JOIN municipios m ON m.Id = t.IdMunicipioVisitado1
ORDER BY t.Id DESC
";


							$ejecutar = mysqli_query($conexion, $consulta);

							while ($fila = mysqli_fetch_assoc($ejecutar)) {

								$id = $fila['Id'];
								?>

								<tr>

									<!-- VER -->
									<td class="text-center">
										<a href="VerDatos.php?ver=<?php echo $id; ?>"
											class="btn btn-sm btn-outline-secondary">
											<i class="fas fa-eye"></i>
										</a>
									</td>

									<!-- AÑO -->
									<td class="text-center fw-semibold">
										<?php echo $fila['Ano']; ?>
									</td>

									<!-- MES -->
									<td class="text-center text-capitalize">
										<?php echo $fila['Mes']; ?>
									</td>

									<!-- PROCEDENCIA -->
									<td>
										<i class="fas fa-globe text-muted me-1"></i>
										<?php echo $fila['Ciudad']; ?>
									</td>

									<!-- MUNICIPIO -->
									<td>
										<i class="fas fa-map-marker-alt text-danger me-1"></i>
										<?php echo $fila['NombreMunicipio']; ?>
									</td>

									<!-- DOCUMENTO -->
									<td class="text-center">
										<span class="badge bg-secondary">
											<?php echo $fila['TipoDocumento']; ?>
										</span>
									</td>

									<!-- TURISTA -->
									<td>
										<i class="fas fa-user text-muted me-1"></i>
										<?php echo $fila['NombreCompleto']; ?>
									</td>

									<!-- ESTADO -->
									<td class="text-center">
										<?php if ($fila['estado'] == 1) { ?>
											<span class="badge bg-success">Activo</span>
										<?php } else { ?>
											<span class="badge bg-danger">Anulado</span>
										<?php } ?>
									</td>

									<!-- EDITAR -->
									<td class="text-center">
										<a href="UpdateDatosTurista.php?actualizar=<?php echo $id; ?>" class="btn btn-sm"
											style="background:#F6900F;color:white;">
											<i class="fas fa-pen"></i>
										</a>
									</td>

									<!-- ANULAR / ACTIVAR -->
									<td class="text-center">
										<?php if ($rol == 1) { ?>

											<?php if ($fila['estado'] == 1) { ?>
												<a href="SelectDatosTuristas.php?anular=<?php echo $id; ?>" class="btn btn-sm"
													style="background:#990E08;color:white;"
													onclick="return confirm('¿Anular este registro?');">
													<i class="fas fa-ban"></i>
												</a>
											<?php } else { ?>
												<a href="SelectDatosTuristas.php?activar=<?php echo $id; ?>"
													class="btn btn-sm btn-success"
													onclick="return confirm('¿Activar este registro?');">
													<i class="fas fa-check"></i>
												</a>
											<?php } ?>

										<?php } else { ?>
											<button class="btn btn-sm btn-outline-secondary">
												<i class="fas fa-lock"></i>
											</button>
										<?php } ?>
									</td>

								</tr>

							<?php } ?>

						</tbody>
					</table>

				</div>
			</div>
		</div>
	</div>





	<?php
	/* ================= ANULAR ================= */
	if (isset($_GET['anular'])) {

		$id = intval($_GET['anular']);

		$sql = "UPDATE datosturista 
            SET estado = 0 
            WHERE Id = $id";

		if (mysqli_query($conexion, $sql)) {
			echo "<script>alert('Registro anulado correctamente');</script>";
		} else {
			echo "<script>alert('Error al anular el registro');</script>";
		}

		echo "<script>window.location='SelectDatosTuristas.php';</script>";
		exit();
	}


	/* ================= ACTIVAR ================= */
	if (isset($_GET['activar'])) {

		$id = intval($_GET['activar']);

		$sql = "UPDATE datosturista 
            SET estado = 1 
            WHERE Id = $id";

		if (mysqli_query($conexion, $sql)) {
			echo "<script>alert('Registro reactivado correctamente');</script>";
		} else {
			echo "<script>alert('Error al reactivar el registro');</script>";
		}

		echo "<script>window.location='SelectDatosTuristas.php';</script>";
		exit();
	}
	?>



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
                        <a href="../DatosTuristas/SelectDatosTuristas.php">Encuestas</a>
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


	<!-- ================= SCRIPTS ================= -->
	<script src="../../Js/jquery-3.6.0.min.js"></script>
	<script src="../../Js/bootstrap.min.js"></script>
	<script src="../../DataTables/datatables.min.js"></script>
	<script>
		$(function () {

			var tabla = $('#tablaTuristas').DataTable({

				pageLength: 10,
				lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],

				responsive: true,
				order: [[1, "desc"]],

				pagingType: "simple_numbers",

				dom:
					"<'row mb-3'<'col-md-6'l><'col-md-6 text-end'f>>" +
					"<'row'<'col-12'tr>>" +
					"<'row mt-3'<'col-md-5'i><'col-md-7'p>>",

				language: {
					lengthMenu: "Mostrar _MENU_",
					zeroRecords: "No se encontraron datos",
					info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
					infoEmpty: "Sin registros",
					infoFiltered: "(filtrado de _MAX_ totales)",
					search: "",
					searchPlaceholder: "Buscar turista...",
					paginate: {
						next: "›",
						previous: "‹"
					}
				},

			});


			/* ===== FILTRO ESTADO (CORRECTO) ===== */
			$('#filtroEstado').on('change', function () {

				var valor = this.value;

				if (valor == "1") {
					tabla.column(7).search("Activo").draw();
				}
				else if (valor == "0") {
					tabla.column(7).search("Anulado").draw();
				}
				else {
					tabla.column(7).search("").draw();
				}

			});



			/* ===== FILTROS RESTANTES ===== */
			$('#filtroAno').on('change', function () {
				tabla.column(1).search(this.value).draw();
			});

			$('#filtroMes').on('change', function () {
				tabla.column(2).search(this.value).draw();
			});


			$('#filtroProcedencia').on('change', function () {
				tabla.column(3).search(this.value).draw();
			});

			$('#filtroMunicipio').on('change', function () {
				tabla.column(4).search(this.value).draw();
			});


			/* ===== APLICAR ACTIVO POR DEFECTO AL CARGAR ===== */
			tabla.column(7).search("Activo").draw();



			/* ===== LIMPIAR ===== */
			$('#limpiarFiltros').click(function () {

				$('#filtroEstado').val("1");
				$('#filtroAno, #filtroMes, #filtroProcedencia, #filtroMunicipio').val('');

				tabla.search('').columns().search('');
				tabla.column(7).search("Activo");
				tabla.draw();
			});


		});
	</script>





</body>

</html>