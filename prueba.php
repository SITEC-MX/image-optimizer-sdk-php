<?php
require_once __DIR__ . "/vendor/autoload.php";

use \Mpsoft\ImageOptimizer\ImageOptimizer;

$token = "";

$image_optimizer = new ImageOptimizer($token);

$parametros = array
(
    "url"=>"https://aeweb.sitec-mx.com/repositorio/o/3mOzs2x6GUdvoMpaKjnFNg/marsanvet/presentacion/1/imagen-1592852048.png",
    "ancho"=>600,
    "alto"=>800,
    "calidad"=>100
);

$estado = $image_optimizer->POST_Optimizar(NULL, NULL, $parametros);

echo json_encode($estado);