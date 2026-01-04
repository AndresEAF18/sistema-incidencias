<?php 
session_start();
// 1. Seguridad: Verificar sesión
if(!isset($_SESSION['id']) || $_SESSION['tipo'] != 'usuario'){
    header("Location: /incidencias/public/index.html");
    exit();
}

// 2. Incluimos la lógica (Controlador) que buscará los datos en la BD
// Pasamos el ID por la URL, por lo que el controlador lo recibirá vía $_GET
include_once "../Controllers/detalle_incidente_logica.php";

// Si el controlador no encuentra el incidente o no pertenece al usuario, $detalle estará vacío
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
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>

<div class="card">

    <h3>Resumen del Incidente</h3>
    <h3 class="titulo-incidente"><?= $detalle['titulo'] ?></h3>

    <div class="narrativa">

        <p>• Incidente reportado por <strong><?= $detalle['usuario'] ?></strong>.</p>
        <p>• Fecha de registro: <strong><?= $detalle['fechaRegistro'] ?></strong>.</p>
        <p>• Ubicación del incidente: <strong><?= $detalle['ubicacion'] ?></strong>.</p>

        <!-- MAPA -->
        <div id="map" style="height:300px; width:100%; margin-top:10px;"></div>
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

        <?php 
        $coords = explode(',', $detalle['ubicacion']);
        $lat = $coords[0] ?? 0;
        $lng = $coords[1] ?? 0;
        ?>

        <script>
            var map = L.map('map').setView([<?= $lat ?>, <?= $lng ?>], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            L.marker([<?= $lat ?>, <?= $lng ?>]).addTo(map);
        </script>

        <p>• Descripción del incidente: <strong><?= $detalle['descripcion'] ?></strong></p>

   

        <p>• Categoría: <strong><?= $detalle['categoria'] ?></strong></p>

        <p>• Subcategoría: <strong><?= $detalle['subcategoria'] ?></strong></p>

        <p>• Prioridad asignada:
            <strong>
                <?= ($detalle['idPrioridad']==1?"Alta":($detalle['idPrioridad']==2?"Media":"Baja")) ?>
            </strong>
        </p>
    </div>

    <hr>

    <!-- =========================
         SEGUIMIENTO / RESOLUCIÓN
    ========================== -->

    <h3 class="titulo-responsable">Seguimiento del Incidente</h3>



<div class="seguimiento <?= strtolower(str_replace(' ', '-', $detalle['estado'])) == 'resuelto' ? 'seguimiento-resuelto' : '' ?>">

 
  <p><strong>Asignación:</strong> El incidente fue asignado al responsable 
        <strong><?= $detalle['responsable'] ?></strong>.
    </p>

<p><strong>Estado actual:</strong>
        <span class="estado estado-<?= strtolower(str_replace(' ', '-', $detalle['estado'])) ?>">
    <?= $detalle['estado'] ?>
</span>

    </p>


    <?php if (!empty($detalle['accionRealizada'])): ?>
        <p><strong>Acción realizada:</strong>
            <?= nl2br($detalle['accionRealizada']) ?>
        </p>
    <?php endif; ?>

    <?php if (!empty($detalle['resultadoObtenido'])): ?>
        <p><strong>Resultado obtenido:</strong>
            <?= nl2br($detalle['resultadoObtenido']) ?>
        </p>
    <?php endif; ?>

    <?php if (!empty($detalle['observacionesFinales'])): ?>
        <p><strong>Observaciones finales:</strong>
            <?= nl2br($detalle['observacionesFinales']) ?>
        </p>
    <?php endif; ?>

    <?php if (!empty($detalle['tiempoResolucion'])): ?>
        <p><strong> Tiempo de resolución:</strong>
            <?= $detalle['tiempoResolucion'] ?>
        </p>
    <?php endif; ?>

     <p> <strong>Fecha de cierre:  </strong>
           
                <?= $detalle['fechaCierre'] ?: "<i>Aún no ha sido cerrado.</i>" ?>
           
        </p>

 
</div>


<a class="btn" href="historial_incidentes.php">Volver al Historial</a>


</body>
</html>