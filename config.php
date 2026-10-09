<?php
// =========================================================
// Conexión a MySQL. Se usan DOS bases de datos separadas:
//   - usuarios   -> tabla users      (login y registro)
//   - resultados -> tabla resultados (historial de la calculadora)
//
// No se selecciona ninguna base por defecto: cada consulta usa
// el nombre completo, por ejemplo "usuarios.users" o
// "resultados.resultados". Para que esto funcione, el usuario
// de MySQL que pongas acá abajo necesita permisos sobre AMBAS
// bases de datos.
// =========================================================
$conex = mysqli_connect("localhost", "root", "");

if (!$conex) {
    die("Conexión fallida: " . mysqli_connect_error());
}

mysqli_set_charset($conex, "utf8mb4");
