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
    <div id="contenedor" class="app-container contenedorActivo">
        <header class="header">
            <div class="header-top">
                <h1>Gestión de Movimientos de Stock</h1>
                <div class="header-actions">
                    <button type="button" id="btn-cargar">Cargar datos</button>
                    <button type="button" id="btn-limpiar">Limpiar filtros</button>
                    <button type="button" id="btn-alta">Alta registro</button>
                    <button type="button" id="btn-cerrar-sesion" style="background-color: #dc3545;">Cerrar Sesión</button>
                </div>
            </div>
            
            <div class="filtros-grid">
                <div class="filtro-item">
                    <label class="filtro-label" for="filtro-cod">Cod. Artículo:</label>
                    <input type="text" id="filtro-cod" name="filtro-cod" placeholder="Cod. Artículo">
                </div>
                <div class="filtro-item">
                    <label class="filtro-label" for="filtro-desc">Descripción:</label>
                    <input type="text" id="filtro-desc" name="filtro-desc" placeholder="Descripción">
                </div>
                <div class="filtro-item">
                    <label class="filtro-label" for="filtro-lote">Nro. de Lote:</label>
                    <input type="text" id="filtro-lote" name="filtro-lote" placeholder="Nro. de Lote">
                </div>
                <select id="filtro-tipo-mov" name="filtro-tipo-mov">
                    <option value="">Todos</option>
                </select>
            </div>
        </header>

        <main class="main-content">
            <div class="table-container">
                <div id="tabla-container">
                    <p>Presione "Cargar Datos" para ver los movimientos.</p>
                </div>
            </div>
        </main>

        <footer class="footer">
            <p> Pie</p>
        </footer>
    </div>

    <div id="modal-formulario" class="ventanaModalApagado">
        <div class="modal-content">
            <button id="modal-form-cerrar">✖</button>
            <h2 id="modal-titulo">Formulario</h2>
            <form id="formulario-abm" enctype="multipart/form-data">
                
                <input type="hidden" id="form-idmov" name="IdMov_original">
                <input type="hidden" id="form-codarticulo" name="CodArticulo_original">
                <input type="hidden" id="form-lote" name="NroDeLote_original">

                <div class="form-grid">
                    <div>
                        <label for="form-tipo-mov-dd">Tipo Movimiento:</label>
                        <select id="form-tipo-mov-dd" name="TipodeMov" required></select>
                    </div>
                    <div>
                        <label for="form-cod-art">Cod. Artículo:</label>
                        <!-- CodArticulo ahora es autonumérico; no editable en el formulario -->
                        <input type="text" id="form-cod-art" name="CodArticulo" readonly placeholder="Autogenerado" maxlength="10">
                    </div>
                    <div>
                        <label for="form-nro-lote">Nro. de Lote:</label>
                        <input type="text" id="form-nro-lote" name="NroDeLote" required maxlength="50">
                    </div>
                    <div>
                        <label for="form-fecha">Fecha Movimiento:</label>
                        <input type="date" id="form-fecha" name="fecha_Movimiento" required>
                    </div>
                    <div>
                        <label for="form-unidad">Unidad Medida:</label>
                        <input type="text" id="form-unidad" name="Unidad_medida" required maxlength="10">
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

    <div id="modal-respuesta" class="ventanaModalApagado">
        <div class="modal-content">
            <button id="modal-resp-cerrar">✖</button>
            <h2>Respuesta del Servidor</h2>
            <div id="modal-respuesta-contenido" style="white-space: pre-line;"></div>
        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>