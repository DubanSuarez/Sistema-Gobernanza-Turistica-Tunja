<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once('../Conexion/conexion.php');

if (!isset($_SESSION['rol'])) {
    die("❌ Sesión expirada");
}

mysqli_begin_transaction($conexion);

try{

$id = intval($_POST['Id']);

/* =========================
   1. DATOS TURISTA
=========================*/

$NombreCompleto = $_POST['NombreCompleto'] ?? '';
$Telefono = $_POST['Telefono'] ?? '';
$Email = $_POST['Email'] ?? '';
$TipoDocumento = $_POST['TipoDocumento'] ?? '';
$NuIdentificacion = $_POST['NuIdentificacion'] ?? '';
$IdProcedencia = $_POST['IdProcedencia'] ?? 'NULL';
$TipoAcompanantes = $_POST['TipoAcompanantes'] ?? '';
$CantidadAcompanantes = $_POST['CantidadAcompanantes'] ?? 0;
$TipoTransporte = $_POST['TipoTransporte'] ?? '';
$FrecuenciaVisitaAnual = $_POST['FrecuenciaVisitaAnual'] ?? '';

$m1 = $_POST['IdMunicipioVisitado1'] ?? null;
$m2 = $_POST['IdMunicipioVisitado2'] ?? null;
$m3 = $_POST['IdMunicipioVisitado3'] ?? null;
$m4 = $_POST['IdMunicipioVisitado4'] ?? null;
$m5 = $_POST['IdMunicipioVisitado5'] ?? null;
$m6 = $_POST['IdMunicipioVisitado6'] ?? null;
$m7 = $_POST['IdMunicipioVisitado7'] ?? null;

$sql = "UPDATE datosturista SET
NombreCompleto='$NombreCompleto',
Telefono='$Telefono',
Email='$Email',
TipoDocumento='$TipoDocumento',
NuIdentificacion='$NuIdentificacion',
IdProcedencia='$IdProcedencia',
TipoAcompanantes='$TipoAcompanantes',
CantidadAcompanantes='$CantidadAcompanantes',
TipoTransporte='$TipoTransporte',
FrecuenciaVisitaAnual='$FrecuenciaVisitaAnual',
IdMunicipioVisitado1=".($m1?:'NULL').",
IdMunicipioVisitado2=".($m2?:'NULL').",
IdMunicipioVisitado3=".($m3?:'NULL').",
IdMunicipioVisitado4=".($m4?:'NULL').",
IdMunicipioVisitado5=".($m5?:'NULL').",
IdMunicipioVisitado6=".($m6?:'NULL').",
IdMunicipioVisitado7=".($m7?:'NULL')."
WHERE Id='$id'";

if(!mysqli_query($conexion,$sql)){
throw new Exception("Error DATOSTURISTA: ".mysqli_error($conexion));
}

/* =========================
   2. GASTOS
=========================*/

$UsoAgencia = $_POST['UsoAgencia'] ?? '';
$NombreAgencia = $_POST['NombreAgencia'] ?? '';
$CantidadNochesAgencia = $_POST['CantidadNochesAgencia'] ?? 0;
$QuedoCiudad = $_POST['QuedoCiudad'] ?? '';
$UsoAlojamiento = $_POST['UsoAlojamiento'] ?? '';
$CostoAlojamiento = $_POST['CostoAlojamiento'] ?? 0;
$CantidadNochesAlojamiento = $_POST['CantidadNochesAlojamiento'] ?? 0;
$CasaFamiliaroConocido = $_POST['CasaFamiliaroConocido'] ?? '';
$UsoRestaurante = $_POST['UsoRestaurante'] ?? '';
$CostoRestaurante = $_POST['CostoRestaurante'] ?? 0;
$RealizoActividadesRyC = $_POST['RealizoActividadesRyC'] ?? '';
$CostoActividadesRyC = $_POST['CostoActividadesRyC'] ?? 0;
$ComproAlimentoyBebidas = $_POST['ComproAlimentoyBebidas'] ?? '';
$CostoAlimentoyBebidas = $_POST['CostoAlimentoyBebidas'] ?? 0;
$ComproArtesanias = $_POST['ComproArtesanias'] ?? '';
$CostoArtesanias = $_POST['CostoArtesanias'] ?? 0;

$sqlG = "UPDATE gastos SET
UsoAgencia='$UsoAgencia',
NombreAgencia='$NombreAgencia',
CantidadNochesAgencia='$CantidadNochesAgencia',
QuedoCiudad='$QuedoCiudad',
UsoAlojamiento='$UsoAlojamiento',
CostoAlojamiento='$CostoAlojamiento',
CantidadNochesAlojamiento='$CantidadNochesAlojamiento',
CasaFamiliaroConocido='$CasaFamiliaroConocido',
UsoRestaurante='$UsoRestaurante',
CostoRestaurante='$CostoRestaurante',
RealizoActividadesRyC='$RealizoActividadesRyC',
CostoActividadesRyC='$CostoActividadesRyC',
ComproAlimentoyBebidas='$ComproAlimentoyBebidas',
CostoAlimentoyBebidas='$CostoAlimentoyBebidas',
ComproArtesanias='$ComproArtesanias',
CostoArtesanias='$CostoArtesanias'
WHERE IdDatosTurista='$id'";

if(!mysqli_query($conexion,$sqlG)){
throw new Exception("Error GASTOS: ".mysqli_error($conexion));
}

/* =========================
   3. MOTIVOS (FIX REAL)
=========================*/

$motivos = $_POST['motivos'] ?? [];

$motivosCampos = [
"EspectaculosArtisticos","MusicaCineDanzas","FeriayFiestas","Cultura",
"ParquesTematicos","ParquesNaturales","CallesyParques","HaciendasCultural",
"Casino","Deportes","Discotecas","CentrosComerciales","Compras","Religion",
"Inversiones","Conferencias","Familiares","Acampar","ExcursionoViaje","Ninguno"
];

$setMotivos="";

foreach($motivosCampos as $m){
$val = isset($motivos[$m]) ? 1 : 0;
$setMotivos .= "$m='$val',";
}

$Otros = isset($motivos['Otros']) ? 1 : 0;
$Cuales = $motivos['Cuales'] ?? '';

$setMotivos .= "Otros='$Otros', Cuales='".mysqli_real_escape_string($conexion,$Cuales)."'";

$sqlM = "UPDATE motivos SET $setMotivos WHERE IdDatosTurista='$id'";
if(!mysqli_query($conexion,$sqlM)){
throw new Exception("Error MOTIVOS: ".mysqli_error($conexion));
}


/* =========================
   4. CULTURA (FIX REAL)
=========================*/

$cultura = $_POST['cultura'] ?? [];

$culturaCampos = [
"Catedrales","CasasCultura","MuseosArte","MuseosArqueologicos",
"HaciendasCultura","Puentes","Monumentos","Cementerios","Santuarios","Ninguna"
];

$setCultura="";

foreach($culturaCampos as $c){
$val = isset($cultura[$c]) ? 1 : 0;
$setCultura .= "$c='$val',";
}

$OtrosC = isset($cultura['Otros']) ? 1 : 0;
$Cual = $cultura['Cual'] ?? '';

$setCultura .= "Otros='$OtrosC', Cual='".mysqli_real_escape_string($conexion,$Cual)."'";

$sqlC = "UPDATE cultura SET $setCultura WHERE IdDatosTurista='$id'";
if(!mysqli_query($conexion,$sqlC)){
throw new Exception("Error CULTURA: ".mysqli_error($conexion));
}


/* =========================
   5. COMO SE ENTERO
=========================*/

$enteroCampos = [
"YaConocia","AmigosyFamiliares","BusqueInternet",
"MediosComunicacion","AvisosInternet","Ninguno"
];

$setEntero="";
foreach($enteroCampos as $e){
$val = isset($_POST[$e]) ? 1 : 0;
$setEntero .= "$e='$val',";
}

$OtrosEntero = isset($_POST['OtrosEntero']) ? 1 : 0;
$CualesEntero = $_POST['CualesEntero'] ?? '';
$setEntero .= "Otros='$OtrosEntero', Cuales='$CualesEntero'";

$sqlE = "UPDATE comoseentero SET $setEntero WHERE IdDatosTurista='$id'";
if(!mysqli_query($conexion,$sqlE)){
throw new Exception("Error COMOSEENTERO: ".mysqli_error($conexion));
}

/* =========================
   6. DONDE BUSCO INFO
=========================*/

$buscoCampos = [
"OtrosTuristas","GuiasTuristicos","Amigos","Hotel",
"BusqueInternet","Familiares","Nobusque"
];

$setBusco="";
foreach($buscoCampos as $b){
$val = isset($_POST[$b]) ? 1 : 0;
$setBusco .= "$b='$val',";
}

$OtrasBusco = isset($_POST['OtrasBusco']) ? 1 : 0;
$CualesBusco = $_POST['CualesBusco'] ?? '';
$setBusco .= "Otras='$OtrasBusco', Cuales='$CualesBusco'";

$sqlB = "UPDATE buscoinformacion SET $setBusco WHERE IdDatosTurista='$id'";
if(!mysqli_query($conexion,$sqlB)){
throw new Exception("Error BUSCOINFO: ".mysqli_error($conexion));
}

/* =========================
   7. REDES
=========================*/

$redCampos = [
"Facebook","Instagram","Twitter","Youtube",
"TikTok","Pinterest","Mensajeria","NoCompartio"
];

$setRed="";
foreach($redCampos as $r){
$val = isset($_POST[$r]) ? 1 : 0;
$setRed .= "$r='$val',";
}

$OtrasRed = isset($_POST['OtrasRedes']) ? 1 : 0; // CORREGIDO
$CualesRed = $_POST['CualesRed'] ?? '';
$setRed .= "Otras='$OtrasRed', Cuales='$CualesRed'";

$sqlR = "UPDATE compartio SET $setRed WHERE IdDatosTurista='$id'";
if(!mysqli_query($conexion,$sqlR)){
throw new Exception("Error REDES: ".mysqli_error($conexion));
}

/* =========================
   8. PERCEPCION
=========================*/

$percepcionCampos = [
"ValoracionGeneral","LimpiezaAseaMunicipios","LimpiezaZonasNaturales",
"ActividadesCulturales","ActividadesDeportivas","Parques",
"DiscotecasBaresCasinos","EstadoCarreteras","TransporteLocal",
"Seguridad","SaborPlatosServidos","VariedadesOfertasGastronomica",
"TratoPersonalRestaurantes","HigieneLimpiezaRestaurantes","PreciosPlatos",
"EstadoEdificio","EstadoMuebles","EstadoSabanasToallas",
"HigieneLimpiezaAlojamiento","TratoPersonalAlojamiento","ServicioComidas",
"PreciosAlojamiento","Volveria","Recomendaria","Recomendaciones"
];

$setPer="";
foreach($percepcionCampos as $p){
$val = $_POST[$p] ?? '';
$setPer .= "$p='$val',";
}

$setPer = rtrim($setPer,",");
$sqlP = "UPDATE percepcion SET $setPer WHERE IdDatosTurista='$id'";
if(!mysqli_query($conexion,$sqlP)){
throw new Exception("Error PERCEPCION: ".mysqli_error($conexion));
}

/* =========================
   FINAL
=========================*/

mysqli_commit($conexion);

echo "<script>
alert('✅ Los datos fueron actualizados correctamente');
window.location.href='SelectDatosTuristas.php';
</script>";
exit();

} catch (Exception $e) {

mysqli_rollback($conexion);
$error = addslashes($e->getMessage());

echo "<script>
alert('❌ ERROR AL ACTUALIZAR:\\n\\n$error');
window.history.back();
</script>";
exit();
}
?>
