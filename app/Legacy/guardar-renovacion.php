<?php
$host = "82.223.118.182";
$user = "dominios_hawkins";
$password = "5z452iA#e";
$database = "dominios_hawkins";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

$id = $_POST['id'] ?? '';
$fecha = $_POST['fecha_renovacion'] ?? null;

if ($id && $fecha) {
    $stmt = $conn->prepare("UPDATE subdominios SET fecha_renovacion = ? WHERE id = ?");
    $stmt->bind_param("ss", $fecha, $id);
    if ($stmt->execute()) {
        echo "✅ Fecha de renovación actualizada.";
    } else {
        echo "❌ Error al guardar.";
    }
    $stmt->close();
} else {
    echo "❌ Datos inválidos.";
}
$conn->close();
