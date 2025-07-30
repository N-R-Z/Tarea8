<?php
include 'db_config.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $color = $_POST["color"];
    $tipo = $_POST["tipo"];
    $nivel = $_POST["nivel"];

    // Procesar imagen
    $fotoNombre = $_FILES["foto"]["name"];
    $fotoTmp = $_FILES["foto"]["tmp_name"];
    $fotoDestino = "uploads/" . basename($fotoNombre);

    if (move_uploaded_file($fotoTmp, $fotoDestino)) {
        $sql = "INSERT INTO personajes (nombre, color, tipo, nivel, foto)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssis", $nombre, $color, $tipo, $nivel, $fotoDestino);

        if ($stmt->execute()) {
            $mensaje = "✅ Personaje agregado correctamente.";
        } else {
            $mensaje = "❌ Error al guardar en la base de datos.";
        }

        $stmt->close();
    } else {
        $mensaje = "❌ Error al subir la imagen.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Personaje - Naruto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .naruto-box {
            background-color: #ff9900;
            border: 3px solid #000;
            border-radius: 15px;
            padding: 25px;
            margin-top: 40px;
        }
        .naruto-header {
            font-size: 28px;
            font-weight: bold;
            color: #000;
        }
        .btn-naruto {
            background-color: #000;
            color: #fff;
        }
    </style>
</head>
<body class="container">

    <div class="naruto-box">
        <div class="naruto-header">Agregar Personaje de Naruto</div>
        <?php if ($mensaje) echo "<div class='alert alert-info mt-3'>$mensaje</div>"; ?>

        <form method="POST" enctype="multipart/form-data" class="mt-4">
            <div class="mb-3">
                <label>Nombre:</label>
                <input type="text" name="nombre" required class="form-control">
            </div>

            <div class="mb-3">
                <label>Color Representativo:</label>
                <input type="text" name="color" required class="form-control">
            </div>

            <div class="mb-3">
                <label>Tipo/Rol:</label>
                <input type="text" name="tipo" required class="form-control">
            </div>

            <div class="mb-3">
                <label>Nivel:</label>
                <input type="number" name="nivel" required class="form-control">
            </div>

            <div class="mb-3">
                <label>Foto:</label>
                <input type="file" name="foto" accept="image/*" required class="form-control">
            </div>

            <button type="submit" class="btn btn-naruto">Guardar Personaje</button>
        </form>
    </div>

</body>
</html>
