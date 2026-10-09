<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Solo usuarios logueados pueden borrar, y solo SU PROPIO historial
if (!isset($_SESSION['user'])) {
    echo json_encode(["ok" => false, "error" => "Sesión no iniciada."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["ok" => false, "error" => "Método no permitido."]);
    exit;
}

require "config.php";
$usuario = $_SESSION['user'];

$stmt = mysqli_prepare($conex, "DELETE FROM resultados.resultados WHERE usuario = ?");
mysqli_stmt_bind_param($stmt, "s", $usuario);

if (mysqli_stmt_execute($stmt)) {
    $borrados = mysqli_stmt_affected_rows($stmt);
    echo json_encode(["ok" => true, "borrados" => $borrados, "mensaje" => "Historial borrado."]);
} else {
    echo json_encode(["ok" => false, "error" => "Error al borrar: " . mysqli_stmt_error($stmt)]);
}

mysqli_stmt_close($stmt);
