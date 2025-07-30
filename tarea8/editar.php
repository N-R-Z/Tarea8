<?php
require_once 'db_config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = '';

// Validar si llegó un ID
if (!isset($_GET['id'])) {
    die("ID de personaje no especificado.");
}

$id = $_GET['id'];

// Obtener datos actuales
$sql = "SELECT * FROM personajes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$personaje = $resultado->fetch_assoc();

if (!$personaje) {
    die("Personaje no encontrado.");
}

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $color = $_POST['color'];
    $tipo = $_POST['tipo'];
    $nivel = $_POST['nivel'];

    // Revisar si se subió una nueva imagen
    if ($_FILES['foto']['name']) {
        $foto_nombre = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $ruta_destino = "uploads/" . uniqid() . "_" . $foto_nombre;

        if (move_uploaded_file($foto_tmp, $ruta_destino)) {
            // Eliminar imagen anterior si existía
            if (file_exists($personaje['foto'])) {
                unlink($personaje['foto']);
            }

            $sql = "UPDATE personajes SET nombre=?, color=?, tipo=?, nivel=?, foto=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssisi", $nombre, $color, $tipo, $nivel, $ruta_destino, $id);
        } else {
            $mensaje = "Error al subir nueva imagen.";
        }
    } else {
        $sql = "UPDATE personajes SET nombre=?, color=?, tipo=?, nivel=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $nombre, $color, $tipo, $nivel, $id);
    }

    if ($stmt->execute()) {
        $mensaje = "Personaje actualizado exitosamente.";
        // Recargar datos actualizados
        header("Location: editar.php?id=$id&success=1");
        exit;
    } else {
        $mensaje = "Error al actualizar el personaje.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Personaje</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Editar Personaje</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Personaje actualizado correctamente.</div>
        <?php elseif ($mensaje): ?>
            <div class="alert alert-info"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= $personaje['nombre'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Color Representativo</label>
                <input type="text" name="color" class="form-control" value="<?= $personaje['color'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Tipo / Rol</label>
                <input type="text" name="tipo" class="form-control" value="<?= $personaje['tipo'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Nivel</label>
                <input type="number" name="nivel" class="form-control" value="<?= $personaje['nivel'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Foto Actual</label><br>
                <img src="<?= $personaje['foto'] ?>" width="100"><br><br>
                <label>Cambiar Foto (opcional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-warning">Actualizar</button>
                <a href="index.php" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>
</body>
</html>
