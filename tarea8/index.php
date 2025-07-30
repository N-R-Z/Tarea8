<?php
require_once 'db_config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT * FROM personajes";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Personajes de la Serie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h1 class="mb-4 text-center">📺 Personajes de la Serie</h1>

    <div class="d-flex justify-content-between mb-3">
        <a href="agregar_personaje.php" class="btn btn-primary">➕ Agregar nuevo personaje</a>
        <a href="acerca_de.php" class="btn btn-info">👤 Acerca De</a>
    </div>

    <table class="table table-bordered table-striped table-hover text-center">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nombre</th>
                <th>Color</th>
                <th>Tipo</th>
                <th>Nivel</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while($row = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><img src="<?= $row['foto'] ?>" width="80" height="80" style="object-fit: cover; border-radius: 8px;"></td>
                        <td><?= $row['nombre'] ?></td>
                        <td><?= $row['color'] ?></td>
                        <td><?= $row['tipo'] ?></td>
                        <td><?= $row['nivel'] ?></td>
                        <td>
                            <a href="editar_personaje.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
                            <a href="eliminar_personaje.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este personaje?')">🗑️ Eliminar</a>
                            <a href="generar_pdf.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success" target="_blank">📄 PDF</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">No hay personajes registrados aún.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
