<?php

function generarNumeroRecibo() {

    return 'REC-' . date('Ymd') . '-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
}

$reciboGenerado = '';
$mostrarRecibo = false;
$datosFactura = [];
$reporteDiario = [
    'cantidad_facturas' => 0,
    'total_dinero' => 0.0
];


$clientes = [
    'MAT-001' => 'Juan Pérez',
    'MAT-002' => 'María García',
    'MAT-003' => 'Pedro López'
];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_factura'])) {
    $datosFactura['fecha'] = date('d/m/Y');
    $datosFactura['numero_recibo'] = generarNumeroRecibo();
    $datosFactura['codigo_cliente'] = htmlspecialchars($_POST['codigo_cliente'] ?? '');
    $datosFactura['nombre_cliente'] = htmlspecialchars($_POST['nombre_cliente'] ?? '');
    $datosFactura['comentario'] = htmlspecialchars($_POST['comentario'] ?? '');

    $datosFactura['articulos'] = [];
    $totalPagar = 0;

    
    if (isset($_POST['nombre_articulo']) && is_array($_POST['nombre_articulo'])) {
        for ($i = 0; $i < count($_POST['nombre_articulo']); $i++) {
            $nombre = htmlspecialchars($_POST['nombre_articulo'][$i]);
            $cantidad = (int)($_POST['cantidad_articulo'][$i]);
            $precio = (float)($_POST['precio_articulo'][$i]);

            if (!empty($nombre) && $cantidad > 0 && $precio >= 0) {
                $totalArticulo = $cantidad * $precio;
                $datosFactura['articulos'][] = [
                    'nombre' => $nombre,
                    'cantidad' => $cantidad,
                    'precio_unidad' => $precio,
                    'total_articulo' => $totalArticulo
                ];
                $totalPagar += $totalArticulo;
            }
        }
    }
    $datosFactura['total_pagar'] = $totalPagar;

    
    $mostrarRecibo = true;


    $reporteDiario['cantidad_facturas'] = 1;
    $reporteDiario['total_dinero'] = $totalPagar;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ventas - La Rubia</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            margin-bottom: 20px;
        }
        h1, h2 {
            color: #333;
            text-align: center;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"], input[type="number"], textarea {
            width: calc(100% - 20px);
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
        }
        input[type="number"] {
            -moz-appearance: textfield; /* Firefox */
        }
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .articulo-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        .articulo-row input {
            flex: 1;
            margin-bottom: 0;
        }
        .articulo-row .nombre-articulo {
            flex: 3;
        }
        .articulo-row .cantidad-articulo, .articulo-row .precio-articulo {
            flex: 1;
        }
        .articulo-row .total-articulo {
            flex: 1;
            background-color: #e9ecef;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
        }
        .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        button {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        button.primary {
            background-color: #28a745; /* Verde */
            color: white;
            background-image: linear-gradient(to right, #28a745, #218838);
        }
        button.primary:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        button.secondary {
            background-color: #dc3545; /* Rojo */
            color: white;
            background-image: linear-gradient(to right, #dc3545, #c82333);
        }
        button.secondary:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        button.add-item {
            background-color: #007bff; /* Azul */
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 8px;
            margin-top: 10px;
            display: block;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
            background-image: linear-gradient(to right, #007bff, #0056b3);
        }
        button.add-item:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }
        .total-display {
            text-align: right;
            font-size: 24px;
            font-weight: bold;
            margin-top: 20px;
            color: #333;
        }
        .recibo-preview {
            background-color: #e9ffe9;
            border: 1px solid #c3e6cb;
            padding: 25px;
            margin-top: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 600px;
            display: <?php echo $mostrarRecibo ? 'block' : 'none'; ?>;
        }
        .recibo-preview h3 {
            text-align: center;
            color: #28a745;
            margin-bottom: 20px;
            font-size: 28px;
        }
        .recibo-preview p {
            margin-bottom: 10px;
            line-height: 1.6;
            color: #444;
        }
        .recibo-preview .line-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #ccc;
            padding: 5px 0;
        }
        .recibo-preview .line-item span:first-child {
            flex: 2;
        }
        .recibo-preview .line-item span:nth-child(2) {
            flex: 0.5;
            text-align: center;
        }
        .recibo-preview .line-item span:nth-child(3) {
            flex: 1;
            text-align: right;
        }
        .recibo-preview .recibo-total {
            font-size: 22px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
            color: #28a745;
        }
        .reporte-diario {
            background-color: #e0f7fa;
            border: 1px solid #b2ebf2;
            padding: 25px;
            margin-top: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }
        .reporte-diario h3 {
            color: #007bff;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .reporte-diario p {
            font-size: 18px;
            margin-bottom: 10px;
            color: #444;
        }
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
            .articulo-row {
                flex-direction: column;
                gap: 5px;
            }
            .articulo-row input, .articulo-row .total-articulo {
                width: 100%;
            }
            .button-group {
                flex-direction: column;
                gap: 10px;
            }
            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sistema de Ventas - La Rubia</h1>
        <h2>Registro de Factura</h2>

        <form method="POST" action="index.php">
            <div class="form-group">
                <label>Fecha:</label>
                <input type="text" value="<?php echo date('d/m/Y'); ?>" readonly>
            </div>

            <div class="form-group">
                <label>Nº Recibo:</label>
                <input type="text" value="<?php echo generarNumeroRecibo(); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="codigo_cliente">Código Cliente:</label>
                <input type="text" id="codigo_cliente" name="codigo_cliente" placeholder="Ej: MAT-001" onkeyup="buscarCliente()" required>
            </div>

            <div class="form-group">
                <label for="nombre_cliente">Nombre Cliente:</label>
                <input type="text" id="nombre_cliente" name="nombre_cliente" placeholder="Nombre del cliente" required>
            </div>

            <div class="form-group">
                <label>Artículos:</label>
                <div id="articulos-container">
                    <!-- Fila de artículo inicial -->
                    <div class="articulo-row">
                        <input type="text" name="nombre_articulo[]" class="nombre-articulo" placeholder="Nombre del artículo" required>
                        <input type="number" name="cantidad_articulo[]" class="cantidad-articulo" placeholder="Cantidad" min="1" value="1" oninput="calcularTotal()">
                        <input type="number" name="precio_articulo[]" class="precio-articulo" placeholder="Precio Unidad" step="0.01" min="0" oninput="calcularTotal()">
                        <div class="total-articulo">RD$ <span>0.00</span></div>
                    </div>
                </div>
                <button type="button" class="add-item" onclick="agregarArticulo()">+ Añadir Artículo</button>
            </div>

            <div class="total-display">
                Total a pagar: RD$ <span id="total-pagar">0.00</span>
            </div>

            <div class="form-group">
                <label for="comentario">Comentario (opcional):</label>
                <textarea id="comentario" name="comentario" rows="3" placeholder="Ej: Pagó en efectivo"></textarea>
            </div>

            <div class="button-group">
                <button type="submit" name="guardar_factura" class="primary">Guardar e Imprimir</button>
                <button type="reset" class="secondary">Cancelar</button>
            </div>
        </form>
    </div>

    <?php if ($mostrarRecibo): ?>
        <div class="recibo-preview" id="recibo-imprimir">
            <h3>Recibo de Venta</h3>
            <p><strong>Fecha:</strong> <?php echo $datosFactura['fecha']; ?></p>
            <p><strong>Nº Recibo:</strong> <?php echo $datosFactura['numero_recibo']; ?></p>
            <p><strong>Código Cliente:</strong> <?php echo $datosFactura['codigo_cliente']; ?></p>
            <p><strong>Nombre Cliente:</strong> <?php echo $datosFactura['nombre_cliente']; ?></p>
            <hr>
            <p><strong>Artículos:</strong></p>
            <?php foreach ($datosFactura['articulos'] as $item): ?>
                <div class="line-item">
                    <span><?php echo $item['nombre']; ?></span>
                    <span><?php echo $item['cantidad']; ?></span>
                    <span>RD$ <?php echo number_format($item['precio_unidad'], 2); ?></span>
                    <span>RD$ <?php echo number_format($item['total_articulo'], 2); ?></span>
                </div>
            <?php endforeach; ?>
            <hr>
            <p class="recibo-total">Total a pagar: RD$ <?php echo number_format($datosFactura['total_pagar'], 2); ?></p>
            <?php if (!empty($datosFactura['comentario'])): ?>
                <p><strong>Comentario:</strong> <?php echo $datosFactura['comentario']; ?></p>
            <?php endif; ?>
            <div class="button-group">
                <button type="button" class="primary" onclick="window.print()">Imprimir Recibo</button>
            </div>
        </div>
    <?php endif; ?>

    <div class="reporte-diario">
        <h3>Reporte Diario (Simulado)</h3>
        <p>Cantidad de facturas hoy: <?php echo $reporteDiario['cantidad_facturas']; ?></p>
        <p>Total de dinero cobrado hoy: RD$ <?php echo number_format($reporteDiario['total_dinero'], 2); ?></p>
        <p style="font-size: 14px; color: #777;">(Este reporte es solo para la factura actual. Se requiere una base de datos para un reporte real.)</p>
    </div>

    <script>
        
        const clientesData = <?php echo json_encode($clientes); ?>;

        function buscarCliente() {
            const codigoClienteInput = document.getElementById('codigo_cliente');
            const nombreClienteInput = document.getElementById('nombre_cliente');
            const codigo = codigoClienteInput.value.trim().toUpperCase();

            if (clientesData[codigo]) {
                nombreClienteInput.value = clientesData[codigo];
                nombreClienteInput.readOnly = true;
            } else {
                nombreClienteInput.value = '';
                nombreClienteInput.readOnly = false;
            }
        }

        
        function agregarArticulo() {
            const container = document.getElementById('articulos-container');
            const newRow = document.createElement('div');
            newRow.classList.add('articulo-row');
            newRow.innerHTML = `
                <input type="text" name="nombre_articulo[]" class="nombre-articulo" placeholder="Nombre del artículo" required>
                <input type="number" name="cantidad_articulo[]" class="cantidad-articulo" placeholder="Cantidad" min="1" value="1" oninput="calcularTotal()">
                <input type="number" name="precio_articulo[]" class="precio-articulo" placeholder="Precio Unidad" step="0.01" min="0" oninput="calcularTotal()">
                <div class="total-articulo">RD$ <span>0.00</span></div>
                <button type="button" class="secondary" style="padding: 8px 12px; font-size: 14px; border-radius: 5px; box-shadow: none; background-image: none;" onclick="eliminarArticulo(this)">X</button>
            `;
            container.appendChild(newRow);
            calcularTotal();
        }

        
        function eliminarArticulo(button) {
            const row = button.parentNode;
            row.parentNode.removeChild(row);
            calcularTotal();
        }

        
        function calcularTotal() {
            const articulosRows = document.querySelectorAll('.articulo-row');
            let totalPagarGeneral = 0;

            articulosRows.forEach(row => {
                const cantidadInput = row.querySelector('.cantidad-articulo');
                const precioInput = row.querySelector('.precio-articulo');
                const totalArticuloSpan = row.querySelector('.total-articulo span');

                const cantidad = parseFloat(cantidadInput.value) || 0;
                const precio = parseFloat(precioInput.value) || 0;

                const totalArticulo = cantidad * precio;
                totalArticuloSpan.textContent = totalArticulo.toFixed(2);
                totalPagarGeneral += totalArticulo;
            });

            document.getElementById('total-pagar').textContent = totalPagarGeneral.toFixed(2);
        }

        
        window.onload = function() {
            calcularTotal();
            
            <?php if ($mostrarRecibo): ?>
                const recibo = document.getElementById('recibo-imprimir');
                if (recibo) {
                    recibo.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            <?php endif; ?>
        };
    </script>
</body>
</html>
