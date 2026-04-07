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
                        <i class="fa fa-map-location-dot fa-2x text-success"></i>
                    </div>

                    <div>
                        <h4 class="mb-1 fw-bold text-dark">
                            Reporte de Municipios Visitados
                        </h4>

                        <p class="mb-0 text-muted">
                            Análisis estadístico de los municipios visitados por los turistas registrados en el sistema.
                            Permite identificar los destinos con mayor demanda, medir la distribución territorial del
                            flujo
                            turístico y apoyar la planificación estratégica del sector.
                        </p>
                    </div>

                </div>

            </div>
        </div>

        <div class="card">
            <h5 class="card-header Fuente-cajas">Reportes</h5>
            <div class="card-body">
                <?php

                $fecha_inicio = $_GET['fecha_inicio'] ?? '';
                $fecha_fin = $_GET['fecha_fin'] ?? '';
                $procedencia = $_GET['procedencia'] ?? '';
                $municipioFiltro = $_GET['municipio'] ?? '';
                $top = (isset($_GET['top']) && (int) $_GET['top'] > 0) ? (int) $_GET['top'] : 10;
                $tipoGrafica = $_GET['tipo'] ?? 'bar';

                $tiposPermitidos = ['bar', 'line', 'pie', 'doughnut'];
                $tipoGrafica = in_array($tipoGrafica, $tiposPermitidos) ? $tipoGrafica : 'bar';

                $where = [];
                $where[] = "estado = 1";

                /* =========================
                   FILTRO RANGO DE FECHAS
                ========================= */
                if (!empty($fecha_inicio) && !empty($fecha_fin)) {

                    $fecha_inicio = mysqli_real_escape_string($conexion, $fecha_inicio);
                    $fecha_fin = mysqli_real_escape_string($conexion, $fecha_fin);

                    $where[] = "fecha_registro BETWEEN '$fecha_inicio' AND '$fecha_fin'";

                } elseif (!empty($fecha_inicio)) {

                    $fecha_inicio = mysqli_real_escape_string($conexion, $fecha_inicio);
                    $where[] = "fecha_registro >= '$fecha_inicio'";

                } elseif (!empty($fecha_fin)) {

                    $fecha_fin = mysqli_real_escape_string($conexion, $fecha_fin);
                    $where[] = "fecha_registro <= '$fecha_fin'";
                }

                /* =========================
                   FILTRO PROCEDENCIA
                ========================= */
                if (!empty($procedencia)) {
                    $where[] = "IdProcedencia = " . (int) $procedencia;
                }

                $whereSQL = "WHERE " . implode(" AND ", $where);

                /* =========================
                   CONSULTA PRINCIPAL
                ========================= */
                $sql = "
SELECT m.NombreMunicipio, COUNT(*) as total
FROM (
    SELECT IdMunicipioVisitado1 as municipio FROM datosturista $whereSQL
    UNION ALL
    SELECT IdMunicipioVisitado2 FROM datosturista $whereSQL
    UNION ALL
    SELECT IdMunicipioVisitado3 FROM datosturista $whereSQL
    UNION ALL
    SELECT IdMunicipioVisitado4 FROM datosturista $whereSQL
    UNION ALL
    SELECT IdMunicipioVisitado5 FROM datosturista $whereSQL
    UNION ALL
    SELECT IdMunicipioVisitado6 FROM datosturista $whereSQL
    UNION ALL
    SELECT IdMunicipioVisitado7 FROM datosturista $whereSQL
) as visitas
INNER JOIN municipios m ON visitas.municipio = m.Id
WHERE visitas.municipio IS NOT NULL
AND visitas.municipio <> 125
";

                /* FILTRO MUNICIPIO ESPECÍFICO */
                if (!empty($municipioFiltro)) {
                    $sql .= " AND visitas.municipio = " . (int) $municipioFiltro;
                }

                $sql .= "
GROUP BY m.NombreMunicipio
ORDER BY total DESC
LIMIT $top
";

                $resultado = mysqli_query($conexion, $sql);

                /* =========================
                   PROCESAMIENTO RESULTADOS
                ========================= */

                $municipiosTabla = [];

                while ($fila = mysqli_fetch_assoc($resultado)) {
                    $municipiosTabla[] = $fila;
                }

                $labels = array_column($municipiosTabla, 'NombreMunicipio');
                $data = array_column($municipiosTabla, 'total');

                $totalGeneral = array_sum($data);

                ?>


                <form id="filtrosForm" method="GET" class="row g-3 mb-4 bg-light p-3 rounded shadow-sm">

                    <!-- FECHA DESDE -->
                    <div class="col-md-2">
                        <label>Desde</label>
                        <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>"
                            class="form-control filtro-auto">
                    </div>

                    <!-- FECHA HASTA -->
                    <div class="col-md-2">
                        <label>Hasta</label>
                        <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>"
                            class="form-control filtro-auto">
                    </div>

                    <!-- ORIGEN DEL TURISTA (SE MANTIENE IGUAL) -->
                    <div class="col-md-2">
                        <label>Origen del Turista</label>
                        <select name="procedencia" class="form-select filtro-auto">
                            <option value="">Todos</option>
                            <?php
                            $proc = mysqli_query($conexion, "SELECT * FROM procedencia ORDER BY Ciudad");
                            while ($p = mysqli_fetch_assoc($proc)) {
                                $selected = ($procedencia == $p['Id']) ? 'selected' : '';
                                echo "<option value='{$p['Id']}' $selected>{$p['Ciudad']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- MUNICIPIO ESPECÍFICO (SE MANTIENE IGUAL) -->
                    <div class="col-md-2">
                        <label>Municipio específico</label>
                        <select name="municipio" class="form-select filtro-auto">
                            <option value="">Todos</option>
                            <?php
                            $mun = mysqli_query($conexion, "SELECT * FROM municipios ORDER BY NombreMunicipio");
                            while ($m = mysqli_fetch_assoc($mun)) {
                                $selected = ($municipioFiltro == $m['Id']) ? 'selected' : '';
                                echo "<option value='{$m['Id']}' $selected>{$m['NombreMunicipio']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- TOP MUNICIPIOS (SE MANTIENE IGUAL) -->
                    <div class="col-md-2">
                        <label>Top Municipios</label>
                        <select name="top" class="form-select filtro-auto">
                            <option value="5" <?= $top == 5 ? 'selected' : '' ?>>Top 5</option>
                            <option value="10" <?= $top == 10 ? 'selected' : '' ?>>Top 10</option>
                            <option value="15" <?= $top == 15 ? 'selected' : '' ?>>Top 15</option>
                            <option value="20" <?= $top == 20 ? 'selected' : '' ?>>Top 20</option>
                        </select>
                    </div>

                    <!-- TIPO DE GRÁFICA (SE MANTIENE IGUAL) -->
                    <div class="col-md-2">
                        <label>Tipo de gráfica</label>
                        <select name="tipo" class="form-select filtro-auto">
                            <option value="bar" <?= $tipoGrafica == 'bar' ? 'selected' : '' ?>>Barras</option>
                            <option value="line" <?= $tipoGrafica == 'line' ? 'selected' : '' ?>>Línea</option>
                            <option value="pie" <?= $tipoGrafica == 'pie' ? 'selected' : '' ?>>Torta</option>
                            <option value="doughnut" <?= $tipoGrafica == 'doughnut' ? 'selected' : '' ?>>Dona</option>
                        </select>
                    </div>

                    <!-- LIMPIAR (SE MANTIENE IGUAL) -->
                    <div class="col-md-1 d-flex align-items-end">
                        <a href="ReporteMunicipios.php" class="btn btn-outline-secondary w-100" title="Limpiar">
                            <i class="fa fa-eraser"></i>
                        </a>
                    </div>

                </form>



                <div class="row">
                    <div class="col-md-6">
                        <?php if (!empty($municipiosTabla)) { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th>#</th>
                                            <th>Municipio</th>
                                            <th>Total Visitas</th>
                                            <th>% Participación</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        <?php
                                        $totalGeneral = array_sum($data);
                                        $contador = 1;

                                        foreach ($municipiosTabla as $fila):
                                            $porcentaje = ($totalGeneral > 0)
                                                ? round(($fila['total'] / $totalGeneral) * 100, 2)
                                                : 0;
                                            ?>
                                            <tr>
                                                <td><?= $contador++ ?></td>
                                                <td><?= htmlspecialchars($fila['NombreMunicipio']) ?></td>
                                                <td><?= $fila['total'] ?></td>
                                                <td><?= $porcentaje ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light fw-bold text-center">
                                        <tr>
                                            <td colspan="2">TOTAL</td>
                                            <td><?= $totalGeneral ?></td>
                                            <td>100%</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-6">


                        <?php if (!empty($labels)) { ?>
                            <div style="width:100%; height:500px;">
                                <canvas id="graficoMunicipios"></canvas>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-warning text-center">
                                No hay datos para los filtros seleccionados.
                            </div>
                        <?php } ?>
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


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.querySelectorAll('.filtro-auto').forEach(select => {
            select.addEventListener('change', function () {
                document.getElementById('filtrosForm').submit();
            });
        });
    </script>

    <script>
        <?php if (!empty($labels)) { ?>

            let ctx = document.getElementById('graficoMunicipios').getContext('2d');

            let grafico = new Chart(ctx, {
                type: '<?= $tipoGrafica ?>',
                data: {
                    labels: <?= json_encode($labels) ?>,
                    datasets: [{
                        label: 'Cantidad de Visitas',
                        data: <?= json_encode($data) ?>,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: <?= $tipoGrafica == 'bar' && count($labels) > 8 ? "'y'" : "'x'" ?>,
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

        <?php } ?>
    </script>


</body>

</html>