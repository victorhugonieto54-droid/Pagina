<?php
session_start();
require "config.php";

$error = "";

if (isset($_POST['registrar'])) {
    $user  = trim($_POST['user'] ?? '');
    $pass  = $_POST['pass'] ?? '';
    $pass2 = $_POST['pass2'] ?? '';

    if ($user === '' || $pass === '') {
        $error = "Completá usuario y contraseña.";
    } elseif (strlen($pass) < 4) {
        $error = "La contraseña tiene que tener al menos 4 caracteres.";
    } elseif ($pass !== $pass2) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Verificar si el usuario ya existe (prepared statement)
        $verificar = mysqli_prepare($conex, "SELECT id FROM usuarios.users WHERE user = ?");
        mysqli_stmt_bind_param($verificar, "s", $user);
        mysqli_stmt_execute($verificar);
        mysqli_stmt_store_result($verificar);

        if (mysqli_stmt_num_rows($verificar) > 0) {
            $error = "El nombre de usuario ya está en uso. Elegí otro.";
        } else {
            // Hasheamos la contraseña, nunca se guarda en texto plano
            $hash = password_hash($pass, PASSWORD_DEFAULT);

            $insertar = mysqli_prepare($conex, "INSERT INTO usuarios.users (user, pass) VALUES (?, ?)");
            mysqli_stmt_bind_param($insertar, "ss", $user, $hash);

            if (mysqli_stmt_execute($insertar)) {
                // Sesión iniciada automáticamente y lo mandamos a la calculadora
                session_regenerate_id(true);
                $_SESSION['user'] = $user;
                header("Location: index.php");
                exit();
            } else {
                $error = "Error al crear la cuenta.";
            }
            mysqli_stmt_close($insertar);
        }
        mysqli_stmt_close($verificar);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear cuenta - Calculadora de Redes</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="fontawesome-free-5.12.1-web/css/all.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-body">

<div class="container d-flex align-items-center justify-content-center min-vh-100">

    <div class="card shadow auth-card">
        <div class="card-body">
            <h4 class="card-title text-center mb-4"><i class="fas fa-user-plus"></i> Crear cuenta</h4>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label>Usuario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" name="user" class="form-control" autofocus required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="pass" id="pass" class="form-control" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary toggle-pass-btn" data-target="pass" tabindex="-1"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Repetir contraseña</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="pass2" id="pass2" class="form-control" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary toggle-pass-btn" data-target="pass2" tabindex="-1"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>
                <button type="submit" name="registrar" class="btn btn-primary btn-block"><i class="fas fa-user-plus"></i> Registrarse</button>
            </form>

            <a href="login.php" class="d-block text-center mt-3"><i class="fas fa-arrow-left"></i> Volver al login</a>
        </div>
    </div>

</div>

<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/auth.js"></script>
</body>
</html>
