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
		<div class="card border-0 shadow-sm rounded-4 mb-4">
			<div class="card-body">










<?php
/* =========================================
   OBTENER ID
========================================= */
$idUsuario = intval($_GET['id'] ?? 0);

if ($idUsuario <= 0) {
    echo '<div class="alert alert-danger">Usuario inválido.</div>';
    return;
}

/* =========================================
   OBTENER DATOS DEL USUARIO
========================================= */
$consulta = mysqli_query($conexion, "
    SELECT * FROM usuario WHERE Id = $idUsuario
");

if (mysqli_num_rows($consulta) == 0) {
    echo '<div class="alert alert-danger">El usuario no existe.</div>';
    return;
}

$usuario = mysqli_fetch_assoc($consulta);

/* =========================================
   VERIFICAR SI YA EXISTE OTRO ADMIN
========================================= */
$existeAdmin = false;

$checkAdmin = mysqli_query($conexion, "
    SELECT u.Id 
    FROM usuario u
    INNER JOIN rol r ON u.IdRol = r.Id
    WHERE r.Nombre = 'Administrador'
    AND u.Id != $idUsuario
    LIMIT 1
");

if (mysqli_num_rows($checkAdmin) > 0) {
    $existeAdmin = true;
}

/* =========================================
   PROCESAR UPDATE
========================================= */
if (isset($_POST['btnActualizar'])) {

    $idRol     = intval($_POST['idRol']);
    $nombres   = mysqli_real_escape_string($conexion, $_POST['nombres']);
    $apellidos = mysqli_real_escape_string($conexion, $_POST['apellidos']);
    $documento = mysqli_real_escape_string($conexion, $_POST['documento']);
    $fecha     = $_POST['fechaNacimiento'];
    $telefono  = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $email     = mysqli_real_escape_string($conexion, $_POST['email']);

    /* ===== VALIDACIONES ===== */

    if (empty($idRol) || empty($nombres) || empty($apellidos) || empty($documento) || empty($email)) {

        echo '<div class="alert alert-danger">Los campos obligatorios son requeridos.</div>';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        echo '<div class="alert alert-warning">Correo electrónico inválido.</div>';

    } else {

        /* ===== VALIDAR ADMIN UNICO ===== */

        $rolConsulta = mysqli_query($conexion, "SELECT Nombre FROM rol WHERE Id = $idRol");
        $rolData = mysqli_fetch_assoc($rolConsulta);
        $nombreRol = $rolData['Nombre'];

        if ($nombreRol == 'Administrador' && $existeAdmin) {

            echo '<div class="alert alert-danger">
                    Ya existe otro Administrador registrado.
                  </div>';

        } else {

            /* ===== VALIDAR DUPLICADOS ===== */

            $verificar = mysqli_query($conexion, "
                SELECT Id FROM usuario 
                WHERE (Email='$email' OR Documento='$documento')
                AND Id != $idUsuario
            ");

            if (mysqli_num_rows($verificar) > 0) {

                echo '<div class="alert alert-danger">
                        Ya existe otro usuario con ese correo o documento.
                      </div>';

            } else {

                /* ===== ACTUALIZAR ===== */

                $update = mysqli_query($conexion, "
                    UPDATE usuario SET
                        IdRol = '$idRol',
                        Nombres = '$nombres',
                        Apellidos = '$apellidos',
                        Documento = '$documento',
                        FechaNacimiento = '$fecha',
                        Telefono = '$telefono',
                        Email = '$email'
                    WHERE Id = $idUsuario
                ");

                if ($update) {

                    echo '<div class="alert alert-success">
                            Usuario actualizado correctamente.
                          </div>';

                    // Recargar datos actualizados
                    $consulta = mysqli_query($conexion, "
                        SELECT * FROM usuario WHERE Id = $idUsuario
                    ");
                    $usuario = mysqli_fetch_assoc($consulta);

                } else {

                    echo '<div class="alert alert-danger">
                            Error al actualizar el usuario.
                          </div>';
                }
            }
        }
    }
}
?>

<!-- =========================================
     FORMULARIO
========================================= -->
<form method="POST">

    <div class="row">

        <!-- ROL -->
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Rol *</label>

            <select name="idRol" class="form-select" required>
                <option value="">Seleccione...</option>

                <?php
                $roles = mysqli_query($conexion, "SELECT * FROM rol");

                while ($r = mysqli_fetch_assoc($roles)) {

                    if ($existeAdmin 
                        && $r['Nombre'] == 'Administrador' 
                        && $usuario['IdRol'] != $r['Id']) {
                        continue;
                    }

                    $selected = ($usuario['IdRol'] == $r['Id']) ? 'selected' : '';
                    echo "<option value='{$r['Id']}' $selected>{$r['Nombre']}</option>";
                }
                ?>
            </select>
        </div>

        <!-- DOCUMENTO -->
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Documento *</label>
            <input type="text"
                   name="documento"
                   class="form-control"
                   value="<?= $usuario['Documento']; ?>"
                   pattern="[0-9]{6,15}"
                   required>
        </div>

        <!-- NOMBRES -->
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Nombres *</label>
            <input type="text"
                   name="nombres"
                   class="form-control"
                   value="<?= $usuario['Nombres']; ?>"
                   pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,50}"
                   required>
        </div>

        <!-- APELLIDOS -->
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Apellidos *</label>
            <input type="text"
                   name="apellidos"
                   class="form-control"
                   value="<?= $usuario['Apellidos']; ?>"
                   pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,50}"
                   required>
        </div>

        <!-- FECHA -->
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Fecha de Nacimiento</label>
            <input type="date"
                   name="fechaNacimiento"
                   class="form-control"
                   value="<?= $usuario['FechaNacimiento']; ?>"
                   max="<?= date('Y-m-d'); ?>">
        </div>

        <!-- TELEFONO -->
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Teléfono</label>
            <input type="text"
                   name="telefono"
                   class="form-control"
                   value="<?= $usuario['Telefono']; ?>"
                   pattern="[0-9]{7,15}">
        </div>

        <!-- EMAIL -->
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Correo Electrónico *</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   value="<?= $usuario['Email']; ?>"
                   required>
        </div>

    </div>

    <div class="text-end mt-3">
        <button type="submit" name="btnActualizar" class="btn btn-warning px-4">
            Actualizar Usuario
        </button>
    </div>

</form>






			



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

	<script src="../Js/wizard.js"></script>

	<script>
		document.getElementById("formUsuario").addEventListener("submit", function (e) {

			let rolSelect = document.getElementById("idRol");
			let rolTexto = rolSelect.options[rolSelect.selectedIndex]?.text;

	<?php if ($existeAdmin): ?>
			if (rolTexto === "Administrador") {
					e.preventDefault();
					alert("Ya existe un Administrador. No se puede crear otro.");
				}
	<?php endif; ?>

});
	</script>




</body>

</html>