<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mensaje del Sistema</title>
    <link rel="stylesheet" href="/incidencias/assets/css/mensajes.css">
</head>
<body>

<div class="login-container mensaje-box">
    <h2>Mensaje del Sistema</h2>

    <p class="mensaje-texto">
        <?php 
            // htmlspecialchars evita ataques XSS (muy bien ahí)
            echo htmlspecialchars($_GET['msg'] ?? 'Mensaje no disponible');
        ?>
    </p>

    <a class="registro-btn" href="/incidencias/public/index.html">Volver al Inicio</a>
</div>

</body>
</html>