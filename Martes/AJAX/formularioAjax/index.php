<?php
if (isset($_POST['clave'])) {
    
    $clave_ingresada = $_POST['clave'] ?? ''; 

    sleep(5); 

    $clave_md5 = md5($clave_ingresada);
    $clave_sha1 = sha1($clave_ingresada); 
    
    $respuesta_html = '
        <strong>Request_method: POST</strong><br>
        <strong>Clave: ' . $clave_ingresada . '</strong><br>
        <br>
        Clave encriptada en md5 (128 bits o 16 pares hexadecimales):<br>
        ' . $clave_md5 . '<br>
        <br>
        Clave encriptada en sha1 (160 bits o 20 pares hexadecimales):<br>
        ' . $clave_sha1 . '
    ';

    echo $respuesta_html;
    
    exit; 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>AJAX con Retardo y Encriptación (Archivo Único)</title>
    <style>
        body { margin: 0; font-family: sans-serif; }
        .contenedor { 
            display: grid; 
            /* ANCHO: Columna 2 es 2 veces más grande que Columna 1 */
            grid-template-columns: 1fr 2fr; 
            /* CORRECCIÓN DE ALTO: Fila 1 (azul) es 1.5 veces más alta que Fila 2 (naranja) */
            grid-template-rows: 1.5fr 1fr; 
            height: 100vh;
        }
        #div-entrada { background-color: #D3D3D3; padding: 20px; grid-area: 1 / 1 / 2 / 2; }
        #div-encriptar { 
            background-color: #00FFFF; 
            padding: 20px; 
            grid-area: 1 / 2 / 2 / 3; 
            text-align: center; 
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        #div-resultado { background-color: #FFA500; padding: 20px; grid-area: 2 / 2 / 3 / 3; }
        /* CORRECCIÓN: Fondo Gris Oscuro */
        #div-estado { background-color: #36454F; padding: 20px; grid-area: 2 / 1 / 3 / 2; color: white; } 
        
        .titulo { color: black; font-weight: bold; margin-top: 0; }
        .esperando { font-size: 1.2em; font-weight: bold; }
        
        #img-disparador {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            cursor: pointer;
            width: 150px;
            display: none; 
        }
    </style>
</head>
<body>

    <div class="contenedor">
        
        <div id="div-entrada">
            <h3 class="titulo">Ingrese dato de entrada:</h3>
            <input type="text" id="clave-input" value="" required>
        </div>

        <div id="div-encriptar">
            <h3 style="color: #333; margin-top: 5px;">Encriptar</h3> 
            <img src="bomba.jpg" alt="Disparar AJAX" id="img-disparador">
            <div style="height: 10px;"></div>
        </div>

        <div id="div-resultado">
            <h3 class="titulo">Resultado:</h3>
            <div id="resultado-contenido"></div> 
        </div>

        <div id="div-estado">
            <span class="esperando">Estado del requerimiento:</span> 
            <span class="esperando" id="estado-requerimiento"></span>
        </div>

    </div>

    <script>
        const divEncriptar = document.getElementById('div-encriptar');
        const imgDisparador = document.getElementById('img-disparador'); 
        const inputClave = document.getElementById('clave-input');
        const spanEstado = document.getElementById('estado-requerimiento');
        const divResultado = document.getElementById('resultado-contenido');
        
        const urlRespuesta = '<?php echo basename(__FILE__); ?>'; 

        divEncriptar.addEventListener('mouseover', () => {
            imgDisparador.style.display = 'block'; 
        });

        divEncriptar.addEventListener('mouseout', () => {
            imgDisparador.style.display = 'none'; 
        });

        imgDisparador.addEventListener('click', () => {
            
            if (inputClave.value.trim() === '') {
                alert('Debe ingresar una clave para encriptar.');
                return;
            }

            spanEstado.innerHTML = 'ESPERANDO RESPUESTA ..'; 
            divResultado.innerHTML = 'Esperando respuesta ..'; 
            
            const claveAEncriptar = inputClave.value;
            const data = new URLSearchParams();
            data.append('clave', claveAEncriptar);
            
            alert(`localhost dice\nComo pienso usar el metodo POST en el req HTTP, esto viajara en el body del mismo con el formato clasico:\nclave=${claveAEncriptar}&variable=valor`);
            
            const options = {
                method: 'POST',
                body: data 
            };

            fetch(urlRespuesta, options)
                .then(respuesta => {
                    return respuesta.text();
                })
                .then(textoDeRespuesta => {
                    alert(textoDeRespuesta); 
                    
                    divResultado.innerHTML = textoDeRespuesta;
                    spanEstado.innerHTML = 'CUMPLIDO'; 
                })
                .catch(error => {
                    spanEstado.innerHTML = 'ERROR';
                    console.error('Error en fetch:', error);
                });
        });
    </script> 
</body>
</html>