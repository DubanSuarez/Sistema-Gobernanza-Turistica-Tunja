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




		<!-- BARRA PROGRESO -->
		<div class="progress mb-4" style="height:8px;">
			<div class="progress-bar bg-info" id="barra" style="width:12%"></div>
		</div>

		<form id="formEncuesta" action="guardar_turista.php" method="POST">

			<!-- ================= PASO 1 DATOS TURISTA ================= -->
			<div class="paso" id="paso1">
				<h5 class="mb-3">1. Datos del turista</h5>

				<div class="row">

					<div class="col-md-6">
						<label>Nombre completo</label>
						<input type="text" name="NombreCompleto" class="form-control" required>
					</div>

					<div class="col-md-3">
						<label>Teléfono</label>
						<input type="number" name="Telefono" class="form-control" required>
					</div>

					<div class="col-md-3">
						<label>Email</label>
						<input type="email" name="Email" class="form-control">
					</div>

					<div class="col-md-3 mt-3">
						<label>Tipo documento</label>
						<select name="TipoDocumento" class="form-select" required>
							<option value="">Seleccione</option>
							<option>Cédula</option>
							<option>Pasaporte</option>
							<option>Tarjeta Identidad</option>
							<option>Cédula Extranjería</option>
						</select>
					</div>

					<div class="col-md-3 mt-3">
						<label>Número documento</label>
						<input type="number" name="NuIdentificacion" class="form-control" required>
					</div>

					<!-- 🌎 PROCEDENCIA -->

					<div class="col-md-4 mt-3">
						<label class="fw-semibold">Ciudad de procedencia</label>

						<select name="IdProcedencia" id="procedenciaSelect" class="form-select" required
							style="width:100%">
							<option value="">Buscar ciudad...</option>
							<option value="OTRA">➕ No aparece mi ciudad</option>

							<?php
							$q = mysqli_query($conexion, "
            SELECT procedencia.Id, procedencia.Ciudad, pais.NombrePais 
            FROM procedencia 
            INNER JOIN pais ON pais.Id = procedencia.IdPais
            ORDER BY Ciudad
        ");
							while ($r = mysqli_fetch_array($q)) {
								echo "<option value='" . $r['Id'] . "'>" . $r['Ciudad'] . " - " . $r['NombrePais'] . "</option>";
							}
							?>
						</select>
					</div>


					<div class="col-md-4 mt-3 d-none" id="divNuevaCiudad">
						<label>Ciudad</label>
						<input type="text" name="NuevaCiudad" class="form-control" placeholder="Ej: San Francisco">
					</div>

					<div class="col-md-4 mt-3 d-none" id="divPais">
						<label>País</label>

						<select name="IdPais" class="form-select">
							<option value="">Seleccione país</option>

							<?php
							$pais = mysqli_query($conexion, "SELECT * FROM pais ORDER BY NombrePais");
							while ($p = mysqli_fetch_array($pais)) {
								echo "<option value='" . $p['Id'] . "'>" . $p['NombrePais'] . "</option>";
							}
							?>
						</select>
					</div>




				</div>

				<button type="button" class="btn btn-primary mt-4 siguiente">Siguiente</button>
			</div>


			<!-- ================= PASO 2 VIAJE ================= -->
			<div class="paso d-none" id="paso2">
				<h5>2. Información del viaje</h5>

				<div class="row">
					<div class="col-md-4">
						<label>Acompañantes</label>
						<select name="TipoAcompanantes" class="form-select" required>
							<option>Solo</option>
							<option>Familia</option>
							<option>Pareja</option>
							<option>Amigos</option>
							<option>Tour</option>
						</select>
					</div>

					<div class="col-md-4">
						<label>Cantidad</label>
						<select name="CantidadAcompanantes" class="form-select" required>
							<option>0</option>
							<option>1</option>
							<option>2</option>
							<option>3</option>
							<option>4</option>
							<option>5+</option>
						</select>
					</div>

					<div class="col-md-4">
						<label>Transporte</label>
						<select name="TipoTransporte" class="form-select" required>
							<option>Bus</option>
							<option>Carro</option>
							<option>Avión</option>
							<option>Moto</option>
							<option>Bicicleta</option>
							<option>Otro</option>
						</select>
					</div>

					<div class="col-md-4 mt-3">
						<label>Frecuencia visita</label>
						<select name="FrecuenciaVisitaAnual" class="form-select" required>
							<option>Primera vez</option>
							<option>1 vez al año</option>
							<option>2 veces</option>
							<option>3 veces</option>
							<option>4 veces</option>
							<option>5 o más</option>
						</select>
					</div>
				</div>

				<button type="button" class="btn btn-secondary mt-4 anterior">Atrás</button>
				<button type="button" class="btn btn-primary mt-4 siguiente">Siguiente</button>
			</div>

			<!-- ================= PASO 3 MUNICIPIOS ================= -->
			<div class="paso d-none" id="paso3">
				<h5>3. Municipios visitados</h5>

				<div class="row">

					<!-- CANTIDAD -->
					<div class="col-md-4">
						<label>¿Cuántos municipios visitó?</label>
						<select id="cantidadMunicipios" class="form-select" onchange="mostrarMunicipios()" required>
							<option value="">Seleccione</option>
							<option value="1">1 municipio</option>
							<option value="2">2 municipios</option>
							<option value="3">3 municipios</option>
							<option value="4">4 municipios</option>
							<option value="5">5 municipios</option>
							<option value="6">6 municipios</option>
							<option value="7">7 municipios</option>
						</select>
					</div>

				</div>

				<div class="row" id="contenedorMunicipios" style="display:none;">
					<?php
					$qm = mysqli_query($conexion, "SELECT * FROM municipios ORDER BY NombreMunicipio");
					$opts = "";
					while ($m = mysqli_fetch_array($qm)) {
						$opts .= "<option value='$m[Id]'>$m[NombreMunicipio]</option>";
					}

					for ($i = 1; $i <= 7; $i++) {
						echo "
            <div class='col-md-4 mt-3 municipio-select d-none' id='municipio$i'>
                <label>Municipio $i</label>
                <select name='IdMunicipioVisitado$i' class='form-select'>
                    <option value=''>Seleccione</option>
                    $opts
                </select>
            </div>";
					}
					?>
				</div>

				<button type="button" class="btn btn-secondary mt-4 anterior">Atrás</button>
				<button type="button" class="btn btn-primary mt-4 siguiente">Siguiente</button>
			</div>

			<!-- ================= PASO 4 GASTOS ================= -->
			<div class="paso d-none" id="paso4">
				<h5>4. Gastos del turista</h5>

				<div class="row">

					<!-- AGENCIA -->
					<div class="col-md-4">
						<label>¿Usó agencia turística?</label>
						<select name="UsoAgencia" class="form-select" onchange="toggleAgencia(this.value)" required>
							<option value="">Seleccione</option>
							<option value="SI">SI</option>
							<option value="NO">NO</option>
						</select>
					</div>

					<div id="boxAgencia" class="row d-none">
						<div class="col-md-4 mt-3">
							<label>Nombre agencia</label>
							<input type="text" name="NombreAgencia" class="form-control">
						</div>

						<div class="col-md-4 mt-3">
							<label>Noches con agencia</label>
							<input type="number" name="CantidadNochesAgencia" class="form-control">
						</div>
					</div>


					<!-- SE QUEDÓ EN LA CIUDAD -->
					<div class="col-md-4 mt-3">
						<label>¿Se quedó en la ciudad?</label>
						<select name="QuedoCiudad" class="form-select" onchange="toggleAlojamiento(this.value)"
							required>
							<option value="">Seleccione</option>
							<option value="SI">SI</option>
							<option value="NO">NO</option>
						</select>
					</div>


					<div id="boxAlojamiento" class="row d-none">

						<div class="col-md-4 mt-3">
							<label>Tipo alojamiento</label>
							<select name="UsoAlojamiento" class="form-select">
								<option value="">Seleccione</option>
								<option>Hotel</option>
								<option>Hostal</option>
								<option>Airbnb</option>
								<option>Apartamento turístico</option>
								<option>Finca</option>
								<option>Camping</option>
								<option>No usó</option>
							</select>
						</div>

						<div class="col-md-4 mt-3">
							<label>Costo alojamiento</label>
							<input type="number" name="CostoAlojamiento" class="form-control">
						</div>

						<div class="col-md-4 mt-3">
							<label>Noches alojamiento</label>
							<input type="number" name="CantidadNochesAlojamiento" class="form-control">
						</div>

						<div class="col-md-4 mt-3">
							<label>¿Casa de familiar o conocido?</label>
							<select name="CasaFamiliaroConocido" class="form-select">
								<option value="">Seleccione</option>
								<option>SI</option>
								<option>NO</option>
							</select>
						</div>

					</div>


					<!-- RESTAURANTE -->
					<div class="col-md-4 mt-3">
						<label>¿Consumió en restaurantes?</label>
						<select name="UsoRestaurante" class="form-select" onchange="toggleRest(this.value)" required>
							<option value="">Seleccione</option>
							<option>SI</option>
							<option>NO</option>
						</select>
					</div>

					<div id="boxRest" class="col-md-4 mt-3 d-none">
						<label>Costo restaurantes</label>
						<input type="number" name="CostoRestaurante" class="form-control">
					</div>


					<!-- ACTIVIDADES -->
					<div class="col-md-4 mt-3">
						<label>¿Pagó actividades recreativas/culturales?</label>
						<select name="RealizoActividadesRyC" class="form-select" onchange="toggleAct(this.value)"
							required>
							<option value="">Seleccione</option>
							<option>SI</option>
							<option>NO</option>
						</select>
					</div>

					<div id="boxAct" class="col-md-4 mt-3 d-none">
						<label>Costo actividades</label>
						<input type="number" name="CostoActividadesRyC" class="form-control">
					</div>


					<!-- ALIMENTOS -->
					<div class="col-md-4 mt-3">
						<label>¿Compró alimentos o bebidas?</label>
						<select name="ComproAlimentoyBebidas" class="form-select" onchange="toggleAli(this.value)"
							required>
							<option value="">Seleccione</option>
							<option>SI</option>
							<option>NO</option>
						</select>
					</div>

					<div id="boxAli" class="col-md-4 mt-3 d-none">
						<label>Costo alimentos/bebidas</label>
						<input type="number" name="CostoAlimentoyBebidas" class="form-control">
					</div>


					<!-- ARTESANIAS -->
					<div class="col-md-4 mt-3">
						<label>¿Compró artesanías?</label>
						<select name="ComproArtesanias" class="form-select" onchange="toggleArt(this.value)" required>
							<option value="">Seleccione</option>
							<option>SI</option>
							<option>NO</option>
						</select>
					</div>

					<div id="boxArt" class="col-md-4 mt-3 d-none">
						<label>Costo artesanías</label>
						<input type="number" name="CostoArtesanias" class="form-control">
					</div>

				</div>

				<button type="button" class="btn btn-secondary mt-4 anterior">Atrás</button>
				<button type="button" class="btn btn-primary mt-4 siguiente">Siguiente</button>
			</div>

			<!-- ================= PASO 5 MOTIVOS ================= -->
			<div class="paso d-none" id="paso5">
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
						echo "
<div class='col-md-3 col-6 mb-3'>
<div class='card shadow-sm border-0 h-100 motivo-card'>
<div class='card-body py-3'>
<div class='form-check'>
<input class='form-check-input' type='checkbox' name='$name' value='1'>
<label class='form-check-label fw-semibold'>$label</label>
</div>
</div>
</div>
</div>";
					}
					?>

					<!-- OTROS -->
					<div class="col-md-6 mt-2">
						<label class="fw-semibold">Otro motivo (opcional)</label>
						<input type="text" name="OtrosMotivo" class="form-control" placeholder="Especifique si aplica">
					</div>

				</div>

				<button type="button" class="btn btn-secondary mt-4 anterior">Atrás</button>
				<button type="button" class="btn btn-primary mt-4 siguiente">Siguiente</button>
			</div>

			<!-- ================= PASO 6 CULTURA ================= -->
			<div class="paso d-none" id="paso6">
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
						echo "
<div class='col-md-3 col-6 mb-3'>
<div class='card shadow-sm border-0 h-100 cultura-card'>
<div class='card-body py-3'>
<div class='form-check'>
<input class='form-check-input culturaCheck' type='checkbox' name='$name' value='1'>
<label class='form-check-label fw-semibold'>$label</label>
</div>
</div>
</div>
</div>";
					}
					?>

					<!-- OTROS -->
					<div class="col-md-6 mt-2">
						<label class="fw-semibold">Otro sitio cultural</label>
						<input type="text" name="CualCultura" class="form-control"
							placeholder="Especifique si visitó otro lugar">
					</div>

				</div>

				<button type="button" class="btn btn-secondary mt-4 anterior">Atrás</button>
				<button type="button" class="btn btn-primary mt-4 siguiente">Siguiente</button>
			</div>


			<!-- ================= PASO 7 COMO SE ENTERO ================= -->
			<div class="paso d-none" id="paso7">
				<h5 class="mb-4">7. Información y difusión del destino</h5>

				<div class="alert alert-info py-2">
					Registre cómo el turista conoció el destino, dónde buscó información y si compartió su experiencia
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
						echo "
<div class='col-md-3 col-6 mb-3'>
<div class='card shadow-sm border-0 h-100 info-card'>
<div class='card-body py-3'>
<div class='form-check'>
<input class='form-check-input enteroCheck' type='checkbox' name='$name' value='1'>
<label class='form-check-label fw-semibold'>$label</label>
</div>
</div>
</div>
</div>";
					}
					?>

					<div class="col-md-6">
						<label class="fw-semibold">Otro medio</label>
						<input type="text" name="CualesEntero" class="form-control"
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
						echo "
<div class='col-md-3 col-6 mb-3'>
<div class='card shadow-sm border-0 h-100 info-card'>
<div class='card-body py-3'>
<div class='form-check'>
<input class='form-check-input buscoCheck' type='checkbox' name='$name' value='1'>
<label class='form-check-label fw-semibold'>$label</label>
</div>
</div>
</div>
</div>";
					}
					?>

					<div class="col-md-6">
						<label class="fw-semibold">Otro medio</label>
						<input type="text" name="CualesBusco" class="form-control" placeholder="Especifique otro medio">
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
						echo "
<div class='col-md-3 col-6 mb-3'>
<div class='card shadow-sm border-0 h-100 info-card'>
<div class='card-body py-3'>
<div class='form-check'>
<input class='form-check-input redCheck' type='checkbox' name='$name' value='1'>
<label class='form-check-label fw-semibold'>$label</label>
</div>
</div>
</div>
</div>";
					}
					?>

					<div class="col-md-6">
						<label class="fw-semibold">Otra red o medio</label>
						<input type="text" name="CualesRed" class="form-control" placeholder="Especifique">
					</div>

				</div>

				<button type="button" class="btn btn-secondary mt-4 anterior">Atrás</button>
				<button type="button" class="btn btn-primary mt-4 siguiente">Siguiente</button>
			</div>


			<!-- ================= PASO 8 PERCEPCION ================= -->
			<div class="paso d-none" id="paso8">

				<h5 class="mb-4 fw-bold text-primary">
					<i class="bi bi-graph-up"></i> 8. Percepción del destino
				</h5>

				<p class="text-muted small mb-4">
					Califique de <b>1 a 10</b> su experiencia en los siguientes aspectos.
				</p>

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
						echo "
<div class='col-md-6 mb-4'>
  <div class='p-3 border rounded-4 shadow-sm bg-white h-100'>
    
    <label class='fw-semibold small mb-2'>$label</label>

    <input type='range'
           class='form-range slider-percepcion'
           min='1'
           max='10'
           step='1'
           value='5'
           name='$name'
           data-target='val_$name'>

    <div class='d-flex justify-content-between small text-muted'>
      <span>1 Muy malo</span>
      <span id='val_$name' class='fw-bold text-primary fs-5'>5</span>
      <span>10 Excelente</span>
    </div>

  </div>
</div>";
					}
					?>
				</div>

				<hr class="my-4">

				<div class="row mt-4">

					<!-- VOLVERIA -->
					<div class="col-md-6 mb-3">
						<div class="p-3 border rounded-4 shadow-sm bg-white h-100">
							<label class="fw-semibold mb-2">
								<i class="bi bi-arrow-repeat text-primary"></i> ¿Volvería al destino?
							</label>

							<select name="Volveria" class="form-select form-select-lg" required>
								<option value="">Seleccione una opción</option>
								<option value="SI">Sí, definitivamente volvería</option>
								<option value="PROBABLE">Probablemente volvería</option>
								<option value="NOSE">No está seguro/a</option>
								<option value="PROBABLE_NO">Probablemente no volvería</option>
								<option value="NO">No volvería</option>
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
								<option value="SI">Sí lo recomendaría totalmente</option>
								<option value="PROBABLE">Probablemente lo recomendaría</option>
								<option value="NOSE">No está seguro/a</option>
								<option value="PROBABLE_NO">Probablemente no lo recomendaría</option>
								<option value="NO">No lo recomendaría</option>
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
								placeholder="Ej: mejorar señalización turística, precios, seguridad, transporte, eventos culturales..."></textarea>

							<small class="text-muted">
								Campo opcional. Las sugerencias ayudan a mejorar el turismo del municipio.
							</small>
						</div>
					</div>

				</div>


				<div class="d-flex justify-content-between mt-4">
					<button type="button" class="btn btn-secondary anterior px-4">
						<i class="bi bi-arrow-left"></i> Atrás
					</button>

					<button type="submit" class="btn btn-success px-5 fw-bold">
						<i class="bi bi-check-circle"></i> Finalizar encuesta
					</button>

				</div>

			</div>



		</form>

		<div id="msg" class="mt-3"></div>


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


	<!-- Procedencia -->
	<script>
		document.getElementById("procedenciaSelect").addEventListener("change", function () {

			let val = this.value;

			let divCiudad = document.getElementById("divNuevaCiudad");
			let divPais = document.getElementById("divPais");

			if (val === "OTRA") {
				divCiudad.classList.remove("d-none");
				divPais.classList.remove("d-none");
			} else {
				divCiudad.classList.add("d-none");
				divPais.classList.add("d-none");
			}
		});
	</script>




	<!-- Municipios -->

	<script>
		function mostrarMunicipios() {

			let cantidad = document.getElementById("cantidadMunicipios").value;
			let contenedor = document.getElementById("contenedorMunicipios");

			if (cantidad === "") {
				contenedor.style.display = "none";
				return;
			}

			contenedor.style.display = "flex";

			// ocultar todos
			for (let i = 1; i <= 7; i++) {
				document.getElementById("municipio" + i).classList.add("d-none");
			}

			// mostrar los seleccionados
			for (let i = 1; i <= cantidad; i++) {
				document.getElementById("municipio" + i).classList.remove("d-none");
			}
		}
	</script>


	<!--  Gastos  -->

	<script>
		function toggleAgencia(val) {
			document.getElementById("boxAgencia").classList.toggle("d-none", val !== "SI");
		}

		function toggleAlojamiento(val) {
			document.getElementById("boxAlojamiento").classList.toggle("d-none", val !== "SI");
		}

		function toggleRest(val) {
			document.getElementById("boxRest").classList.toggle("d-none", val !== "SI");
		}

		function toggleAct(val) {
			document.getElementById("boxAct").classList.toggle("d-none", val !== "SI");
		}

		function toggleAli(val) {
			document.getElementById("boxAli").classList.toggle("d-none", val !== "SI");
		}

		function toggleArt(val) {
			document.getElementById("boxArt").classList.toggle("d-none", val !== "SI");
		}
	</script>


	<!-- SCRIPT SLIDER DINAMICO -->
	<script>
		document.addEventListener("input", function (e) {
			if (e.target.classList.contains("slider-percepcion")) {
				let target = e.target.getAttribute("data-target");
				document.getElementById(target).innerText = e.target.value;
			}
		});
	</script>





</body>

</html>