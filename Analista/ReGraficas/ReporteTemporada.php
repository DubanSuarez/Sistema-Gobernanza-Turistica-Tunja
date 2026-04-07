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

        <div class="card shadow border-0 mb-4">
            <div class="card-body border-start border-4 border-danger bg-light">

                <div class="d-flex align-items-center">

                    <div class="me-4 text-center">
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                            <i class="fa fa-calendar-alt fa-2x text-danger"></i>
                        </div>
                    </div>

                    <div>
                        <h4 class="fw-bold mb-2 text-danger">
                            Reporte de Temporadas Turísticas
                        </h4>

                        <p class="mb-1 text-muted">
                            Análisis estadístico del comportamiento de visitantes según la temporada del año.
                        </p>

                        <p class="mb-0 text-muted small">
                            Este reporte clasifica los registros en temporada Alta, Media y Baja
                            según el mes de visita, permitiendo identificar periodos de mayor
                            y menor flujo turístico para apoyar la planificación estratégica
                            y la gestión del destino.
                        </p>
                    </div>

                </div>

            </div>
        </div>

        <div class="card">
            <h5 class="card-header Fuente-cajas">Reportes</h5>
            <div class="card-body">

                <?php

                /* ======================================================
                   1️⃣ CAPTURA Y VALIDACIÓN DE FILTROS
                ====================================================== */

                $fecha_inicio = $_GET['fecha_inicio'] ?? '';
                $fecha_fin = $_GET['fecha_fin'] ?? '';
                $top = isset($_GET['top']) ? (int) $_GET['top'] : 0;
                $pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

                $limitePagina = 15;
                if ($pagina < 1)
                    $pagina = 1;

                $offset = ($pagina - 1) * $limitePagina;

                /* Validar fechas */
                if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                    if ($fecha_inicio > $fecha_fin) {
                        die("La fecha inicio no puede ser mayor que la fecha fin.");
                    }
                }

                /* ======================================================
                   2️⃣ WHERE DINÁMICO SEGURO
                ====================================================== */

                $where = [];
                $where[] = "d.estado = 1";

                if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                    $fecha_inicio = mysqli_real_escape_string($conexion, $fecha_inicio);
                    $fecha_fin = mysqli_real_escape_string($conexion, $fecha_fin);
                    $where[] = "DATE(d.fecha_registro) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                }

                $whereSQL = implode(" AND ", $where);

                /* ======================================================
                   3️⃣ CONSULTA BASE
                ====================================================== */

                $sql = "
SELECT 
    m.Id,
    m.NombreMunicipio,

    COALESCE(
        COUNT(DISTINCT CASE WHEN d.IdMunicipioVisitado1 = m.Id THEN d.Id END) +
        COUNT(DISTINCT CASE WHEN d.IdMunicipioVisitado2 = m.Id THEN d.Id END) +
        COUNT(DISTINCT CASE WHEN d.IdMunicipioVisitado3 = m.Id THEN d.Id END) +
        COUNT(DISTINCT CASE WHEN d.IdMunicipioVisitado4 = m.Id THEN d.Id END) +
        COUNT(DISTINCT CASE WHEN d.IdMunicipioVisitado5 = m.Id THEN d.Id END) +
        COUNT(DISTINCT CASE WHEN d.IdMunicipioVisitado6 = m.Id THEN d.Id END) +
        COUNT(DISTINCT CASE WHEN d.IdMunicipioVisitado7 = m.Id THEN d.Id END)
    ,0) AS TotalVisitas

FROM municipios m

LEFT JOIN datosturista d 
    ON (
        d.IdMunicipioVisitado1 = m.Id OR
        d.IdMunicipioVisitado2 = m.Id OR
        d.IdMunicipioVisitado3 = m.Id OR
        d.IdMunicipioVisitado4 = m.Id OR
        d.IdMunicipioVisitado5 = m.Id OR
        d.IdMunicipioVisitado6 = m.Id OR
        d.IdMunicipioVisitado7 = m.Id
    )
    AND $whereSQL

WHERE m.Id != 125

GROUP BY m.Id, m.NombreMunicipio
ORDER BY TotalVisitas DESC
";

                $resultado = mysqli_query($conexion, $sql);

                if (!$resultado) {
                    die("Error en la consulta: " . mysqli_error($conexion));
                }

                $datos = [];
                $totalGeneral = 0;

                while ($fila = mysqli_fetch_assoc($resultado)) {
                    $datos[] = $fila;
                    $totalGeneral += $fila['TotalVisitas'];
                }

                /* ======================================================
                   4️⃣ APLICAR TOP O PAGINACIÓN
                ====================================================== */

                if ($top > 0) {

                    $datos = array_slice($datos, 0, $top);
                    $totalGeneral = array_sum(array_column($datos, 'TotalVisitas'));
                    $totalPaginas = 1;

                } else {

                    $totalRegistros = count($datos);
                    $totalPaginas = ($totalRegistros > 0) ? ceil($totalRegistros / $limitePagina) : 1;

                    if ($pagina > $totalPaginas)
                        $pagina = $totalPaginas;

                    $offset = ($pagina - 1) * $limitePagina;
                    $datos = array_slice($datos, $offset, $limitePagina);
                }
                ?>

                <!-- ======================================================
   FILTROS
====================================================== -->

                <form method="GET" id="filtrosForm" class="card p-3 shadow-sm mb-4">

                    <div class="row g-3 align-items-end">

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Fecha inicio</label>
                            <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>"
                                class="form-control filtro-auto">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Fecha fin</label>
                            <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>"
                                class="form-control filtro-auto">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Mostrar</label>
                            <select name="top" class="form-select filtro-auto">
                                <option value="0">Todos</option>
                                <?php foreach ([5, 10, 20, 50, 100] as $n): ?>
                                    <option value="<?= $n ?>" <?= $top == $n ? 'selected' : '' ?>>
                                        Top <?= $n ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Buscar</label>
                            <input type="text" id="buscadorTabla" class="form-control" placeholder="Municipio...">
                        </div>

                        <div class="col-md-2 d-grid">
                            <label class="form-label invisible">Acción</label>
                            <a href="ReporteTemporada.php" class="btn btn-outline-secondary">
                                <i class="fa fa-eraser"></i>
                            </a>
                        </div>

                    </div>
                </form>


                <p class="text-muted">
                    Total general de visitas: <strong><?= $totalGeneral ?></strong>
                </p>



                <div class="row">
                    <div class="col-md-5">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm" id="tablaMunicipios">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Municipio</th>
                                        <th>Total</th>
                                        <th>%</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php if (empty($datos)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">
                                                No se encontraron resultados
                                            </td>
                                        </tr>
                                    <?php else: ?>

                                        <?php
                                        $contador = ($top > 0) ? 1 : $offset + 1;

                                        foreach ($datos as $fila):

                                            $porcentaje = ($totalGeneral > 0)
                                                ? round(($fila['TotalVisitas'] / $totalGeneral) * 100, 2)
                                                : 0;
                                            ?>

                                            <tr>
                                                <td><?= $contador++; ?></td>
                                                <td><?= htmlspecialchars($fila['NombreMunicipio']); ?></td>
                                                <td><strong><?= $fila['TotalVisitas']; ?></strong></td>
                                                <td><?= $porcentaje ?>%</td>
                                            </tr>

                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Gráfica de visitas</h5>

                            <select id="tipoGrafica" class="form-select w-auto">
                                <option value="bar-horizontal">Barras Horizontales</option>
                                <option value="bar">Barras Verticales</option>
                                <option value="pie">Pastel</option>
                                <option value="doughnut">Dona</option>
                            </select>
                        </div>
                        <div class="card mt-4">
                            <div class="card-body">
                                <h5 class="mb-3">Gráfica de visitas por municipio</h5>
                                <div style="max-width: 600px; margin:auto;">
                                    <canvas id="graficaMunicipios"></canvas>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <?php if ($top == 0 && $totalPaginas > 1): ?>

                    <nav>
                        <ul class="pagination justify-content-center">

                            <li class="page-item <?= ($pagina <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link"
                                    href="?pagina=<?= $pagina - 1 ?>&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&top=0">
                                    &laquo;
                                </a>
                            </li>

                            <?php
                            $rango = 5;
                            $inicio = max(1, $pagina - $rango);
                            $fin = min($totalPaginas, $pagina + $rango);

                            for ($i = $inicio; $i <= $fin; $i++):
                                ?>

                                <li class="page-item <?= ($pagina == $i) ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?pagina=<?= $i ?>&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&top=0">
                                        <?= $i ?>
                                    </a>
                                </li>

                            <?php endfor; ?>

                            <li class="page-item <?= ($pagina >= $totalPaginas) ? 'disabled' : '' ?>">
                                <a class="page-link"
                                    href="?pagina=<?= $pagina + 1 ?>&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&top=0">
                                    &raquo;
                                </a>
                            </li>

                        </ul>
                    </nav>

                    <div class="text-center text-muted small">
                        Página <?= $pagina ?> de <?= $totalPaginas ?>
                    </div>

                <?php endif; ?>




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
        document.querySelectorAll('.filtro-auto').forEach(el => {
            el.addEventListener('change', () => {
                document.getElementById('filtrosForm').submit();
            });
        });

        document.getElementById("buscadorTabla").addEventListener("keyup", function () {
            let filtro = this.value.toLowerCase();
            let filas = document.querySelectorAll("#tablaMunicipios tbody tr");

            filas.forEach(function (fila) {
                let texto = fila.innerText.toLowerCase();
                fila.style.display = texto.includes(filtro) ? "" : "none";
            });
        });
    </script>

    <script>
        const etiquetas = <?= json_encode(array_column($datos, 'NombreMunicipio')) ?>;
        const totales = <?= json_encode(array_column($datos, 'TotalVisitas')) ?>;

        const ctx = document.getElementById('graficaMunicipios');

        let grafica;

        // 🎨 Generador de colores automáticos
        function generarColores(cantidad) {
            const colores = [];
            for (let i = 0; i < cantidad; i++) {
                colores.push(`hsl(${i * 360 / cantidad}, 70%, 60%)`);
            }
            return colores;
        }

        function crearGrafica(tipo) {

            if (grafica) {
                grafica.destroy();
            }

            let configuracion = {
                type: tipo === 'bar-horizontal' ? 'bar' : tipo,
                data: {
                    labels: etiquetas,
                    datasets: [{
                        label: 'Total de visitas',
                        data: totales,
                        backgroundColor: generarColores(totales.length),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // 🔥 permite controlar altura con CSS
                    plugins: {
                        legend: {
                            display: (tipo === 'pie' || tipo === 'doughnut')
                        }
                    }
                }
            };

            // 📊 Barras horizontales
            if (tipo === 'bar-horizontal') {
                configuracion.options.indexAxis = 'y';
                configuracion.options.scales = {
                    x: { beginAtZero: true }
                };
            }

            // 📈 Barras verticales
            if (tipo === 'bar') {
                configuracion.options.scales = {
                    y: { beginAtZero: true }
                };
            }

            grafica = new Chart(ctx, configuracion);

            // 🎯 Ajustar tamaño dinámicamente según tipo
            if (tipo === 'pie' || tipo === 'doughnut') {
                ctx.style.height = "250px";
            } else {
                ctx.style.height = "320px";
            }
        }

        // Crear gráfica inicial
        crearGrafica('bar-horizontal');

        // Detectar cambio
        document.getElementById('tipoGrafica').addEventListener('change', function () {
            crearGrafica(this.value);
        });
    </script>



</body>

</html>