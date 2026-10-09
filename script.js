let historial = [];

// Convierte la fecha que manda MySQL ("YYYY-MM-DD HH:MM:SS") a un
// formato corto y legible ("02/10 14:23")
function formatFechaServidor(fechaSql) {
    const d = new Date(fechaSql.replace(' ', 'T'));
    if (isNaN(d.getTime())) return fechaSql;
    return d.toLocaleString('es-AR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
}

// Trae del servidor el historial ya guardado del usuario logueado
// (así no se pierde al cerrar sesión y volver a entrar)
function cargarHistorialServidor() {
    $.getJSON("obtener_historial.php", function(data) {
        if (!data.ok) {
            console.error("No se pudo cargar el historial:", data.error);
            return;
        }
        historial = data.registros.map(r => ({
            ip: r.ip,
            mask: r.mask,
            red: r.red,
            broadcast: r.broadcast,
            clase: r.clase,
            hora: formatFechaServidor(r.fecha)
        }));
        renderHistorial();
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error trayendo el historial:", textStatus, errorThrown);
    });
}

function soloNumeros(e) {
    let key = e.keyCode || e.which;
    if ([8, 9, 13, 27, 37, 38, 39, 40].indexOf(key) !== -1 ||
        (key >= 48 && key <= 57) || (key >= 96 && key <= 105)) {
        return true;
    }
    return false;
}

function calcular() {
    const ip = [
        parseInt(document.getElementById("ip1").value) || 0,
        parseInt(document.getElementById("ip2").value) || 0,
        parseInt(document.getElementById("ip3").value) || 0,
        parseInt(document.getElementById("ip4").value) || 0
    ];

    const mask = [
        parseInt(document.getElementById("m1").value) || 0,
        parseInt(document.getElementById("m2").value) || 0,
        parseInt(document.getElementById("m3").value) || 0,
        parseInt(document.getElementById("m4").value) || 0
    ];

    let clase = "";
    let mascaraBase = "";
    let bitsRedBase = 0;

    if (ip[0] >= 1 && ip[0] <= 126) {
        clase = "A"; mascaraBase = "255.0.0.0"; bitsRedBase = 8;
    } else if (ip[0] >= 128 && ip[0] <= 191) {
        clase = "B"; mascaraBase = "255.255.0.0"; bitsRedBase = 16;
    } else if (ip[0] >= 192 && ip[0] <= 223) {
        clase = "C"; mascaraBase = "255.255.255.0"; bitsRedBase = 24;
    } else {
        document.getElementById("resultadoVacio").style.display = "flex";
        document.getElementById("resultadoContenido").style.display = "none";
        document.getElementById("resultadoVacio").innerHTML = "IP fuera de rango.";
        return;
    }

    let maskBinary = mask.map(o => o.toString(2).padStart(8, '0')).join('');
    let bitsMascaraTotal = (maskBinary.match(/1/g) || []).length;
    let n = bitsMascaraTotal - bitsRedBase;
    let h = 32 - bitsMascaraTotal;


    let red = [];
    let broadcast = [];
    for (let i = 0; i < 4; i++) {
        red.push(ip[i] & mask[i]);
        broadcast.push(red[i] | (255 - mask[i]));
    }

    let numSubredes = Math.pow(2, Math.max(0, n));
    let numHostsTotales = Math.pow(2, h);
    let numHostsDisponibles = numHostsTotales - 2;
    let tipoMascara = mask.join('.') === mascaraBase ? 'Base' : 'Trabajada';

    // Pintar resultados
    document.getElementById("resultadoVacio").style.display = "none";
    document.getElementById("resultadoContenido").style.display = "block";

    document.getElementById("rRed").textContent = red.join('.');
    document.getElementById("rBroadcast").textContent = broadcast.join('.');
    document.getElementById("rIp").textContent = ip.join('.');
    document.getElementById("rMask").textContent = mask.join('.');
    document.getElementById("rClase").textContent = clase;
    document.getElementById("rTipo").textContent = tipoMascara;
    document.getElementById("rSubredes").textContent = n > 0 ? numSubredes : 1;
    document.getElementById("rHostsTot").textContent = numHostsTotales;
    document.getElementById("rHostsDisp").textContent = numHostsDisponibles < 0 ? 0 : numHostsDisponibles;
    document.getElementById("rBitsResumen").textContent = `/${bitsMascaraTotal} (red: ${bitsMascaraTotal} · host: ${h})`;

    // Mapa de bits
    const bitsRow = document.getElementById("bitsRow");
    bitsRow.innerHTML = "";
    for (let i = 0; i < 32; i++) {
        const bit = document.createElement("div");
        bit.className = "bit " + (i < bitsMascaraTotal ? "net" : "host");
        bitsRow.appendChild(bit);
    }

    // Strings listos para mostrar y para mandar al servidor
    const ipt = ip.join('.');
    const mkt = mask.join('.');
    const redStr = red.join('.');
    const broadcastStr = broadcast.join('.');

    // Vista local inmediata (optimista): se agrega arriba de todo
    // mientras esperamos la confirmación del servidor.
    const ahora = new Date();
    const hora = ahora.toLocaleString('es-AR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
    historial.unshift({
        ip: ipt,
        mask: mkt,
        red: redStr,
        broadcast: broadcastStr,
        clase,
        hora
    });
    renderHistorial();

    // Un solo POST al servidor, con red/broadcast ya como strings.
    // El servidor guarda también el usuario logueado, así este
    // registro va a seguir apareciendo la próxima vez que entre.
    $.post("cargar_historial.php", { ipt, mkt, red: redStr, broadcast: broadcastStr, clase }, function(response) {
        if (!response.ok) {
            console.error("El servidor no pudo guardar el registro:", response.error);
            // Si no se pudo guardar, refrescamos desde el servidor para
            // no mostrar en pantalla algo que en realidad no quedó guardado.
            cargarHistorialServidor();
        }
    }, "json").fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error guardando el historial en el servidor:", textStatus, errorThrown);
        cargarHistorialServidor();
    });

    // Recién ahora la columna de resultados tiene su altura final
    sincronizarAlturaHistorial();
}

function renderHistorial() {
    const cont = document.getElementById("historyList");
    if (historial.length === 0) {
        cont.innerHTML = '<div class="history-empty">Todavía no calculaste nada.</div>';
        return;
    }
    cont.innerHTML = historial.map(h => `
        <div class="history-item">
            <div class="h-ip">${h.ip} /${h.mask}</div>
            <div class="h-meta">Red: ${h.red}<br>Bcast: ${h.broadcast} · Clase ${h.clase}</div>
            <div class="h-time">${h.hora}</div>
        </div>
    `).join('');
}

// Esto solo limpia lo que se ve en pantalla en este momento.
// Los registros siguen guardados en el servidor: si recargás la
// página o volvés a entrar, el historial real va a reaparecer.
function limpiarHistorial() {
    historial = [];
    renderHistorial();
}

// Borra DE VERDAD el historial guardado en el servidor, pero solo
// si el usuario confirma explícitamente en el diálogo.
function borrarHistorial() {
    const confirmado = confirm("¿Borrar TODO tu historial guardado? Esta acción no se puede deshacer.");
    if (!confirmado) return;

    $.post("borrar_historial.php", {}, function(data) {
        if (data.ok) {
            historial = [];
            renderHistorial();
            toastSimple(`Se borraron ${data.borrados} registro(s).`);
        } else {
            toastSimple("No se pudo borrar: " + data.error, true);
        }
    }, "json").fail(function() {
        toastSimple("Error de conexión al borrar el historial.", true);
    });
}

// Mensaje simple y descartable, sin depender de ninguna librería
function toastSimple(mensaje, esError = false) {
    alert(mensaje); // placeholder simple; se puede reemplazar por un toast visual si se desea
}

function sincronizarAlturaHistorial() {
    const resultsPanel = document.getElementById("resultsPanel");
    const historyPanel = document.getElementById("historyPanel");
    if (!resultsPanel || !historyPanel) return;

    if (window.innerWidth <= 980) {
        // En mobile las columnas se apilan: sin altura forzada
        historyPanel.style.height = "auto";
        return;
    }
    historyPanel.style.height = resultsPanel.offsetHeight + "px";
}

window.addEventListener("load", sincronizarAlturaHistorial);
window.addEventListener("resize", sincronizarAlturaHistorial);
window.addEventListener("load", cargarHistorialServidor);
