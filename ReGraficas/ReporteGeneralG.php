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
                        <i class="fa fa-chart-line fa-2x text-success"></i>
                    </div>

                    <div>
                        <h4 class="mb-1 fw-bold text-dark">
                            Reporte General de Gastos Turísticos
                        </h4>

                        <p class="mb-0 text-muted">
                            Visualización estadística del comportamiento de gasto de los turistas
                            registrados en el sistema. Permite analizar el impacto económico por
                            categoría (alojamiento, alimentación, actividades, artesanías y restaurantes),
                            identificar los sectores con mayor participación financiera y apoyar la
                            toma de decisiones estratégicas para el fortalecimiento del desarrollo
                            turístico del destino.
                        </p>
                    </div>

                </div>

            </div>
        </div>

        <div class="card">
            <h5 class="card-header Fuente-cajas">Reportes</h5>
            <div class="card-body">

                <?php
                // =============================
// 1️⃣ OBTENER RANGO AUTOMÁTICO
// =============================
                $rangoQuery = mysqli_query($conexion, "
    SELECT MIN(fecha_registro) as min_fecha,
           MAX(fecha_registro) as max_fecha
    FROM datosturista
");
                $rango = mysqli_fetch_assoc($rangoQuery);

                // =============================
// 2️⃣ CAPTURA DE FILTROS
// =============================
                $fecha_inicio = $_GET['fecha_inicio'] ?? $rango['min_fecha'];
                $fecha_fin = $_GET['fecha_fin'] ?? $rango['max_fecha'];
                $municipio = $_GET['municipio'] ?? '';
                $procedencia = $_GET['procedencia'] ?? '';
                $tipoGrafica = $_GET['tipo'] ?? 'bar';

                // =============================
// 3️⃣ CONSTRUCCIÓN WHERE
// =============================
                $where = [];

                $where[] = "DATE(d.fecha_registro) BETWEEN '$fecha_inicio' AND '$fecha_fin'";

                if (!empty($municipio)) {
                    $municipio = intval($municipio);
                    $where[] = "d.IdMunicipioVisitado1 = $municipio";
                }

                if (!empty($procedencia)) {
                    $procedencia = intval($procedencia);
                    $where[] = "d.IdProcedencia = $procedencia";
                }

                $whereSQL = "WHERE " . implode(" AND ", $where);

                // =============================
// 4️⃣ CONSULTA PRINCIPAL
// =============================
                $sql = "
SELECT 
    COUNT(DISTINCT d.Id) as TotalTuristas,

    SUM(g.CostoRestaurante) as Restaurante,
    SUM(g.CostoActividadesRyC) as Actividades,
    SUM(g.CostoAlimentoyBebidas) as Alimentos,
    SUM(g.CostoArtesanias) as Artesanias,
    SUM(g.CostoAlojamiento) as Alojamiento,

    COUNT(CASE WHEN g.CostoRestaurante > 0 THEN 1 END) as CantRestaurante,
    COUNT(CASE WHEN g.CostoActividadesRyC > 0 THEN 1 END) as CantActividades,
    COUNT(CASE WHEN g.CostoAlimentoyBebidas > 0 THEN 1 END) as CantAlimentos,
    COUNT(CASE WHEN g.CostoArtesanias > 0 THEN 1 END) as CantArtesanias,
    COUNT(CASE WHEN g.CostoAlojamiento > 0 THEN 1 END) as CantAlojamiento

FROM gastos g
INNER JOIN datosturista d ON g.IdDatosTurista = d.Id
$whereSQL
";

                $result = mysqli_query($conexion, $sql);
                $datos = mysqli_fetch_assoc($result);

                // Evitar valores NULL
                foreach ($datos as $k => $v) {
                    $datos[$k] = $v ?? 0;
                }

                // =============================
// 5️⃣ CÁLCULOS ADICIONALES
// =============================
                $totalGeneral =
                    $datos['Restaurante'] +
                    $datos['Actividades'] +
                    $datos['Alimentos'] +
                    $datos['Artesanias'] +
                    $datos['Alojamiento'];

                $promedioGeneral = ($datos['TotalTuristas'] > 0)
                    ? $totalGeneral / $datos['TotalTuristas']
                    : 0;

                // Detectar categoría con mayor gasto
                $mayorCategoria = max(
                    $datos['Restaurante'],
                    $datos['Actividades'],
                    $datos['Alimentos'],
                    $datos['Artesanias'],
                    $datos['Alojamiento']
                );

                // =============================
// 6️⃣ CARGAR FILTROS DINÁMICOS
// =============================
                $municipios = mysqli_query(
                    $conexion,
                    "SELECT Id, NombreMunicipio FROM Municipios ORDER BY NombreMunicipio"
                );

                $procedencias = mysqli_query(
                    $conexion,
                    "SELECT Id, Ciudad FROM Procedencia ORDER BY Ciudad"
                );
                ?>

                <!-- ============================= -->
                <!-- FORMULARIO FILTROS -->
                <!-- ============================= -->

                <form method="GET" id="formFiltros" class="row g-3 mb-4">

                    <div class="col-md-2">
                        <label>Desde</label>
                        <input type="date" name="fecha_inicio" value="<?= $fecha_inicio ?>"
                            min="<?= $rango['min_fecha'] ?>" max="<?= $rango['max_fecha'] ?>"
                            class="form-control filtro-auto">
                    </div>

                    <div class="col-md-2">
                        <label>Hasta</label>
                        <input type="date" name="fecha_fin" value="<?= $fecha_fin ?>" min="<?= $rango['min_fecha'] ?>"
                            max="<?= $rango['max_fecha'] ?>" class="form-control filtro-auto">
                    </div>

                    <div class="col-md-3">
                        <label>Municipio</label>
                        <select name="municipio" class="form-select filtro-auto">
                            <option value="">Todos</option>
                            <?php while ($m = mysqli_fetch_assoc($municipios)): ?>
                                <option value="<?= $m['Id'] ?>" <?= ($municipio == $m['Id']) ? 'selected' : '' ?>>
                                    <?= $m['NombreMunicipio'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Procedencia</label>
                        <select name="procedencia" class="form-select filtro-auto">
                            <option value="">Todas</option>
                            <?php while ($p = mysqli_fetch_assoc($procedencias)): ?>
                                <option value="<?= $p['Id'] ?>" <?= ($procedencia == $p['Id']) ? 'selected' : '' ?>>
                                    <?= $p['Ciudad'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="<?= strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-secondary w-100">
                            <i class="fa fa-eraser"></i>
                        </a>
                    </div>

                </form>

                <script>
                    document.querySelectorAll('.filtro-auto').forEach(f => {
                        f.addEventListener('change', () => {
                            document.getElementById('formFiltros').submit();
                        });
                    });
                </script>

                <!-- ============================= -->
                <!-- KPI SUPERIORES -->
                <!-- ============================= -->

                <div class="row text-center mb-4">

                    <div class="col-md-4">
                        <div class="card bg-primary text-white shadow">
                            <div class="card-body">
                                <h6>Total Turistas</h6>
                                <h3><?= number_format($datos['TotalTuristas']) ?></h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-success text-white shadow">
                            <div class="card-body">
                                <h6>Total General Gastado</h6>
                                <h3>$<?= number_format($totalGeneral, 0, ',', '.') ?></h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-dark text-white shadow">
                            <div class="card-body">
                                <h6>Promedio por Turista</h6>
                                <h3>$<?= number_format($promedioGeneral, 0, ',', '.') ?></h3>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ============================= -->
                <!-- TABLA PROFESIONAL -->
                <!-- ============================= -->

                <div class="table-responsive mb-4">
                    <table class="table table-hover table-bordered align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Categoría</th>
                                <th>Total Gastado</th>
                                <th>% del Total</th>
                                <th>Turistas que Gastaron</th>
                                <th>Promedio por Comprador</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            // Calcular el mayor valor antes del foreach
                            $mayorValor = max(
                                $datos['Restaurante'],
                                $datos['Actividades'],
                                $datos['Alimentos'],
                                $datos['Artesanias'],
                                $datos['Alojamiento']
                            );

                            $categorias = [
                                "Restaurante" => [$datos['Restaurante'], $datos['CantRestaurante']],
                                "Actividades Recreativas" => [$datos['Actividades'], $datos['CantActividades']],
                                "Alimentos y Bebidas" => [$datos['Alimentos'], $datos['CantAlimentos']],
                                "Artesanías" => [$datos['Artesanias'], $datos['CantArtesanias']],
                                "Alojamiento" => [$datos['Alojamiento'], $datos['CantAlojamiento']]
                            ];

                            foreach ($categorias as $nombre => $info):

                                $valor = $info[0];
                                $cantidad = $info[1];

                                $porcentaje = $totalGeneral > 0 ? ($valor / $totalGeneral) * 100 : 0;
                                $promedioReal = $cantidad > 0 ? $valor / $cantidad : 0;

                                $resaltar = ($valor == $mayorValor && $mayorValor > 0)
                                    ? "table-success fw-bold"
                                    : "";
                                ?>

                                <tr class="<?= $resaltar ?>">
                                    <td><?= $nombre ?></td>
                                    <td>$<?= number_format($valor, 0, ',', '.') ?></td>
                                    <td><?= number_format($porcentaje, 2) ?>%</td>
                                    <td><?= number_format($cantidad) ?></td>
                                    <td>$<?= number_format($promedioReal, 0, ',', '.') ?></td>
                                </tr>

                            <?php endforeach; ?>

                            <tr class="table-dark fw-bold">
                                <td>TOTAL GENERAL</td>
                                <td>$<?= number_format($totalGeneral, 0, ',', '.') ?></td>
                                <td>100%</td>
                                <td><?= number_format($datos['TotalTuristas']) ?></td>
                                <td>$<?= number_format($promedioGeneral, 0, ',', '.') ?></td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- ============================= -->
                <!-- GRÁFICA -->
                <!-- ============================= -->

                <div class="mb-3 text-end">
                    <select id="selectorGrafica" class="form-select w-auto d-inline">
                        <option value="bar">Barras</option>
                        <option value="line">Línea</option>
                        <option value="pie">Pastel</option>
                        <option value="doughnut">Dona</option>
                        <option value="radar">Radar</option>
                    </select>
                </div>

                <div style="height:400px;">
                    <canvas id="graficaGastos"></canvas>
                </div>


                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                <script>
                    <?php
                    $labelsGrafica = [];
                    $valoresGrafica = [];

                    foreach ($categorias as $nombre => $datos) {
                        $labelsGrafica[] = $nombre;
                        $valoresGrafica[] = (float) $datos[0]; // 👈 total dinero
                    }
                    ?>

                    const labels = <?= json_encode($labelsGrafica) ?>;
                    const dataValores = <?= json_encode($valoresGrafica) ?>;

                    let grafica = null;

                    function crearGrafica(tipo) {

                        const contenedor = document.getElementById('graficaGastos').parentNode;

                        // 🔥 eliminar canvas anterior completamente
                        contenedor.innerHTML = '<canvas id="graficaGastos"></canvas>';

                        const nuevoCanvas = document.getElementById('graficaGastos');
                        const ctx = nuevoCanvas.getContext('2d');

                        grafica = new Chart(ctx, {
                            type: tipo,
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Total Gastado',
                                    data: dataValores
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    }

                    // Inicial
                    crearGrafica('bar');

                    // Cambio de tipo
                    document.getElementById('selectorGrafica').addEventListener('change', function () {
                        crearGrafica(this.value);
                    });
                </script>





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

</html>