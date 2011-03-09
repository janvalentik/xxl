<?php

//jadro
define('_indexroot', '../');
define('_tmp_customheader', '');
require(_indexroot."core.php");

//kontrola GD
_checkGD("jpg");

//inicializace obrazku, nacteni kodu
$imgw=65; $imgh=22;
$img=imagecreate($imgw, $imgh);
if(isset($_GET['n']) and isset($_SESSION[_sessionprefix.'captcha_code'][$_GET['n']])){$code=$_SESSION[_sessionprefix.'captcha_code'][$_GET['n']];}
else{$code='';}

//zavedeni barev
$bg=imagecolorallocate($img, 255, 255, 255);
$dark=imagecolorallocate($img, 000, 000, 000);
$normal=imagecolorallocate($img, 200, 200, 200);
$bright=imagecolorallocate($img, 225, 225, 225);

//normalni tecky
$pocet=0;
while($pocet<=0){
imagesetpixel($img, mt_rand(0, $imgw-2), mt_rand(0, $imgh), $normal);
$pocet++;
}

//svetle tecky
$pocet=0;
while($pocet<=0){
imagesetpixel($img, mt_rand(0, $imgw), mt_rand(0, $imgh), $bright);
$pocet++;
}

//temne tecky
$pocet=0;
while($pocet<=0){
imagesetpixel($img, mt_rand(0, $imgw), mt_rand(0, $imgh), $dark);
$pocet++;
}

//cary
$pocet=0;
while($pocet<=0){
imageline($img, mt_rand(0, $imgw), mt_rand(0, $imgh), mt_rand(0, $imgw), mt_rand(0, $imgh), $normal);
$pocet++;
}

//ramecek
imagerectangle($img, 0, 0, $imgw-1, $imgh-1, $normal);

//text
imagestring($img, 5, 10, 3, $code, $dark);

//odeslani obrazku
header("Content-type: image/JPEG");
imagejpeg($img, null, 100);
imagedestroy($img);

?>