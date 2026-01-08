<?php 
// 1. El controlador carga la variable $detalle y maneja el POST
include_once "../Controllers/detalle_incidente_logica_responsable.php";

// LÓGICA DE BLOQUEO: Si el estado ya es Resuelto, bloqueamos el formulario
$estaResuelto = ($detalle['estado'] === 'Resuelto');
$atributoBloqueo = $estaResuelto ? 'disabled' : '';
$readonlyBloqueo = $estaResuelto ? 'readonly' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Profesional de Incidentes</title>
    <link rel="stylesheet" href="/incidencias/assets/css/detalle_incidente.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    
</head>
<body>

<div class="card">
    <h3 class="titulo-incidente">Resumen del Incidente: <?= htmlspecialchars($detalle['titulo']) ?></h3>

    <?php if ($estaResuelto): ?>
        <div class="mensaje-bloqueo">
            ⚠ Este incidente ha sido RESUELTO.
        </div>
    <?php endif; ?>

    <div class="narrativa">
        <p>• <strong>Descripción del Reporte:</strong> <?= htmlspecialchars($detalle['descripcion']) ?></p>
        <p>• <strong>Categoría:</strong> <?= htmlspecialchars($detalle['categoria']) ?></p>
        <p>• <strong>Subcategoría:</strong> <?= htmlspecialchars($detalle['subcategoria']) ?></p>
        <p>• <strong>Reportado por:</strong> <?= htmlspecialchars($detalle['usuario']) ?></p>
        <p>• <strong>Ubicación:</strong> <?= htmlspecialchars($detalle['ubicacion']) ?></p>
        
        <div id="map" style="height:250px; width:100%; margin: 15px 0; border-radius: 8px;"></div>
        
        <?php 
            $coords = explode(',', $detalle['ubicacion']);
            $lat = trim($coords[0] ?? 0);
            $lng = trim($coords[1] ?? 0);
        ?>
        <script>
            var map = L.map('map').setView([<?= $lat ?>, <?= $lng ?>], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            L.marker([<?= $lat ?>, <?= $lng ?>]).addTo(map);
        </script>

        <p>• <strong>Prioridad de Sistema:</strong> 
            <span class="badge-prioridad prioridad-<?= $detalle['idPrioridad'] ?>">
                <?= ($detalle['idPrioridad']==1?"ALTA":($detalle['idPrioridad']==2?"MEDIA":"BAJA")) ?>
            </span>
        </p>
        <p>• <strong>Estado actual:</strong> <strong class="badge-estado"><?= $detalle['estado'] ?></strong></p>
    </div>

    <hr>

    <h3 class="titulo-responsable">Gestión Operativa del Responsable</h3>

    <form class="form-responsable" method="POST" enctype="multipart/form-data" action="../Controllers/detalle_incidente_logica_responsable.php?id=<?= $detalle['idIncidente'] ?>">

        <div class="grupo-vertical">
            <label>Actualizar Estado del Incidente</label>
            <select name="estado" id="estadoSelector" onchange="actualizarInterfaz()" required <?= $atributoBloqueo ?>>
                <option value="Pendiente" <?= $detalle['estado']=="Pendiente"?"selected":"" ?>>Pendiente (Recepción)</option>
                <option value="En Proceso" <?= $detalle['estado']=="En Proceso"?"selected":"" ?>>En Proceso (Intervención)</option>
                <option value="No Resuelto" <?= $detalle['estado']=="No Resuelto"?"selected":"" ?>>No Resuelto (Bloqueado)</option>
                <option value="Resuelto" <?= $detalle['estado']=="Resuelto"?"selected":"" ?>>Resuelto (Cierre)</option>
            </select>
        </div>

        <div id="sec_pendiente" class="bloque-estado">
            <h4 class="titulo-seccion">Confirmación de Recepción</h4>
            <div class="grupo-vertical">
                <label>Comentario Inicial</label>
                <textarea name="comentario" rows="2" <?= $readonlyBloqueo ?> <?= $atributoBloqueo ?>><?= htmlspecialchars($detalle['comentario'] ?? '') ?></textarea>
            </div>
        </div>

        <div id="sec_proceso" class="bloque-estado">
            <h4 class="titulo-seccion">Intervención en Campo</h4>
            <div class="grupo-vertical">
                <label>Agente / Operativo Encargado</label>
                <input type="text" name="agente_encargado" value="<?= htmlspecialchars($detalle['agente_encargado'] ?? '') ?>" <?= $readonlyBloqueo ?> <?= $atributoBloqueo ?>>
            </div>
            <div class="grupo-vertical">
                <label>Identificación de Unidad</label>
                <input type="text" name="unidad_id" value="<?= htmlspecialchars($detalle['unidad_id'] ?? '') ?>" <?= $readonlyBloqueo ?> <?= $atributoBloqueo ?>>
            </div>
            <div class="grupo-vertical">
                <label>Acciones en Ejecución</label>
                <textarea name="accionRealizada" rows="3" <?= $readonlyBloqueo ?> <?= $atributoBloqueo ?>><?= htmlspecialchars($detalle['accionRealizada'] ?? '') ?></textarea>
            </div>
        </div>

        <div id="sec_no_resuelto" class="bloque-estado">
            <h4 class="titulo-seccion">Incidente No Solucionado</h4>
            <div class="grupo-vertical">
                <label>Motivo del Incumplimiento</label>
                <select name="motivoPausa" <?= $atributoBloqueo ?>>
                    <option value="Falta de Recursos" <?= ($detalle['motivoPausa'] == 'Falta de Recursos') ? 'selected' : '' ?>>Falta de Recursos / Equipos</option>
                    <option value="Zona Insegura" <?= ($detalle['motivoPausa'] == 'Zona Insegura') ? 'selected' : '' ?>>Inseguridad en el Perímetro</option>
                    <option value="Condiciones Climaticas" <?= ($detalle['motivoPausa'] == 'Condiciones Climaticas') ? 'selected' : '' ?>>Clima Adverso</option>
                </select>
            </div>
            <div class="grupo-vertical">
                <label>Justificación Detallada</label>
                <textarea name="observacionesFinales" rows="3" <?= $readonlyBloqueo ?> <?= $atributoBloqueo ?>><?= htmlspecialchars($detalle['observacionesFinales'] ?? '') ?></textarea>
            </div>
        </div>

        <div id="sec_resuelto" class="bloque-estado">
            <h4 class="titulo-seccion">Finalización y Archivo</h4>
            <div class="grupo-vertical">
                <label>Solución Definitiva</label>
                <textarea name="resultadoObtenido" rows="3" <?= $readonlyBloqueo ?> <?= $atributoBloqueo ?>><?= htmlspecialchars($detalle['resultadoObtenido'] ?? '') ?></textarea>
            </div>
            <div class="grupo-vertical">
                <label>Tiempo de Resolución Empleado</label>
                <input type="text" name="tiempoResolucion" value="<?= htmlspecialchars($detalle['tiempoResolucion'] ?? '') ?>" <?= $readonlyBloqueo ?> <?= $atributoBloqueo ?>>
            </div>
            <div class="grupo-vertical">
                <label style="color:red; font-weight:bold;">Acta Policial</label>
                <input type="file" name="acta_documento" <?= $atributoBloqueo ?>>
                <?php if(!empty($detalle['acta_ruta'])): ?>
                    <a href="<?= $detalle['acta_ruta'] ?>" target="_blank">📄 Ver documento cargado</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!$estaResuelto): ?>
            <button class="btn-guardar" type="submit" style="width:100%; margin-top:10px;">Actualizar Incidente</button>
        <?php else: ?>
            <button class="btn-guardar" type="button" style="width:100%; margin-top:10px; background:#6c757d; cursor:not-allowed;" disabled>Incidente Cerrado</button>
        <?php endif; ?>
    </form>

    <?php if ($estaResuelto): ?>
        <hr>
        <form method="POST" action="../Controllers/detalle_incidente_logica_responsable.php?id=<?= $detalle['idIncidente'] ?>" onsubmit="return confirm('¿Seguro que desea reabrir este incidente? Se habilitará la edición para corregir la información.');">
            <input type="hidden" name="accion" value="reabrir">
            <input type="hidden" name="estado" value="En Proceso">
            <button type="submit" class="btn-reabrir">🔓 Reabrir Incidente </button>
        </form>
    <?php endif; ?>

    <br>
    <a class="btn" href="incidentes_asignados.php">Volver a Incidentes Asignados</a>
</div>

<script>
function actualizarInterfaz() {
    const estado = document.getElementById('estadoSelector').value;
    document.querySelectorAll('.bloque-estado').forEach(div => div.style.display = 'none');

    if (estado === 'Pendiente') document.getElementById('sec_pendiente').style.display = 'block';
    if (estado === 'En Proceso') document.getElementById('sec_proceso').style.display = 'block';
    if (estado === 'No Resuelto') document.getElementById('sec_no_resuelto').style.display = 'block';
    if (estado === 'Resuelto') document.getElementById('sec_resuelto').style.display = 'block';
}
window.onload = actualizarInterfaz;
</script>

</body>
</html>