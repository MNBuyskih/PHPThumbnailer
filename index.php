<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$yiiPath = 'C:\PHP\includes\yii\yii.php';
require_once $yiiPath;

require_once "lib/PHPThumb.php";
$srcImage = dirname(__FILE__) . '\test\car.jpg';
$thumb    = new PHPThumb($srcImage);
$thumb->filter("AdaptiveResize", array(
                              'width'  => 227,
                              'height' => 193,
                         ));
//$newThumb = $thumb->save(dirname(__FILE__) . '\test\safe-copy.jpg', PHPThumbImageBase::FORMAT_JPG, array('quality' => 80));
$thumb->show();