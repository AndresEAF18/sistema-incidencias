<?php
// 1. Indicamos marcar como leídas y llamamos al controlador (Ruta corregida)
$marcarLeidas = true;
include_once "../Controllers/notificaciones_res.php"; 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notificaciones</title>

    <!-- USANDO EL MISMO CSS DEL USUARIO -->
    <link rel="stylesheet" href="/incidencias/assets/css/notificaciones.css">
</head>
<body>

<div class="topbar">
    <div class="logo">Sistema de Incidencias</div>
    <div class="actions">
        <a href="panel_responsable.php"class="btn-volver">Volver</a>
        <a href="../servidor/logout.php" class="btn-logout">Cerrar sesión</a>
    </div>
</div>

<div class="main-content">
    <h2> Notificaciones</h2>

    <ul class="lista-notificaciones">
        <?php if (empty($notificaciones)): ?>
            <li class="vacio">No tienes notificaciones.</li>
        <?php else: ?>
            <?php foreach ($notificaciones as $noti): ?>
                <li>
                    <strong><?php echo $noti['fecha']; ?></strong>
                    <span class="tipo">[<?php echo $noti['tipo']; ?>]</span><br>
                    <?php echo $noti['mensaje']; ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>