<?php
require_once 'db_config.php';
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("ID no especificado");
}

$id = $_GET['id'];

$sql = "SELECT * FROM personajes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("Personaje no encontrado");
}

$personaje = $resultado->fetch_assoc();

// Iniciar DomPDF
$dompdf = new Dompdf();

// Estilo del PDF
$estilo = '
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .contenedor {
            border: 2px solid #333;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
        }
        .titulo {
            font-size: 24px;
            font-weight: bold;
            color: #d35400;
            text-align: center;
        }
        .dato {
            font-size: 16px;
            margin: 10px 0;
        }
        .foto {
            width: 200px;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 10px;
        }
    </style>
';

$html = '
    <div class="contenedor">
        <div class="titulo">Perfil del Personaje</div>
        <img class="foto" src="' . $personaje['foto'] . '" alt="Foto del personaje">
        <div class="dato"><strong>Nombre:</strong> ' . $personaje['nombre'] . '</div>
        <div class="dato"><strong>Color Representativo:</strong> ' . $personaje['color'] . '</div>
        <div class="dato"><strong>Tipo:</strong> ' . $personaje['tipo'] . '</div>
        <div class="dato"><strong>Nivel:</strong> ' . $personaje['nivel'] . '</div>
    </div>
';

$dompdf->loadHtml($estilo . $html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Descargar
$dompdf->stream("personaje_" . $personaje['nombre'] . ".pdf", array("Attachment" => false));
exit;
?>
