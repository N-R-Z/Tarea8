<?php
require_once 'db_config.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $color = $_POST['color'];
    $tipo = $_POST['tipo'];
    $nivel = $_POST['nivel'];
    
    // Manejo de imagen
    $foto_nombre = $_FILES['foto']['name'];
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $ruta_destino = "uploads/" . uniqid() . "_" . $foto_nombre;

    if (move_uploaded_file($foto_tmp, $ruta_destino)) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        $sql = "INSERT INTO personajes (nombre, color, tipo, nivel, foto)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssis", $nombre, $color, $tipo, $nivel, $ruta_destino);

        if ($stmt->execute()) {
            $mensaje = "Personaje registrado exitosamente.";
        } else {
            $mensaje = "Error al registrar el personaje.";
        }

        $stmt->close();
        $conn->close();
    } else {
        $mensaje = "Error al subir la imagen.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Personaje</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">

    <div class="container mt-5">
        <h2 class="text-center mb-4">Agregar Nuevo Personaje</h2>

        <?php if ($mensaje): ?>
            <div class="alert alert-info"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Nombre del Personaje</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Color Representativo</label>
                <input type="text" name="color" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Tipo / Rol</label>
                <input type="text" name="tipo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Nivel</label>
                <input type="number" name="nivel" class="form-control" min="1" required>
            </div>
            <div class="mb-3">
                <label>Foto del Personaje</label>
                <input type="file" name="foto" class="form-control" accept="image/*" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Guardar Personaje</button>
                <a href="index.php" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>

</body>
</html>
