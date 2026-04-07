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
		<!-- HEADER ACTUALIZAR -->
		<div class="card border-0 shadow-sm rounded-4 mb-4" style="border-left: 6px solid #B65919;">
			<div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3 px-4 py-4">
				<!-- IZQUIERDA -->
				<div class="d-flex align-items-center gap-3">
					<!-- Icono -->
					<div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm"
						style="width:50px;height:50px;">

						<i class="fas fa-user-edit text-danger fs-5"></i>
					</div>
					<div>
						<h4 class="fw-bold mb-1 text-dark">
							Actualizar Registro
						</h4>
						<small class="text-muted">
							Modifica la información del turista seleccionado y guarda los cambios realizados.
						</small>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php

	$id = intval($_GET['actualizar']);

	$sql = "SELECT * FROM datosturista WHERE Id = $id";
	$ejecutar = mysqli_query($conexion, $sql);
	$datos = mysqli_fetch_assoc($ejecutar);

	// 🔹 Traer datos de gastos
	$sqlGastos = "SELECT * FROM gastos WHERE IdDatosTurista = $id";
	$qGastos = mysqli_query($conexion, $sqlGastos);
	$gastos = mysqli_fetch_assoc($qGastos);

	// 🔹 Traer datos de Motivos
	$sqlmotivos = "SELECT * FROM motivos WHERE IdDatosTurista = '$id'";
	$resulmotivos = $conexion->query($sqlmotivos);
	$datosMotivos = $resulmotivos->fetch_assoc() ?? [];

	$sqlCultura = "SELECT * FROM cultura WHERE IdDatosTurista = '$id'";
	$resulCultura = mysqli_query($conexion, $sqlCultura);
	$datosCultura = mysqli_fetch_assoc($resulCultura);

	// COMO SE ENTERO
	$sqlEntero = "SELECT * FROM comoseentero WHERE IdDatosTurista = '$id'";
	$resulEntero = mysqli_query($conexion, $sqlEntero);
	$datosEntero = mysqli_fetch_assoc($resulEntero) ?: [];

	// DONDE BUSCO
	$sqlBusco = "SELECT * FROM buscoinformacion WHERE IdDatosTurista = '$id'";
	$resulBusco = mysqli_query($conexion, $sqlBusco);
	$datosBusco = mysqli_fetch_assoc($resulBusco) ?: [];

	// REDES / COMPARTIO
	$sqlRed = "SELECT * FROM compartio WHERE IdDatosTurista = '$id'";
	$resulRed = mysqli_query($conexion, $sqlRed);
	$datosRed = mysqli_fetch_assoc($resulRed) ?: [];


	if (!$datos) {
		echo "Registro no encontrado";
		exit();
	}
	?>


	<div class="container">
		<div class="card border-0 shadow-sm rounded-4 mb-4">
			<div class="card-body">
				<form id="formEncuesta" action="update_turista.php" method="POST">
					<input type="hidden" name="Id" value="<?php echo $datos['Id']; ?>">

					<!-- ================= PASO 1 DATOS TURISTA ================= -->
					<h5 class="mb-3">1. Datos del turista</h5>

					<div class="row">

						<div class="col-md-3">
							<label>Nombre completo</label>
							<input type="text" name="NombreCompleto" class="form-control"
								value="<?php echo $datos['NombreCompleto']; ?>" required>
						</div>

						<div class="col-md-3">
							<label>Teléfono</label>
							<input type="number" name="Telefono" class="form-control"
								value="<?php echo $datos['Telefono']; ?>" required>
						</div>

						<div class="col-md-3">
							<label>Email</label>
							<input type="email" name="Email" class="form-control"
								value="<?php echo $datos['Email']; ?>">
						</div>

						<div class="col-md-3">
							<label>Tipo de Documento</label>
							<select name="TipoDocumento" class="form-select" required>
								<option value="">Seleccione</option>
								<option value="Cédula" <?php if ($datos['TipoDocumento'] == "Cédula")
									echo "selected"; ?>>
									Cédula</option>
								<option value="Pasaporte" <?php if ($datos['TipoDocumento'] == "Pasaporte")
									echo "selected"; ?>>Pasaporte</option>
								<option value="Tarjeta Identidad" <?php if ($datos['TipoDocumento'] == "Tarjeta Identidad")
									echo "selected"; ?>>Tarjeta Identidad</option>
								<option value="Cédula Extranjería" <?php if ($datos['TipoDocumento'] == "Cédula Extranjería")
									echo "selected"; ?>>Cédula Extranjería</option>
							</select>
						</div>

						<div class="col-md-3">
							<label>Número de Documento</label>
							<input type="number" name="NuIdentificacion" class="form-control"
								value="<?php echo $datos['NuIdentificacion']; ?>" required>
						</div>

						<!-- 🌎 PROCEDENCIA -->
						<div class="col-md-3">
							<label class="fw-semibold">Ciudad de procedencia</label>

							<select name="IdProcedencia" id="procedenciaSelect" class="form-select" required
								style="width:100%">
								<option value="">Buscar ciudad...</option>

								<?php
								$q = mysqli_query($conexion, "
            SELECT procedencia.Id, procedencia.Ciudad, pais.NombrePais 
            FROM procedencia 
            INNER JOIN pais ON pais.Id = procedencia.IdPais
            ORDER BY Ciudad
        ");
								while ($r = mysqli_fetch_array($q)) {

									$selected = ($datos['IdProcedencia'] == $r['Id']) ? "selected" : "";

									echo "<option value='" . $r['Id'] . "' $selected>
            " . $r['Ciudad'] . " - " . $r['NombrePais'] . "
          </option>";
								}

								?>
							</select>
						</div>




					</div>

					<!-- ================= PASO 2 VIAJE ================= -->
					<h5>2. Información del viaje</h5>

					<div class="row">

						<div class="col-md-4">
							<label>Acompañantes</label>
							<select name="TipoAcompanantes" class="form-select" required>
								<option value="Solo" <?php if ($datos['TipoAcompanantes'] == "Solo")
									echo "selected"; ?>>
									Solo</option>
								<option value="Familia" <?php if ($datos['TipoAcompanantes'] == "Familia")
									echo "selected"; ?>>Familia</option>
								<option value="Pareja" <?php if ($datos['TipoAcompanantes'] == "Pareja")
									echo "selected"; ?>>Pareja</option>
								<option value="Amigos" <?php if ($datos['TipoAcompanantes'] == "Amigos")
									echo "selected"; ?>>Amigos</option>
								<option value="Tour" <?php if ($datos['TipoAcompanantes'] == "Tour")
									echo "selected"; ?>>
									Tour</option>
							</select>
						</div>

						<div class="col-md-4">
							<label>Cantidad</label>
							<select name="CantidadAcompanantes" class="form-select" required>
								<option value="0" <?php if ($datos['CantidadAcompanantes'] == "0")
									echo "selected"; ?>>0
								</option>
								<option value="1" <?php if ($datos['CantidadAcompanantes'] == "1")
									echo "selected"; ?>>1
								</option>
								<option value="2" <?php if ($datos['CantidadAcompanantes'] == "2")
									echo "selected"; ?>>2
								</option>
								<option value="3" <?php if ($datos['CantidadAcompanantes'] == "3")
									echo "selected"; ?>>3
								</option>
								<option value="4" <?php if ($datos['CantidadAcompanantes'] == "4")
									echo "selected"; ?>>4
								</option>
								<option value="5+" <?php if ($datos['CantidadAcompanantes'] == "5+")
									echo "selected"; ?>>
									5+
								</option>
							</select>
						</div>

						<div class="col-md-4">
							<label>Transporte</label>
							<select name="TipoTransporte" class="form-select" required>
								<option value="Bus" <?php if ($datos['TipoTransporte'] == "Bus")
									echo "selected"; ?>>Bus
								</option>
								<option value="Carro" <?php if ($datos['TipoTransporte'] == "Carro")
									echo "selected"; ?>>
									Carro</option>
								<option value="Avión" <?php if ($datos['TipoTransporte'] == "Avión")
									echo "selected"; ?>>
									Avión</option>
								<option value="Moto" <?php if ($datos['TipoTransporte'] == "Moto")
									echo "selected"; ?>>
									Moto
								</option>
								<option value="Bicicleta" <?php if ($datos['TipoTransporte'] == "Bicicleta")
									echo "selected"; ?>>Bicicleta</option>
								<option value="Otro" <?php if ($datos['TipoTransporte'] == "Otro")
									echo "selected"; ?>>
									Otro
								</option>
							</select>
						</div>

						<div class="col-md-4 mt-3">
							<label>Frecuencia visita</label>
							<select name="FrecuenciaVisitaAnual" class="form-select" required>
								<option value="Primera vez" <?php if ($datos['FrecuenciaVisitaAnual'] == "Primera vez")
									echo "selected"; ?>>Primera vez</option>
								<option value="1 vez al año" <?php if ($datos['FrecuenciaVisitaAnual'] == "1 vez al año")
									echo "selected"; ?>>1 vez al año</option>
								<option value="2 veces" <?php if ($datos['FrecuenciaVisitaAnual'] == "2 veces")
									echo "selected"; ?>>2 veces</option>
								<option value="3 veces" <?php if ($datos['FrecuenciaVisitaAnual'] == "3 veces")
									echo "selected"; ?>>3 veces</option>
								<option value="4 veces" <?php if ($datos['FrecuenciaVisitaAnual'] == "4 veces")
									echo "selected"; ?>>4 veces</option>
								<option value="5 o más" <?php if ($datos['FrecuenciaVisitaAnual'] == "5 o más")
									echo "selected"; ?>>5 o más</option>
							</select>
						</div>

					</div>




					<!-- ================= PASO 3 MUNICIPIOS ================= -->
					<h5>3. Municipios visitados</h5>

					<div class="row">

						<?php
						for ($i = 1; $i <= 7; $i++) {

							$campo = "IdMunicipioVisitado$i";

							echo "<div class='col-md-4 mt-3'>
            <label>Municipio $i</label>
            <select name='$campo' class='form-select'>
                <option value=''>Seleccione</option>";

							$qm = mysqli_query($conexion, "SELECT * FROM municipios ORDER BY NombreMunicipio");

							while ($m = mysqli_fetch_array($qm)) {

								$selected = ($datos[$campo] == $m['Id']) ? "selected" : "";

								echo "<option value='" . $m['Id'] . "' $selected>
                " . $m['NombreMunicipio'] . "
              </option>";
							}

							echo "  </select>
          </div>";
						}
						?>

					</div>




					<!-- ================= PASO 4 GASTOS ================= -->
					<h5>4. Gastos del turista</h5>

					<div class="row">

						<!-- AGENCIA -->
						<div class="col-md-4">
							<label>¿Usó agencia turística?</label>
							<select name="UsoAgencia" class="form-select">
								<option value="">Seleccione</option>
								<option value="SI" <?php if ($gastos['UsoAgencia'] == "SI")
									echo "selected"; ?>>SI
								</option>
								<option value="NO" <?php if ($gastos['UsoAgencia'] == "NO")
									echo "selected"; ?>>NO
								</option>
							</select>
						</div>

						<div class="col-md-4 mt-3">
							<label>Nombre agencia</label>
							<input type="text" name="NombreAgencia" class="form-control"
								value="<?php echo $gastos['NombreAgencia'] ?? ''; ?>">
						</div>

						<div class="col-md-4 mt-3">
							<label>Noches con agencia</label>
							<input type="number" name="CantidadNochesAgencia" class="form-control"
								value="<?php echo $gastos['CantidadNochesAgencia'] ?? ''; ?>">
						</div>


						<!-- SE QUEDÓ EN LA CIUDAD -->
						<div class="col-md-4 mt-3">
							<label>¿Se quedó en la ciudad?</label>
							<select name="QuedoCiudad" class="form-select">
								<option value="">Seleccione</option>
								<option value="SI" <?php if ($gastos['QuedoCiudad'] == "SI")
									echo "selected"; ?>>SI
								</option>
								<option value="NO" <?php if ($gastos['QuedoCiudad'] == "NO")
									echo "selected"; ?>>NO
								</option>
							</select>
						</div>


						<!-- ALOJAMIENTO -->
						<div class="col-md-4 mt-3">
							<label>Tipo alojamiento</label>
							<select name="UsoAlojamiento" class="form-select">
								<option value="">Seleccione</option>
								<option value="Hotel" <?php if ($gastos['UsoAlojamiento'] == "Hotel")
									echo "selected"; ?>>
									Hotel</option>
								<option value="Hostal" <?php if ($gastos['UsoAlojamiento'] == "Hostal")
									echo "selected"; ?>>Hostal</option>
								<option value="Airbnb" <?php if ($gastos['UsoAlojamiento'] == "Airbnb")
									echo "selected"; ?>>Airbnb</option>
								<option value="Apartamento turístico" <?php if ($gastos['UsoAlojamiento'] == "Apartamento turístico")
									echo "selected"; ?>>Apartamento turístico</option>
								<option value="Finca" <?php if ($gastos['UsoAlojamiento'] == "Finca")
									echo "selected"; ?>>
									Finca</option>
								<option value="Camping" <?php if ($gastos['UsoAlojamiento'] == "Camping")
									echo "selected"; ?>>Camping</option>
								<option value="No usó" <?php if ($gastos['UsoAlojamiento'] == "No usó")
									echo "selected"; ?>>No usó</option>
							</select>
						</div>

						<div class="col-md-4 mt-3">
							<label>Costo alojamiento</label>
							<input type="number" name="CostoAlojamiento" class="form-control"
								value="<?php echo $gastos['CostoAlojamiento'] ?? ''; ?>">
						</div>

						<div class="col-md-4 mt-3">
							<label>Noches alojamiento</label>
							<input type="number" name="CantidadNochesAlojamiento" class="form-control"
								value="<?php echo $gastos['CantidadNochesAlojamiento'] ?? ''; ?>">
						</div>

						<div class="col-md-4 mt-3">
							<label>¿Casa de familiar o conocido?</label>
							<select name="CasaFamiliaroConocido" class="form-select">
								<option value="">Seleccione</option>
								<option value="SI" <?php if ($gastos['CasaFamiliaroConocido'] == "SI")
									echo "selected"; ?>>
									SI</option>
								<option value="NO" <?php if ($gastos['CasaFamiliaroConocido'] == "NO")
									echo "selected"; ?>>
									NO</option>
							</select>
						</div>


						<!-- RESTAURANTE -->
						<div class="col-md-4 mt-3">
							<label>¿Consumió en restaurantes?</label>
							<select name="UsoRestaurante" class="form-select">
								<option value="">Seleccione</option>
								<option value="SI" <?php if ($gastos['UsoRestaurante'] == "SI")
									echo "selected"; ?>>SI
								</option>
								<option value="NO" <?php if ($gastos['UsoRestaurante'] == "NO")
									echo "selected"; ?>>NO
								</option>
							</select>
						</div>

						<div class="col-md-4 mt-3">
							<label>Costo restaurantes</label>
							<input type="number" name="CostoRestaurante" class="form-control"
								value="<?php echo $gastos['CostoRestaurante'] ?? ''; ?>">
						</div>


						<!-- ACTIVIDADES -->
						<div class="col-md-4 mt-3">
							<label>¿Pagó actividades recreativas/culturales?</label>
							<select name="RealizoActividadesRyC" class="form-select">
								<option value="">Seleccione</option>
								<option value="SI" <?php if ($gastos['RealizoActividadesRyC'] == "SI")
									echo "selected"; ?>>
									SI</option>
								<option value="NO" <?php if ($gastos['RealizoActividadesRyC'] == "NO")
									echo "selected"; ?>>
									NO</option>
							</select>
						</div>

						<div class="col-md-4 mt-3">
							<label>Costo actividades</label>
							<input type="number" name="CostoActividadesRyC" class="form-control"
								value="<?php echo $gastos['CostoActividadesRyC'] ?? ''; ?>">
						</div>


						<!-- ALIMENTOS -->
						<div class="col-md-4 mt-3">
							<label>¿Compró alimentos o bebidas?</label>
							<select name="ComproAlimentoyBebidas" class="form-select">
								<option value="">Seleccione</option>
								<option value="SI" <?php if ($gastos['ComproAlimentoyBebidas'] == "SI")
									echo "selected"; ?>>SI</option>
								<option value="NO" <?php if ($gastos['ComproAlimentoyBebidas'] == "NO")
									echo "selected"; ?>>NO</option>
							</select>
						</div>

						<div class="col-md-4 mt-3">
							<label>Costo alimentos/bebidas</label>
							<input type="number" name="CostoAlimentoyBebidas" class="form-control"
								value="<?php echo $gastos['CostoAlimentoyBebidas'] ?? ''; ?>">
						</div>


						<!-- ARTESANIAS -->
						<div class="col-md-4 mt-3">
							<label>¿Compró artesanías?</label>
							<select name="ComproArtesanias" class="form-select">
								<option value="">Seleccione</option>
								<option value="SI" <?php if ($gastos['ComproArtesanias'] == "SI")
									echo "selected"; ?>>SI
								</option>
								<option value="NO" <?php if ($gastos['ComproArtesanias'] == "NO")
									echo "selected"; ?>>NO
								</option>
							</select>
						</div>

						<div class="col-md-4 mt-3">
							<label>Costo artesanías</label>
							<input type="number" name="CostoArtesanias" class="form-control"
								value="<?php echo $gastos['CostoArtesanias'] ?? ''; ?>">
						</div>

					</div>






					<!-- ================= PASO 5 MOTIVOS ================= -->
					<h5 class="mb-4">5. Motivo principal del viaje</h5>

					<div class="alert alert-info py-2">
						Seleccione uno o varios motivos del viaje del turista
					</div>

					<div class="row">

						<?php
						$motivos = [
							"EspectaculosArtisticos" => "🎭 Espectáculos artísticos",
							"MusicaCineDanzas" => "🎶 Música / cine / danza",
							"FeriayFiestas" => "🎉 Ferias y fiestas",
							"Cultura" => "🏛️ Cultura",
							"ParquesTematicos" => "🎢 Parques temáticos",
							"ParquesNaturales" => "🌄 Parques naturales",
							"CallesyParques" => "🌳 Calles y parques",
							"HaciendasCultural" => "🏡 Haciendas culturales",
							"Casino" => "🎰 Casino",
							"Deportes" => "⚽ Deportes",
							"Discotecas" => "🕺 Discotecas",
							"CentrosComerciales" => "🛍️ Centros comerciales",
							"Compras" => "🛒 Compras",
							"Religion" => "⛪ Religión",
							"Inversiones" => "💼 Inversiones",
							"Conferencias" => "🎤 Conferencias",
							"Familiares" => "👨‍👩‍👧 Familiares",
							"Acampar" => "🏕️ Acampar",
							"ExcursionoViaje" => "🚌 Excursión",
							"Ninguno" => "❌ Ninguno"
						];

						foreach ($motivos as $name => $label) {

							$checked = (!empty($datosMotivos[$name]) && $datosMotivos[$name] == 1) ? "checked" : "";

							echo "
    <div class='col-md-3 col-6 mb-3'>
        <div class='card shadow-sm border-0 h-100 motivo-card'>
            <div class='card-body py-3'>
                <div class='form-check'>
                    <input class='form-check-input'
                           type='checkbox'
                           name='motivos[$name]'
                           value='1'
                           $checked>

                    <label class='form-check-label fw-semibold'>$label</label>
                </div>
            </div>
        </div>
    </div>";
						}

						// OTROS
						$checkedOtros = (!empty($datosMotivos['Otros']) && $datosMotivos['Otros'] == 1) || !empty($datosMotivos['Cuales']) ? "checked" : "";
						?>

						<!-- OTROS -->
						<div class="col-md-3 col-6 mb-3">
							<div class="card shadow-sm border-0 h-100 motivo-card">
								<div class="card-body py-3">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="motivos[Otros]" value="1"
											<?php echo $checkedOtros; ?>>

										<label class="form-check-label fw-semibold">Otros</label>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-6 mt-2">
							<label class="fw-semibold">Especifique otro motivo</label>
							<input type="text" name="motivos[Cuales]" class="form-control"
								value="<?php echo $datosMotivos['Cuales'] ?? ''; ?>"
								placeholder="Especifique si aplica">
						</div>

					</div>


					<!-- ================= PASO 6 CULTURA ================= -->
					<h5 class="mb-4">6. Sitios culturales visitados</h5>

					<div class="alert alert-info py-2">
						Seleccione los lugares culturales visitados por el turista durante su estadía
					</div>

					<div class="row">

						<?php
						$cultura = [
							"Catedrales" => "⛪ Catedrales",
							"CasasCultura" => "🏛️ Casas de cultura",
							"MuseosArte" => "🖼️ Museos de arte",
							"MuseosArqueologicos" => "🗿 Museos arqueológicos",
							"HaciendasCultura" => "🏡 Haciendas culturales",
							"Puentes" => "🌉 Puentes",
							"Monumentos" => "🗽 Monumentos",
							"Cementerios" => "🪦 Cementerios patrimoniales",
							"Santuarios" => "🙏 Santuarios",
							"Ninguna" => "❌ Ninguno"
						];

						foreach ($cultura as $name => $label) {

							$checked = (!empty($datosCultura[$name]) && $datosCultura[$name] == 1) ? "checked" : "";

							echo "
    <div class='col-md-3 col-6 mb-3'>
        <div class='card shadow-sm border-0 h-100 cultura-card'>
            <div class='card-body py-3'>
                <div class='form-check'>
                    <input class='form-check-input culturaCheck'
                           type='checkbox'
                           name='cultura[$name]'
                           value='1'
                           $checked>

                    <label class='form-check-label fw-semibold'>$label</label>
                </div>
            </div>
        </div>
    </div>";
						}

						// OTROS
						$checkedOtros = (!empty($datosCultura['Otros']) && $datosCultura['Otros'] == 1) || !empty($datosCultura['Cual']) ? "checked" : "";
						?>

						<!-- OTROS -->
						<div class="col-md-3 col-6 mb-3">
							<div class="card shadow-sm border-0 h-100 cultura-card">
								<div class="card-body py-3">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="cultura[Otros]" value="1"
											<?php echo $checkedOtros; ?>>

										<label class="form-check-label fw-semibold">Otros</label>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-6 mt-2">
							<label class="fw-semibold">Especifique otro sitio cultural</label>
							<input type="text" name="cultura[Cual]" class="form-control"
								value="<?php echo $datosCultura['Cual'] ?? ''; ?>"
								placeholder="Especifique si visitó otro lugar">
						</div>

					</div>




					<!-- ================= PASO 7 COMO SE ENTERO ================= -->
					<h5 class="mb-4">7. Información y difusión del destino</h5>

					<div class="alert alert-info py-2">
						Registre cómo el turista conoció el destino, dónde buscó información y si compartió su
						experiencia
					</div>

					<!-- COMO SE ENTERO -->
					<h6 class="fw-bold mt-3">¿Cómo se enteró del destino?</h6>
					<div class="row mt-2">
						<?php
						$entero = [
							"YaConocia" => "⭐ Ya conocía el destino",
							"AmigosyFamiliares" => "👨‍👩‍👧 Amigos o familiares",
							"BusqueInternet" => "🌐 Búsqueda en internet",
							"MediosComunicacion" => "📺 TV / radio / prensa",
							"AvisosInternet" => "📢 Publicidad en internet",
							"Ninguno" => "❌ Ninguno"
						];

						foreach ($entero as $name => $label) {
							$checked = (!empty($datosEntero[$name]) && $datosEntero[$name] == 1) ? "checked" : "";
							echo "
    <div class='col-md-3 col-6 mb-3'>
        <div class='card shadow-sm border-0 h-100 info-card'>
            <div class='card-body py-3'>
                <div class='form-check'>
                    <input class='form-check-input enteroCheck' type='checkbox' name='$name' value='1' $checked>
                    <label class='form-check-label fw-semibold'>$label</label>
                </div>
            </div>
        </div>
    </div>";
						}

						// OTROS REAL BD
						$checkedOtros = (!empty($datosEntero['Otros']) && $datosEntero['Otros'] == 1) || !empty($datosEntero['Cuales']) ? "checked" : "";
						?>

						<!-- OTROS -->
						<div class="col-md-3 col-6 mb-3">
							<div class="card shadow-sm border-0 h-100 info-card">
								<div class="card-body py-3">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="Otros" value="1" <?php echo $checkedOtros; ?>>
										<label class="form-check-label fw-semibold">Otros</label>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<label class="fw-semibold">Otro medio</label>
							<input type="text" name="CualesEntero" class="form-control"
								value="<?php echo $datosEntero['Cuales'] ?? ''; ?>"
								placeholder="Especifique otro medio">
						</div>

					</div>


					<!-- DONDE BUSCO -->
					<h6 class="fw-bold mt-4">¿Dónde buscó información?</h6>
					<div class="row mt-2">
						<?php
						$busco = [
							"OtrosTuristas" => "👥 Otros turistas",
							"GuiasTuristicos" => "🧭 Guías turísticos",
							"Amigos" => "🤝 Amigos",
							"Hotel" => "🏨 Hotel",
							"BusqueInternet" => "💻 Internet",
							"Familiares" => "👨‍👩‍👧 Familiares",
							"Nobusque" => "❌ No buscó"
						];

						foreach ($busco as $name => $label) {
							$checked = (!empty($datosBusco[$name]) && $datosBusco[$name] == 1) ? "checked" : "";
							echo "
    <div class='col-md-3 col-6 mb-3'>
        <div class='card shadow-sm border-0 h-100 info-card'>
            <div class='card-body py-3'>
                <div class='form-check'>
                    <input class='form-check-input buscoCheck' type='checkbox' name='$name' value='1' $checked>
                    <label class='form-check-label fw-semibold'>$label</label>
                </div>
            </div>
        </div>
    </div>";
						}

						// OTROS REAL BD
						$checkedOtros = (!empty($datosBusco['Otras']) && $datosBusco['Otras'] == 1) || !empty($datosBusco['Cuales']) ? "checked" : "";
						?>

						<!-- OTROS -->
						<div class="col-md-3 col-6 mb-3">
							<div class="card shadow-sm border-0 h-100 info-card">
								<div class="card-body py-3">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="Otras" value="1" <?php echo $checkedOtros; ?>>
										<label class="form-check-label fw-semibold">Otros</label>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<label class="fw-semibold">Otro medio</label>
							<input type="text" name="CualesBusco" class="form-control"
								value="<?php echo $datosBusco['Cuales'] ?? ''; ?>" placeholder="Especifique otro medio">
						</div>

					</div>

					<!-- REDES -->
					<h6 class="fw-bold mt-4">¿Compartió su experiencia?</h6>
					<div class="row mt-2">
						<?php
						$red = [
							"Facebook" => "📘 Facebook",
							"Instagram" => "📸 Instagram",
							"Twitter" => "🐦 Twitter / X",
							"Youtube" => "▶️ YouTube",
							"TikTok" => "🎵 TikTok",
							"Pinterest" => "📌 Pinterest",
							"Mensajeria" => "💬 WhatsApp / Mensajería",
							"NoCompartio" => "❌ No compartió"
						];

						foreach ($red as $name => $label) {
							$checked = (!empty($datosRed[$name]) && $datosRed[$name] == 1) ? "checked" : "";
							echo "
    <div class='col-md-3 col-6 mb-3'>
        <div class='card shadow-sm border-0 h-100 info-card'>
            <div class='card-body py-3'>
                <div class='form-check'>
                    <input class='form-check-input redCheck' type='checkbox' name='$name' value='1' $checked>
                    <label class='form-check-label fw-semibold'>$label</label>
                </div>
            </div>
        </div>
    </div>";
						}

						// OTROS REAL BD
						$checkedOtros = (!empty($datosRed['Otras']) && $datosRed['Otras'] == 1) || !empty($datosRed['Cuales']) ? "checked" : "";
						?>

						<!-- OTROS -->
						<div class="col-md-3 col-6 mb-3">
							<div class="card shadow-sm border-0 h-100 info-card">
								<div class="card-body py-3">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="OtrasRed" value="1" <?php echo $checkedOtros; ?>>
										<label class="form-check-label fw-semibold">Otras</label>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<label class="fw-semibold">Otra red o medio</label>
							<input type="text" name="CualesRed" class="form-control"
								value="<?php echo $datosRed['Cuales'] ?? ''; ?>" placeholder="Especifique">
						</div>

					</div>





					<!-- ================= PASO 8 PERCEPCION ================= -->

					<h5 class="mb-4 fw-bold text-primary">
						<i class="bi bi-graph-up"></i> 8. Percepción del destino
					</h5>

					<p class="text-muted small mb-4">
						Califique de <b>1 a 10</b> su experiencia en los siguientes aspectos.
					</p>

					<?php
					// Traemos los datos de percepción del turista
					$sqlPercepcion = "SELECT * FROM percepcion WHERE IdDatosTurista = '$id'";
					$datosPercepcion = mysqli_fetch_assoc(mysqli_query($conexion, $sqlPercepcion)) ?: [];
					?>

					<div class="row">
						<?php
						$percepciones = [
							"ValoracionGeneral" => "Valoración general del destino",
							"LimpiezaAseaMunicipios" => "Limpieza en municipios",
							"LimpiezaZonasNaturales" => "Limpieza zonas naturales",
							"ActividadesCulturales" => "Actividades culturales",
							"ActividadesDeportivas" => "Actividades deportivas",
							"Parques" => "Parques y espacios públicos",
							"DiscotecasBaresCasinos" => "Bares, discotecas y casinos",
							"EstadoCarreteras" => "Estado de carreteras",
							"TransporteLocal" => "Transporte local",
							"Seguridad" => "Seguridad",
							"SaborPlatosServidos" => "Sabor de la comida",
							"VariedadesOfertasGastronomica" => "Variedad gastronómica",
							"TratoPersonalRestaurantes" => "Atención en restaurantes",
							"HigieneLimpiezaRestaurantes" => "Higiene restaurantes",
							"PreciosPlatos" => "Precios de comida",
							"EstadoEdificio" => "Estado del alojamiento",
							"EstadoMuebles" => "Muebles del alojamiento",
							"EstadoSabanasToallas" => "Sábanas y toallas",
							"HigieneLimpiezaAlojamiento" => "Limpieza alojamiento",
							"TratoPersonalAlojamiento" => "Atención alojamiento",
							"ServicioComidas" => "Servicio de comidas",
							"PreciosAlojamiento" => "Precios alojamiento"
						];

						foreach ($percepciones as $name => $label) {
							$valor = $datosPercepcion[$name] ?? 5; // Valor por defecto 5 si no hay dato
							echo "
    <div class='col-md-6 mb-4'>
        <div class='p-3 border rounded-4 shadow-sm bg-white h-100'>
            <label class='fw-semibold small mb-2'>$label</label>
            <input type='range' class='form-range slider-percepcion' min='1' max='10' step='1' value='$valor' name='$name' data-target='val_$name'>
            <div class='d-flex justify-content-between small text-muted'>
                <span>1 Muy malo</span>
                <span id='val_$name' class='fw-bold text-primary fs-5'>$valor</span>
                <span>10 Excelente</span>
            </div>
        </div>
    </div>";
						}
						?>
					</div>

					<hr class="my-4">

					<div class="row mt-4">
						<?php
						$volveria = $datosPercepcion['Volveria'] ?? '';
						$recomendaria = $datosPercepcion['Recomendaria'] ?? '';
						$recomendaciones = $datosPercepcion['Recomendaciones'] ?? '';
						?>

						<!-- VOLVERIA -->
						<div class="col-md-6 mb-3">
							<div class="p-3 border rounded-4 shadow-sm bg-white h-100">
								<label class="fw-semibold mb-2">
									<i class="bi bi-arrow-repeat text-primary"></i> ¿Volvería al destino?
								</label>

								<select name="Volveria" class="form-select form-select-lg" required>
									<option value="">Seleccione una opción</option>
									<option value="SI" <?php if ($volveria == 'SI')
										echo 'selected'; ?>>Sí,
										definitivamente
										volvería</option>
									<option value="PROBABLE" <?php if ($volveria == 'PROBABLE')
										echo 'selected'; ?>>
										Probablemente volvería</option>
									<option value="NOSE" <?php if ($volveria == 'NOSE')
										echo 'selected'; ?>>No está
										seguro/a</option>
									<option value="PROBABLE_NO" <?php if ($volveria == 'PROBABLE_NO')
										echo 'selected'; ?>>
										Probablemente no volvería</option>
									<option value="NO" <?php if ($volveria == 'NO')
										echo 'selected'; ?>>No volvería
									</option>
								</select>
							</div>
						</div>

						<!-- RECOMENDARIA -->
						<div class="col-md-6 mb-3">
							<div class="p-3 border rounded-4 shadow-sm bg-white h-100">
								<label class="fw-semibold mb-2">
									<i class="bi bi-hand-thumbs-up text-success"></i> ¿Recomendaría el destino?
								</label>

								<select name="Recomendaria" class="form-select form-select-lg" required>
									<option value="">Seleccione una opción</option>
									<option value="SI" <?php if ($recomendaria == 'SI')
										echo 'selected'; ?>>Sí lo
										recomendaría totalmente</option>
									<option value="PROBABLE" <?php if ($recomendaria == 'PROBABLE')
										echo 'selected'; ?>>
										Probablemente lo recomendaría</option>
									<option value="NOSE" <?php if ($recomendaria == 'NOSE')
										echo 'selected'; ?>>No está
										seguro/a</option>
									<option value="PROBABLE_NO" <?php if ($recomendaria == 'PROBABLE_NO')
										echo 'selected'; ?>>Probablemente no lo recomendaría</option>
									<option value="NO" <?php if ($recomendaria == 'NO')
										echo 'selected'; ?>>No lo
										recomendaría</option>
								</select>
							</div>
						</div>

						<!-- RECOMENDACIONES -->
						<div class="col-12 mt-2">
							<div class="p-3 border rounded-4 shadow-sm bg-white">
								<label class="fw-semibold mb-2">
									<i class="bi bi-chat-left-text text-warning"></i> Recomendaciones o sugerencias
								</label>

								<textarea name="Recomendaciones" class="form-control" rows="4"
									placeholder="Ej: mejorar señalización turística, precios, seguridad, transporte, eventos culturales..."><?php echo htmlspecialchars($recomendaciones); ?></textarea>

								<small class="text-muted">
									Campo opcional. Las sugerencias ayudan a mejorar el turismo del municipio.
								</small>
							</div>
						</div>
					</div>

					<div class="d-flex justify-content-between mt-4">
						<a href="SelectDatosTuristas.php" class="btn btn-secondary anterior px-4"><i
								class="bi bi-arrow-left"></i>
							Volver</a>

						<button type="submit" class="btn btn-success px-5 fw-bold">
							<i class="bi bi-check-circle"></i> Finalizar encuesta
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

	<script src="../../Js/wizard.js"></script>

	<script>
		// Actualizar el valor de los sliders al moverlos
		document.querySelectorAll('.slider-percepcion').forEach(slider => {
			slider.addEventListener('input', function () {
				document.getElementById(this.dataset.target).textContent = this.value;
			});
		});
	</script>


</body>

</html>