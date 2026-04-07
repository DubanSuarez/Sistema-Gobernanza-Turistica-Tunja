<?php
session_start();
require_once('../Conexion/conexion.php');

$idUsuario = intval($_GET['id'] ?? 0);

if ($idUsuario <= 0) {
    echo "
        <script>
            alert('❌ ID de usuario inválido');
            window.location.href = 'UsuarioSelect.php';
        </script>
    ";
    exit();
}

/* ====== VERIFICAR SI EXISTE ====== */
$consulta = mysqli_query($conexion, "
    SELECT u.Id, r.Nombre AS NombreRol
    FROM usuario u
    INNER JOIN rol r ON u.IdRol = r.Id
    WHERE u.Id = $idUsuario
");

if (mysqli_num_rows($consulta) == 0) {

    echo "
        <script>
            alert('❌ El usuario no existe');
            window.location.href = 'UsuarioSelect.php';
        </script>
    ";
    exit();
}

$usuario = mysqli_fetch_assoc($consulta);

/* ====== EVITAR BORRAR ÚNICO ADMIN ====== */
if ($usuario['NombreRol'] == 'Administrador') {

    $admins = mysqli_query($conexion, "
        SELECT u.Id
        FROM usuario u
        INNER JOIN rol r ON u.IdRol = r.Id
        WHERE r.Nombre = 'Administrador'
    ");

    if (mysqli_num_rows($admins) <= 1) {

        echo "
            <script>
                alert('⚠️ No se puede eliminar el único Administrador del sistema');
                window.location.href = 'UsuarioSelect.php';
            </script>
        ";
        exit();
    }
}

/* ====== ELIMINAR USUARIO ====== */
$sql_delete = "DELETE FROM usuario WHERE Id = $idUsuario";

if (mysqli_query($conexion, $sql_delete)) {

    echo "
        <script>
            alert('🗑️ Usuario eliminado correctamente');
            window.location.href = 'UsuarioSelect.php';
        </script>
    ";

} else {

    echo "
        <script>
            alert('❌ Error al eliminar el usuario');
            window.location.href = 'UsuarioSelect.php';
        </script>
    ";
}
?>
