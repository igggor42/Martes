document.addEventListener('DOMContentLoaded', () => {

    const btnCargar = document.getElementById('btn-cargar');
    const btnLimpiar = document.getElementById('btn-limpiar');
    const btnAlta = document.getElementById('btn-alta');
    const btnCerrarSesion = document.getElementById('btn-cerrar-sesion');
    const tablaContainer = document.getElementById('tabla-container');
    const filtroTipoMov = document.getElementById('filtro-tipo-mov');

    const modalForm = document.getElementById('modal-formulario');
    const btnCerrarModalForm = document.getElementById('modal-form-cerrar');
    const formABM = document.getElementById('formulario-abm');
    const modalTitulo = document.getElementById('modal-titulo');
    const btnFormEnviar = document.getElementById('btn-form-enviar');
    const formTipoMovDD = document.getElementById('form-tipo-mov-dd');
    const fotoActualPreview = document.getElementById('foto-actual-preview');

    const modalRespuesta = document.getElementById('modal-respuesta');
    const btnCerrarModalResp = document.getElementById('modal-resp-cerrar');
    const modalRespuestaContenido = document.getElementById('modal-respuesta-contenido');
    
    // Asegurar que el contenedor existe
    const contenedor = document.getElementById('contenedor');

    async function manejarRespuestaFetch(response) {
        if (response.status === 401) {
            alert('Su sesión ha expirado. Será redirigido al login.');
            window.location.href = '../formularioDeLogin.html';
            return null;
        }
        if (!response.ok) {
            const error = await response.json();
            mostrarModalRespuesta(`Error: ${error.error || response.statusText}`);
            return null;
        }
        return response.json();
    }

    async function cargarTiposMov() {
        try {
            const response = await fetch('salida_json_tipomov.php');
            const data = await manejarRespuestaFetch(response);
            if (!data) return;

            filtroTipoMov.innerHTML = '<option value="">-- Todos --</option>';
            formTipoMovDD.innerHTML = '<option value="">-- Seleccione --</option>';

            data.forEach(tipo => {
                const optionFiltro = new Option(tipo.Descripcion, tipo.Codigo);
                filtroTipoMov.add(optionFiltro);
                
                const optionForm = new Option(tipo.Descripcion, tipo.Codigo);
                formTipoMovDD.add(optionForm);
            });
        } catch (error) {
            mostrarModalRespuesta(`Error al cargar tipos de movimiento: ${error.message}`);
        }
    }
    cargarTiposMov();

    async function cargarMovimientos() {
        tablaContainer.innerHTML = "<p>Cargando datos...</p>";
        const params = new URLSearchParams({
            cod: document.getElementById('filtro-cod').value,
            desc: document.getElementById('filtro-desc').value,
            lote: document.getElementById('filtro-lote').value,
            tipo_mov: filtroTipoMov.value
        });
        
        try {
            const response = await fetch(`salida_json_movimientos.php?${params.toString()}`);
            const data = await manejarRespuestaFetch(response);
            if (!data) {
                tablaContainer.innerHTML = "<p>No se pudieron cargar los datos.</p>";
                return;
            }
            if (data.length === 0) {
                tablaContainer.innerHTML = "<p>No se encontraron registros con esos filtros.</p>";
                return;
            }
            construirTabla(data);
        } catch (error) {
            tablaContainer.innerHTML = `<p>Error en la solicitud: ${error.message}</p>`;
        }
    }

    function construirTabla(data) {
        let html = '<table>';
        html += `<thead><tr>
            <th>Tipo Mov.</th><th>Cod. Art.</th><th>Descripción</th>
            <th>Lote</th><th>Fecha</th><th>UM</th><th>Cant.</th>
            <th>Foto</th><th>PDF</th><th>Acciones</th>
        </tr></thead>`;
        
        html += '<tbody>';
        data.forEach(item => {
            // Ahora el servidor devuelve FotoDataURI (base64) cuando existe
            const fotoUrl = item.FotoDataURI || '';
            const fotoHtml = fotoUrl ? `<img src="${fotoUrl}" alt="Foto" style="max-height:60px;">` : 'N/A';
            
            // Botón PDF
            const tieneFoto = item.tiene_foto === 'SI';
            const btnPDFClass = tieneFoto ? 'btn-pdf' : 'btn-pdf btn-pdf-disabled';
            const btnPDFHtml = `<button class="btn-accion ${btnPDFClass}" onclick="verPDF(${item.CodArticulo})" ${!tieneFoto ? 'disabled' : ''}>PDF</button>`;

            // Asegurar que siempre usemos el código, no el IdMov
            const tipodeMovCodigo = item.TipoMovCodigo || item.TipodeMov || '';
            
            html += `<tr 
                data-codarticulo="${item.CodArticulo}"
                    data-descripcion="${item.Descripcion}"
                    data-nrodelote="${item.NroDeLote}"
                    data-fecha_movimiento="${item.fecha_Movimiento}"
                    data-tipodemov="${tipodeMovCodigo}"
                    data-unidad_medida="${item.Unidad_medida || item.UnidadMedida || ''}"
                    data-cantidad="${item.Cantidad}"
                    data-fotoarticulo="${item.FotoDataURI || ''}">
                
                    <td>${item.TipoMovDescripcion} (${item.TipodeMov || item.TipoMovCodigo || ''})</td>
                <td>${item.CodArticulo}</td>
                <td>${item.Descripcion}</td>
                <td>${item.NroDeLote}</td>
                <td>${item.fecha_Movimiento}</td>
                <td>${item.Unidad_medida || item.UnidadMedida || ''}</td>
                <td>${item.Cantidad}</td>
                <td>${fotoHtml}</td>
                <td>${btnPDFHtml}</td>
                <td class="acciones-cell">
                    <button class="btn-warning btn-modi">Modi</button>
                    <button class="btn-danger btn-baja">Baja</button>
                </td>
            </tr>`;
        });
        html += '</tbody></table>';
        tablaContainer.innerHTML = html;
    }

    async function enviarFormulario(e) {
        e.preventDefault();
        btnFormEnviar.disabled = true;
        btnFormEnviar.textContent = 'Enviando...';
        
        const formData = new FormData(formABM);
        const esAlta = (modalTitulo.textContent === 'Alta de Movimiento');
        
        const url = esAlta ? 'alta.php' : 'modi.php';

        try {
            const response = await fetch(url, { method: 'POST', body: formData });
            const data = await manejarRespuestaFetch(response);
            if (!data) return;

            mostrarModalRespuesta(JSON.stringify(data, null, 2));
            cerrarModalFormulario();
            cargarMovimientos();
        } catch (error) {
            mostrarModalRespuesta(`Error: ${error.message}`);
        } finally {
            btnFormEnviar.disabled = false;
            btnFormEnviar.textContent = 'Enviar';
        }
    }
    
    async function ejecutarBaja(row) {
        const { codarticulo } = row.dataset;
        
        if (!confirm(`¿Está seguro que desea eliminar el registro (Art: ${codarticulo})?`)) {
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('CodArticulo', codarticulo);

            const response = await fetch('baja.php', { method: 'POST', body: formData });
            const data = await manejarRespuestaFetch(response);
            if (!data) return;

            mostrarModalRespuesta(JSON.stringify(data, null, 2));
            cargarMovimientos();
        } catch (error) {
            mostrarModalRespuesta(`Error: ${error.message}`);
        }
    }

    // --- LÓGICA DE MODALES ---
    function abrirModalAlta() {
        formABM.reset();
        modalTitulo.textContent = 'Alta de Movimiento';
        document.getElementById('form-tipo-mov-dd').disabled = false;
        // CodArticulo es autogenerado: mantenerlo readonly y vacío
        const inputCod = document.getElementById('form-cod-art');
        inputCod.readOnly = true;
        inputCod.value = '';
        document.getElementById('form-nro-lote').disabled = false;
        document.getElementById('form-idmov').value = '';
        document.getElementById('form-codarticulo').value = '';
        document.getElementById('form-lote').value = '';
        fotoActualPreview.innerHTML = '';
        modalForm.className = 'ventanaModalPrendido';
        document.getElementById('contenedor').className = 'app-container contenedorPasivo';
    }

    function abrirModalModi(row) {
        formABM.reset();
        modalTitulo.textContent = 'Modificación de Movimiento';
        const data = row.dataset;

        // Los atributos data- están en minúsculas, convertir a camelCase
        const codArticulo = data.codarticulo || '';
        const nroDeLote = data.nrodelote || '';
        const descripcion = data.descripcion || '';
        const fechaMovimiento = data.fecha_movimiento || '';
        // Asegurar que usemos el código, no el IdMov
        const tipodeMov = data.tipodemov || '';
        const unidadMedida = data.unidad_medida || '';
        const cantidad = data.cantidad || '';

        // Establecer el valor en el select (debe ser el código, no el IdMov)
        document.getElementById('form-tipo-mov-dd').value = tipodeMov;
        document.getElementById('form-cod-art').value = codArticulo;
        document.getElementById('form-nro-lote').value = nroDeLote;
        document.getElementById('form-desc').value = descripcion;
        document.getElementById('form-fecha').value = fechaMovimiento;
        document.getElementById('form-unidad').value = unidadMedida;
        document.getElementById('form-cantidad').value = cantidad;

        document.getElementById('form-codarticulo').value = codArticulo;
        document.getElementById('form-lote').value = nroDeLote;

        // Solo el CodArticulo no se puede cambiar (es el ID)
        document.getElementById('form-cod-art').disabled = true;
        document.getElementById('form-cod-art').readOnly = true;
        
        // El tipo de movimiento y el lote SÍ se pueden cambiar
        document.getElementById('form-tipo-mov-dd').disabled = false;
        document.getElementById('form-nro-lote').disabled = false;
        
        if (data.fotoarticulo) {
            // data-fotoarticulo contiene ahora la dataURI (base64) si existe
            fotoActualPreview.innerHTML = `<p>Foto actual:</p><img src="${data.fotoarticulo}" alt="Foto actual" style="max-width:200px;">`;
        } else {
            fotoActualPreview.innerHTML = '<p>No hay foto actual.</p>';
        }

        modalForm.className = 'ventanaModalPrendido';
        document.getElementById('contenedor').className = 'app-container contenedorPasivo';
    }

    function cerrarModalFormulario() { 
        modalForm.className = 'ventanaModalApagado';
        document.getElementById('contenedor').className = 'app-container contenedorActivo';
    }
    function mostrarModalRespuesta(texto) {
        modalRespuestaContenido.textContent = texto;
        modalRespuesta.className = 'ventanaModalPrendido';
        document.getElementById('contenedor').className = 'app-container contenedorPasivo';
    }
    function cerrarModalRespuesta() { 
        modalRespuesta.className = 'ventanaModalApagado';
        document.getElementById('contenedor').className = 'app-container contenedorActivo';
    }
    
    // Función para ver PDF (similar a galvis)
    function verPDF(codArticulo) {
        fetch(`traeDoc.php?cod_articulo=${encodeURIComponent(codArticulo)}`)
            .then(response => {
                if (!response.ok) {
                    return response.text().then(txt => { throw new Error(txt || 'Error al recuperar documento'); });
                }

                const contentType = response.headers.get('Content-Type') || '';
                if (!contentType.includes('application/pdf')) {
                    return response.text().then(txt => { throw new Error(txt || 'Respuesta inesperada del servidor'); });
                }

                return response.blob();
            })
            .then(blob => {
                console.debug('PDF recibido del servidor, abriendo ventana modal...');

                const url = URL.createObjectURL(blob);

                // Cambiar el contenido para mostrar el PDF en un iframe
                const iframe = document.createElement('iframe');
                iframe.src = url;
                iframe.style.width = '100%';
                iframe.style.height = '500px';
                iframe.style.border = '1px solid #ccc';
                
                // Limpiar contenido anterior y agregar iframe
                modalRespuestaContenido.innerHTML = '';
                modalRespuestaContenido.appendChild(iframe);
                
                // Asegurar que el iframe sea visible
                iframe.style.display = 'block';
                iframe.style.width = '100%';
                iframe.style.height = '600px';
                iframe.style.border = '1px solid #ccc';
                iframe.style.background = '#fff';

                // Mostrar el modal
                modalRespuesta.className = 'ventanaModalPrendido';
                document.getElementById('contenedor').className = 'app-container contenedorPasivo';
            })
            .catch(error => {
                console.error('Error al cargar PDF:', error);
                mostrarModalRespuesta('Error al cargar el documento: ' + (error.message || error));
            });
    }
    
    // Hacer verPDF disponible globalmente
    window.verPDF = verPDF;
    
    // --- EVENT LISTENERS ---
    btnCargar.addEventListener('click', cargarMovimientos);
    btnAlta.addEventListener('click', abrirModalAlta);
    btnCerrarModalForm.addEventListener('click', cerrarModalFormulario);
    btnCerrarModalResp.addEventListener('click', cerrarModalRespuesta);
    formABM.addEventListener('submit', enviarFormulario);

    btnLimpiar.addEventListener('click', () => {
        document.getElementById('filtro-cod').value = '';
        document.getElementById('filtro-desc').value = '';
        document.getElementById('filtro-lote').value = '';
        filtroTipoMov.value = '';
        tablaContainer.innerHTML = '<p>Filtros limpiados. Presione "Cargar Datos".</p>';
    });

    btnCerrarSesion.addEventListener('click', () => {
        if (confirm('¿Está seguro que desea cerrar la sesión?')) {
            window.location.href = '../destruirsesion.php';
        }
    });

    tablaContainer.addEventListener('click', (e) => {
        const fila = e.target.closest('tr');
        if (!fila) return;
        if (e.target.classList.contains('btn-modi')) { abrirModalModi(fila); }
        if (e.target.classList.contains('btn-baja')) { ejecutarBaja(fila); }
    });

    modalForm.addEventListener('click', (e) => { if (e.target === modalForm) { cerrarModalFormulario(); } });
    modalRespuesta.addEventListener('click', (e) => { if (e.target === modalRespuesta) { cerrarModalRespuesta(); } });
});