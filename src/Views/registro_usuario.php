
<?php
session_start();

// Si ya hay sesión iniciada, redirige al panel de usuario
// Como panel_usuario.php estará en esta misma carpeta (Views), la ruta es simple:
if(isset($_SESSION['id']) && $_SESSION['tipo'] == 'usuario'){
    header("Location: panel_usuario.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="/incidencias/assets/css/registro_usuario.css">
</head>
<body>

    <div class="titulo-sistema">Sistema de Incidencias Hermanos Cristianos</div>

    <div class="login-container">
        <h2>Registro de Usuario</h2>
        
        <form action="/incidencias/src/Controllers/registrar_usuario_procesar.php" method="POST">
            <label>Nombre:</label>
            <input type="text" name="nombre" required>

            <label>Correo:</label>
            <input type="email" name="correo" required 
                   pattern=".+@hermanoscristianos\.com"
                   title="Debe usar un correo @hermanoscristianos.com">

            <label>Contraseña:</label>
            <input type="password" name="contrasena" required>

            <input type="submit" value="Registrarse">
        </form>

        <p style="margin-top:15px; text-align:center;">
            <a class="registro-btn" href="/incidencias/public/index.html">¿Ya tienes cuenta? Inicia sesión</a>
        </p>
    </div>

</body>
</html>