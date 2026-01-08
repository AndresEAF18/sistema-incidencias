<?php 
session_start();
if(!isset($_SESSION['id']) || $_SESSION['tipo'] != 'usuario'){
    header("Location: /incidencias/public/index.html");
    exit();
}

include_once "../Controllers/detalle_incidente_logica.php";

if (!$detalle) {
    echo "<script>alert('Incidente no encontrado o acceso denegado.'); window.location.href='historial_incidentes.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Incidente #<?= htmlspecialchars($detalle['idIncidente']) ?></title>
    <link rel="stylesheet" href="/incidencias/assets/css/detalle_incidente.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<div class="card">
    <h3>Resumen del Incidente</h3>
    <h3 class="titulo-incidente"><?= htmlspecialchars($detalle['titulo']) ?></h3>

    <div class="narrativa">
        <p>• Descripción: <strong> <?= htmlspecialchars($detalle['descripcion']) ?> </strong></p>
        <div class="info-grid">
            <div class="info-item">• Categoría: <strong><?= $detalle['categoria'] ?></strong></div>
            <div class="info-item">• Subcategoría: <strong><?= $detalle['subcategoria'] ?></strong></div>
            <div class="info-item">• Fecha: <strong><?= $detalle['fechaRegistro'] ?></strong></div>
            <div class="info-item">• Prioridad: 
                <span class="badge-prioridad prio-<?= $detalle['idPrioridad'] ?>">
                    <?= ($detalle['idPrioridad']==1?"Alta":($detalle['idPrioridad']==2?"Media":"Baja")) ?>
                </span>
            </div>
        </div>

        <div id="map" style="height:250px; width:100%; margin:15px 0; border-radius: 8px;"></div>
        
        <?php 
            $coords = explode(',', $detalle['ubicacion']);
            $lat = trim($coords[0] ?? 0);
            $lng = trim($coords[1] ?? 0);
        ?>
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script>
            var map = L.map('map').setView([<?= $lat ?>, <?= $lng ?>], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            L.marker([<?= $lat ?>, <?= $lng ?>]).addTo(map);
        </script>
    </div>

    <hr>

    <h3 class="titulo-responsable">Seguimiento en Tiempo Real</h3>

    <div class="estado-box estado-<?= strtolower(str_replace(' ', '-', $detalle['estado'])) ?>">
        
        <p style="font-size: 1.1em;"><strong>Responsable Asignado:</strong> <?= htmlspecialchars($detalle['responsable'] ?: 'Pendiente') ?></p>
        <p style="font-size: 1.2em;"><strong>Estado Actual:</strong> <span class="badge-estado"><?= strtoupper($detalle['estado']) ?></span></p>

        <hr style="opacity: 0.2;">

        <?php if ($detalle['estado'] == "Pendiente"): ?>
            <p><strong>El sistema ha recibido tu reporte, el responsable está revisando los detalles y el análisis de riesgo para asignar unidades.</strong></p>
            <?php if(!empty($detalle['comentario'])): ?>
                <p><strong>Comentario inicial:</strong> <?= htmlspecialchars($detalle['comentario']) ?></p>
            <?php endif; ?>

        <?php elseif ($detalle['estado'] == "En Proceso"): ?>
            <p><i class="fas fa-running"></i> <strong>¡Tu incidente está siendo atendido!</strong></p>
            <?php if(!empty($detalle['agente_encargado'])): ?>
                <p>• <strong>Agente a cargo:</strong> <?= htmlspecialchars($detalle['agente_encargado']) ?></p>
            <?php endif; ?>
            <?php if(!empty($detalle['unidad_id'])): ?>
                <p>• <strong>Unidad asignada:</strong> <?= htmlspecialchars($detalle['unidad_id']) ?></p>
            <?php endif; ?>
            <?php if(!empty($detalle['accionRealizada'])): ?>
                <p>• <strong>Acciones actuales:</strong> <?= nl2br(htmlspecialchars($detalle['accionRealizada'])) ?></p>
            <?php endif; ?>

        <?php elseif ($detalle['estado'] == "No Resuelto"): ?>
            <p><i class="fas fa-exclamation-triangle"></i> <strong>Atención: El proceso se ha detenido.</strong></p>
            <p>• <strong>Agente a cargo:</strong> <?= htmlspecialchars($detalle['agente_encargado']) ?></p>
            
            <p>• <strong>Unidad asignada:</strong> <?= htmlspecialchars($detalle['unidad_id']) ?></p>

            <p>• <strong>Motivo del cierre:</strong> <?= htmlspecialchars($detalle['motivoPausa'] ?? 'No especificado') ?></p>
            <?php if(!empty($detalle['observacionesFinales'])): ?>
                <p>• <strong>Detalles adicionales:</strong> <?= nl2br(htmlspecialchars($detalle['observacionesFinales'])) ?></p>
            <?php endif; ?>

        <?php elseif ($detalle['estado'] == "Resuelto"): ?>
            <p><i class="fas fa-check-circle"></i> <strong>Incidente Finalizado con éxito.</strong></p>
            <p>• <strong>Agente a cargo:</strong> <?= htmlspecialchars($detalle['agente_encargado']) ?></p>
            
            <p>• <strong>Unidad asignada:</strong> <?= htmlspecialchars($detalle['unidad_id']) ?></p>
            <p>• <strong>Resultado final:</strong> <?= nl2br(htmlspecialchars($detalle['resultadoObtenido'])) ?></p>
            <p>• <strong>Fecha de cierre:</strong> <?= $detalle['fechaCierre'] ?></p>
            
            <?php if (!empty($detalle['acta_ruta'])): ?>
                <div style="margin-top: 15px;">
                    <a href="<?= htmlspecialchars($detalle['acta_ruta']) ?>" target="_blank" class="btn-acta">
                        📂 Ver Acta de Resolución Final
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <a class="btn" href="historial_incidentes.php" style="display: block; text-align: center;">Volver al Historial</a>
</div>

</body>
</html>