<?php

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Tablero de Movimientos de Stock (SPA)</title>

    <style>

        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }

        .contenedor { width: 90%; max-width: 1200px; margin: 0 auto; background-color: #fff; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }

        h2 { text-align: center; color: #333; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }

        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }

 

        th.sortable { background-color: #FA6; color: black; cursor: pointer; }

        .filters td { padding: 4px; }

        .filters input, .filters select { width: 100%; box-sizing: border-box; }

        .tfooter { background-color: #F88; font-weight: bold; }

        .button-group { text-align: right; margin-bottom: 10px; }



        th { 

            background-color: #FA6;

            color: black;

            border: 2px solid #D66;

        }

        

        #tbodyMovimientos tr {

            background-color: #C0E8F0;

        }



        .tfooter { 

            background-color: #F88;

            font-weight: bold;

            color: black;

            border: 2px solid #D66;

        }



    </style>

</head>

<body>



<div class="contenedor">

    <h2>Movimientos de Stock</h2>



    <input type="hidden" id="orden" value="CodArticulo">



    <div class="button-group">

        <button onclick="cargaTabla()">Cargar Datos</button>

        <button onclick="vaciarDatos()">Vaciar Datos</button>

    </div>



    <table>

        <thead>

            <tr>

                <th class="sortable" id="th_CodArticulo" onclick="setOrder('CodArticulo')">Cod. Artículo</th>

                <th class="sortable" id="th_Descripcion" onclick="setOrder('Descripcion')">Descripción</th>

                <th class="sortable" id="th_NroDeLote" onclick="setOrder('NroDeLote')">Nro. de Lote</th>

                <th class="sortable" id="th_FechaMovimiento" onclick="setOrder('FechaMovimiento')">Fecha Mov.</th>

                <th class="sortable" id="th_TipoMovimiento" onclick="setOrder('IdMov')">Tipo Movimiento</th>

                <th class="sortable" id="th_UnidadMedida" onclick="setOrder('UnidadMedida')">UM</th>

                <th class="sortable" id="th_Cantidad" onclick="setOrder('Cantidad')">Cantidad</th>

            </tr>

            <tr class="filters">

                <td><input type="text" id="f_mov_codArt" placeholder="Filtro Cod. Art." onchange="cargaTabla()"></td>

                <td><input type="text" id="f_mov_descripcion" placeholder="Filtro Descripción" onchange="cargaTabla()"></td>

                <td><input type="text" id="f_mov_nroLote" placeholder="Filtro Lote" onchange="cargaTabla()"></td>

                <td><input type="text" id="f_mov_fecha" placeholder="Filtro Fecha" onchange="cargaTabla()"></td>

                <td><select id="f_mov_tipoMov" onchange="cargaTabla()"></select></td>

                <td><input type="text" id="f_mov_um" placeholder="Filtro UM" onchange="cargaTabla()"></td>

                <td></td> </tr>

        </thead>

        <tbody id="tbodyMovimientos">

            </tbody>

        <tfoot>

            <tr class="tfooter">

                <td colspan="6">Nro de registros:</td>

                <td><input type="text" id="nroRegistros" readonly size="5"></td>

            </tr>

        </tfoot>

    </table>

</div>



<script>

    const URL_MOVIMIENTOS = './salidaJsonMovimientos.php';

    const URL_TIPOS_MOV = './salidaJsonTiposMov.php';

    const tbody = document.getElementById('tbodyMovimientos');

    const nroRegistros = document.getElementById('nroRegistros');

    const ordenInput = document.getElementById('orden');

    const selectTipoMov = document.getElementById('f_mov_tipoMov');

    const filters = document.querySelectorAll('.filters input');





    function vaciarDatos() {

        tbody.innerHTML = '';

        nroRegistros.value = 0;

        filters.forEach(input => input.value = '');

        selectTipoMov.value = '';

    }



    function setOrder(columnName) {

        ordenInput.value = columnName;

        cargaTabla();

    }



    function getFilterData() {

        const objDatosOrdenFiltros = new URLSearchParams();



        objDatosOrdenFiltros.append('orden', ordenInput.value);

        objDatosOrdenFiltros.append('f_mov_codArt', document.getElementById('f_mov_codArt').value);

        objDatosOrdenFiltros.append('f_mov_tipoMov', selectTipoMov.value);

        objDatosOrdenFiltros.append('f_mov_nroLote', document.getElementById('f_mov_nroLote').value);

        objDatosOrdenFiltros.append('f_mov_descripcion', document.getElementById('f_mov_descripcion').value);

        objDatosOrdenFiltros.append('f_mov_fecha', document.getElementById('f_mov_fecha').value);

        objDatosOrdenFiltros.append('f_mov_um', document.getElementById('f_mov_um').value);

        

        return objDatosOrdenFiltros;

    }



    function renderTable(data) {

        tbody.innerHTML = '';

        

        if (data.error) {

            tbody.innerHTML = `<tr><td colspan="7" style="color: red;">${data.error}</td></tr>`;

        } else {

            data.MovimientosDeStock.forEach(mov => {

                const fila = `<tr>

                    <td>${mov.CodArticulo}</td>

                    <td>${mov.Descripcion}</td>

                    <td>${mov.NroDeLote}</td>

                    <td>${mov.FechaMovimiento}</td>

                    <td>${mov.TipoMovimiento}</td> 

                    <td>${mov.UnidadMedida}</td>

                    <td>${mov.Cantidad}</td>

                </tr>`;

                tbody.insertAdjacentHTML('beforeend', fila);

            });

            nroRegistros.value = data.cuenta;

        }

    }



    function cargaTabla() {

        const objDatosOrdenFiltros = getFilterData();

        

        tbody.innerHTML = '<tr><td colspan="7">Esperando respuesta del servidor</td></tr>';



        alert("Disparo AJAX con datos:\n" + objDatosOrdenFiltros.toString());



        fetch(URL_MOVIMIENTOS, {

            method: 'POST', 

            headers: {

                'Content-Type': 'application/x-www-form-urlencoded',

            },

            body: objDatosOrdenFiltros,

        })

        .then(response => {

            if (!response.ok) {

                throw new Error('Respuesta de red no fue exitosa. Código: ' + response.status);

            }

            return response.json();

        })

        .then(data => {

            alert("JSON recibido:\n" + JSON.stringify(data, null, 2));

            renderTable(data);

        })

        .catch(error => {

            console.error('Error producido:', error);

            tbody.innerHTML = '<tr style="color: red;"><td colspan="7">Error en la solicitud al servidor. Consulte la consola y errores.log.</td></tr>';

        });

    }



    function cargarTiposMovimiento() {

        fetch(URL_TIPOS_MOV)

        .then(response => {

            if (!response.ok) {

                throw new Error('Respuesta de red para tipos de movimiento no fue exitosa. Código: ' + response.status);

            }

            return response.json();

        })

        .then(data => {

            selectTipoMov.innerHTML = '';

            selectTipoMov.insertAdjacentHTML('beforeend', '<option value="">Todos</option>');

            

            if (!data.error && data.TipoDeMov) {

                data.TipoDeMov.forEach(tipo => {

                    const option = `<option value="${tipo.IdMov}">${tipo.Descripcion}</option>`;

                    selectTipoMov.insertAdjacentHTML('beforeend', option);

                });

            }

        })

        .catch(error => console.error('Error al cargar tipos de movimiento:', error));

    }



    document.addEventListener('DOMContentLoaded', cargarTiposMovimiento);

</script>



</body>

</html>


