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
            <div class="card-body border-start border-4 border-primary">

                <div class="d-flex align-items-center">

                    <div class="me-3">
                        <i class="fa fa-chart-bar fa-2x text-primary"></i>
                    </div>

                    <div>
                        <h4 class="mb-1 fw-bold text-dark">
                            Reporte de Motivos de Visita Turística
                        </h4>

                        <p class="mb-0 text-muted">
                            Muestra las principales razones por las cuales los turistas visitan el destino,
                            permitiendo identificar tendencias de interés y comportamiento del visitante.
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
                $anio = $_GET['anio'] ?? '';
                $municipio = $_GET['municipio'] ?? '';
                $procedencia = $_GET['procedencia'] ?? '';
                $tipoGrafica = $_GET['tipo'] ?? 'bar';

                // Tunja por defecto
                if (!isset($_GET['municipio'])) {
                    $municipio = 114;
                }

                // ===============================
// WHERE DINÁMICO
// ===============================
                $where = [];
                $where[] = "1=1";

                if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                    $fecha_inicio = mysqli_real_escape_string($conexion, $fecha_inicio);
                    $fecha_fin = mysqli_real_escape_string($conexion, $fecha_fin);
                    $where[] = "d.fecha_registro BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                }

                if (!empty($anio) && is_numeric($anio)) {
                    $where[] = "YEAR(d.fecha_registro) = " . (int) $anio;
                }

                if (!empty($municipio) && is_numeric($municipio)) {

                    $municipio = (int) $municipio;

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

                if (!empty($procedencia) && is_numeric($procedencia)) {
                    $where[] = "d.IdProcedencia = " . (int) $procedencia;
                }

                $whereSQL = implode(" AND ", $where);

                // ===============================
// CONSULTA DE MOTIVOS
// ===============================
                $sql = "
SELECT 
    SUM(m.EspectaculosArtisticos) AS Espectaculos,
    SUM(m.MusicaCineDanzas) AS Musica,
    SUM(m.FeriayFiestas) AS Ferias,
    SUM(m.Cultura) AS Cultura,
    SUM(m.ParquesTematicos) AS ParquesTematicos,
    SUM(m.ParquesNaturales) AS ParquesNaturales,
    SUM(m.CallesyParques) AS CallesParques,
    SUM(m.Compras) AS Compras,
    SUM(m.Religion) AS Religion,
    SUM(m.Familiares) AS Familiares,
    SUM(m.ExcursionoViaje) AS Excursion,
    SUM(m.Otros) AS Otros
FROM motivos m
INNER JOIN datosturista d ON d.Id = m.IdDatosTurista
WHERE $whereSQL
";

                $resultado = mysqli_query($conexion, $sql);

                if (!$resultado) {
                    die("Error en consulta motivos: " . mysqli_error($conexion));
                }

                $fila = mysqli_fetch_assoc($resultado);

                // ===============================
// ARMAR DATOS PARA GRÁFICA
// ===============================
                $labels = [
                    "Espectáculos",
                    "Música/Cine/Danzas",
                    "Ferias y Fiestas",
                    "Cultura",
                    "Parques Temáticos",
                    "Parques Naturales",
                    "Calles y Parques",
                    "Compras",
                    "Religión",
                    "Visita Familiares",
                    "Excursión/Viaje",
                    "Otros"
                ];

                $data = [
                    (int) $fila['Espectaculos'],
                    (int) $fila['Musica'],
                    (int) $fila['Ferias'],
                    (int) $fila['Cultura'],
                    (int) $fila['ParquesTematicos'],
                    (int) $fila['ParquesNaturales'],
                    (int) $fila['CallesParques'],
                    (int) $fila['Compras'],
                    (int) $fila['Religion'],
                    (int) $fila['Familiares'],
                    (int) $fila['Excursion'],
                    (int) $fila['Otros']
                ];
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
                                $mun = mysqli_query($conexion, "
                    SELECT * FROM municipios 
                    ORDER BY NombreMunicipio
                ");

                                while ($m = mysqli_fetch_assoc($mun)) {

                                    // Tunja por defecto si no hay GET
                                    if (!isset($_GET['municipio']) && $m['Id'] == 114) {
                                        $selected = 'selected';
                                    } else {
                                        $selected = ($municipio == $m['Id']) ? 'selected' : '';
                                    }

                                    echo "<option value='{$m['Id']}' $selected>
                            {$m['NombreMunicipio']}
                          </option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Procedencia -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Procedencia</label>
                            <select name="procedencia" class="form-select form-select-sm" onchange="this.form.submit()">

                                <option value="">Todas</option>

                                <?php
                                $proc = mysqli_query($conexion, "
                    SELECT * FROM procedencia 
                    ORDER BY Ciudad
                ");

                                while ($p = mysqli_fetch_assoc($proc)) {
                                    $selected = ($procedencia == $p['Id']) ? 'selected' : '';
                                    echo "<option value='{$p['Id']}' $selected>
                            {$p['Ciudad']}
                          </option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Tipo de Gráfica -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Tipo</label>
                            <select name="tipo" class="form-select form-select-sm" onchange="this.form.submit()">

                                <option value="bar" <?= $tipoGrafica == 'bar' ? 'selected' : '' ?>>
                                    Barras
                                </option>

                                <option value="pie" <?= $tipoGrafica == 'pie' ? 'selected' : '' ?>>
                                    Circular
                                </option>

                                <option value="doughnut" <?= $tipoGrafica == 'doughnut' ? 'selected' : '' ?>>
                                    Dona
                                </option>

                                <option value="line" <?= $tipoGrafica == 'line' ? 'selected' : '' ?>>
                                    Línea
                                </option>

                            </select>
                        </div>

                        <!-- Limpiar -->
                        <div class="col-md-1">
                            <label class="form-label invisible">Limpiar</label>
                            <a href="?" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fa fa-eraser"></i>
                            </a>
                        </div>

                    </div>
                </form>

                <div class="mt-4">
                    <div style="height: 400px;">
                        <canvas id="graficaMotivos"></canvas>
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
        const ctx = document.getElementById('graficaMotivos');

        new Chart(ctx, {
            type: '<?= $tipoGrafica ?>',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Cantidad de turistas',
                    data: <?= json_encode($data) ?>,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>