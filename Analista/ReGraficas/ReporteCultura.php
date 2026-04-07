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


        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body border-start border-4 border-success">

                <div class="d-flex align-items-center">

                    <div class="me-3">
                        <i class="fa fa-landmark fa-2x text-success"></i>
                    </div>

                    <div>
                        <h4 class="mb-1 fw-bold text-dark">
                            Reporte de Actividades Culturales
                        </h4>

                        <p class="mb-0 text-muted">
                            Visualización estadística de las actividades culturales realizadas por los turistas
                            durante su visita. Este reporte permite identificar los principales atractivos
                            culturales del destino, medir su nivel de interés y fortalecer la planificación
                            estratégica del sector turístico a través de datos cuantitativos.
                        </p>
                    </div>

                </div>

            </div>
        </div>

        <div class="card">
            <h5 class="card-header Fuente-cajas">Reportes</h5>
            <div class="card-body">





                <?php
                // ==============================
// FILTROS
// ==============================
                
                $fecha_inicio = $_GET['fecha_inicio'] ?? '';
                $fecha_fin = $_GET['fecha_fin'] ?? '';
                $idMunicipio = $_GET['municipio'] ?? '';
                $idProcedencia = $_GET['procedencia'] ?? '';
                $tipoGrafica = $_GET['tipo_grafica'] ?? 'bar';

                // ==============================
// CONSULTA
// ==============================
                
                $sql = "SELECT 
            COALESCE(SUM(c.Catedrales),0) AS Catedrales,
            COALESCE(SUM(c.CasasCultura),0) AS CasasCultura,
            COALESCE(SUM(c.MuseosArte),0) AS MuseosArte,
            COALESCE(SUM(c.MuseosArqueologicos),0) AS MuseosArqueologicos,
            COALESCE(SUM(c.HaciendasCultura),0) AS Haciendas,
            COALESCE(SUM(c.Puentes),0) AS Puentes,
            COALESCE(SUM(c.Monumentos),0) AS Monumentos,
            COALESCE(SUM(c.Cementerios),0) AS Cementerios,
            COALESCE(SUM(c.Santuarios),0) AS Santuarios,
            COALESCE(SUM(c.Ninguna),0) AS Ninguna,
            COALESCE(SUM(c.Otros),0) AS Otros
        FROM cultura c
        INNER JOIN datosturista d ON c.IdDatosTurista = d.Id
        WHERE 1=1";

                if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                    $sql .= " AND d.fecha_registro BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                }

                if (!empty($idMunicipio)) {
                    $sql .= " AND (
                d.IdMunicipioVisitado1 = '$idMunicipio' OR
                d.IdMunicipioVisitado2 = '$idMunicipio' OR
                d.IdMunicipioVisitado3 = '$idMunicipio' OR
                d.IdMunicipioVisitado4 = '$idMunicipio' OR
                d.IdMunicipioVisitado5 = '$idMunicipio' OR
                d.IdMunicipioVisitado6 = '$idMunicipio' OR
                d.IdMunicipioVisitado7 = '$idMunicipio'
            )";
                }

                if (!empty($idProcedencia)) {
                    $sql .= " AND d.IdProcedencia = '$idProcedencia'";
                }

                $resultado = mysqli_query($conexion, $sql);
                $data = mysqli_fetch_assoc($resultado);

                $total = array_sum($data);

                $actividades = [
                    "Catedrales" => $data['Catedrales'],
                    "Casas de Cultura" => $data['CasasCultura'],
                    "Museos Arte" => $data['MuseosArte'],
                    "Museos Arqueológicos" => $data['MuseosArqueologicos'],
                    "Haciendas Culturales" => $data['Haciendas'],
                    "Puentes" => $data['Puentes'],
                    "Monumentos" => $data['Monumentos'],
                    "Cementerios" => $data['Cementerios'],
                    "Santuarios" => $data['Santuarios'],
                    "Ninguna Actividad" => $data['Ninguna'],
                    "Otras Actividades" => $data['Otros']
                ];
                ?>

                <!-- ============================== -->
                <!-- FILTROS -->
                <!-- ============================== -->

                <form method="GET" class="row g-3 mb-4">

                    <div class="col-md-2">
                        <label>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="<?= $fecha_inicio ?>"
                            onchange="this.form.submit()">
                    </div>

                    <div class="col-md-2">
                        <label>Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" value="<?= $fecha_fin ?>"
                            onchange="this.form.submit()">
                    </div>

                    <div class="col-md-2">
                        <label>Municipio</label>
                        <select name="municipio" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <?php
                            $mun = mysqli_query($conexion, "SELECT * FROM municipios");
                            while ($m = mysqli_fetch_assoc($mun)) {
                                $sel = ($idMunicipio == $m['Id']) ? 'selected' : '';
                                echo "<option value='{$m['Id']}' $sel>{$m['NombreMunicipio']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Procedencia</label>
                        <select name="procedencia" class="form-select" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            <?php
                            $proc = mysqli_query($conexion, "SELECT * FROM Procedencia");
                            while ($p = mysqli_fetch_assoc($proc)) {
                                $sel = ($idProcedencia == $p['Id']) ? 'selected' : '';
                                echo "<option value='{$p['Id']}' $sel>{$p['Ciudad']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Tipo Gráfica</label>
                        <select name="tipo_grafica" class="form-select" onchange="this.form.submit()">
                            <option value="bar" <?= $tipoGrafica == 'bar' ? 'selected' : '' ?>>Barras</option>
                            <option value="pie" <?= $tipoGrafica == 'pie' ? 'selected' : '' ?>>Torta</option>
                            <option value="doughnut" <?= $tipoGrafica == 'doughnut' ? 'selected' : '' ?>>Dona</option>
                            <option value="line" <?= $tipoGrafica == 'line' ? 'selected' : '' ?>>Línea</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="ReporteCultura.php" class="btn btn-secondary w-100">Limpiar Filtros</a>
                    </div>

                </form>

                <!-- ============================== -->
                <!-- TABLA -->
                <!-- ============================== -->

                <div class="row">
                    <div class="col-md-5">

                        <table class="table table-bordered text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Actividad</th>
                                    <th>Cantidad</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($actividades as $nombre => $valor):
                                    $porcentaje = ($total > 0) ? round(($valor / $total) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td><?= $nombre ?></td>
                                        <td><?= $valor ?></td>
                                        <td><?= $porcentaje ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="table-primary fw-bold">
                                    <td>Total</td>
                                    <td><?= $total ?></td>
                                    <td>100%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-7">
                        <div style="width: 600px; height: 350px;">
                            <canvas id="graficaCultura"></canvas>
                        </div>
                    </div>
                </div>





                <a href="VerReportes.php" class="btn btn-outline-primary" title="Volver">
                    <i class="fa fa-arrow-left"></i>
                </a>

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

</body>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

    const ctx = document.getElementById('graficaCultura');

    new Chart(ctx, {
        type: '<?= $tipoGrafica ?>',
        data: {
            labels: <?= json_encode(array_keys($actividades)) ?>,
            datasets: [{
                label: 'Cantidad',
                data: <?= json_encode(array_values($actividades)) ?>,
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545',
                    '#6f42c1', '#20c997', '#fd7e14', '#17a2b8',
                    '#6610f2', '#6c757d'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // 🔥 MUY IMPORTANTE
            plugins: {
                legend: {
                    display: <?= ($tipoGrafica == 'bar' || $tipoGrafica == 'line') ? 'false' : 'true' ?>
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    display: <?= ($tipoGrafica == 'pie' || $tipoGrafica == 'doughnut') ? 'false' : 'true' ?>
                }
            }
        }
    });

</script>


</html>