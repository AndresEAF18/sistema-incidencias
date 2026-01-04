<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// 1. Ruta corregida a la conexión
include_once "../Models/base.php"; 

// =============================
// 0. Validación de sesión
// =============================
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'responsable') {
    header("Location: /incidencias/public/index.html");
    exit();
}

// =============================
// 1. Validar ID del incidente
// =============================
if (!isset($_GET['id'])) {
    header("Location: ../Views/panel_responsable.php");
    exit();
}

$id = intval($_GET['id']);

// =============================
// 2. PROCESAR FORMULARIO (POST)
// =============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $estado        = trim($_POST['estado']);
    $accion        = trim($_POST['accionRealizada']);
    $resultado     = trim($_POST['resultadoObtenido']);
    $observaciones = trim($_POST['observacionesFinales']);
    $tiempo        = trim($_POST['tiempoResolucion']);

    // Obtener idUsuario creador para la notificación
    $stmtUser = $conexion->prepare("SELECT idUsuario FROM incidentes WHERE idIncidente = ?");
    $stmtUser->bind_param("i", $id);
    $stmtUser->execute();
    $idUsuarioCreador = $stmtUser->get_result()->fetch_assoc()['idUsuario'];
    $stmtUser->close();

    // Actualizar incidente
    if ($estado === "Resuelto") {
        $sql = "UPDATE incidentes SET 
                estado = ?, accionRealizada = ?, resultadoObtenido = ?, 
                observacionesFinales = ?, tiempoResolucion = ?, fechaCierre = NOW() 
                WHERE idIncidente = ?";
    } else {
        $sql = "UPDATE incidentes SET 
                estado = ?, accionRealizada = ?, resultadoObtenido = ?, 
                observacionesFinales = ?, tiempoResolucion = ? 
                WHERE idIncidente = ?";
    }

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssi", $estado, $accion, $resultado, $observaciones, $tiempo, $id);
    $stmt->execute();
    $stmt->close();

    // =============================
    // 2.2 Notificación al usuario
    // =============================
    $mensaje = "Tu incidente #$id fue actualizado. Estado: $estado.";
    $sqlNoti = "INSERT INTO notificaciones (idIncidente, idUsuario, mensaje, tipo, leida, fecha) 
                VALUES (?, ?, ?, ?, 0, NOW())";
    $stmtN = $conexion->prepare($sqlNoti);
    $stmtN->bind_param("iiss", $id, $idUsuarioCreador, $mensaje, $estado);
    $stmtN->execute();
    $stmtN->close();

    // Redirección corregida a la Vista de detalle
    header("Location: ../Views/detalle_incidente_responsable.php?id=$id&msj=actualizado");
    exit();
}

// =============================
// 3. CONSULTAR DETALLE PARA MOSTRAR EN VISTA
// =============================
$sql = "SELECT 
            i.*, 
            c.nombre AS categoria, 
            s.nombre AS subcategoria, 
            u.nombre AS usuario, 
            r.nombre AS responsable
        FROM incidentes i
        INNER JOIN categorias c ON i.idCategoria = c.idCategoria
        INNER JOIN subcategorias s ON i.idSubcategoria = s.idSubcategoria
        INNER JOIN usuarios u ON i.idUsuario = u.idUsuario
        INNER JOIN responsables r ON i.idResponsable = r.idResponsable
        WHERE i.idIncidente = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$detalle = $stmt->get_result()->fetch_assoc();
$stmt->close();

