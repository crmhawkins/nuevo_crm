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
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #007BFF; color: white; cursor: pointer; }
        input, button { padding: 5px; }
        .filtros { display: flex; justify-content: space-between; margin-bottom: 15px; flex-wrap: wrap; gap: 10px; }
        .filtros div { display: flex; gap: 10px; align-items: center; }
    </style>
</head>
<body>
    <h2>Gestión de Subdominios</h2>

    <div class="filtros">
        <div>
            <label>Buscar dominio o subdominio:</label>
            <input type="text" id="buscar" onkeyup="filtrarTabla()">
        </div>
        <div>
            <label>Fecha inicio renovación:</label>
            <input type="date" id="fechaInicio" onchange="filtrarTabla()">
            <label>Fecha fin renovación:</label>
            <input type="date" id="fechaFin" onchange="filtrarTabla()">
            <button onclick="filtrarTabla()">Filtrar</button>
            <button onclick="limpiarFiltros()">Limpiar Filtros</button>
        </div>
    </div>

    <table id="tabla-subdominios" data-orden="asc">
        <thead>
            <tr>
                <th onclick="ordenarTabla(0)">#</th>
                <th onclick="ordenarTabla(1)">Dominio</th>
                <th onclick="ordenarTabla(2)">Subdominio</th>
                <th onclick="ordenarTabla(3)">Último cambio</th>
                <th onclick="ordenarTabla(4)">Próxima renovación</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="tabla-datos">
            <?php foreach ($subdominios as $i => $sub) : ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><?php echo htmlspecialchars($sub['dominio']); ?></td>
                    <td><?php echo htmlspecialchars($sub['subdominio']); ?></td>
                    <td><?php echo $sub['fecha_cambio'] ? date("d/m/Y", strtotime($sub['fecha_cambio'])) : ''; ?></td>
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

        function ordenarTabla(n) {
            const tabla = document.getElementById("tabla-subdominios");
            const filas = Array.from(tabla.rows).slice(1);
            const ordenAsc = tabla.dataset.orden === "asc";

            filas.sort((a, b) => {
                let valA = a.cells[n].textContent.trim();
                let valB = b.cells[n].textContent.trim();

                // Si son fechas
                if (/\d{4}-\d{2}-\d{2}/.test(valA) && /\d{4}-\d{2}-\d{2}/.test(valB)) {
                    valA = new Date(valA);
                    valB = new Date(valB);
                }

                return ordenAsc ? (valA > valB ? 1 : -1) : (valA < valB ? 1 : -1);
            });

            filas.forEach(fila => tabla.appendChild(fila));
            tabla.dataset.orden = ordenAsc ? "desc" : "asc";
        }

        function filtrarTabla() {
            const buscar = document.getElementById("buscar").value.toLowerCase();
            const fechaInicio = document.getElementById("fechaInicio").value;
            const fechaFin = document.getElementById("fechaFin").value;
            const filas = document.querySelectorAll("#tabla-datos tr");

            filas.forEach(fila => {
                const dominio = fila.cells[1].textContent.toLowerCase();
                const subdominio = fila.cells[2].textContent.toLowerCase();
                const fechaRenovacion = fila.querySelector("input[type='date']").value;

                let visible = true;

                // Filtro por texto
                if (!(dominio.includes(buscar) || subdominio.includes(buscar))) {
                    visible = false;
                }

                // Filtro por fecha
                if (fechaInicio && fechaRenovacion < fechaInicio) visible = false;
                if (fechaFin && fechaRenovacion > fechaFin) visible = false;

                fila.style.display = visible ? "" : "none";
            });
        }

        function limpiarFiltros() {
            document.getElementById("buscar").value = "";
            document.getElementById("fechaInicio").value = "";
            document.getElementById("fechaFin").value = "";
            filtrarTabla();
        }
    </script>
</body>
</html>
