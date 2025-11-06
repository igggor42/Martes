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
                
                const optionForm = new Option(tipo.Descripcion, tipo.IdMov);
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
        let html = '<table class="tabla-abm">';
        html += `<thead><tr>
            <th>Tipo Mov.</th><th>Cod. Art.</th><th>Descripción</th>
            <th>Lote</th><th>Fecha</th><th>UM</th><th>Cant.</th>
            <th>Foto</th><th>Acciones</th>
        </tr></thead>`;
        
        html += '<tbody>';
        data.forEach(item => {
            const fotoUrl = item.FotoArticulo ? `uploads/${item.FotoArticulo}` : '';
            const fotoHtml = item.FotoArticulo ? `<img src="${fotoUrl}" alt="Foto">` : 'N/A';

            html += `<tr 
                data-idmov="${item.IdMov}"
                data-codarticulo="${item.CodArticulo}"
                data-nrodelote="${item.NroDeLote}"
                data-descripcion="${item.Descripcion}"
                data-fechamovimiento="${item.FechaMovimiento}"
                data-unidadmedida="${item.UnidadMedida}"
                data-cantidad="${item.Cantidad}"
                data-fotoarticulo="${item.FotoArticulo}">
                
                <td>${item.TipoMovDescripcion} (${item.IdMov})</td>
                <td>${item.CodArticulo}</td>
                <td>${item.Descripcion}</td>
                <td>${item.NroDeLote}</td>
                <td>${item.FechaMovimiento}</td>
                <td>${item.UnidadMedida}</td>
                <td>${item.Cantidad}</td>
                <td>${fotoHtml}</td>
                <td class="col-acciones">
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
        const { idmov, codarticulo, nrodelote } = row.dataset;
        
        if (!confirm(`¿Está seguro que desea eliminar el registro (Art: ${codarticulo}, Lote: ${nrodelote}, Mov: ${idmov})?`)) {
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('IdMov', idmov);
            formData.append('CodArticulo', codarticulo);
            formData.append('NroDeLote', nrodelote);

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
        document.getElementById('form-cod-art').disabled = false;
        document.getElementById('form-nro-lote').disabled = false;
        document.getElementById('form-idmov').value = '';
        document.getElementById('form-codarticulo').value = '';
        document.getElementById('form-lote').value = '';
        fotoActualPreview.innerHTML = '';
        modalForm.className = 'modal-visible';
    }

    function abrirModalModi(row) {
        formABM.reset();
        modalTitulo.textContent = 'Modificación de Movimiento';
        const data = row.dataset;

        document.getElementById('form-tipo-mov-dd').value = data.idmov;
        document.getElementById('form-cod-art').value = data.codarticulo;
        document.getElementById('form-nro-lote').value = data.nrodelote;
        document.getElementById('form-desc').value = data.descripcion;
        document.getElementById('form-fecha').value = data.fechamovimiento;
        document.getElementById('form-unidad').value = data.unidadmedida;
        document.getElementById('form-cantidad').value = data.cantidad;

        document.getElementById('form-idmov').value = data.idmov;
        document.getElementById('form-codarticulo').value = data.codarticulo;
        document.getElementById('form-lote').value = data.nrodelote;

        document.getElementById('form-tipo-mov-dd').disabled = true;
        document.getElementById('form-cod-art').disabled = true;
        document.getElementById('form-nro-lote').disabled = true;
        
        if (data.fotoarticulo) {
            fotoActualPreview.innerHTML = `<p>Foto actual:</p><img src="uploads/${data.fotoarticulo}" alt="Foto actual">`;
        } else {
            fotoActualPreview.innerHTML = '<p>No hay foto actual.</p>';
        }

        modalForm.className = 'modal-visible';
    }

    function cerrarModalFormulario() { modalForm.className = 'modal-oculto'; }
    function mostrarModalRespuesta(texto) {
        modalRespuestaContenido.textContent = texto;
        modalRespuesta.className = 'modal-visible';
    }
    function cerrarModalRespuesta() { modalRespuesta.className = 'modal-oculto'; }
    
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