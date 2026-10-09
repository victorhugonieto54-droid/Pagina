<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Solo usuarios logueados pueden ver su propio historial
if (!isset($_SESSION['user'])) {
    echo json_encode(["ok" => false, "error" => "Sesión no iniciada."]);
    exit;
}

require "config.php";
$usuario = $_SESSION['user'];

// Traemos los últimos registros de ESTE usuario, más nuevos primero
$stmt = mysqli_prepare(
    $conex,
    "SELECT IP, MDR, CLA, RED, BCAST, creado_en
     FROM resultados.resultados
     WHERE usuario = ?
     ORDER BY creado_en DESC, id DESC
     LIMIT 20"
);
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$registros = [];
while ($row = mysqli_fetch_assoc($resultado)) {
    $registros[] = [
        "ip"        => $row['IP'],
        "mask"      => $row['MDR'],
        "clase"     => $row['CLA'],
        "red"       => $row['RED'],
        "broadcast" => $row['BCAST'],
        "fecha"     => $row['creado_en'],
    ];
}

mysqli_stmt_close($stmt);
echo json_encode(["ok" => true, "registros" => $registros]);
