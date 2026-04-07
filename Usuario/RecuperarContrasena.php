<?php
session_start();
require_once('../Conexion/conexion.php');

$email = $_POST['search1'] ?? '';
$telefono = $_POST['search2'] ?? '';

$usuarioVerificado = false;
$idUsuario = "";

/* ================================
   VERIFICAR USUARIO
================================ */

if (isset($_POST['btn2'])) {

	$resultados = mysqli_query(
		$conexion,
		"SELECT * FROM usuario WHERE Email='$email' AND Telefono='$telefono'"
	);

	if (mysqli_num_rows($resultados) > 0) {

		$consulta = mysqli_fetch_assoc($resultados);
		$usuarioVerificado = true;
		$idUsuario = $consulta['Id'];
	}
}


/* ================================
   ACTUALIZAR CONTRASEÑA
================================ */

if (isset($_POST['btnActualizar'])) {

	$idUsuario = $_POST['idusuariocon'];
	$password = $_POST['txtpwd'];

	$passwordHash = password_hash($password, PASSWORD_DEFAULT);

	$update = mysqli_query(
		$conexion,
		"UPDATE usuario SET Contrasena='$passwordHash' WHERE Id='$idUsuario'"
	);

	if ($update) {

		echo "<script>
        alert('Contraseña actualizada correctamente');
        window.location='InicioSesion.php';
        </script>";

		exit();
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport"
		content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title> M.Gobernanza - Inicio de sesión </title>
	<!-- BOOTSTRAP PRIMERO -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- GOOGLE FONTS -->
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
		rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
	<!-- TU CSS DESPUÉS -->
	<link rel="stylesheet" href="../Css/StyleLogin.css?v=<?php echo time(); ?>">
	<style>
		body {
			background: #f5f5f5;
			font-family: 'Poppins', sans-serif;
		}

		.recuperar-wrapper {
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.recuperar-card {
			width: 100%;
			max-width: 1000px;
			border-radius: 20px;
			overflow: hidden;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
		}

		/* PANEL IZQUIERDO */

		.panel-info {
			background: #F6990F;
			color: white;
			padding: 60px 40px;
		}

		.panel-info h2 {
			font-weight: 700;
			margin-bottom: 20px;
		}

		.panel-info ul {
			padding-left: 18px;
		}

		.panel-info li {
			margin-bottom: 10px;
		}

		/* PANEL DERECHO */

		.panel-form {
			background: white;
			padding: 50px 40px;
		}

		.panel-form h3 {
			font-weight: 700;
			margin-bottom: 30px;
		}

		/* INPUTS */

		.input-group-text {
			background: #f3f3f3;
		}

		.form-control {
			border-left: 0;
		}

		.form-control:focus {
			box-shadow: none;
			border-color: #F6990F;
		}

		/* BOTONES */

		.btn-verificar {
			background: #990E0B;
			color: white;
		}

		.btn-verificar:hover {
			background: #7c0b08;
		}

		.btn-cambiar {
			background: #F6990F;
		}

		.btn-cambiar:hover {
			background: #d68405;
		}

		/* ICONOS REQUISITOS */

		.req {
			font-size: 14px;
			margin-bottom: 6px;
		}
	</style>
</head>

<body>

	<div class="recuperar-wrapper">

		<div class="card recuperar-card border-0">

			<div class="row g-0">

				<!-- PANEL IZQUIERDO -->

				<div class="col-md-5 panel-info">

					<h2>
						<i class="fas fa-shield-alt me-2"></i>
						Seguridad de la cuenta
					</h2>

					<p>
						Para garantizar la protección de la información dentro del
						<strong>Sistema de Gobernanza Turística</strong>, tu nueva contraseña
						debe cumplir con los siguientes criterios de seguridad:
					</p>

					<hr>

					<ul>

						<li class="req">
							<i class="fas fa-check-circle me-2"></i>
							Tener mínimo <strong>8 caracteres</strong>
						</li>

						<li class="req">
							<i class="fas fa-check-circle me-2"></i>
							Incluir al menos <strong>una letra mayúscula</strong>
						</li>

						<li class="req">
							<i class="fas fa-check-circle me-2"></i>
							Incluir al menos <strong>una letra minúscula</strong>
						</li>

						<li class="req">
							<i class="fas fa-check-circle me-2"></i>
							Contener al menos <strong>un número</strong>
						</li>

						<li class="req">
							<i class="fas fa-check-circle me-2"></i>
							Incluir al menos <strong>un carácter especial</strong>
							(ej: ! @ # $ %)
						</li>

					</ul>

					<hr>

					<p style="font-size:14px;">
						Esta medida permite proteger el acceso al sistema y resguardar
						la información relacionada con la gestión, análisis y toma de
						decisiones dentro del modelo de gobernanza turística.
					</p>

				</div>

				<!-- PANEL DERECHO -->

				<div class="col-md-7 panel-form">

					<h3 class="text-center">
						Recuperar acceso
					</h3>

					<form method="post">

						<div class="mb-3">

							<label class="form-label">
								Correo electrónico
							</label>

							<div class="input-group">

								<span class="input-group-text">
									<i class="fas fa-envelope"></i>
								</span>

								<input type="email" name="search1" class="form-control" placeholder="correo@ejemplo.com"
									value="<?php echo $email ?>" required>

							</div>

						</div>


						<div class="mb-3">

							<label class="form-label">
								Número de teléfono
							</label>

							<div class="input-group">

								<span class="input-group-text">
									<i class="fas fa-phone"></i>
								</span>

								<input type="number" name="search2" class="form-control" placeholder="Número registrado"
									value="<?php echo $telefono ?>" required>

							</div>

						</div>

						<div class="d-grid mb-4">

							<button class="btn btn-verificar" name="btn2">

								<i class="fas fa-user-check me-2"></i>
								Verificar usuario

							</button>

						</div>

					</form>


					<?php if ($usuarioVerificado) { ?>

						<div class="alert alert-success text-center">

							<i class="fas fa-check-circle"></i>
							Usuario verificado correctamente

						</div>

						<form method="post">

							<input type="hidden" name="idusuariocon" value="<?php echo $idUsuario ?>">

							<div class="mb-3">

								<label class="form-label">
									Nueva contraseña
								</label>

								<div class="input-group">

									<span class="input-group-text">
										<i class="fas fa-lock"></i>
									</span>

									<input type="password" name="txtpwd" id="contrasena" class="form-control"
										placeholder="Nueva contraseña" required>

									<button type="button" class="btn btn-outline-secondary" onclick="mostrarContrasena()">

										<i class="fas fa-eye"></i>

									</button>

								</div>

							</div>

							<div class="d-grid">

								<button class="btn btn-cambiar" name="btnActualizar">

									<i class="fas fa-save me-2"></i>
									Cambiar contraseña

								</button>

							</div>

						</form>

					<?php } ?>

				</div>
			</div>
		</div>
	</div>

	<script>

		function mostrarContrasena() {

			let input = document.getElementById("contrasena");

			if (input.type === "password") {
				input.type = "text";
			} else {
				input.type = "password";
			}

		}

	</script>


	<script src="../Js/jquery-3.6.0.min.js"></script>
	<script type="../Js/popper.min.js"></script>
	<script src="../Js/bootstrap.min.js"></script>
</body>

</html>