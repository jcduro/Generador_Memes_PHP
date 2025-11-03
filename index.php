<!--  
     |  ___|  __ \  |   |  _ \   _ \   
     | |      |   | |   | |   | |   |  
 \   | |      |   | |   | __ <  |   |  
\___/ \____| ____/ \___/ _| \_\\___/   
                                       
  ___|  _ \  __ \  ____|    _ )   _ _| __ \  ____|    \     ___|  
 |     |   | |   | __|     _ \ \   |  |   | __|     _ \  \___ \  
 |     |   | |   | |      ( `  <   |  |   | |      ___ \       | 
\____|\___/ ____/ _____| \___/\/ ___|____/ _____|_/    _\_____/  

  https://jcduro.bexartideas.com/index.php | 2025 | JC Duro Code & Ideas

-->

<?php
$imagen_meme = '';
$info = '';
$has_result = false;

if(isset($_POST['crear']) && isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['img']['tmp_name'];
    $tipo = mime_content_type($file);

    // Solo JPG/PNG
    if($tipo == 'image/jpeg' || $tipo == 'image/jpg') {
        $img = imagecreatefromjpeg($file);
    } elseif($tipo == 'image/png') {
        $img = imagecreatefrompng($file);
    } else {
        $info = "<span class='error'>Solo imágenes JPG o PNG</span>";
        $img = null;
    }
    
    if($img) {
        $has_result = true;
        $top_text = strtoupper(trim($_POST['top_text']));
        $bottom_text = strtoupper(trim($_POST['bottom_text']));
        $font = __DIR__ . '/impact/impact.ttf'; // Usar fuente tipo meme
        $font_size = imagesx($img) > 500 ? 44 : 28;
        $color = imagecolorallocate($img, 255, 255, 255);
        $stroke = imagecolorallocate($img, 0, 0, 0);

        // Función para texto con borde tipo meme
        function imagettfstroketext(&$img, $size, $angle, $x, $y, $color, $stroke, $font, $text, $px)
        {
            for($c1 = ($x - abs($px)); $c1 <= ($x + abs($px)); $c1++)
            for($c2 = ($y - abs($px)); $c2 <= ($y + abs($px)); $c2++)
            imagettftext($img, $size, $angle, $c1, $c2, $stroke, $font, $text);
            imagettftext($img, $size, $angle, $x, $y, $color, $font, $text);
        }

        $width = imagesx($img);
        $height = imagesy($img);

        // Centrar y acomodar top y bottom
        if($top_text) {
            $bbox = imagettfbbox($font_size, 0, $font, $top_text);
            $x = ($width - ($bbox[2] - $bbox[0])) / 2;
            $y = $font_size + 12;
            imagettfstroketext($img, $font_size, 0, $x, $y, $color, $stroke, $font, $top_text, 2);
        }
        if($bottom_text) {
            $bbox = imagettfbbox($font_size, 0, $font, $bottom_text);
            $x = ($width - ($bbox[2] - $bbox[0])) / 2;
            $y = $height - 25;
            imagettfstroketext($img, $font_size, 0, $x, $y, $color, $stroke, $font, $bottom_text, 2);
        }

        // Enviar a base64 para mostrar en página
        ob_start();
        imagejpeg($img, null, 98);
        $imagen_meme = base64_encode(ob_get_clean());
        imagedestroy($img);
        $info = "<span class='success'>¡Meme creado!</span>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Generador de Memes PHP puro</title>
 <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container">
    <h1>Generador de Memes Online PHP</h1>
    <form method="post" enctype="multipart/form-data" autocomplete="off">
        <input type="file" name="img" accept=".jpg,.jpeg,.png" required><br>
        <input type="text" name="top_text" maxlength="35" placeholder="Texto superior"><br>
        <input type="text" name="bottom_text" maxlength="35" placeholder="Texto inferior"><br>
        <button type="submit" name="crear">Crear meme</button>
    </form>
    <div class="info"><?= $info ?></div>
    <?php if ($has_result): ?>
        <img src="data:image/jpeg;base64,<?= $imagen_meme ?>" alt="Meme final">
        <br>
        <a href="data:image/jpeg;base64,<?= $imagen_meme ?>" download="meme.jpg" class="download">Descargar meme</a>
    <?php endif; ?>
    <div style="margin-top:18px; font-size:13px; opacity:.7;">
        <b>* Los textos se recortan si son muy largos.<br>* Usa imágenes horizontales para mejores memes.</b>
    </div>
</div>
</body>
</html>
