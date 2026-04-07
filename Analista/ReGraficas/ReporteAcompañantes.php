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
            <div class="card-body border-start border-4 border-primary">

                <div class="d-flex align-items-center">

                    <div class="me-3">
                        <i class="fa fa-users fa-2x text-primary"></i>
                    </div>

                    <div>
                        <h4 class="mb-1 fw-bold text-dark">
                            Reporte de Acompañantes del Visitante
                        </h4>

                        <p class="mb-0 text-muted">
                            Este reporte muestra el tipo de acompañantes con los que llegan los turistas
                            (familia, amigos, pareja, solos, entre otros) y la cantidad total registrada
                            en el sistema. Permite analizar el perfil de viaje y el comportamiento grupal
                            de los visitantes según los filtros seleccionados.
                        </p>
                    </div>

                </div>

            </div>
        </div>
        <div class="card">
            <h5 class="card-header Fuente-cajas">Reportes</h5>
            <div class="card-body">

                <?php
                // ===============================
// CAPTURA DE FILTROS
// ===============================
                $fecha_inicio = $_GET['fecha_inicio'] ?? '';
                $fecha_fin = $_GET['fecha_fin'] ?? '';
                $municipio = $_GET['municipio'] ?? '';
                $procedencia = $_GET['procedencia'] ?? '';
                $tipoGrafica = $_GET['tipo'] ?? 'bar';

                // ===============================
// CONSTRUCCIÓN DINÁMICA DEL WHERE
// ===============================
                $where = ["1=1"]; // Siempre verdadero
                
                // ===============================
// FILTRO DE FECHAS (independiente)
// ===============================
                if (!empty($fecha_inicio)) {
                    $fecha_inicio = date('Y-m-d', strtotime($fecha_inicio));
                    $where[] = "DATE(fecha_registro) >= '$fecha_inicio'";
                }

                if (!empty($fecha_fin)) {
                    $fecha_fin = date('Y-m-d', strtotime($fecha_fin));
                    $where[] = "DATE(fecha_registro) <= '$fecha_fin'";
                }

                // ===============================
// FILTRO DE MUNICIPIO (DINÁMICO)
// ===============================
                if (!empty($municipio) && is_numeric($municipio)) {

                    $municipio = intval($municipio);

                    $where[] = "(
        IdMunicipioVisitado1 = $municipio OR
        IdMunicipioVisitado2 = $municipio OR
        IdMunicipioVisitado3 = $municipio OR
        IdMunicipioVisitado4 = $municipio OR
        IdMunicipioVisitado5 = $municipio OR
        IdMunicipioVisitado6 = $municipio OR
        IdMunicipioVisitado7 = $municipio
    )";
                }

                // ===============================
// FILTRO DE PROCEDENCIA
// ===============================
                if (!empty($procedencia) && is_numeric($procedencia)) {
                    $procedencia = intval($procedencia);
                    $where[] = "IdProcedencia = $procedencia";
                }

                $whereSQL = implode(" AND ", $where);

                // ===============================
// CONSULTA PRINCIPAL
// ===============================
                $sql = "
SELECT 
    TipoAcompanantes,
    COUNT(*) AS TotalTuristas,
    SUM(COALESCE(CantidadAcompanantes,0)) AS TotalAcompanantes
FROM datosturista
WHERE $whereSQL
GROUP BY TipoAcompanantes
ORDER BY TotalAcompanantes DESC
";

                $resultado = mysqli_query($conexion, $sql);

                if (!$resultado) {
                    die("Error en la consulta: " . mysqli_error($conexion));
                }

                // ===============================
// ARMAR DATOS PARA TABLA Y GRÁFICA
// ===============================
                $labels = [];
                $data = [];
                $tabla = [];

                while ($fila = mysqli_fetch_assoc($resultado)) {

                    $tipo = !empty($fila['TipoAcompanantes'])
                        ? $fila['TipoAcompanantes']
                        : "No especificado";

                    $total = (int) $fila['TotalAcompanantes'];

                    $labels[] = $tipo;
                    $data[] = $total;

                    $tabla[] = [
                        'tipo' => $tipo,
                        'total' => $total
                    ];
                }

                // ===============================
// TOTAL GENERAL PARA PORCENTAJES
// ===============================
                $totalGeneral = array_sum($data);
                ?>

                <form method="GET">
                    <div class="row g-3 align-items-end">

                        <!-- Fecha Inicio -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" value="<?= $fecha_inicio ?>"
                                class="form-control form-control-sm" onchange="this.form.submit()">
                        </div>

                        <!-- Fecha Final -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Fecha Final</label>
                            <input type="date" name="fecha_fin" value="<?= $fecha_fin ?>"
                                class="form-control form-control-sm" onchange="this.form.submit()">
                        </div>

                        <!-- Municipio -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Municipio Visitado</label>
                            <select name="municipio" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                <?php
                                $mun = mysqli_query($conexion, "SELECT * FROM municipios ORDER BY NombreMunicipio");
                                while ($m = mysqli_fetch_assoc($mun)) {
                                    $selected = ($municipio == $m['Id']) ? 'selected' : '';
                                    // Tunja por defecto
                                    if (!isset($_GET['municipio']) && $m['Id'] == 114)
                                        $selected = 'selected';
                                    echo "<option value='{$m['Id']}' $selected>{$m['NombreMunicipio']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Procedencia -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Procedencia</label>
                            <select name="procedencia" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Todas</option>
                                <?php
                                $proc = mysqli_query($conexion, "SELECT * FROM procedencia ORDER BY Ciudad");
                                while ($p = mysqli_fetch_assoc($proc)) {
                                    $selected = ($procedencia == $p['Id']) ? 'selected' : '';
                                    echo "<option value='{$p['Id']}' $selected>{$p['Ciudad']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Tipo gráfica -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Tipo de Gráfica</label>
                            <select name="tipo" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="bar" <?= $tipoGrafica == 'bar' ? 'selected' : '' ?>>Barras</option>
                                <option value="pie" <?= $tipoGrafica == 'pie' ? 'selected' : '' ?>>Circular</option>
                                <option value="doughnut" <?= $tipoGrafica == 'doughnut' ? 'selected' : '' ?>>Dona</option>
                            </select>
                        </div>

                        <!-- Limpiar -->
                        <div class="col-md-1">
                            <label class="form-label invisible">Limpiar</label>
                            <a href="?" class="btn btn-outline-secondary btn-sm w-100"><i class="fa fa-eraser"></i></a>
                        </div>

                    </div>
                </form>






                <div class="row mt-3">

                    <!-- Tabla -->
                    <div class="col-md-6">
                        <h5 class="mb-3">Resumen de Acompañantes</h5>
                        <table class="table table-bordered table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tipo Acompañante</th>
                                    <th>Total Acompañantes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tabla as $fila):
                                    $porcentaje = ($totalGeneral > 0) ? round(($fila['total'] / $totalGeneral) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td><?= $fila['tipo'] ?></td>
                                        <td><?= $fila['total'] ?> (<?= $porcentaje ?>%)</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Gráfica -->
                    <div class="col-md-6">
                        <h5 class="mb-3">Gráfica de Acompañantes</h5>
                        <div style="height:400px;">
                            <canvas id="graficaAcompanantes"></canvas>
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


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
        const ctx = document.getElementById('graficaAcompanantes');

        new Chart(ctx, {
            type: '<?= $tipoGrafica ?>',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Total Acompañantes',
                    data: <?= json_encode($data) ?>,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#20c997'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let value = context.raw;
                                let porcentaje = ((value / total) * 100).toFixed(1);
                                return value + " acompañantes (" + porcentaje + "%)";
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    </script>

</body>

</html>