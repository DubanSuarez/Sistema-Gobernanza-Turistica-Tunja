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


        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body border-start border-4 border-success">

                <div class="d-flex align-items-center">

                    <div class="me-3">
                        <i class="fa fa-share-alt fa-2x text-success"></i>
                    </div>

                    <div>
                        <h5 class="mb-1 fw-bold">
                            Difusión de la Experiencia Turística
                        </h5>

                        <p class="mb-0 text-muted">
                            Identifica si los turistas compartieron su visita y los medios utilizados,
                            permitiendo medir el impacto y la promoción del destino.
                        </p>
                    </div>

                </div>

            </div>
        </div>

        <div class="card">
            <h5 class="card-header Fuente-cajas">Reportes</h5>
            <div class="card-body">


                <?php
                // ======================================================
// 1️⃣ CARGAR FILTROS AUTOMÁTICOS (DINÁMICOS)
// ======================================================
                
                // Municipios
                $listaMunicipios = [];
                $resMun = mysqli_query($conexion, "SELECT Id, NombreMunicipio FROM Municipios ORDER BY NombreMunicipio ASC");
                while ($row = mysqli_fetch_assoc($resMun)) {
                    $listaMunicipios[] = $row;
                }

                // Procedencias
                $listaProcedencias = [];
                $resProc = mysqli_query($conexion, "SELECT Id, Ciudad FROM Procedencia ORDER BY Ciudad ASC");
                while ($row = mysqli_fetch_assoc($resProc)) {
                    $listaProcedencias[] = $row;
                }


                // ======================================================
// 2️⃣ CAPTURA SEGURA DE FILTROS
// ======================================================
                
                $fecha_inicio = mysqli_real_escape_string($conexion, $_GET['fecha_inicio'] ?? '');
                $fecha_fin = mysqli_real_escape_string($conexion, $_GET['fecha_fin'] ?? '');
                $municipio = isset($_GET['municipio']) ? (int) $_GET['municipio'] : 0;
                $procedencia = isset($_GET['procedencia']) ? (int) $_GET['procedencia'] : 0;


                // ======================================================
// 3️⃣ CONSTRUCCIÓN DINÁMICA DEL WHERE
// (Si no hay filtros → cálculo general automático)
// ======================================================
                
                $where = [];

                if (!empty($fecha_inicio)) {
                    $where[] = "DATE(d.fecha_registro) >= '$fecha_inicio'";
                }

                if (!empty($fecha_fin)) {
                    $where[] = "DATE(d.fecha_registro) <= '$fecha_fin'";
                }

                if ($municipio > 0) {
                    $where[] = "(
        d.IdMunicipioVisitado1 = $municipio OR
        d.IdMunicipioVisitado2 = $municipio OR
        d.IdMunicipioVisitado3 = $municipio OR
        d.IdMunicipioVisitado4 = $municipio OR
        d.IdMunicipioVisitado5 = $municipio OR
        d.IdMunicipioVisitado6 = $municipio OR
        d.IdMunicipioVisitado7 = $municipio
    )";
                }

                if ($procedencia > 0) {
                    $where[] = "d.IdProcedencia = $procedencia";
                }

                // Si no hay filtros → WHERE = 1=1 (general)
                $whereSQL = !empty($where) ? implode(" AND ", $where) : "1=1";


                // ======================================================
// 4️⃣ TOTAL TURISTAS FILTRADOS (O GENERAL)
// ======================================================
                
                $sqlTotal = "
SELECT COUNT(DISTINCT d.Id) as total
FROM datosturista d
WHERE $whereSQL
";

                $resTotal = mysqli_query($conexion, $sqlTotal);
                $totalTuristas = mysqli_fetch_assoc($resTotal)['total'] ?? 0;


                // ======================================================
// 5️⃣ DETECTAR CAMPOS DINÁMICOS DE COMPARTIO
// ======================================================
                
                $columnas = [];
                $resColumnas = mysqli_query($conexion, "SHOW COLUMNS FROM Compartio");

                while ($col = mysqli_fetch_assoc($resColumnas)) {
                    $campo = $col['Field'];

                    if ($campo != 'Id' && $campo != 'IdDatosTurista' && $campo != 'Cuales') {
                        $columnas[] = $campo;
                    }
                }

                if (empty($columnas)) {
                    echo "<div class='alert alert-danger'>No se encontraron campos evaluables.</div>";
                    return;
                }


                // ======================================================
// 6️⃣ ARMAR SELECT DINÁMICO
// ======================================================
                
                $selectPartes = [];

                foreach ($columnas as $campo) {
                    $selectPartes[] = "SUM(IFNULL(c.`$campo`,0)) AS `$campo`";
                }

                $selectSQL = implode(", ", $selectPartes);


                // ======================================================
// 7️⃣ CONSULTA PRINCIPAL
// ======================================================
                
                $sql = "
SELECT $selectSQL
FROM Compartio c
INNER JOIN datosturista d ON d.Id = c.IdDatosTurista
WHERE $whereSQL
";

                $result = mysqli_query($conexion, $sql);

                if (!$result) {
                    die("Error en consulta: " . mysqli_error($conexion));
                }

                $data = mysqli_fetch_assoc($result);


                // ======================================================
// 8️⃣ FORMATEAR RESULTADOS
// ======================================================
                
                $categoriasTabla = [];
                $categoriasGrafica = [];

                $nombresPersonalizados = [
                    'Facebook' => 'Facebook',
                    'Instagram' => 'Instagram',
                    'Twitter' => 'Twitter / X',
                    'Youtube' => 'YouTube',
                    'TikTok' => 'TikTok',
                    'Pinterest' => 'Pinterest',
                    'Mensajeria' => 'Mensajería (WhatsApp u otras)',
                    'NoCompartio' => 'No compartió su experiencia',
                    'Otras' => 'Otras plataformas'
                ];

                foreach ($columnas as $campo) {

                    $valor = (int) ($data[$campo] ?? 0);

                    if ($valor > 0) {

                        $nombreBonito = $nombresPersonalizados[$campo]
                            ?? ucwords(str_replace("_", " ", $campo));

                        $categoriasTabla[$nombreBonito] = $valor;

                        if ($campo !== 'NoCompartio') {
                            $categoriasGrafica[$nombreBonito] = $valor;
                        }
                    }
                }

                arsort($categoriasTabla);
                arsort($categoriasGrafica);


                // ======================================================
// 9️⃣ CÁLCULO GENERAL DE INTERACCIONES
// ======================================================
                
                $totalRespuestas = array_sum($categoriasGrafica);

                $labelsGrafica = array_keys($categoriasGrafica);
                $valoresCantidad = array_values($categoriasGrafica);

                $valoresPorcentaje = [];

                foreach ($valoresCantidad as $valor) {
                    $valoresPorcentaje[] = $totalRespuestas > 0
                        ? round(($valor / $totalRespuestas) * 100, 2)
                        : 0;
                }
                ?>
                <?php
                // Detectar si hay filtros activos
                $filtrosActivos = !empty($_GET['fecha_inicio'])
                    || !empty($_GET['fecha_fin'])
                    || !empty($_GET['municipio'])
                    || !empty($_GET['procedencia']);
                ?>

                <form method="GET" id="formFiltros" class="card shadow-sm mb-4 border-0">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">Filtros de Análisis</h5>

                            <?php if (!$filtrosActivos): ?>
                                <span class="badge bg-success">
                                    Reporte General
                                </span>
                            <?php else: ?>
                                <span class="badge bg-primary">
                                    Reporte Filtrado
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="row g-3">

                            <!-- Fecha Inicio -->
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control filtro-auto"
                                    value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
                            </div>

                            <!-- Fecha Fin -->
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Fecha Fin</label>
                                <input type="date" name="fecha_fin" class="form-control filtro-auto"
                                    value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
                            </div>

                            <!-- Municipio -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Municipio Visitado</label>
                                <select name="municipio" class="form-select filtro-auto">
                                    <option value="">Todos los municipios</option>
                                    <?php foreach ($listaMunicipios as $mun): ?>
                                        <option value="<?= $mun['Id'] ?>" <?= (($_GET['municipio'] ?? '') == $mun['Id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($mun['NombreMunicipio']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Procedencia -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Ciudad de Procedencia</label>
                                <select name="procedencia" class="form-select filtro-auto">
                                    <option value="">Todas las ciudades</option>
                                    <?php foreach ($listaProcedencias as $proc): ?>
                                        <option value="<?= $proc['Id'] ?>" <?= (($_GET['procedencia'] ?? '') == $proc['Id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($proc['Ciudad']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                            <div class="col-md-2">
                                <label class="form-label invisible">Limpiar</label>
                                <a href="?" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="fa fa-eraser"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                </form>

                <!-- SCRIPT AUTO SUBMIT -->
                <script>
                    document.querySelectorAll('.filtro-auto').forEach(function (element) {
                        element.addEventListener('change', function () {
                            document.getElementById('formFiltros').submit();
                        });
                    });
                </script>
                <!-- KPI -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="alert alert-info mb-2">
                            Total Turistas Filtrados:
                            <strong><?= number_format($totalTuristas) ?></strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-success mb-2">
                            Total Interacciones Digitales:
                            <strong><?= number_format($totalRespuestas) ?></strong>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <!-- TABLA -->
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered table-hover text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Medio de Compartición</th>
                                        <th>Cantidad</th>
                                        <th>% sobre Interacciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categoriasTabla as $medio => $cantidad):
                                        $porcentaje = $totalRespuestas > 0
                                            ? round(($cantidad / $totalRespuestas) * 100, 2)
                                            : 0;
                                        ?>
                                        <tr>
                                            <td class="text-start fw-semibold"><?= $medio ?></td>
                                            <td><?= number_format($cantidad) ?></td>
                                            <td><?= $porcentaje ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <?php if (empty($categoriasTabla)): ?>
                                        <tr>
                                            <td colspan="3">No hay datos con los filtros seleccionados</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- SELECTORES -->
                        <div class="mb-3 text-end">
                            <select id="selectorDatoCompartio" class="form-select w-auto d-inline">
                                <option value="cantidad">Cantidad</option>
                                <option value="porcentaje">Porcentaje</option>
                            </select>

                            <select id="selectorGraficaCompartio" class="form-select w-auto d-inline ms-2">
                                <option value="bar">Barras</option>
                                <option value="pie">Pastel</option>
                                <option value="doughnut">Dona</option>
                                <option value="radar">Radar</option>
                                <option value="polarArea">Polar</option>
                            </select>
                        </div>

                        <div style="height:400px;">
                            <canvas id="graficaComoCompartio"></canvas>
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
        const labelsC = <?= json_encode($labelsGrafica) ?>;
        const datosCantidadC = <?= json_encode($valoresCantidad) ?>;
        const datosPorcentajeC = <?= json_encode($valoresPorcentaje) ?>;

        let graficaC = null;
        let datosActualesC = datosCantidadC;

        function crearGraficaC(tipo) {

            if (graficaC !== null) {
                graficaC.destroy();
            }

            const ctx = document.getElementById('graficaComoCompartio').getContext('2d');

            graficaC = new Chart(ctx, {
                type: tipo,
                data: {
                    labels: labelsC,
                    datasets: [{
                        label: document.getElementById('selectorDatoCompartio').value === 'cantidad'
                            ? 'Cantidad de Interacciones'
                            : 'Porcentaje (%)',
                        data: datosActualesC,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        crearGraficaC('bar');

        document.getElementById('selectorGraficaCompartio').addEventListener('change', function () {
            crearGraficaC(this.value);
        });

        document.getElementById('selectorDatoCompartio').addEventListener('change', function () {
            datosActualesC = this.value === 'cantidad'
                ? datosCantidadC
                : datosPorcentajeC;

            crearGraficaC(document.getElementById('selectorGraficaCompartio').value);
        });
    </script>

</body>

</html>