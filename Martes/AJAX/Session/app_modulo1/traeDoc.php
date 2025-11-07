<?php
// Protección de sesión
include_once 'manejoDeSesion.inc.php';

// Incluir datos de conexión
include('../datos_conexion_a_la_base.php');

// Validar parámetro
if (!isset($_GET['cod_articulo']) || trim($_GET['cod_articulo']) === '') {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Parámetro cod_articulo requerido.';
    exit;
}

$cod_articulo = $_GET['cod_articulo'];

try {
    if (!$pdo) throw new Exception('No se pudo conectar a la BD');

    $stmt = $pdo->prepare('SELECT FotoArticulo, FotoMime FROM MovimientosDeStock WHERE CodArticulo = :cod_articulo');
    $stmt->bindParam(':cod_articulo', $cod_articulo, PDO::PARAM_INT);
    $stmt->execute();
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fila || $fila['FotoArticulo'] === null) {
        http_response_code(404);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html><head><meta charset="utf-8"><title>No Foto</title></head><body>';
        echo '<h2>No hay foto asociada al artículo: ' . htmlspecialchars($cod_articulo) . '</h2>';
        echo '<p>Registro no encontrado o sin foto.</p>';
        echo '</body></html>';
        exit;
    }

    $foto_blob = $fila['FotoArticulo'];
    $foto_mime = $fila['FotoMime'] ?: 'image/jpeg';

    // Si es un recurso (stream), leerlo
    if (is_resource($foto_blob)) {
        $foto_blob = stream_get_contents($foto_blob);
    }

    // Convertir imagen a PDF usando FPDF si está disponible
    $pdf_content = null;
    
    // Intentar usar FPDF (buscar en varias ubicaciones comunes)
    $fpdf_paths = [
        __DIR__ . '/fpdf/fpdf.php',
        __DIR__ . '/../fpdf/fpdf.php',
        __DIR__ . '/../../fpdf/fpdf.php',
        'fpdf.php'
    ];
    
    $fpdf_loaded = false;
    foreach ($fpdf_paths as $path) {
        if (file_exists($path)) {
            require_once($path);
            $fpdf_loaded = true;
            break;
        }
    }
    
    // Si FPDF no está disponible, intentar cargarlo desde el include_path
    if (!$fpdf_loaded && class_exists('FPDF')) {
        $fpdf_loaded = true;
    }
    
    if ($fpdf_loaded) {
        // Usar FPDF para crear el PDF
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        
        // Crear archivo temporal con la imagen
        $temp_file = tempnam(sys_get_temp_dir(), 'img_');
        file_put_contents($temp_file, $foto_blob);
        
        // Determinar tipo de imagen
        $image_type = null;
        if (strpos($foto_mime, 'jpeg') !== false || strpos($foto_mime, 'jpg') !== false) {
            $image_type = 'JPEG';
        } elseif (strpos($foto_mime, 'png') !== false) {
            $image_type = 'PNG';
        } elseif (strpos($foto_mime, 'gif') !== false) {
            $image_type = 'GIF';
        }
        
        if ($image_type) {
            // Obtener dimensiones de la imagen
            $image_info = getimagesize($temp_file);
            if ($image_info) {
                $img_width = $image_info[0];
                $img_height = $image_info[1];
                
                // Ajustar tamaño para que quepa en la página (máximo 190x250 mm)
                $max_width = 190;
                $max_height = 250;
                
                // Convertir píxeles a mm (aproximadamente 0.264583 mm por píxel a 96 DPI)
                $width_mm = $img_width * 0.264583;
                $height_mm = $img_height * 0.264583;
                
                // Calcular ratio para ajustar
                $ratio = min($max_width / $width_mm, $max_height / $height_mm);
                $new_width = $width_mm * $ratio;
                $new_height = $height_mm * $ratio;
                
                // Centrar la imagen
                $x = (210 - $new_width) / 2;
                $y = (297 - $new_height) / 2;
                
                $pdf->Image($temp_file, $x, $y, $new_width, $new_height, $image_type);
            }
        }
        
        unlink($temp_file);
        $pdf_content = $pdf->Output('', 'S');
    } else {
        // Si FPDF no está disponible, usar una solución alternativa simple
        // Crear un PDF básico usando TCPDF si está disponible, o una solución manual
        $pdf_content = crearPDFSimple($foto_blob, $foto_mime);
    }

    if ($pdf_content) {
        header('Content-Type: application/pdf');
        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $cod_articulo);
        header('Content-Disposition: inline; filename="articulo_' . $safeName . '.pdf"');
        header('Content-Length: ' . strlen($pdf_content));
        echo $pdf_content;
        exit;
    } else {
        throw new Exception('No se pudo generar el PDF');
    }

} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Error al recuperar documento: ' . $e->getMessage();
    exit;
}

/**
 * Función alternativa para crear un PDF simple sin FPDF ni GD
 * Crea un PDF básico directamente con la imagen JPEG embebida
 */
function crearPDFSimple($image_blob, $mime_type) {
    // Solo funciona para JPEG sin necesidad de GD
    if (strpos($mime_type, 'jpeg') === false && strpos($mime_type, 'jpg') === false) {
        return null;
    }
    
    // Obtener dimensiones de la imagen JPEG sin GD
    // Leer los primeros bytes del JPEG para obtener dimensiones
    $width = 0;
    $height = 0;
    
    // Intentar leer dimensiones del JPEG
    if (strlen($image_blob) > 20) {
        // Buscar el marcador SOF (Start of Frame) en JPEG
        $pos = 2; // Saltar FF D8
        while ($pos < strlen($image_blob) - 1) {
            if (ord($image_blob[$pos]) == 0xFF) {
                $marker = ord($image_blob[$pos + 1]);
                // SOF markers: 0xC0-0xC3, 0xC5-0xC7, 0xC9-0xCB, 0xCD-0xCF
                if ($marker >= 0xC0 && $marker <= 0xCF && $marker != 0xC4 && $marker != 0xC8 && $marker != 0xCC) {
                    if ($pos + 7 < strlen($image_blob)) {
                        $height = (ord($image_blob[$pos + 5]) << 8) | ord($image_blob[$pos + 6]);
                        $width = (ord($image_blob[$pos + 7]) << 8) | ord($image_blob[$pos + 8]);
                        break;
                    }
                }
                $pos += 2;
                if ($pos + 1 < strlen($image_blob)) {
                    $segment_length = (ord($image_blob[$pos]) << 8) | ord($image_blob[$pos + 1]);
                    $pos += $segment_length;
                } else {
                    break;
                }
            } else {
                $pos++;
            }
        }
    }
    
    // Si no se pudieron obtener las dimensiones, usar valores por defecto
    if ($width == 0 || $height == 0) {
        $width = 800;
        $height = 600;
    }
    
    // Crear un PDF básico usando la especificación PDF
    $pdf = "%PDF-1.4\n";
    
    // Calcular dimensiones en puntos (72 DPI)
    $pdf_width = ($width / 96) * 72; // Convertir a puntos
    $pdf_height = ($height / 96) * 72;
    
    // Ajustar a tamaño A4 si es muy grande
    $max_width = 612; // A4 width in points
    $max_height = 792; // A4 height in points
    $ratio = min($max_width / $pdf_width, $max_height / $pdf_height);
    if ($ratio < 1) {
        $pdf_width *= $ratio;
        $pdf_height *= $ratio;
    }
    
    // Centrar en página A4
    $x = (612 - $pdf_width) / 2;
    $y = (792 - $pdf_height) / 2;
    
    // Crear objetos PDF básicos
    $objects = [];
    
    // Objeto 1: Catalog
    $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
    
    // Objeto 2: Pages
    $objects[2] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
    
    // Objeto 3: Page
    $objects[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /XObject << /Im1 5 0 R >> >> >>";
    
    // Objeto 4: Contents (stream con la imagen)
    $content = "q\n";
    $content .= sprintf("%.2f 0 0 %.2f %.2f %.2f cm\n", $pdf_width, $pdf_height, $x, 792 - $y - $pdf_height);
    $content .= "/Im1 Do\n";
    $content .= "Q\n";
    $objects[4] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
    
    // Objeto 5: Image XObject
    $image_length = strlen($image_blob);
    $objects[5] = "<< /Type /XObject /Subtype /Image /Width $width /Height $height /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length $image_length >>\nstream\n" . $image_blob . "\nendstream";
    
    // Construir PDF
    $pdf_content = "";
    $offsets = [];
    $current_offset = 0;
    
    foreach ($objects as $num => $obj) {
        $offsets[$num] = $current_offset;
        $pdf_content .= "$num 0 obj\n$obj\nendobj\n";
        $current_offset = strlen($pdf_content);
    }
    
    // XRef table
    $xref_offset = strlen($pdf_content);
    $pdf_content .= "xref\n0 " . (count($objects) + 1) . "\n";
    $pdf_content .= "0000000000 65535 f \n";
    foreach ($offsets as $num => $offset) {
        $pdf_content .= sprintf("%010d 00000 n \n", $offset);
    }
    
    // Trailer
    $pdf_content .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
    $pdf_content .= "startxref\n$xref_offset\n%%EOF\n";
    
    return $pdf . $pdf_content;
}
?>

