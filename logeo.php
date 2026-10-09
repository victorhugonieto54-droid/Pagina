<?php
session_start();
require "config.php";

if (isset($_POST['enviar'])) {
    $user = trim($_POST['user']);
    $pass = $_POST['pass'];

    // Prepared statement: buscamos solo por usuario (nunca por contraseña
    // en el SQL), así el texto que escriba alguien no puede alterar la consulta.
    $consulta = "SELECT * FROM usuarios.users WHERE user = ?";
    $stmt = mysqli_prepare($conex, $consulta);
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);

        // Comparamos la contraseña contra el hash guardado, nunca en texto plano
        if (password_verify($pass, $row['pass'])) {
            session_regenerate_id(true); // evita fijación de sesión

            $_SESSION['user'] = $row['user'];

            header("Location: index.php");
            exit();
        }
    }

    // Usuario inexistente o contraseña incorrecta: mismo mensaje para los dos casos
    header("Location: login.php?error=1");
    exit();
}

// Si entran a logeo.php sin mandar el formulario, los mandamos al login
header("Location: login.php");
exit();
