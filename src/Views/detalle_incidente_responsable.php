<?php 
// 1. Ruta corregida al controlador (que maneja tanto la carga como el guardado)
include_once "../Controllers/detalle_incidente_logica_responsable.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Incidente - Gestión</title>
    <link rel="stylesheet" href="/incidencias/assets/css/detalle_incidente.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>

<div class="card">

    <h3>Resumen del Incidente</h3>
    <h3 class="titulo-incidente"><?= htmlspecialchars($detalle['titulo']) ?></h3>

    <div class="narrativa">
        <p>• Este incidente fue reportado por <strong><?= htmlspecialchars($detalle['usuario']) ?></strong>.</p>
        <p>• Se registró el día <strong><?= $detalle['fechaRegistro'] ?></strong>.</p>
        <p>• Ubicación: <strong><?= htmlspecialchars($detalle['ubicacion']) ?></strong>.</p>

        <div id="map" style="height:300px; width:100%; margin-top:10px; border-radius: 8px;"></div>
        
        <?php 
        $coords = explode(',', $detalle['ubicacion']);
        $lat = trim($coords[0] ?? 0);
        $lng = trim($coords[1] ?? 0);
        ?>

        <script>
            var map = L.map('map').setView([<?= $lat ?>, <?= $lng ?>], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);
            L.marker([<?= $lat ?>, <?= $lng ?>]).addTo(map);
        </script>

        <p>• Descripción: <strong><?= htmlspecialchars($detalle['descripcion']) ?></strong>.</p>
        <p>• Categoría: <strong><?= $detalle['categoria'] ?></strong></p>
        <p>• Subcategoría: <strong><?= $detalle['subcategoria'] ?></strong></p>
        <p>• Prioridad: <strong><?= ($detalle['idPrioridad']==1?"Alta":($detalle['idPrioridad']==2?"Media":"Baja")) ?></strong></p>
        <p>• Estado actual: <strong class="badge-estado"><?= $detalle['estado'] ?></strong></p>
        <p>• Fecha de cierre: <strong><?= $detalle['fechaCierre'] ?: "Aún abierto" ?></strong></p>
    </div>

    <hr>

    <h3 class="titulo-responsable">Gestión del Responsable</h3>

    <?php if ($detalle['estado'] !== "Resuelto"): ?>
        <form class="form-responsable" method="POST"
              action="../Controllers/detalle_incidente_logica_responsable.php?id=<?= $detalle['idIncidente'] ?>">

            <div class="grupo">
                <label>Estado del incidente</label>
                <select name="estado" required>
                    <option value="Pendiente" <?= $detalle['estado']=="Pendiente"?"selected":"" ?>>Pendiente</option>
                    <option value="En Proceso" <?= $detalle['estado']=="En Proceso"?"selected":"" ?>>En Proceso</option>
                    <option value="Resuelto" <?= $detalle['estado']=="Resuelto"?"selected":"" ?>>Resuelto</option>
                </select>
            </div>

            <div class="grupo">
                <label>Acción realizada</label>
                <textarea name="accionRealizada" rows="3" required><?= htmlspecialchars($detalle['accionRealizada'] ?? '') ?></textarea>
            </div>

            <div class="grupo">
                <label>Resultado obtenido</label>
                <textarea name="resultadoObtenido" rows="3" required><?= htmlspecialchars($detalle['resultadoObtenido'] ?? '') ?></textarea>
            </div>

            <div class="grupo">
                <label>Observaciones finales</label>
                <textarea name="observacionesFinales" rows="3"><?= htmlspecialchars($detalle['observacionesFinales'] ?? '') ?></textarea>
            </div>

            <div class="grupo">
                <label>Tiempo estimado de resolución</label>
                <input type="text" name="tiempoResolucion" placeholder="Ej: 2 días" value="<?= htmlspecialchars($detalle['tiempoResolucion'] ?? '') ?>" required>
            </div>

            <button class="btn-guardar" type="submit">Actualizar Incidente</button>
        </form>

 <?php else: ?>

        <div class="comentario-resuelto">
            <h4>Detalle de la resolución</h4>

            <div class="grupo-resuelto">
                <label>Acción realizada</label>
                <div class="contenido"><?= $detalle['accionRealizada'] ?></div>
            </div>

            <div class="grupo-resuelto">
                <label>Resultado obtenido</label>
                <div class="contenido"><?= $detalle['resultadoObtenido'] ?></div>
            </div>

            <div class="grupo-resuelto">
                <label>Observaciones finales</label>
                <div class="contenido"><?= $detalle['observacionesFinales'] ?: 'Ninguna' ?></div>
            </div>

            <div class="grupo-resuelto">
                <label>Tiempo estimado de resolución</label>
                <div class="contenido"><?= $detalle['tiempoResolucion'] ?></div>
            </div>
        </div>

    <?php endif; ?>

    <a class="btn" href="incidentes_asignados.php">Volver a Incidentes Asignados</a>

</div>

</body>
</html>