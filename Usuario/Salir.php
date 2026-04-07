<?php
session_start();
require_once('../Conexion/conexion.php');

session_destroy();
header('location: InicioSesion.php');
?>