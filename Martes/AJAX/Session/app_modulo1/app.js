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

            alert('JSON de tipos de movimiento cargado:\n' + JSON.stringify(data, null, 2));

            //limpia los selects
            filtroTipoMov.innerHTML = '';
            formTipoMovDD.innerHTML = '';
            

            //opciones para el form y para el filtro
            const optionTodosFiltro = document.createElement('option');
            optionTodosFiltro.value = '';
            optionTodosFiltro.textContent = '-- Todos --';
            filtroTipoMov.appendChild(optionTodosFiltro);
            
            const optionSeleccione = document.createElement('option');
            optionSeleccione.value = '';
            optionSeleccione.textContent = '-- Seleccione --';
            formTipoMovDD.appendChild(optionSeleccione);

            data.forEach(tipo => {
                const optionFiltro = document.createElement('option');
                optionFiltro.value = tipo.Codigo;
                optionFiltro.textContent = tipo.Descripcion;
                filtroTipoMov.appendChild(optionFiltro);
                
                const optionForm = document.createElement('option');
                optionForm.value = tipo.Codigo;
                optionForm.textContent = tipo.Descripcion;
                formTipoMovDD.appendChild(optionForm);
            });
        } catch (error) {
            mostrarModalRespuesta(`Error al cargar tipos de movimiento: ${error.message}`);
        }
    }
    cargarTiposMov();

    async function cargarMovimientos() {
        tablaContainer.innerHTML = "<p>Cargando datos...</p>";
        
        const filtroCod = document.getElementById('filtro-cod').value.trim();
        const filtroDesc = document.getElementById('filtro-desc').value.trim();
        const filtroLote = document.getElementById('filtro-lote').value.trim();
        const filtroTipo = filtroTipoMov.value.trim();
        
        const params = new URLSearchParams({
            cod: filtroCod,
            desc: filtroDesc,
            lote: filtroLote,
            tipo_mov: filtroTipo
        });
        
        alert('Variables que se envían al servidor:\n' + params.toString());
        
        try {
            const response = await fetch(`salida_json_movimientos.php?${params.toString()}`);
            const data = await manejarRespuestaFetch(response);
            if (!data) {
                tablaContainer.innerHTML = "<p>No se pudieron cargar los datos.</p>";
                return;
            }
            
            alert('Respuesta del servidor:\n' + JSON.stringify(data, null, 2));
            
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
            <th>Tipo Mov.</th>
            <th>Cod. Art.</th>
            <th>Descripción</th>
            <th>Lote</th><th>Fecha</th><th>UM</th><th>Cant.</th>
            <th>Acciones</th>
        </tr></thead>`;
        
        html += '<tbody>';
        data.forEach(item => {
            const tieneFoto = item.tiene_foto === 'SI';
            const btnPDFClass = tieneFoto ? 'btn-pdf' : 'btn-pdf btn-pdf-disabled';
            const btnPDFHtml = `<button class="btn-accion ${btnPDFClass}" onclick="verPDF(${item.CodArticulo})" ${!tieneFoto ? 'disabled' : ''}>PDF</button>`;

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
                <td class="acciones-cell">
                    ${btnPDFHtml}
                    <button class="btn-accion btn-modi">Modi</button>
                    <button class="btn-accion btn-baja">Baja</button>
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
        
        alert(`Enviando formulario a: ${url}`);

        try {
            const response = await fetch(url, { method: 'POST', body: formData });
            const data = await manejarRespuestaFetch(response);
            if (!data) return;

            alert('Respuesta del servidor:\n' + JSON.stringify(data, null, 2));
            
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
        
        alert(`Enviando petición de baja para CodArticulo: ${codarticulo}`);
        
        try {
            const formData = new FormData();
            formData.append('CodArticulo', codarticulo);

            const response = await fetch('baja.php', { method: 'POST', body: formData });
            const data = await manejarRespuestaFetch(response);
            if (!data) return;

            alert('Respuesta del servidor:\n' + JSON.stringify(data, null, 2));
            
            mostrarModalRespuesta(JSON.stringify(data, null, 2));
            cargarMovimientos();
        } catch (error) {
            mostrarModalRespuesta(`Error: ${error.message}`);
        }
    }

    //modales
    function abrirModalAlta() {
        formABM.reset();
        modalTitulo.textContent = 'Alta de Movimiento';
        document.getElementById('form-tipo-mov-dd').disabled = false;

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

        const codArticulo = data.codarticulo || '';
        const nroDeLote = data.nrodelote || '';
        const descripcion = data.descripcion || '';
        const fechaMovimiento = data.fecha_movimiento || '';
        const tipodeMov = data.tipodemov || '';
        const unidadMedida = data.unidad_medida || '';
        const cantidad = data.cantidad || '';

        //se asegura que se use el codigo en el select
        document.getElementById('form-tipo-mov-dd').value = tipodeMov;
        document.getElementById('form-cod-art').value = codArticulo;
        document.getElementById('form-nro-lote').value = nroDeLote;
        document.getElementById('form-desc').value = descripcion;
        document.getElementById('form-fecha').value = fechaMovimiento;
        document.getElementById('form-unidad').value = unidadMedida;
        document.getElementById('form-cantidad').value = cantidad;

        document.getElementById('form-codarticulo').value = codArticulo;
        document.getElementById('form-lote').value = nroDeLote;

        //el ID no se cambia
        document.getElementById('form-cod-art').disabled = true;
        document.getElementById('form-cod-art').readOnly = true;

        document.getElementById('form-tipo-mov-dd').disabled = false;
        document.getElementById('form-nro-lote').disabled = false;
        
        if (data.fotoarticulo) {
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
    
    //muestra el PDF
    function verPDF(codArticulo) {
        alert(`Solicitando PDF para CodArticulo: ${codArticulo}`);
        
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
                alert('PDF recibido del servidor, abriendo ventana modal...');

                const url = URL.createObjectURL(blob);

                //el PDF se muestra con iframe
                const iframe = document.createElement('iframe');
                iframe.src = url;
                iframe.style.width = '100%';
                iframe.style.height = '500px';
                iframe.style.border = '1px solid #ccc';
                
                modalRespuestaContenido.innerHTML = '';
                modalRespuestaContenido.appendChild(iframe);
            
                iframe.style.display = 'block';
                iframe.style.width = '100%';
                iframe.style.height = '600px';
                iframe.style.border = '1px solid #ccc';
                iframe.style.background = '#fff';

                //modal
                modalRespuesta.className = 'ventanaModalPrendido';
                document.getElementById('contenedor').className = 'app-container contenedorPasivo';
            })
            .catch(error => {
                console.error('Error al cargar PDF:', error);
                alert('Error al cargar el documento: ' + (error.message || error));
                mostrarModalRespuesta('Error al cargar el documento: ' + (error.message || error));
            });
    }
    
    window.verPDF = verPDF;
    
    btnCargar.addEventListener('click', cargarMovimientos);
    btnAlta.addEventListener('click', abrirModalAlta);
    btnCerrarModalForm.addEventListener('click', cerrarModalFormulario);
    btnCerrarModalResp.addEventListener('click', cerrarModalRespuesta);
    formABM.addEventListener('submit', enviarFormulario);

    btnLimpiar.addEventListener('click', () => {
        //limpia filtros
        document.getElementById('filtro-cod').value = '';
        document.getElementById('filtro-desc').value = '';
        document.getElementById('filtro-lote').value = '';
        
        filtroTipoMov.selectedIndex = 0;
        filtroTipoMov.value = '';
        
        ///evento change para que el navegador entienda que hubo cambio
        filtroTipoMov.dispatchEvent(new Event('change', { bubbles: true }));
        
        //mensaje temporal
        tablaContainer.innerHTML = '<p>Filtros limpiados. Cargando todos los registros...</p>';
        
        //recarga los datos
        cargarMovimientos();
    });

    filtroTipoMov.addEventListener('change', () => {
        cargarMovimientos();
    });

    const inputFiltros = [
        document.getElementById('filtro-cod'),
        document.getElementById('filtro-desc'),
        document.getElementById('filtro-lote')
    ];
    
    inputFiltros.forEach(input => {
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                cargarMovimientos();
            }
        });
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