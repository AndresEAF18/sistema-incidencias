<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'usuario') {
    header("Location: /incidencias/public/index.html");
    exit();
}

// Incluimos la lógica (ahora está en Controllers)
include_once "../Controllers/incidencias_usuarios.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Incidentes</title>
    <link rel="stylesheet" href="/incidencias/assets/css/tablas.css">
</head>
<body>

<div class="topbar">
    <div class="logo">Sistema de Incidencias</div>
    <div class="actions">
        <a href="panel_usuario.php" class="btn-volver">Volver al Panel</a>
    </div>
</div>

<h2 class="titulo">Historial de Incidentes</h2>

<form method="GET" class="filtros-incidentes">

    <select name="estado">
        <option value="">Todos los estados</option>
        <option value="Pendiente" <?php if(($_GET['estado'] ?? '')=='Pendiente') echo 'selected'; ?>>Pendiente</option>
        <option value="En proceso" <?php if(($_GET['estado'] ?? '')=='En proceso') echo 'selected'; ?>>En proceso</option>
        <option value="Resuelto" <?php if(($_GET['estado'] ?? '')=='Resuelto') echo 'selected'; ?>>Resuelto</option>
    </select>

    <select name="categoria">
        <option value="">Todas las categorías</option>
        <?php foreach ($categorias as $cat): ?>
            <option value="<?php echo $cat['idCategoria']; ?>"
                <?php if(($_GET['categoria'] ?? '') == $cat['idCategoria']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($cat['nombre']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="date" name="fecha_desde" value="<?php echo $_GET['fecha_desde'] ?? ''; ?>">
    <input type="date" name="fecha_hasta" value="<?php echo $_GET['fecha_hasta'] ?? ''; ?>">

    <button type="submit">Filtrar</button>
    <a href="historial_incidentes.php" class="btn-limpiar">Limpiar</a>
</form>

<div class="contenedor-tabla">
    <table class="tabla-incidentes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Categoría</th>
                <th>Subcategoría</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Responsable</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($incidentes)): ?>
                <?php foreach ($incidentes as $fila): ?>
                    <tr>
                        <td><?php echo $fila['idIncidente']; ?></td>
                        <td><?php echo htmlspecialchars($fila['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
                        <td><?php echo htmlspecialchars($fila['subcategoria']); ?></td>
                        <td>
                            <span class="estado-<?php echo strtolower(str_replace(' ', '-', $fila['estado'])); ?>">
                                <?php echo $fila['estado']; ?>
                            </span>
                        </td>
                        <td><?php echo $fila['fechaRegistro']; ?></td>
                        <td><?php echo htmlspecialchars($fila['responsable'] ?? 'Sin asignar'); ?></td>
                        <td>
                            <a href="detalle_incidente.php?id=<?php echo $fila['idIncidente']; ?>" class="btn-detalle">
                                Ver Detalles
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="sin-datos">No se encontraron incidentes con estos filtros.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>