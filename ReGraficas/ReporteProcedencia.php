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
                        <i class="fa fa-globe-americas fa-2x text-primary"></i>
                    </div>

                    <div>
                        <h4 class="mb-1 fw-bold text-dark">
                            Reporte de Procedencia de Turistas
                        </h4>

                        <p class="mb-0 text-muted">
                            Visualización estadística de la ciudad o lugar de origen de los turistas
                            registrados en el sistema. Permite identificar los principales mercados
                            emisores y apoyar la toma de decisiones en estrategias de promoción y
                            fortalecimiento del destino.
                        </p>
                    </div>

                </div>

            </div>
        </div>


        <div class="card">
            <h5 class="card-header Fuente-cajas">Reportes</h5>
            <div class="card-body">



                <?php
                // ============================
// FILTROS (SOLO AÑO Y MES)
// ============================
                
                $ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? (int) $_GET['ano'] : null;
                $mes = isset($_GET['mes']) && $_GET['mes'] !== '' ? (int) $_GET['mes'] : null;

                // ============================
// WHERE DINÁMICO
// ============================
                
                $where = [];

                if ($ano !== null) {
                    $where[] = "YEAR(d.fecha_registro) = $ano";
                }

                if ($mes !== null) {
                    $where[] = "MONTH(d.fecha_registro) = $mes";
                }

                $whereSQL = "";
                if (!empty($where)) {
                    $whereSQL = "WHERE " . implode(" AND ", $where);
                }

                // ============================
// CONSULTA PROCEDENCIA
// ============================
                
                $sql = "
SELECT p.ciudad, COUNT(*) as total
FROM datosturista d
INNER JOIN procedencia p ON d.IdProcedencia = p.Id
$whereSQL
GROUP BY p.ciudad
ORDER BY total DESC
";

                $resultado = mysqli_query($conexion, $sql);

                $labels = [];
                $data = [];

                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    while ($fila = mysqli_fetch_assoc($resultado)) {
                        $labels[] = $fila['ciudad'];
                        $data[] = $fila['total'];
                    }
                }
                ?>







                <form id="filtrosForm" method="GET" class="row g-3 mb-4">

                    <!-- Año -->
                    <div class="col-md-4">
                        <label>Año</label>
                        <select name="ano" class="form-select filtro-auto">
                            <option value="">Todos</option>
                            <?php
                            $anos = mysqli_query($conexion, "
                SELECT DISTINCT YEAR(fecha_registro) as ano 
                FROM datosturista 
                WHERE fecha_registro IS NOT NULL
                ORDER BY ano DESC
            ");
                            while ($a = mysqli_fetch_assoc($anos)) {
                                $selected = ($ano == $a['ano']) ? 'selected' : '';
                                echo "<option value='{$a['ano']}' $selected>{$a['ano']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Mes -->
                    <div class="col-md-4">
                        <label>Mes</label>
                        <select name="mes" class="form-select filtro-auto">
                            <option value="">Todos</option>
                            <?php
                            $meses = [
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
                            foreach ($meses as $num => $nombre) {
                                $selected = ($mes === $num) ? 'selected' : '';
                                echo "<option value='$num' $selected>$nombre</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Tipo gráfica -->
                    <div class="col-md-3">
                        <label>Tipo Gráfica</label>
                        <select id="tipoGrafica" class="form-select">
                            <option value="bar">Barras</option>
                            <option value="pie">Torta</option>
                            <option value="line">Línea</option>
                            <option value="doughnut">Dona</option>
                        </select>
                    </div>

                    <!-- Limpiar -->
                    <div class="col-md-1 d-flex align-items-end">
                        <a href="ReporteProcedencia.php" class="btn btn-outline-secondary w-100"
                            title="Limpiar filtros">
                            <i class="fa fa-eraser"></i>
                        </a>
                    </div>

                </form>




                <?php if (!empty($labels)) { ?>
                    <div style="width:100%; height:450px;">
                        <canvas id="graficoProcedencia"></canvas>
                    </div>
                <?php } else { ?>
                    <div class="alert alert-warning text-center">
                        No hay datos para los filtros seleccionados.
                    </div>
                <?php } ?>



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

        // ============================
        // FILTROS AUTOMÁTICOS (SIEMPRE ACTIVOS)
        // ============================

        document.querySelectorAll('.filtro-auto').forEach(select => {
            select.addEventListener('change', function () {
                document.getElementById('filtrosForm').submit();
            });
        });

        <?php if (!empty($labels)) { ?>

            // ============================
            // GRÁFICO
            // ============================

            let ctx = document.getElementById('graficoProcedencia').getContext('2d');

            let grafico = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Cantidad de Turistas por Ciudad de Origen',
                        data: <?php echo json_encode($data); ?>,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // ============================
            // CAMBIO TIPO GRÁFICA
            // ============================

            document.getElementById('tipoGrafica').addEventListener('change', function () {

                let nuevoTipo = this.value;

                grafico.destroy();

                grafico = new Chart(ctx, {
                    type: nuevoTipo,
                    data: {
                        labels: <?php echo json_encode($labels); ?>,
                        datasets: [{
                            label: 'Cantidad de Turistas por Ciudad de Origen',
                            data: <?php echo json_encode($data); ?>,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

            });

        <?php } ?>

    </script>

</body>

</html>