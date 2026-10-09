<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Solo usuarios logueados pueden guardar en el historial
if (!isset($_SESSION['user'])) {
    echo json_encode(["ok" => false, "error" => "Sesión no iniciada."]);
    exit;
}

// Conexión a la base de datos (centralizada en config.php)
require "config.php";
$conexion = $conex;
$usuario = $_SESSION['user'];

if (
    isset($_POST['ipt']) &&
    isset($_POST['mkt']) &&
    isset($_POST['red']) &&
    isset($_POST['broadcast']) &&
    isset($_POST['clase'])
) {
    $ipt       = trim($_POST['ipt']);
    $mkt       = trim($_POST['mkt']);
    $red       = trim($_POST['red']);
    $broadcast = trim($_POST['broadcast']);
    $clase     = trim($_POST['clase']);

    // Validación básica: formato IP (x.x.x.x) para los 4 campos de red
    $patronIp = '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/';
    if (!preg_match($patronIp, $ipt) || !preg_match($patronIp, $mkt) ||
        !preg_match($patronIp, $red) || !preg_match($patronIp, $broadcast) ||
        !in_array($clase, ['A', 'B', 'C'], true)) {
        echo json_encode(["ok" => false, "error" => "Datos con formato inválido."]);
        mysqli_close($conexion);
        exit;
    }

    $sql = "INSERT INTO resultados.resultados (usuario, IP, MDR, CLA, RED, BCAST) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssss", $usuario, $ipt, $mkt, $clase, $red, $broadcast);
        $ok = mysqli_stmt_execute($stmt);

        if ($ok) {
            echo json_encode(["ok" => true, "id" => mysqli_insert_id($conexion), "mensaje" => "Registro guardado."]);
        } else {
            echo json_encode(["ok" => false, "error" => "Error al insertar: " . mysqli_stmt_error($stmt)]);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["ok" => false, "error" => "Error preparando la consulta: " . mysqli_error($conexion)]);
    }
} else {
    echo json_encode(["ok" => false, "error" => "Faltan parámetros.", "recibido" => $_POST]);
}

mysqli_close($conexion);
