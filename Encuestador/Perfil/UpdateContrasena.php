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
if ($_SESSION['rol'] != 3) {
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
						<a class="nav-link active" href="../DashboardEncuestador.php">
							<i class="fa-solid fa-chart-pie me-1"></i> Dashboard
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="../DatosTuristas/SelectDatosTuristas.php">
							<i class="fa-solid fa-database me-1"></i> Base datos
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
		<div class="card border-0 shadow-sm rounded-4 mb-4">
			<div class="card-body">







				<?php
				$id_usuario = (int) $_SESSION['id'];

				if (isset($_POST['btnCambiar'])) {

					$actual = trim($_POST['actual']);
					$nueva = trim($_POST['nueva']);
					$confirmar = trim($_POST['confirmar']);

					/* =====================================================
					   OBTENER CONTRASEÑA ACTUAL
					===================================================== */
					$sql = "SELECT Contrasena FROM usuario WHERE Id = $id_usuario";
					$resultado = mysqli_query($conexion, $sql);
					$datos = mysqli_fetch_assoc($resultado);

					if (!$datos) {
						echo '<div class="alert alert-danger">Usuario no encontrado.</div>';
						exit;
					}

					$passwordBD = $datos['Contrasena'];
					$passwordCorrecta = false;

					/* =====================================================
					   VERIFICACIÓN SIMPLE Y SEGURA
					===================================================== */

					// 1️⃣ Intentar verificar como hash
					if (password_verify($actual, $passwordBD)) {
						$passwordCorrecta = true;
					}

					// 2️⃣ Si no es hash válido, comparar como texto plano
					elseif ($actual === $passwordBD) {
						$passwordCorrecta = true;
					}

					/* =====================================================
					   VALIDACIONES
					===================================================== */

					if (!$passwordCorrecta) {

						echo '<div class="alert alert-danger alert-dismissible fade show">
                La contraseña actual es incorrecta ❌
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';

					} elseif ($nueva !== $confirmar) {

						echo '<div class="alert alert-warning alert-dismissible fade show">
                Las nuevas contraseñas no coinciden ⚠
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';

					} elseif (strlen($nueva) < 6) {

						echo '<div class="alert alert-warning alert-dismissible fade show">
                La nueva contraseña debe tener mínimo 6 caracteres ⚠
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';

					} else {

						/* =====================================================
						   GUARDAR SIEMPRE COMO HASH
						===================================================== */

						$nuevaHash = password_hash($nueva, PASSWORD_DEFAULT);

						$update = "UPDATE usuario 
                   SET Contrasena = '$nuevaHash'
                   WHERE Id = $id_usuario";

						if (mysqli_query($conexion, $update)) {

							echo '<div class="alert alert-success alert-dismissible fade show">
                    Contraseña actualizada correctamente 🔐✅
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';

						} else {

							echo '<div class="alert alert-danger alert-dismissible fade show">
                    Error al actualizar ❌
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
						}
					}
				}
				?>


				<form method="POST">

					<div class="row justify-content-center">
						<div class="col-md-6">

							<h5 class="fw-bold mb-4 text-center">Cambiar Contraseña</h5>

							<div class="mb-3">
								<label class="form-label">Contraseña Actual</label>
								<input type="password" name="actual" class="form-control" required>
							</div>

							<div class="mb-3">
								<label class="form-label">Nueva Contraseña</label>
								<input type="password" name="nueva" class="form-control" required>
								<div class="form-text">Mínimo 6 caracteres.</div>
							</div>

							<div class="mb-3">
								<label class="form-label">Confirmar Nueva Contraseña</label>
								<input type="password" name="confirmar" class="form-control" required>
							</div>

							<div class="text-end mt-4">
								<button type="submit" name="btnCambiar" class="btn btn-success">
									<i class="bi bi-shield-lock"></i> Actualizar Contraseña
								</button>

								<a href="Perfil.php" class="btn btn-secondary">
									Cancelar
								</a>
							</div>

						</div>
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
                        <a href="../DatosTuristas/SelectDatosTuristas.php">Encuestas</a>
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


</body>

</html>