<!DOCTYPE html>
<html>
<head>
    <title>CRUD Artículos</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; }
        .contenedorPasivo { opacity: 0.5; pointer-events: none; }
        .modal {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5); z-index: 1000;
            display: flex; justify-content: center; align-items: center;
            visibility: hidden;
        }
        .modal-content { 
            background: white; padding: 20px; border-radius: 5px; width: 80%; max-width: 600px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .tabla-articulos { border-collapse: collapse; width: 100%; margin-top: 10px; }
        .tabla-articulos th, .tabla-articulos td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .btCelda { cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; }
        
        #formArticulosModi ul, #formArticulosAlta ul { list-style: none; padding: 0; }
        #formArticulosModi li, #formArticulosAlta li { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div id="contenedorTablaArticulos" class="contenedorActivo">
        <h1>Artículos.</h1>

        <div id="controles">
            Orden: <input type="text" id="orden" value="codArt">
            <button id="cargarDatos">Cargar datos</button>
            <button id="vaciarDatos">Vaciar datos</button>
            <button id="limpiarFiltros">Limpiar filtros</button>
            <button id="altaRegistro">Alta registro</button>
        </div>

        <table class="tabla-articulos">
            <thead>
                <tr style="background-color: #ff6666;">
                    <th>Cod Art</th><th>Familia</th><th>UM</th><th>Descrip</th><th>Fecha Alta</th><th>Saldo stock</th><th>PDFs</th><th>Modis</th><th>Bajas</th>
                </tr>
                <tr>
                    <td><input type="text" id="filtroCodArt"></td>
                    <td><select id="filtroFamilia"></select></td>
                    <td><input type="text" id="filtroUM"></td>
                    <td><input type="text" id="filtroDescrip"></td>
                    <td><input type="date" id="filtroFechaAlta"></td>
                    <td><input type="text" id="filtroSaldoStock"></td>
                    <td></td><td></td><td></td>
                </tr>
            </thead>
            <tbody id="tbDatos"></tbody>
        </table>
    </div>

    <div id="ventanaModalRespuesta" class="modal">
        <div class="modal-content">
            <h2>Respuesta del servidor</h2>
            <div id="contenidoModalRespuesta"></div>
            <button onclick="$('#ventanaModalRespuesta').css('visibility', 'hidden');">Cerrar</button>
        </div>
    </div>

    <div id="ventanaModalFormularioAlta" class="modal">
        <div class="modal-content">
            <h2>Encabezado modal Formulario de alta</h2>
            <form id="formArticulosAlta" method="post" enctype="multipart/form-data">
                <ul>
                    <li><label>codArt: </label><input id="formArticulosEntCodArtAlta" name="codArt" required /></li>
                    <li><label>Familia de artículo: </label><select id="formArticulosEntFamiliaAlta" name="familia" required></select></li>
                    <li><label>Fecha Alta: </label><input type="date" id="formArticulosEntFechaAlta" name="fechaAlta" required /></li>
                    <li><label>Descripción: </label><input id="formArticulosEntDescripcionAlta" name="descripcion" required /></li>
                    <li><label>UM: </label><input id="formArticulosEntUmAlta" name="um" required /></li>
                    <li><label>Saldo stock: </label><input id="formArticulosEntSaldoStockAlta" name="saldoStock" type="number" required /></li>
                    <li><label>Documento Pdf: </label><input type="file" id="formArticulosEntDocumentoPdfAlta" name="documentoPdf" /></li>
                </ul>
                <button type="submit" id="btEnvioFormAlta" disabled>Enviar Alta</button>
            </form>
        </div>
    </div>

    <div id="ventanaModalFormularioModi" class="modal">
        <div class="modal-content">
            <h2>Encabezado modal Formulario de modificación</h2>
            <form id="formArticulosModi" method="post" enctype="multipart/form-data">
                <ul>
                    <li><label>codArt: </label><input id="formArticulosEntCodArtModi" name="codArt" readonly required /></li>
                    <li><label>Familia de artículo: </label><select id="formArticulosEntFamiliaModi" name="familia" required></select></li>
                    <li><label>Fecha Alta: </label><input type="date" id="formArticulosEntFechaAltaModi" name="fechaAlta" required /></li>
                    <li><label>Descripción: </label><input id="formArticulosEntDescripcionModi" name="descripcion" required /></li>
                    <li><label>UM: </label><input id="formArticulosEntUmModi" name="um" required /></li>
                    <li><label>Saldo stock: </label><input id="formArticulosEntSaldoStockModi" name="saldoStock" type="number" required /></li>
                    <li><label>Documento Pdf: </label><input type="file" id="formArticulosEntDocumentoPdfModi" name="documentoPdf" /></li>
                </ul>
                <button type="submit" id="btEnvioFormModi" disabled>Enviar Modi</button>
            </form>
        </div>
    </div>

    <script>
        var objGlobalFamilias = {}; 
        var objTbDatos = document.getElementById("tbDatos");

        function todoListoParaAlta() {
            if (document.getElementById("formArticulosAlta").checkValidity()){
                $("#btEnvioFormAlta").attr("disabled", false);
            } else {
                $("#btEnvioFormAlta").attr("disabled", true);
            }
        }
        
        function todoListoParaModi() {
            if (document.getElementById("formArticulosModi").checkValidity()){
                $("#btEnvioFormModi").attr("disabled", false);
            } else {
                $("#btEnvioFormModi").attr("disabled", true);
            }
        }
        
        function mostrarPDF(argCodArt) {
            $("#contenedorTablaArticulos").addClass("contenedorPasivo");
            const data = new URLSearchParams();
            data.append('codArt', argCodArt); 

            const options = {
                method: 'POST',
                headers: {},
                body: data 
            }
            
            fetch('./traeDoc.php', options)
            .then(respuestaDelServer => {
                return respuestaDelServer.text(); 
            })
            .then(datos => {
                var objetoDato = JSON.parse(datos); 

                $("#ventanaModalRespuesta").css("visibility", "visible"); 
                $("#contenidoModalRespuesta").empty(); 
                
                $("#contenidoModalRespuesta").html("<iframe width='100%' height='600px' src='data:application/pdf;base64," + objetoDato.documentoPdf + "'></iframe>"); 
            });
        }

        function alta() {
            var objFormulario = document.getElementById("formArticulosAlta");
            var objDatosFormulario = new FormData(objFormulario); 
            
            const options = {
                method: 'post',
                body: objDatosFormulario, 
            }

            fetch('./alta.php', options)
            .then(respuesta => {
                return respuesta.text(); 
            })
            .then(datos => {
                alert(datos); 
                $("#ventanaModalFormularioAlta").css("visibility", "hidden");
                $("#contenedorTablaArticulos").removeClass("contenedorPasivo");
                
                $("#contenidoModalRespuesta").empty(); 
                $("#contenidoModalRespuesta").append(datos); 
                $("#ventanaModalRespuesta").css("visibility", "visible"); 
            });
        }

        function modi() {
            var objFormulario = document.getElementById("formArticulosModi");
            var objDatosFormulario = new FormData(objFormulario); 
            
            const options = { 
                method: 'post', 
                headers: {}, 
                body: objDatosFormulario, 
            }

            fetch('./modi.php', options) 
            .then(respuesta => {
                return respuesta.text(); 
            })
            .then(datos => {
                alert(datos); 
                
                $("#ventanaModalFormularioModi").css("visibility", "hidden"); 
                $("#contenedorTablaArticulos").removeClass("contenedorPasivo"); 
                
                $("#contenidoModalRespuesta").empty(); 
                $("#contenidoModalRespuesta").append(datos); 
                $("#ventanaModalRespuesta").css("visibility", "visible"); 
            });
        }
        
        function baja(argCodArt) {
            if (confirm("¿Está seguro de eliminar registro? " + argCodArt)) { 
                const data = new URLSearchParams();
                data.append('codArt', argCodArt);

                const options = {
                    method: 'POST',
                    body: data 
                }

                fetch('./baja.php', options)
                .then(respuesta => respuesta.text())
                .then(datos => {
                    alert(datos);
                    $("#contenidoModalRespuesta").empty(); 
                    $("#contenidoModalRespuesta").append(datos); 
                    $("#ventanaModalRespuesta").css("visibility", "visible"); 
                });
            }
        }
        
        function CompletaFichaArticulo(argArticulo) { 
            $("#formArticulosEntCodArtModi").val(argArticulo.codArt); 
            $("#formArticulosEntFechaAltaModi").val(argArticulo.fechaAlta);
            $("#formArticulosEntDescripcionModi").val(argArticulo.descripcion);
            $("#formArticulosEntUmModi").val(argArticulo.um);
            $("#formArticulosEntSaldoStockModi").val(argArticulo.saldoStock);
            
            var selectFamilia = document.getElementById("formArticulosEntFamiliaModi");
            selectFamilia.innerHTML = ""; 

            objGlobalFamilias.familias.forEach(function(argValorFamilia) {
                var objOption = document.createElement("option"); 
                objOption.setAttribute("value", argValorFamilia.codFamilia); 
                objOption.innerHTML = argValorFamilia.codFamilia + " - " + argValorFamilia.descripcionFamilia;
                
                if (objOption.value == argArticulo.familia) { 
                    objOption.setAttribute("selected", "selected"); 
                }
                selectFamilia.appendChild(objOption); 
            });
            todoListoParaModi();
        }

        function cargarTabla(objDatos) {
            objTbDatos.innerHTML = "";
            objDatos.articulos.forEach(function(argValor) {
                var objFila = document.createElement("tr");
                
                var objTdCodArt = document.createElement("td");
                objTdCodArt.innerHTML = argValor.codArt;
                objFila.appendChild(objTdCodArt);
                
                var objTdFamilia = document.createElement("td");
                objTdFamilia.innerHTML = argValor.familia;
                objFila.appendChild(objTdFamilia);
                
                var objTdUM = document.createElement("td");
                objTdUM.innerHTML = argValor.um;
                objFila.appendChild(objTdUM);

                var objTdDescrip = document.createElement("td");
                objTdDescrip.innerHTML = argValor.descripcion;
                objFila.appendChild(objTdDescrip);

                var objTdFechaAlta = document.createElement("td");
                objTdFechaAlta.innerHTML = argValor.fechaAlta;
                objFila.appendChild(objTdFechaAlta);

                var objTdSaldoStock = document.createElement("td");
                objTdSaldoStock.innerHTML = argValor.saldoStock;
                objFila.appendChild(objTdSaldoStock);
                
                var objTdPDF = document.createElement("td");
                objTdPDF.innerHTML = "<button class='btCelda'>PDF</button>";
                objTdPDF.onclick = function() {
                    mostrarPDF(argValor.codArt); 
                };
                objFila.appendChild(objTdPDF);

                var objTdModi = document.createElement("td");
                objTdModi.setAttribute("campo-dato", "articulos_btModi"); 
                objTdModi.innerHTML = "<button class='btCelda'>Modi</button>"; 
                objTdModi.onclick = function() { 
                    $("#contenedorTablaArticulos").addClass("contenedorPasivo"); 
                    $("#ventanaModalFormularioModi").css("visibility", "visible"); 
                    CompletaFichaArticulo(argValor); 
                };
                objFila.appendChild(objTdModi);

                var objTdBorrar = document.createElement("td");
                objTdBorrar.innerHTML = "<button class='btCelda'>Borrar</button>";
                objTdBorrar.onclick = function() {
                    baja(argValor.codArt);
                };
                objFila.appendChild(objTdBorrar);
                
                objTbDatos.appendChild(objFila);
            });
        }

        function cargarDatosIniciales() {
            fetch('./salidaJsonFamilias.php')
            .then(respuesta => respuesta.text())
            .then(datos => {
                alert(datos); 
                objGlobalFamilias = JSON.parse(datos);
                // Se asume que llenaFiltroFamilias() poblaría los select de filtros
            });
            // Se asume que se llama a cargarTabla después de cargar las familias
        }


        $(document).ready(function() {
            $("#ventanaModalFormularioAlta").css("visibility", "hidden");
            $("#ventanaModalFormularioModi").css("visibility", "hidden");
            $("#ventanaModalRespuesta").css("visibility", "hidden");
            
            $("#btEnvioFormModi").attr("disabled", true);
            $("#btEnvioFormAlta").attr("disabled", true);

            $("#formArticulosAlta").submit(function(e) {
                e.preventDefault(); 
                alta();
            });

            $("#formArticulosModi").submit(function(e) {
                e.preventDefault(); 
                if (confirm("¿Está seguro de modificar registro? " + $("#formArticulosEntCodArtModi").val())) {
                    modi();
                }
            });

            $("#formArticulosAlta input").on('keyup change', todoListoParaAlta);
            $("#formArticulosAlta select").on('change', todoListoParaAlta);
            
            $("#formArticulosModi input").on('keyup change', todoListoParaModi);
            $("#formArticulosModi select").on('change', todoListoParaModi);
            
            $("#altaRegistro").click(function() {
                $("#contenedorTablaArticulos").addClass("contenedorPasivo"); 
                $("#ventanaModalFormularioAlta").css("visibility", "visible"); 
                var selectFamiliaAlta = document.getElementById("formArticulosEntFamiliaAlta");
                selectFamiliaAlta.innerHTML = "";
                objGlobalFamilias.familias.forEach(function(argValorFamilia) {
                    var objOption = document.createElement("option"); 
                    objOption.setAttribute("value", argValorFamilia.codFamilia); 
                    objOption.innerHTML = argValorFamilia.codFamilia + " - " + argValorFamilia.descripcionFamilia;
                    selectFamiliaAlta.appendChild(objOption); 
                });
                todoListoParaAlta();
            });

            $("#cargarDatos").click(cargarDatosIniciales);
        });
    </script>
</body>
</html>