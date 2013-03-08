<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "lib/PHPThumb.php";
$srcImage = dirname(__FILE__) . '\test\car.jpg';
$thumb    = new PHPThumb($srcImage);
$thumb->filter("AdaptiveResize", array(
                              'width'  => 100,
                              'height' => 100,
                         ));
//$newThumb = $thumb->save(dirname(__FILE__) . '\test\safe-copy.jpg', PHPThumbImageBase::FORMAT_JPG, array('quality' => 80));
$thumb->show();