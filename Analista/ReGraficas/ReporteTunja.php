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
                        <i class="fa fa-bullhorn fa-2x text-primary"></i>
                    </div>

                    <div>
                        <h4 class="mb-1 fw-bold text-dark">
                            Reporte: ¿Cómo se enteró del destino?
                        </h4>

                        <p class="mb-0 text-muted">
                            Análisis estadístico de los medios a través de los cuales los turistas
                            conocieron el destino. Este reporte permite identificar los canales
                            de mayor impacto (recomendación, medios digitales, publicidad, entre otros)
                            y evaluar la efectividad de las estrategias de promoción y comunicación.
                            Los resultados pueden visualizarse en cantidad o porcentaje, según los
                            filtros aplicados.
                        </p>
                    </div>

                </div>

            </div>
        </div>

        <div class="card">
            <h5 class="card-header Fuente-cajas">Reportes</h5>
            <div class="card-body">

                <?php
                // =========================
// CAPTURA FILTROS
// =========================
                $fecha_inicio = mysqli_real_escape_string($conexion, $_GET['fecha_inicio'] ?? '');
                $fecha_fin = mysqli_real_escape_string($conexion, $_GET['fecha_fin'] ?? '');
                $municipio = (int) ($_GET['municipio'] ?? 0);
                $procedencia = (int) ($_GET['procedencia'] ?? 0);

                // =========================
// CONSTRUCCIÓN WHERE
// =========================
                $where = [];
                $where[] = "1=1";

                if (!empty($fecha_inicio)) {
                    $where[] = "DATE(d.fecha_registro) >= '$fecha_inicio'";
                }

                if (!empty($fecha_fin)) {
                    $where[] = "DATE(d.fecha_registro) <= '$fecha_fin'";
                }

                if (!empty($municipio)) {
                    $where[] = "(
        d.IdMunicipioVisitado1 = '$municipio' OR
        d.IdMunicipioVisitado2 = '$municipio' OR
        d.IdMunicipioVisitado3 = '$municipio' OR
        d.IdMunicipioVisitado4 = '$municipio' OR
        d.IdMunicipioVisitado5 = '$municipio' OR
        d.IdMunicipioVisitado6 = '$municipio' OR
        d.IdMunicipioVisitado7 = '$municipio'
    )";
                }

                if (!empty($procedencia)) {
                    $where[] = "d.IdProcedencia = '$procedencia'";
                }

                $whereSQL = implode(" AND ", $where);

                // =========================
// TOTAL TURISTAS FILTRADOS
// =========================
                $sqlTotal = "
SELECT COUNT(DISTINCT d.Id) as total
FROM datosturista d
WHERE $whereSQL
";

                $resTotal = mysqli_query($conexion, $sqlTotal);
                $totalTuristas = mysqli_fetch_assoc($resTotal)['total'] ?? 0;

                // =========================
// DETECTAR CAMPOS REALES DE Comoseentero
// =========================
                $columnas = [];

                $resColumnas = mysqli_query($conexion, "SHOW COLUMNS FROM Comoseentero");

                while ($col = mysqli_fetch_assoc($resColumnas)) {

                    $campo = $col['Field'];

                    // Excluir los que no sirven
                    if ($campo != 'Id' && $campo != 'IdDatosTurista' && $campo != 'Cuales') {
                        $columnas[] = $campo;
                    }
                }

                // Seguridad: si no hay columnas, detener
                if (empty($columnas)) {
                    echo "<div class='alert alert-danger'>No se encontraron campos evaluables.</div>";
                    return;
                }

                // =========================
// ARMAR SELECT DINÁMICO
// =========================
                $selectPartes = [];

                foreach ($columnas as $campo) {
                    $selectPartes[] = "SUM(IFNULL(c.`$campo`,0)) AS `$campo`";
                }

                $selectSQL = implode(", ", $selectPartes);

                // =========================
// CONSULTA PRINCIPAL
// =========================
                $sql = "
SELECT $selectSQL
FROM Comoseentero c
INNER JOIN datosturista d ON d.Id = c.IdDatosTurista
WHERE $whereSQL
";

                $result = mysqli_query($conexion, $sql);

                if (!$result) {
                    die("Error en consulta: " . mysqli_error($conexion));
                }

                $data = mysqli_fetch_assoc($result);

                // =========================
// FORMATEAR RESULTADOS
// =========================
                $categorias = [];

                // Nombres personalizados para mostrar en tabla y gráfica
                $nombresPersonalizados = [
                    'YaConocia' => 'Ya conocía el destino',
                    'AmigosyFamiliares' => 'Recomendación de amigos o familiares',
                    'BusqueInternet' => 'Búsqueda en internet',
                    'MediosComunicacion' => 'Medios de comunicación',
                    'AvisosInternet' => 'Publicidad en internet',
                    'Ninguno' => 'No recibió información previa',
                    'Otros' => 'Otros medios'
                ];

                foreach ($columnas as $campo) {

                    $valor = (int) ($data[$campo] ?? 0);

                    if ($valor > 0) {

                        $nombreBonito = $nombresPersonalizados[$campo]
                            ?? ucwords(str_replace("_", " ", $campo));

                        $categorias[$nombreBonito] = $valor;
                    }
                }

                // Ordenar mayor a menor
                arsort($categorias);
                // =========================
// DATOS PARA GRÁFICA
// =========================
                $labelsGrafica = array_keys($categorias);
                $valoresCantidad = array_values($categorias);

                $valoresPorcentaje = [];

                foreach ($valoresCantidad as $valor) {
                    $valoresPorcentaje[] = $totalTuristas > 0
                        ? round(($valor / $totalTuristas) * 100, 2)
                        : 0;
                }
                ?>
                <!-- ========================= -->
                <!-- FILTROS -->
                <!-- ========================= -->

                <form method="GET" id="formFiltros" class="row g-2 mb-4">

                    <!-- Fecha inicio -->
                    <div class="col-md-2">
                        <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?= $fecha_inicio ?>"
                            class="form-control form-control-sm">
                    </div>

                    <!-- Fecha fin -->
                    <div class="col-md-2">
                        <input type="date" name="fecha_fin" id="fecha_fin" value="<?= $fecha_fin ?>"
                            class="form-control form-control-sm">
                    </div>

                    <!-- Municipio -->
                    <div class="col-md-2">
                        <select name="municipio" id="municipio" class="form-select form-select-sm">
                            <option value="">Todos los municipios</option>
                            <?php
                            $mun = mysqli_query($conexion, "SELECT Id, NombreMunicipio FROM Municipios ORDER BY NombreMunicipio");
                            while ($m = mysqli_fetch_assoc($mun)) {
                                $selected = $municipio == $m['Id'] ? 'selected' : '';
                                echo "<option value='{$m['Id']}' $selected>{$m['NombreMunicipio']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Procedencia -->
                    <div class="col-md-2">
                        <select name="procedencia" id="procedencia" class="form-select form-select-sm">
                            <option value="">Todas las procedencias</option>
                            <?php
                            $pro = mysqli_query($conexion, "SELECT Id, Ciudad FROM Procedencia ORDER BY Ciudad");
                            while ($p = mysqli_fetch_assoc($pro)) {
                                $selected = $procedencia == $p['Id'] ? 'selected' : '';
                                echo "<option value='{$p['Id']}' $selected>{$p['Ciudad']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Limpiar -->
                    <div class="col-md-2">
                        <a href="ReporteTunja.php" class="btn btn-secondary btn-sm w-100">
                            <i class="fa fa-eraser"></i>
                        </a>
                    </div>

                </form>


                <script>
                    document.addEventListener("DOMContentLoaded", function () {

                        const form = document.getElementById("formFiltros");
                        const inputs = form.querySelectorAll("input, select");

                        inputs.forEach(input => {

                            input.addEventListener("change", function () {

                                const fechaInicio = document.getElementById("fecha_inicio").value;
                                const fechaFin = document.getElementById("fecha_fin").value;

                                // Si son fechas, solo enviar cuando ambas estén completas
                                if (this.type === "date") {
                                    if (fechaInicio !== "" && fechaFin !== "") {
                                        form.submit();
                                    }
                                } else {
                                    form.submit();
                                }

                            });

                        });

                    });
                </script>

                <!-- ========================= -->
                <!-- KPI TOTAL -->
                <!-- ========================= -->
                <div class="alert alert-info">
                    Total Turistas Filtrados:
                    <strong><?= number_format($totalTuristas) ?></strong>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <!-- ========================= -->
                        <!-- TABLA -->
                        <!-- ========================= -->
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered table-hover text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Medio</th>
                                        <th>Cantidad</th>
                                        <th>% Participación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categorias as $medio => $cantidad):

                                        $porcentaje = $totalTuristas > 0
                                            ? round(($cantidad / $totalTuristas) * 100, 2)
                                            : 0;
                                        ?>
                                        <tr>
                                            <td class="text-start fw-semibold"><?= $medio ?></td>
                                            <td><?= number_format($cantidad) ?></td>
                                            <td><?= $porcentaje ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <?php if (empty($categorias)): ?>
                                        <tr>
                                            <td colspan="3">No hay datos con los filtros seleccionados</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <!-- ========================= -->
                        <!-- SELECTORES GRÁFICA -->
                        <!-- ========================= -->

                        <div class="mb-3 text-end">
                            <select id="selectorDato" class="form-select w-auto d-inline">
                                <option value="cantidad">Cantidad</option>
                                <option value="porcentaje">Porcentaje</option>
                            </select>

                            <select id="selectorGrafica" class="form-select w-auto d-inline ms-2">
                                <option value="bar">Barras</option>
                                <option value="pie">Pastel</option>
                                <option value="doughnut">Dona</option>
                                <option value="radar">Radar</option>
                                <option value="polarArea">Polar</option>
                            </select>
                        </div>

                        <?php if (!empty($categorias)): ?>

                            <div style="height:400px;">
                                <canvas id="graficaComoSeEntero"></canvas>
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

            const labels = <?= json_encode($labelsGrafica) ?>;
            const datosCantidad = <?= json_encode($valoresCantidad) ?>;
            const datosPorcentaje = <?= json_encode($valoresPorcentaje) ?>;

            let grafica = null;
            let datosActuales = datosCantidad;

            function crearGrafica(tipo) {

                if (grafica !== null) {
                    grafica.destroy();
                }

                const ctx = document.getElementById('graficaComoSeEntero').getContext('2d');

                grafica = new Chart(ctx, {
                    type: tipo,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: document.getElementById('selectorDato').value === 'cantidad'
                                ? 'Cantidad de Respuestas'
                                : 'Porcentaje (%)',
                            data: datosActuales,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' }
                        }
                    }
                });
            }

            crearGrafica('bar');

            document.getElementById('selectorGrafica').addEventListener('change', function () {
                crearGrafica(this.value);
            });

            document.getElementById('selectorDato').addEventListener('change', function () {
                datosActuales = this.value === 'cantidad'
                    ? datosCantidad
                    : datosPorcentaje;

                crearGrafica(document.getElementById('selectorGrafica').value);
            });

        </script>
    <?php endif; ?>

</body>

</html>