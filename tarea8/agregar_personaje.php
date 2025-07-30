<?php
require_once 'db_config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $color = $_POST["color"];
    $tipo = $_POST["tipo"];
    $nivel = intval($_POST["nivel"]);
    
    // Subir foto
    if ($_FILES["foto"]["error"] == 0) {
        $foto_nombre = uniqid() . "_" . basename($_FILES["foto"]["name"]);
        $foto_ruta = "uploads/" . $foto_nombre;

        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $foto_ruta)) {
            $stmt = $conn->prepare("INSERT INTO personajes (nombre, color, tipo, nivel, foto) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssds", $nombre, $color, $tipo, $nivel, $foto_ruta);
            if ($stmt->execute()) {
                $mensaje = "✅ Personaje agregado exitosamente.";
            } else {
                $mensaje = "❌ Error al guardar: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensaje = "❌ Error al subir la foto.";
        }
    } else {
        $mensaje = "❌ Debes seleccionar una foto válida.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Personaje</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="mb-4">➕ Agregar nuevo personaje</h2>
    
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Color Representativo</label>
            <input type="text" name="color" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Tipo / Rol</label>
            <input type="text" name="tipo" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Nivel</label>
            <input type="number" name="nivel" class="form-control" required>
        </div>
        <div class="col-md-9">
            <label class="form-label">Foto</label>
            <input type="file" name="foto" accept="image/*" class="form-control" required>
        </div>
        <div class="col-12 d-flex justify-content-between">
            <a href="index.php" class="btn btn-secondary">⬅️ Volver</a>
            <button type="submit" class="btn btn-success">Guardar Personaje</button>
        </div>
    </form>
</div>

</body>
</html>
