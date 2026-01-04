<?php
session_start();

// 1. Seguridad: Redirección al login si no hay sesión
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'usuario') {
    header("Location: /incidencias/public/index.html");
    exit();
}

// 2. Lógica: Ruta corregida al controlador de notificaciones y estadísticas
include_once "../Controllers/notificaciones_usuario.php"; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Usuario - Sistema de Incidencias</title>
    <link rel="stylesheet" href="/incidencias/assets/css/panel_usuario.css">
</head>
<body>

<div class="topbar">
    <div class="logo">Sistema de Incidencias</div>

    <div class="actions">
        <a href="notificaciones.php" class="notif" style="position: relative;">
            <img src="/incidencias/assets/img/notificacion.png" alt="Notificaciones">
            <span id="badgeNotificaciones"><?php echo $notificacionesNoLeidas ?? 0; ?></span>
        </a>

        <a href="/incidencias/src/Controllers/logout.php" class="logout">
            Cerrar sesión
        </a>
    </div>
</div>

<div class="layout">

    <div class="container">
        <div class="titulo-bienvenido">
            Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
        </div>

        <div class="separador"></div>

        <div class="botones-panel">
            <a class="btn-panel" href="registrar_incidente.php">
                <img src="/incidencias/assets/img/anadir.png">
                Registrar Incidente
            </a>

            <a class="btn-panel" href="historial_incidentes.php">
                <img src="/incidencias/assets/img/lista.png">
                Visualizar Historial
            </a>
        </div>
    </div>

    <div class="card-container">
        <div class="card">
            <h3>Incidentes Reportados</h3>
            <p class="numero" style="color: #1417aa;"><?php echo $totalIncidentes ?? 0; ?></p>
        </div>

        <div class="card">
            <h3>Incidencias Resueltas</h3>
            <p class="numero" style="color: #2aaf2a;"><?php echo $totalResueltas ?? 0; ?></p>
        </div>

        <div class="card">
            <h3>Incidencias Pendientes</h3>
            <p class="numero" style="color: #e67e22;"><?php echo $totalPendientes ?? 0; ?></p>
        </div>

        <div class="card">
            <h3>Resolución</h3>
            <p class="numero" style="color: #222;"><?php echo $porcentajeResueltas ?? 0; ?>%</p>
        </div>
    </div>
</div>

<div class="panel-grafica-layout">

    <div class="mini-panel">
        <h3>Últimos incidentes reportados</h3>
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
              <?php if (!empty($incidentesRecientes)): ?>
                  <?php foreach (array_slice($incidentesRecientes, 0, 3) as $incidente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($incidente['titulo']); ?></td>
                            <td><?php echo $incidente['estado']; ?></td>
                            <td><?php echo date("d/m/y", strtotime($incidente['fechaRegistro'])); ?></td>
                        </tr>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr><td colspan="3">No hay reportes recientes.</td></tr>
              <?php endif; ?>
            </tbody>
        </table>

        <div class="mini-panel-btn">
            <a href="historial_incidentes.php">Ver historial completo</a>
        </div>
    </div>

    <div class="graficas-layout">
        <div class="grafica-card">
            <h3>Incidencias por Categoría</h3>
            <canvas id="grafIncidencias"></canvas>
        </div>

        <div class="grafica-card adicionales">
            <h3>Comparativa Mensual</h3>
            <canvas id="grafMensual"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfica de Incidencias por Categoría
    const ctxCat = document.getElementById('grafIncidencias').getContext('2d');
    new Chart(ctxCat, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($categorias ?? []); ?>,
            datasets: [{
                label: 'Cantidad',
                data: <?php echo json_encode($valoresCategorias ?? []); ?>,
                backgroundColor: 'rgba(29,184,171,0.7)',
                borderColor: 'rgba(29,184,171,1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Gráfica Comparativa Mensual
    const ctxMes = document.getElementById('grafMensual').getContext('2d');
    new Chart(ctxMes, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($meses ?? []); ?>,
            datasets: [{
                label: 'Reportes',
                data: <?php echo json_encode($valoresMes ?? []); ?>,
                backgroundColor: 'rgba(52, 152, 219, 0.2)',
                borderColor: 'rgba(52, 152, 219, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>

</body>
</html>