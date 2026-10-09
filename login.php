<?php
session_start();
require "config.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ingresar - Calculadora de Redes</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="fontawesome-free-5.12.1-web/css/all.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-body">

<div class="container d-flex align-items-center justify-content-center min-vh-100">
<?php if (isset($_SESSION['user'])): ?>

    <div class="card text-center shadow auth-card">
        <div class="card-body">
            <i class="fas fa-user-check fa-2x mb-3"></i>
            <h4 class="card-title">Ya iniciaste sesión</h4>
            <p class="card-text">Hola <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>, ya tenés una sesión activa.</p>
            <a href="index.php" class="btn btn-primary btn-block mb-2"><i class="fas fa-calculator"></i> Ir a la calculadora</a>
            <a href="logout.php" class="btn btn-outline-danger btn-block"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
        </div>
    </div>

<?php else: ?>

    <div class="card shadow auth-card">
        <div class="card-body">
            <h4 class="card-title text-center mb-4"><i class="fas fa-network-wired"></i> Iniciar sesión</h4>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger py-2"><i class="fas fa-exclamation-triangle"></i> Usuario o contraseña incorrectos.</div>
            <?php endif; ?>

            <form method="post" action="logeo.php">
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
                <button type="submit" name="enviar" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Acceder</button>
            </form>

            <a href="registro.php" class="d-block text-center mt-3"><i class="fas fa-user-plus"></i> Crear nuevo usuario</a>
        </div>
    </div>

<?php endif; ?>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/auth.js"></script>
</body>
</html>
