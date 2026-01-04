<?php
session_start();

// 1. Seguridad: Redirección al login público si no hay sesión de responsable
if(!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'responsable'){
    header("Location: /incidencias/public/index.html");
    exit();
}

// 2. Lógica: Ruta corregida al controlador que procesa la lista filtrada
include_once "../Controllers/incidente_responsable.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Incidentes Asignados - Responsable</title>
    <link rel="stylesheet" href="/incidencias/assets/css/tablas.css">
</head>
<body>

<div class="topbar">
    <div class="logo">Sistema de Incidencias</div>
    <div class="actions">
        <a href="panel_responsable.php">Volver al menú principal</a>
    </div>
</div>

<h2 class="titulo">Gestión de Incidentes Asignados</h2>

<form method="GET" class="filtros-incidentes">
    <select name="estado">
        <option value="">Todos los estados</option>
        <option value="Pendiente" <?php if(($_GET['estado'] ?? '')=='Pendiente') echo 'selected'; ?>>Pendiente</option>
        <option value="En proceso" <?php if(($_GET['estado'] ?? '')=='En proceso') echo 'selected'; ?>>En proceso</option>
        <option value="Resuelto" <?php if(($_GET['estado'] ?? '')=='Resuelto') echo 'selected'; ?>>Resuelto</option>
    </select>

    <input type="date" name="fecha_desde" value="<?php echo $_GET['fecha_desde'] ?? ''; ?>">

    <input type="date" name="fecha_hasta" value="<?php echo $_GET['fecha_hasta'] ?? ''; ?>">

    <button type="submit">Filtrar</button>
    <a href="incidentes_asignados.php" class="btn-limpiar">Limpiar</a>
</form>


<div class="contenedor-tabla">
    <table class="tabla-incidentes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Categoría</th>
                <th>Subcategoría</th>
                <th>Prioridad</th>
                <th>Estado</th>
                <th>Usuario</th>
                <th>Fecha Registro</th>
                <th>Acciones</th> 
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($incidentes)): ?>
                <?php foreach($incidentes as $fila): ?>
                    <tr>
                        <td><?php echo $fila['idIncidente']; ?></td>
                        <td><strong><?php echo htmlspecialchars($fila['titulo']); ?></strong></td>
                        <td><?php echo $fila['categoria']; ?></td>
                        <td><?php echo $fila['subcategoria']; ?></td>
                        <td>
                            <span class="prioridad-<?php echo strtolower($fila['prioridad']); ?>">
                                <?php echo $fila['prioridad']; ?>
                            </span>
                        </td>
                        <td><span class="badge-estado"><?php echo $fila['estado']; ?></span></td>
                        <td><?php echo htmlspecialchars($fila['usuario']); ?></td>
                        <td><?php echo date("d/m/Y", strtotime($fila['fechaRegistro'])); ?></td>

                        <td>
                            <a href="detalle_incidente_responsable.php?id=<?php echo $fila['idIncidente']; ?>" 
                               class="btn-detalle">
                                <span class="mas-icono">+</span> Gestionar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" style="text-align: center;">No se encontraron incidentes con los filtros seleccionados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>