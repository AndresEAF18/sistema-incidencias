<?php
session_start();

// 1. Ruta corregida para el login (sube dos niveles hasta la raíz de public)
if(!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'usuario'){
    header("Location: /incidencias/public/index.html");
    exit();
}

// Se recibe el mensaje desde registro incidente. 
$mensaje = $_GET['msg'] ?? "Incidente registrado correctamente.";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Incidente</title>
    <link rel="stylesheet" href="/incidencias/assets/css/paneles.css"> 
</head>
<body>

<div class="confirmacion">
    <h1><?php echo htmlspecialchars($mensaje); ?></h1>
    
    <button onclick="window.location.href='panel_usuario.php'">Volver al Menú Principal</button>
    <button onclick="window.location.href='registrar_incidente.php'">Registrar Otro Incidente</button>
</div>

</body>
</html>