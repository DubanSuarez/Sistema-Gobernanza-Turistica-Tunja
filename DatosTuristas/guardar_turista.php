<?php
session_start();
require_once('../Conexion/conexion.php');

if (!isset($_SESSION['rol'])) {
    die("❌ Sesión expirada");
}

mysqli_set_charset($conexion, "utf8");
mysqli_begin_transaction($conexion);

function ejecutar($conexion,$sql,$paso){
    if(!mysqli_query($conexion,$sql)){
        throw new Exception(mysqli_error($conexion));
    }
}


# 🧠 FUNCION PARA TEXTOS OPCIONALES
function textoOpcional($conexion,$campo){
    $txt = trim($_POST[$campo] ?? "");
    if($txt != ""){
        return mysqli_real_escape_string($conexion,$txt);
    }
    return "";
}

function check($campo){
    return isset($_POST[$campo]) ? 1 : 0;
}

try {

date_default_timezone_set("America/Bogota");

//////////////////////////////////////////////////////
// 1️⃣ PROCEDENCIA (PRO + PAIS)
//////////////////////////////////////////////////////

$idProcedencia = $_POST['IdProcedencia'] ?? "";

if($idProcedencia == "OTRA"){

    $ciudad = trim($_POST['NuevaCiudad'] ?? "");
    $idPais = $_POST['IdPais'] ?? "";

    if($ciudad=="" || $idPais==""){
        throw new Exception("❌ Debe escribir ciudad y seleccionar país");
    }

    $ciudad = mysqli_real_escape_string($conexion,$ciudad);
    $idPais = intval($idPais);

    // verificar si ya existe
    $checkProc = mysqli_query($conexion,"
        SELECT Id FROM procedencia 
        WHERE Ciudad='$ciudad' AND IdPais='$idPais'
    ");

    if(mysqli_num_rows($checkProc)>0){
        $row = mysqli_fetch_assoc($checkProc);
        $idProcedencia = $row['Id'];
        echo "✔ Ciudad ya existía\n";
    }else{
        ejecutar($conexion,"
        INSERT INTO procedencia (Ciudad,IdPais)
        VALUES('$ciudad','$idPais')
        ","Insert nueva procedencia");

        $idProcedencia = mysqli_insert_id($conexion);
    }
}

if($idProcedencia=="" || $idProcedencia=="OTRA"){
    throw new Exception("❌ Debe seleccionar procedencia");
}

//////////////////////////////////////////////////////
// 2️⃣ DATOS TURISTA
//////////////////////////////////////////////////////

$m1 = !empty($_POST['IdMunicipioVisitado1']) ? $_POST['IdMunicipioVisitado1'] : 125;
$m2 = !empty($_POST['IdMunicipioVisitado2']) ? $_POST['IdMunicipioVisitado2'] : 125;
$m3 = !empty($_POST['IdMunicipioVisitado3']) ? $_POST['IdMunicipioVisitado3'] : 125;
$m4 = !empty($_POST['IdMunicipioVisitado4']) ? $_POST['IdMunicipioVisitado4'] : 125;
$m5 = !empty($_POST['IdMunicipioVisitado5']) ? $_POST['IdMunicipioVisitado5'] : 125;
$m6 = !empty($_POST['IdMunicipioVisitado6']) ? $_POST['IdMunicipioVisitado6'] : 125;
$m7 = !empty($_POST['IdMunicipioVisitado7']) ? $_POST['IdMunicipioVisitado7'] : 125;

$sqlTurista = "
INSERT INTO datosturista 
(NombreCompleto,Telefono,Email,IdProcedencia,TipoDocumento,NuIdentificacion,
TipoAcompanantes,CantidadAcompanantes,TipoTransporte,
IdMunicipioVisitado1,IdMunicipioVisitado2,IdMunicipioVisitado3,
IdMunicipioVisitado4,IdMunicipioVisitado5,IdMunicipioVisitado6,IdMunicipioVisitado7,
FrecuenciaVisitaAnual,fecha_registro)
VALUES(
'".mysqli_real_escape_string($conexion,$_POST['NombreCompleto'])."',
'".mysqli_real_escape_string($conexion,$_POST['Telefono'])."',
'".mysqli_real_escape_string($conexion,$_POST['Email'])."',
'$idProcedencia',
'".mysqli_real_escape_string($conexion,$_POST['TipoDocumento'])."',
'".mysqli_real_escape_string($conexion,$_POST['NuIdentificacion'])."',
'".($_POST['TipoAcompanantes'] ?? '')."',
'".($_POST['CantidadAcompanantes'] ?? 0)."',
'".($_POST['TipoTransporte'] ?? '')."',
'$m1','$m2','$m3','$m4','$m5','$m6','$m7',
'".($_POST['FrecuenciaVisitaAnual'] ?? '')."',
NOW()
)";
ejecutar($conexion,$sqlTurista,"Insert datosturista");
$idTurista = mysqli_insert_id($conexion);

//////////////////////////////////////////////////////
// 3️⃣ GASTOS
//////////////////////////////////////////////////////

$UsoAgencia = $_POST['UsoAgencia'] ?? "NO";

if($UsoAgencia=="SI"){
    $nombre = textoOpcional($conexion,'NombreAgencia');
    $NombreAgencia = ($nombre!="") ? "'$nombre'" : "'SIN NOMBRE'";
    $NochesAgencia = !empty($_POST['CantidadNochesAgencia']) ? intval($_POST['CantidadNochesAgencia']) : 0;
}else{
    $NombreAgencia = "'NO APLICA'";
    $NochesAgencia = 0;
}

$QuedoCiudad = $_POST['QuedoCiudad'] ?? "NO";

if($QuedoCiudad=="SI"){
    $UsoAlojamiento = "'".($_POST['UsoAlojamiento'] ?? 'NO')."'";
    $CostoAlojamiento = !empty($_POST['CostoAlojamiento']) ? intval($_POST['CostoAlojamiento']) : 0;
    $NochesAlojamiento = !empty($_POST['CantidadNochesAlojamiento']) ? intval($_POST['CantidadNochesAlojamiento']) : 0;
    $CasaFamiliar = "'".($_POST['CasaFamiliaroConocido'] ?? 'NO')."'";
}else{
    $UsoAlojamiento="'NO'";
    $CostoAlojamiento=0;
    $NochesAlojamiento=0;
    $CasaFamiliar="'NO'";
}

$UsoRest = $_POST['UsoRestaurante'] ?? "NO";
$CostoRest = ($UsoRest=="SI") ? intval($_POST['CostoRestaurante'] ?? 0) : 0;

$Act = $_POST['RealizoActividadesRyC'] ?? "NO";
$CostoAct = ($Act=="SI") ? intval($_POST['CostoActividadesRyC'] ?? 0) : 0;

$Ali = $_POST['ComproAlimentoyBebidas'] ?? "NO";
$CostoAli = ($Ali=="SI") ? intval($_POST['CostoAlimentoyBebidas'] ?? 0) : 0;

$Art = $_POST['ComproArtesanias'] ?? "NO";
$CostoArt = ($Art=="SI") ? intval($_POST['CostoArtesanias'] ?? 0) : 0;

$sqlGastos = "
INSERT INTO gastos
(IdDatosTurista,UsoAgencia,NombreAgencia,CantidadNochesAgencia,
QuedoCiudad,UsoAlojamiento,CostoAlojamiento,CantidadNochesAlojamiento,
CasaFamiliaroConocido,UsoRestaurante,CostoRestaurante,
RealizoActividadesRyC,CostoActividadesRyC,
ComproAlimentoyBebidas,CostoAlimentoyBebidas,
ComproArtesanias,CostoArtesanias)
VALUES(
'$idTurista',
'$UsoAgencia',$NombreAgencia,$NochesAgencia,
'$QuedoCiudad',$UsoAlojamiento,$CostoAlojamiento,$NochesAlojamiento,
$CasaFamiliar,
'$UsoRest',$CostoRest,
'$Act',$CostoAct,
'$Ali',$CostoAli,
'$Art',$CostoArt
)";
ejecutar($conexion,$sqlGastos,"Insert gastos");

//////////////////////////////////////////////////////
// 4️⃣ MOTIVOS
//////////////////////////////////////////////////////

$camposMotivo = [
"EspectaculosArtisticos","MusicaCineDanzas","FeriayFiestas","Cultura",
"ParquesTematicos","ParquesNaturales","CallesyParques","HaciendasCultural",
"Casino","Deportes","Discotecas","CentrosComerciales","Compras","Religion",
"Inversiones","Conferencias","Familiares","Acampar","ExcursionoViaje","Ninguno"
];

$valores="";
foreach($camposMotivo as $c){ $valores .= check($c).","; }

$textoOtros = textoOpcional($conexion,'OtrosMotivo');
$valores .= ($textoOtros!=""?1:0).",";
$valores .= "'$textoOtros'";

$sqlMotivo = "
INSERT INTO motivos
(IdDatosTurista,".implode(",",$camposMotivo).",Otros,Cuales)
VALUES('$idTurista',$valores)
";
ejecutar($conexion,$sqlMotivo,"Insert motivos");

//////////////////////////////////////////////////////
// 5️⃣ CULTURA
//////////////////////////////////////////////////////

$camposCultura=[
"Catedrales","CasasCultura","MuseosArte","MuseosArqueologicos",
"HaciendasCultura","Puentes","Monumentos","Cementerios","Santuarios","Ninguna"
];

$val="";
foreach($camposCultura as $c){ $val .= check($c).","; }

$textoCultura = textoOpcional($conexion,'CualCultura');
$val .= ($textoCultura!=""?1:0).",";
$val .= "'$textoCultura'";

$sqlCultura = "
INSERT INTO cultura
(IdDatosTurista,".implode(",",$camposCultura).",Otros,Cual)
VALUES('$idTurista',$val)
";
ejecutar($conexion,$sqlCultura,"Insert cultura");

//////////////////////////////////////////////////////
// 6️⃣ BUSCO INFO
//////////////////////////////////////////////////////

$sqlBusco = "
INSERT INTO buscoinformacion
(IdDatosTurista,OtrosTuristas,GuiasTuristicos,Amigos,Hotel,BusqueInternet,
Familiares,Nobusque,Otras,Cuales)
VALUES(
'$idTurista',
'".check('OtrosTuristas')."',
'".check('GuiasTuristicos')."',
'".check('Amigos')."',
'".check('Hotel')."',
'".check('BusqueInternet')."',
'".check('Familiares')."',
'".check('Nobusque')."',
'".(textoOpcional($conexion,'CualesBusco')!=""?1:0)."',
'".textoOpcional($conexion,'CualesBusco')."'
)";
ejecutar($conexion,$sqlBusco,"Insert busco info");

//////////////////////////////////////////////////////
// 7️⃣ COMO SE ENTERO
//////////////////////////////////////////////////////

$sqlEntero = "
INSERT INTO comoseentero
(IdDatosTurista,YaConocia,AmigosyFamiliares,BusqueInternet,MediosComunicacion,
AvisosInternet,Ninguno,Otros,Cuales)
VALUES(
'$idTurista',
'".check('YaConocia')."',
'".check('AmigosyFamiliares')."',
'".check('BusqueInternetEntero')."',
'".check('MediosComunicacion')."',
'".check('AvisosInternet')."',
'".check('NingunoEntero')."',
'".(textoOpcional($conexion,'CualesEntero')!=""?1:0)."',
'".textoOpcional($conexion,'CualesEntero')."'
)";
ejecutar($conexion,$sqlEntero,"Insert como se entero");

//////////////////////////////////////////////////////
// 8️⃣ COMPARTIO
//////////////////////////////////////////////////////

$sqlCompartio = "
INSERT INTO compartio
(IdDatosTurista,Facebook,Instagram,Twitter,Youtube,TikTok,Pinterest,
Mensajeria,NoCompartio,Otras,Cuales)
VALUES(
'$idTurista',
'".check('Facebook')."',
'".check('Instagram')."',
'".check('Twitter')."',
'".check('Youtube')."',
'".check('TikTok')."',
'".check('Pinterest')."',
'".check('Mensajeria')."',
'".check('NoCompartio')."',
'".(textoOpcional($conexion,'CualesRed')!=""?1:0)."',
'".textoOpcional($conexion,'CualesRed')."'
)";
ejecutar($conexion,$sqlCompartio,"Insert compartio");

//////////////////////////////////////////////////////
// 9️⃣ PERCEPCION
//////////////////////////////////////////////////////

$per = [
"ValoracionGeneral","LimpiezaAseaMunicipios","LimpiezaZonasNaturales",
"ActividadesCulturales","ActividadesDeportivas","Parques",
"DiscotecasBaresCasinos","EstadoCarreteras","TransporteLocal","Seguridad",
"SaborPlatosServidos","VariedadesOfertasGastronomica","TratoPersonalRestaurantes",
"HigieneLimpiezaRestaurantes","PreciosPlatos","EstadoEdificio","EstadoMuebles",
"EstadoSabanasToallas","HigieneLimpiezaAlojamiento","TratoPersonalAlojamiento",
"ServicioComidas","PreciosAlojamiento"
];

$vals="";
foreach($per as $p){ $vals.="'".($_POST[$p] ?? 0)."',"; }

$vals.="'".($_POST['Volveria'] ?? '')."',
'".($_POST['Recomendaria'] ?? '')."',
'".textoOpcional($conexion,'Recomendaciones')."'";

$sqlPer = "
INSERT INTO percepcion
(IdDatosTurista,".implode(",",$per).",Volveria,Recomendaria,Recomendaciones)
VALUES('$idTurista',$vals)
";
ejecutar($conexion,$sqlPer,"Insert percepcion");

//////////////////////////////////////////////////////
// FINAL
//////////////////////////////////////////////////////
mysqli_commit($conexion);

echo "<script>
alert('Los datos fueron guardados correctamente');
window.location.href='selectdatosturistas.php';
</script>";
exit();

} catch (Exception $e) {

    mysqli_rollback($conexion);

    $error = addslashes($e->getMessage());

    echo "<script>
    alert('❌ Error al guardar los datos:\\n\\n$error');
    window.history.back();
    </script>";
    exit();

    
}



