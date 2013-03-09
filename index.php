<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "lib/PHPThumbler.php";
$srcImage = dirname(__FILE__) . '\test\car.jpg';
$thumb    = new PHPThumbler($srcImage);
$thumb->filter("AdaptiveResize", array(
                                      'width'  => 300,
                                      'height' => 300,
                                 ));
$thumb->show();