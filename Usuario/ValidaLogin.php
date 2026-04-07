<?php
session_start();
require_once('../Conexion/conexion.php');

if (isset($_POST['txtusr'])) {

    $txtusuario = mysqli_real_escape_string($conexion, $_POST['txtusr']);
    $txtcontra = $_POST['txtpwd'];

    // Buscar usuario por email
    $consulta = "SELECT * FROM usuario WHERE Email='$txtusuario' LIMIT 1";
    $sql = mysqli_query($conexion, $consulta);

    if (mysqli_num_rows($sql) > 0) {

        $res = mysqli_fetch_assoc($sql);

        // Validar contraseña con password_verify
        if (password_verify($txtcontra, $res['Contrasena'])) {

            // Guardar datos en sesión
            $_SESSION['rol'] = $res['IdRol'];
            $_SESSION['id'] = $res['Id'];
            $_SESSION['nombres'] = $res['Nombres'];
            $_SESSION['apellidos'] = $res['Apellidos'];
            $_SESSION['documento'] = $res['Documento'];
            $_SESSION['fnacimiento'] = $res['FechaNacimiento'];
            $_SESSION['telefono'] = $res['Telefono'];
            $_SESSION['email'] = $res['Email'];

            // Redirección según rol
            switch ($res['IdRol']) {

                case 1:
                    header('Location: Dashboard.php');
                    break;

                case 2:
                    header('Location: ../Analista/DashboardAnalista.php');
                    break;

                case 3:
                    header('Location: ../Encuestador/DashboardEncuestador.php');
                    break;

                default:
                    header('Location: InicioSesion.php');
                    break;
            }

            exit();

        } else {

            $_SESSION['Error'] = "Contraseña incorrecta";
            header('Location: InicioSesion.php');
            exit();
        }

    } else {

        $_SESSION['Error'] = "Usuario no encontrado";
        header('Location: InicioSesion.php');
        exit();
    }
}
?>