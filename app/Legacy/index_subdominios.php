<?php
$host = "82.223.118.182";
$user = "dominios_hawkins";
$password = "5z452iA#e";
$database = "dominios_hawkins";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

$query = "SELECT * FROM subdominios ORDER BY dominio ASC, subdominio ASC";
$result = $conn->query($query);
$subdominios = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $subdominios[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">

    <title>Gestión de Subdominios</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #007BFF; color: white; cursor: pointer; }
        input, button { padding: 4px; }
    </style>
</head>
<body>
    <h2>Gestión de Subdominios</h2>
    <h4>Total: <?php echo count($subdominios); ?> subdominios</h4>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Dominio</th>
                <th>Subdominio</th>
                <th>Último cambio</th>
                <th>Próxima renovación</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subdominios as $i => $sub) : ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><?php echo htmlspecialchars($sub['dominio']); ?></td>
                    <td><?php echo htmlspecialchars($sub['subdominio']); ?></td>
                    <td><?php echo htmlspecialchars($sub['fecha_cambio']); ?></td>
                    <td>
                        <input type="date" value="<?php echo $sub['fecha_renovacion'] ?? ''; ?>" id="renovacion-<?php echo $i; ?>">
                    </td>
                    <td>
                        <button onclick="guardarRenovacion('<?php echo $sub['id']; ?>', <?php echo $i; ?>)">Guardar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    function guardarRenovacion(id, index) {
        const fecha = document.getElementById("renovacion-" + index).value;

        fetch("/actualizar-renovacion", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                id: id,
                fecha_renovacion: fecha
            })
        })
        .then(res => res.json())
        .then(data => alert(data.message || "✅ Actualizado"))
        .catch(error => alert("❌ Error en la solicitud"));
    }
</script>

</body>
</html>
