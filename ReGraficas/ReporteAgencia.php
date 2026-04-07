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
            <div class="card-body border-start border-4 border-warning">

                <div class="d-flex align-items-center">

                    <div class="me-3">
                        <i class="fa fa-building fa-2x text-warning"></i>
                    </div>

                    <div>
                        <h4 class="mb-1 fw-bold text-dark">
                            Reporte de Agencias de Viaje
                        </h4>

                        <p class="mb-0 text-muted">
                            Visualización estadística sobre la utilización de agencias de viaje por parte
                            de los turistas. Este reporte permite identificar el nivel de intermediación
                            en la planificación del viaje, analizar el comportamiento del visitante y
                            apoyar la toma de decisiones estratégicas para fortalecer la articulación
                            entre el destino turístico y los operadores formales.
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

                $tiposPermitidos = ['bar', 'pie', 'doughnut', 'line'];
                $tipoGrafica = in_array($tipoGrafica, $tiposPermitidos) ? $tipoGrafica : 'bar';


                // ==============================
// CONSULTA BASE
// ==============================
                
                $sql = "SELECT 
            g.NombreAgencia,
            COUNT(*) AS TotalUso,
            SUM(g.CantidadNochesAgencia) AS TotalNoches
        FROM gastos g
        INNER JOIN datosturista d ON g.IdDatosTurista = d.Id
        WHERE UPPER(g.UsoAgencia) = 'SI'
        AND g.NombreAgencia IS NOT NULL
        AND g.NombreAgencia <> ''";


                // ==============================
// FILTROS DINÁMICOS
// ==============================
                
                if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                    $sql .= " AND d.fecha_registro >= '$fecha_inicio 00:00:00' 
              AND d.fecha_registro <= '$fecha_fin 23:59:59'";
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


                // ==============================
// AGRUPACIÓN Y ORDEN
// ==============================
                
                $sql .= " GROUP BY g.NombreAgencia
          ORDER BY TotalUso DESC";


                // ==============================
// EJECUCIÓN
// ==============================
                
                $resultado = mysqli_query($conexion, $sql);

                $agencias = [];
                $totalGeneral = 0;

                while ($row = mysqli_fetch_assoc($resultado)) {
                    $nombre = $row['NombreAgencia'];
                    $agencias[$nombre] = $row['TotalUso'];
                    $totalGeneral += $row['TotalUso'];
                }


                // ==============================
// MENSAJE SI NO HAY DATOS
// ==============================
                
                if (empty($agencias)) {
                    echo "<div class='alert alert-warning'>
            No hay registros de uso de agencia con los filtros seleccionados.
          </div>";
                }
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
                        <a href="ReporteAgencia.php" class="btn btn-secondary w-100">
                            Limpiar Filtros
                        </a>
                    </div>

                </form>


                <div class="row">
                    <div class="col-md-6">
                        <!-- ============================== -->
                        <!-- TABLA -->
                        <!-- ============================== -->

                        <table class="table table-bordered table-striped text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Agencia</th>
                                    <th>Usos</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($agencias as $nombre => $cantidad):
                                    $porcentaje = ($totalGeneral > 0) ? round(($cantidad / $totalGeneral) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td><?= $nombre ?></td>
                                        <td><?= $cantidad ?></td>
                                        <td><?= $porcentaje ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="table-primary fw-bold">
                                    <td>Total</td>
                                    <td><?= $totalGeneral ?></td>
                                    <td>100%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <!-- ============================== -->
                        <!-- GRÁFICA -->
                        <!-- ============================== -->

                        <div class="d-flex justify-content-center">
                            <div style="width: 450px; height: 350px;">
                                <canvas id="graficaAgencias"></canvas>
                            </div>
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

        new Chart(document.getElementById('graficaAgencias'), {
            type: '<?= $tipoGrafica ?>',
            data: {
                labels: <?= json_encode(array_keys($agencias)) ?>,
                datasets: [{
                    label: 'Uso de Agencia',
                    data: <?= json_encode(array_values($agencias)) ?>,
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545',
                        '#6f42c1', '#20c997', '#fd7e14', '#17a2b8'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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



</body>

</html>