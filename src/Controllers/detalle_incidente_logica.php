<?php
// 1. Incluimos la conexión con la ruta corregida
include_once "../Models/base.php";

// 2. Validación de sesión (ya iniciada en la Vista)
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'usuario') {
    header("Location: /incidencias/public/index.html");
    exit();
}

$idUsuarioSesion = $_SESSION['id'];

// 3. Validar ID del incidente enviado por la URL
if (!isset($_GET['id'])) {
    header("Location: historial_incidentes.php?msg=" . urlencode("ID de incidente no proporcionado."));
    exit();
}

$id = intval($_GET['id']);

// 4. CONSULTA DETALLADA
// Usamos LEFT JOIN en responsables para que los incidentes "Pendientes" sean visibles
$sql = "SELECT 
            i.idIncidente,
            i.titulo,
            i.descripcion,
            i.ubicacion,
            c.nombre AS categoria,
            s.nombre AS subcategoria,
            i.idPrioridad,
            i.estado,
            i.fechaRegistro,
            i.fechaCierre,
            i.accionRealizada,
            i.resultadoObtenido,
            i.observacionesFinales,
            i.tiempoResolucion,
            u.nombre AS usuario,
            r.nombre AS responsable
        FROM incidentes i
        INNER JOIN categorias c ON i.idCategoria = c.idCategoria
        LEFT JOIN subcategorias s ON s.idSubcategoria = i.idSubcategoria
        INNER JOIN usuarios u ON i.idUsuario = u.idUsuario
        LEFT JOIN responsables r ON i.idResponsable = r.idResponsable
        WHERE i.idIncidente = ? AND i.idUsuario = ?";

$stmt = $conexion->prepare($sql);
// Blindamos la consulta: solo el dueño del incidente puede verlo
$stmt->bind_param("ii", $id, $idUsuarioSesion);
$stmt->execute();
$result = $stmt->get_result();

$detalle = $result->fetch_assoc();

$stmt->close();
// Dejamos la conexión abierta por si la vista necesita realizar otras consultas menores
?>