<?php
// Configuración de conexión a MySQL (Plesk)
$host = "82.223.118.182";
$user = "dominios_hawkins";
$password = "5z452iA#e";
$database = "dominios_hawkins";

// Conectar a MySQL
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Obtener dominios
$query = "SELECT id, nombre, fecha_expiracion, precio_compra, precio_venta, IBAN FROM dominios";
$result = $conn->query($query);
$dominios = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dominios[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">

    <title>Gestión de Dominios</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #007BFF; color: white; cursor: pointer; }
        input, button { padding: 5px; }
        .filtros { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .filtros div { display: flex; gap: 10px; align-items: center; }
    </style>
</head>
<body>
    <h2>Gestión de Dominios</h2>

    <h3 id="resumen">
        Total dominios: <?php echo count($dominios); ?> |
        Total precio de compra: <?php echo array_sum(array_column($dominios, 'precio_compra')); ?>€ |
        Total precio de venta: <?php echo array_sum(array_column($dominios, 'precio_venta')); ?>€
    </h3>

    <div class="filtros">
        <div>
            <label>Buscar dominio:</label>
            <input type="text" id="buscar" onkeyup="filtrarTabla()">
        </div>
        <div>
            <label>Fecha inicio:</label>
            <input type="date" id="fechaInicio" onchange="filtrarTabla()">
            <label>Fecha fin:</label>
            <input type="date" id="fechaFin" onchange="filtrarTabla()">
            <button onclick="filtrarTabla()">Filtrar</button>
            <button onclick="limpiarFiltros()">Limpiar Filtros</button>
        </div>
    </div>

    <table id="tabla-dominios">
        <thead>
            <tr>
                <th onclick="ordenarTabla(0)">#</th>
                <th onclick="ordenarTabla(1)">Nombre</th>
                <th onclick="ordenarTabla(2)">Fecha Expiración</th>
                <th onclick="ordenarTabla(3)">Precio Compra</th>
                <th onclick="ordenarTabla(4)">Precio Venta</th>
                <th>IBAN</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-datos">
            <?php foreach ($dominios as $index => $dominio) : ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $dominio['nombre']; ?></td>
                    <td><?php echo date("Y-m-d", strtotime($dominio['fecha_expiracion'])); ?></td>
                    <td><?php echo $dominio['precio_compra']; ?>€</td>
                    <td><?php echo $dominio['precio_venta']; ?>€</td>
                    <td><input type="text" value="<?php echo $dominio['IBAN']; ?>" id="iban-<?php echo $index; ?>"></td>
                    <td><button onclick="actualizarIBAN('<?php echo $dominio['nombre']; ?>', <?php echo $index; ?>)">Guardar</button></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        function actualizarIBAN(nombre, index) {
            const nuevoIBAN = document.getElementById(`iban-${index}`).value;

            fetch("/actualizar-iban", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: `nombre=${encodeURIComponent(nombre)}&IBAN=${encodeURIComponent(nuevoIBAN)}`
            })
            .then(response => response.text())
            .then(data => alert(data));
        }

        function ordenarTabla(n) {
            let tabla = document.getElementById("tabla-dominios");
            let filas = Array.from(tabla.rows).slice(1);
            let ordenAsc = tabla.dataset.orden === "asc";

            filas.sort((a, b) => {
                let valorA = a.cells[n].textContent.trim();
                let valorB = b.cells[n].textContent.trim();

                if (!isNaN(valorA) && !isNaN(valorB)) {
                    return ordenAsc ? valorA - valorB : valorB - valorA;
                }

                return ordenAsc ? valorA.localeCompare(valorB) : valorB.localeCompare(valorA);
            });

            filas.forEach(fila => tabla.appendChild(fila));
            tabla.dataset.orden = ordenAsc ? "desc" : "asc";
        }

        function filtrarTabla() {
            let buscar = document.getElementById("buscar").value.toLowerCase();
            let fechaInicio = document.getElementById("fechaInicio").value;
            let fechaFin = document.getElementById("fechaFin").value;
            let filas = document.querySelectorAll("#tabla-datos tr");

            filas.forEach(fila => {
                let celdas = fila.getElementsByTagName("td");
                let nombre = celdas[1].textContent.toLowerCase();
                let fechaExpiracion = celdas[2].textContent.trim();
                let precioCompra = celdas[3].textContent.replace("€", "").trim();
                let precioVenta = celdas[4].textContent.replace("€", "").trim();

                let cumpleBusqueda = nombre.includes(buscar) || fechaExpiracion.includes(buscar) || precioCompra.includes(buscar) || precioVenta.includes(buscar);
                let cumpleFecha = true;

                if (fechaInicio && fechaExpiracion < fechaInicio) cumpleFecha = false;
                if (fechaFin && fechaExpiracion > fechaFin) cumpleFecha = false;

                fila.style.display = cumpleBusqueda && cumpleFecha ? "" : "none";
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
