<?php
// Incluimos la protección de sesión EN LA PRIMERA LÍNEA
include_once 'manejoDeSesion.inc.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestión de Stock</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <h1>Gestión de Movimientos de Stock</h1>
        <button id="btn-cerrar-sesion" class="btn-logout">Cerrar Sesión</button>
    </header>

    <main>
        <div class="filtros-container">
            <div class="filtro-item">
                <label for="filtro-cod">Cod. Artículo:</label>
                <input type="text" id="filtro-cod" name="filtro-cod">
            </div>
            <div class="filtro-item">
                <label for="filtro-desc">Descripción:</label>
                <input type="text" id="filtro-desc" name="filtro-desc">
            </div>
            <div class="filtro-item">
                <label for="filtro-lote">Nro. de Lote:</label>
                <input type="text" id="filtro-lote" name="filtro-lote">
            </div>
            <div class="filtro-item">
                <label for="filtro-tipo-mov">Tipo Movimiento:</label>
                <select id="filtro-tipo-mov" name="filtro-tipo-mov">
                    <option value="">-- Todos --</option>
                </select>
            </div>
            <div class="filtro-botones">
                <button id="btn-cargar" class="btn-primary">Cargar Datos</button>
                <button id="btn-limpiar" class="btn-secondary">Limpiar Filtros</button>
                <button id="btn-alta" class="btn-success">Alta Registro</button>
            </div>
        </div>

        <div id="tabla-container">
            <p>Presione "Cargar Datos" para ver los movimientos.</p>
        </div>
    </main>

    <div id="modal-formulario" class="modal-oculto">
        <div class="modal-contenido">
            <span class="modal-cerrar" id="modal-form-cerrar">&times;</span>
            <h2 id="modal-titulo"></h2>
            <form id="formulario-abm" enctype="multipart/form-data">
                
                <input type="hidden" id="form-idmov" name="IdMov_original">
                <input type="hidden" id="form-codarticulo" name="CodArticulo_original">
                <input type="hidden" id="form-lote" name="NroDeLote_original">

                <div class="form-grid">
                    <div>
                        <label for="form-tipo-mov-dd">Tipo Movimiento:</label>
                        <select id="form-tipo-mov-dd" name="IdMov" required></select>
                    </div>
                    <div>
                        <label for="form-cod-art">Cod. Artículo:</label>
                        <input type="text" id="form-cod-art" name="CodArticulo" required maxlength="10">
                    </div>
                    <div>
                        <label for="form-nro-lote">Nro. de Lote:</label>
                        <input type="text" id="form-nro-lote" name="NroDeLote" required maxlength="10">
                    </div>
                    <div>
                        <label for="form-fecha">Fecha Movimiento:</label>
                        <input type="date" id="form-fecha" name="FechaMovimiento" required>
                    </div>
                    <div>
                        <label for="form-unidad">Unidad Medida:</label>
                        <input type="text" id="form-unidad" name="UnidadMedida" required maxlength="5">
                    </div>
                    <div>
                        <label for="form-cantidad">Cantidad:</label>
                        <input type="number" id="form-cantidad" name="Cantidad" required>
                    </div>
                </div>
                
                <div>
                    <label for="form-desc">Descripción:</label>
                    <input type="text" id="form-desc" name="Descripcion" required maxlength="60">
                </div>

                <div>
                    <label for="form-foto">Foto Artículo:</label>
                    <input type="file" id="form-foto" name="FotoArticulo" accept="image/jpeg,image/png,image/gif">
                    <small>Subir una nueva foto reemplazará la existente. Dejar vacío para no modificar.</small>
                    <div id="foto-actual-preview"></div>
                </div>
                
                <button type="submit" id="btn-form-enviar" class="btn-primary">Enviar</button>
            </form>
        </div>
    </div>

    <div id="modal-respuesta" class="modal-oculto">
        <div class="modal-contenido-respuesta">
            <span class="modal-cerrar" id="modal-resp-cerrar">&times;</span>
            <h2>Respuesta del Servidor</h2>
            <pre id="modal-respuesta-contenido"></pre>
        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>