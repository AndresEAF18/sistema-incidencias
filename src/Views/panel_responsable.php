<?php
session_start();

// 1. Seguridad: Solo responsables
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'responsable') {
    header("Location: /incidencias/public/index.html");
    exit();
}

// 2. Lógica: Ruta corregida al controlador del responsable
// Este controlador debe calcular $totalAsignadas, $totalPendientes, $totalResueltas, etc.
include_once "../Controllers/notificaciones_res.php"; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Responsable - Sistema de Incidencias</title>
    <link rel="stylesheet" href="/incidencias/assets/css/paneles.css">
</head>
<body>

<div class="topbar">
    <div class="logo">Sistema de Incidencias</div>

    <div class="actions">
        <a href="notificaciones_responsable.php" class="notif" style="position: relative;">
            <img src="/incidencias/assets/img/notificacion.png" alt="Notificaciones">
            <span id="badgeNotificaciones"><?php echo $notificacionesNoLeidas ?? 0; ?></span>
        </a>

        <a href="/incidencias/src/Controllers/logout.php" class="logout">Cerrar sesión</a>
    </div>
</div>

<div class="container">
    <h2 class="titulo-bienvenido">Bienvenido, Agente <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>

    <div class="separador"></div>

    <div class="botones-panel">
        <a href="incidentes_asignados.php" class="btn-panel">
            <img src="/incidencias/assets/img/incidentes.png" alt="Icono"> Visualizar Incidentes Asignados
        </a>
    </div>
</div>

<div class="card-container">
    <div class="card">
        <h3>Incidentes Asignados</h3>
        <p class="numero" style="color: #1417aa;"><?php echo $totalAsignadas ?? 0; ?></p>
    </div>

    <div class="card">
        <h3>Incidentes Pendientes</h3>
        <p class="numero" style="color: #e67e22;"><?php echo $totalPendientes ?? 0; ?></p>
    </div>

    <div class="card">
        <h3>Incidentes Resueltos</h3>
        <p class="numero" style="color: #2aaf2a;"><?php echo $totalResueltas ?? 0; ?></p>
    </div>

    <!-- Porcentaje de Resolución -->
    <div class="card">
        <h3>Porcentaje de Resolución</h3>
        <p class="numero">
            <?php
                if ($totalIncidentes > 0) {
                    echo round(($totalResueltas / $totalIncidentes) * 100, 1) . "%";
                } else {
                    echo "0%";
                }
            ?>
        </p>
    </div>

</div>

</div>

<div class="panel-grafica-layout">

    <div class="mini-panel">
        <h3>Últimos incidentes asignados</h3>
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
              <?php if(!empty($incidentesRecientes)): ?>
                  <?php foreach (array_slice($incidentesRecientes, 0, 3) as $incidente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($incidente['titulo']); ?></td>
                            <td><?php echo $incidente['estado']; ?></td>
                            <td><?php echo date("d/m/y", strtotime($incidente['fechaRegistro'])); ?></td>
                        </tr>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr><td colspan="3">No hay incidentes pendientes.</td></tr>
              <?php endif; ?>
            </tbody>
        </table>

        <div class="mini-panel-btn">
            <a href="incidentes_asignados.php">Ver todos</a>
        </div>
    </div>

    <div class="graficas-layout">
        <div class="grafica-card adicionales">
            <h3>Comparativa Mensual</h3>
            <canvas id="grafMensual"></canvas>
        </div>

        <div class="grafica-card adicionales">
            <h3>Asignados (Últimos 7 Días)</h3>
            <canvas id="graficoAsignadosDia"></canvas>
        </div>

        <div class="grafica-card">
            <h3>Eficiencia Global</h3>
            <canvas id="grafEficienciaGlobal"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Gráfica Comparativa Mensual
    new Chart(document.getElementById('grafMensual'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($meses ?? []); ?>,
            datasets: [{
                label: 'Incidencias',
                data: <?php echo json_encode($valoresMes ?? []); ?>,
                backgroundColor: 'rgba(75, 115, 141, 0.2)',
                borderColor: 'rgba(29, 184, 171, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        }
    });

    // Incidentes asignados últimos 7 días
    new Chart(document.getElementById('graficoAsignadosDia'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($fechas ?? []); ?>,
            datasets: [{
                label: 'Asignados',
                data: <?php echo json_encode($totales ?? []); ?>,
                backgroundColor: 'rgba(29,184,171,0.7)'
            }]
        }
    });

    // Eficiencia Global (Semáforo)
const eficiencia = <?php echo $eficienciaGlobal; ?>;

// Color semáforo
let color;
if (eficiencia >= 100) {
    color = 'rgba(46, 204, 113, 0.9)'; // verde
} else if (eficiencia >= 70) {
    color = 'rgba(241, 196, 15, 0.9)'; // amarillo
} else {
    color = 'rgba(231, 76, 60, 0.9)'; // rojo
}

const ctx = document.getElementById('grafEficienciaGlobal').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Eficiencia Global'],
        datasets: [{
            data: [Math.min(eficiencia, 100)], // nunca pasa de 100 visualmente
            backgroundColor: [color],
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${eficiencia}% de eficiencia (100% = tiempo óptimo de resolución)`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                },
                title: {
                    display: true,
                    text: 'Eficiencia (%)'
                }
            }
        }
    }
});
</script>


</body>
</html>