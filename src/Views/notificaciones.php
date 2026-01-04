<?php
session_start();

// 1. Seguridad: Solo usuarios logueados
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'usuario') {
    header("Location: /incidencias/public/index.html");
    exit();
}

// 2. Lógica: Indicamos que al entrar aquí, las notificaciones se marquen como leídas
$marcarLeidas = true;

// Traemos el controlador que ya habíamos configurado antes
include_once "../Controllers/notificaciones_usuario.php"; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Notificaciones</title>
    <link rel="stylesheet" href="/incidencias/assets/css/notificaciones.css">
</head>
<body>

<div class="topbar">
    <div class="logo">Sistema de Incidencias</div>
    <div class="actions">
        <a href="panel_usuario.php" class="btn-volver">Volver al Panel</a>
        <a href="/incidencias/src/Controllers/logout.php" class="btn-logout">Cerrar sesión</a>
    </div>
</div>

<div class="main-content">
    <h2>Centro de Notificaciones</h2>

    <ul class="lista-notificaciones">
        <?php if (empty($notificaciones)): ?>
            <li class="vacio">No tienes notificaciones por el momento.</li>
        <?php else: ?>
            <?php foreach ($notificaciones as $noti): ?>
                <li class="item-notificacion">
                    <div class="header-noti">
                        <span class="fecha"><?php echo date("d/m/Y H:i", strtotime($noti['fecha'])); ?></span>
                        <span class="tipo-badge <?php echo strtolower($noti['tipo']); ?>">
                            <?php echo htmlspecialchars($noti['tipo']); ?>
                        </span>
                    </div>
                    <div class="mensaje-noti">
                        <?php echo htmlspecialchars($noti['mensaje']); ?>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>