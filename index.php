<?php
session_start();
// Si no hay sesión iniciada, no se puede ver la calculadora
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Calculadora de Redes</title>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="fontawesome-free-5.12.1-web/css/all.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/auth.css">
</head>

<body>

<header class="top">
    <div class="topbar-row">
        <div>
            <div class="eyebrow">Redes &amp; Subneteo</div>
            <h1>Calculadora de Redes</h1>
            <p>Cargá la IP y la máscara para obtener red, broadcast y el detalle de subredes.</p>
        </div>
        <div class="user-box">
            <span>👤 <?= htmlspecialchars($_SESSION['user']) ?></span>
            <a href="logout.php" class="btn-logout">Salir</a>
        </div>
    </div>
</header>

<div class="layout">

    <!-- COLUMNA 1: DATOS -->
    <section class="panel">
        <h2><span class="n">01</span> Datos de entrada</h2>

        <div class="input-group-custom">
            <label>Dirección IP</label>
            <div class="ip-container">
                <input type="number" id="ip1" class="ip-octet" placeholder="192" onkeydown="return soloNumeros(event)">
                <span class="dot">.</span>
                <input type="number" id="ip2" class="ip-octet" placeholder="168" onkeydown="return soloNumeros(event)">
                <span class="dot">.</span>
                <input type="number" id="ip3" class="ip-octet" placeholder="10" onkeydown="return soloNumeros(event)">
                <span class="dot">.</span>
                <input type="number" id="ip4" class="ip-octet" placeholder="0" onkeydown="return soloNumeros(event)">
            </div>
        </div>

        <div class="input-group-custom">
            <label>Máscara de subred</label>
            <div class="ip-container">
                <input type="number" id="m1" class="ip-octet" placeholder="255" onkeydown="return soloNumeros(event)">
                <span class="dot">.</span>
                <input type="number" id="m2" class="ip-octet" placeholder="255" onkeydown="return soloNumeros(event)">
                <span class="dot">.</span>
                <input type="number" id="m3" class="ip-octet" placeholder="255" onkeydown="return soloNumeros(event)">
                <span class="dot">.</span>
                <input type="number" id="m4" class="ip-octet" placeholder="192" onkeydown="return soloNumeros(event)">
            </div>
        </div>

        <button class="btn-calc" onclick="calcular()">Calcular</button>
    </section>

    <!-- COLUMNA 2: RESULTADOS -->
    <section class="panel results-panel" id="resultsPanel">
        <h2><span class="n">02</span> Resultados</h2>

        <div id="resultadoVacio" class="results-empty">
            <span class="wave">〜</span> Esperando datos para calcular…
        </div>

        <div id="resultadoContenido" style="display:none;">
            <div class="result-grid">
                <div class="result-card accent"><div class="k">Dirección de red</div><div class="v" id="rRed">—</div></div>
                <div class="result-card accent"><div class="k">Broadcast</div><div class="v" id="rBroadcast">—</div></div>
                <div class="result-card"><div class="k">IP ingresada</div><div class="v" id="rIp">—</div></div>
                <div class="result-card"><div class="k">Máscara</div><div class="v" id="rMask">—</div></div>
                <div class="result-card"><div class="k">Clase de red</div><div class="v" id="rClase">—</div></div>
                <div class="result-card"><div class="k">Tipo de máscara</div><div class="v" id="rTipo">—</div></div>
                <div class="result-card"><div class="k">Subredes (2ⁿ)</div><div class="v" id="rSubredes">—</div></div>
                <div class="result-card"><div class="k">Hosts totales (2ʰ)</div><div class="v" id="rHostsTot">—</div></div>
                <div class="result-card ok"><div class="k">Hosts disponibles</div><div class="v" id="rHostsDisp">—</div></div>
            </div>

            <hr class="divider">

            <div class="bits-section">
                <div class="label-row">
                    <span>Mapa de bits de la máscara (32 bits)</span>
                    <span id="rBitsResumen">—</span>
                </div>
                <div class="bits-row" id="bitsRow"></div>
                <div class="bits-legend">
                    <span><i class="dotleg" style="background:var(--foam); box-shadow:0 0 6px var(--foam);"></i>Red</span>
                    <span><i class="dotleg" style="background:#2a5673;"></i>Host</span>
                </div>
            </div>
        </div>
    </section>

    <!-- COLUMNA 3: HISTORIAL -->
    <section class="panel history-panel" id="historyPanel">
        <h2><span class="n">03</span> Historial</h2>
        <div id="historyList" class="history-list">
            <div class="history-empty">Todavía no calculaste nada.</div>
        </div>
        <button class="btn-clear" onclick="limpiarHistorial()">Vaciar vista</button>
        <button class="btn-clear btn-clear-danger" onclick="borrarHistorial()">Borrar historial guardado</button>
    </section>

</div>

<script type="text/javascript" src="js/jquery.min.js"></script>
<!-- Bootstrap tooltips -->
<script type="text/javascript" src="js/popper.min.js"></script>
<!-- Bootstrap core JavaScript -->
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<!-- Your custom scripts (optional) -->
<script type="text/javascript" src="js/imagenCarga.js"></script>
<!-- <script src="escuela/js/imprimir.js"></script> -->
<script type="text/javascript" src="js/jquery.progressbar.min.js"></script>

<script src="js/script.js"></script>

</body>
</html>
