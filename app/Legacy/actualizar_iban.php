<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $host = "82.223.118.182";
    $user = "dominios_hawkins";
    $password = "5z452iA#e";
    $database = "dominios_hawkins";

    // Conectar a MySQL
    $conn = new mysqli($host, $user, $password, $database);
    if ($conn->connect_error) {
        die("❌ Error de conexión: " . $conn->connect_error);
    }

    // Obtener datos del formulario
    $nombre = $_POST["nombre"];
    $nuevo_iban = $_POST["IBAN"];

    // Actualizar IBAN en la base de datos
    $query = "UPDATE dominios SET IBAN = ? WHERE nombre = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $nuevo_iban, $nombre);
    if ($stmt->execute()) {
        echo "✅ IBAN actualizado con éxito.";
    } else {
        echo "❌ Error al actualizar el IBAN.";
    }

    $stmt->close();
    $conn->close();
}
?>
