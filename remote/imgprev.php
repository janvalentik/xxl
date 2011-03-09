<?php

/*--- initialize core ---*/
define('_indexroot', '../');
define('_tmp_customheader', '');
define('_tmp_litemode', 1); 
require(_indexroot."core.php");

/*--- nacteni adresy ---*/
$continue=false;
if(isset($_GET['id'])){

  if(!isset($_GET['c'])){
  $id=intval($_GET['id']);
  $imgdata=mysql_query("SELECT full,home FROM `"._mysql_prefix."-images` WHERE id=".$id);
    if(mysql_num_rows($imgdata)!=0){
    $imgdata=mysql_fetch_array($imgdata);
    $galdata=mysql_fetch_array(mysql_query("SELECT var3 FROM `"._mysql_prefix."-root` WHERE id=".$imgdata['home']));
    $url=pathinfo($imgdata['full']);
    $fname=_indexroot.$imgdata['full'];
    if(isset($_GET['h'])){$newheight=intval($_GET['h']);}else{$newheight=$galdata['var3'];}
      if(@file_exists($fname) and $newheight<=1024 and isset($url['extension']) and in_array(mb_strtolower($url['extension']), $__image_ext)){
        $continue=true;
        _checkGD($url['extension']);
      }
    }
  }
  else{
  $id=_indexroot.base64_decode($_GET['id']);
  $url=pathinfo($id);
  $fname=$id;
  if(isset($_GET['h'])){$newheight=intval($_GET['h']);}else{$newheight=96;}
    if(@file_exists($fname) and $newheight<=1024 and  $newheight>0 and isset($url['extension']) and in_array(mb_strtolower($url['extension']), $__image_ext)){
      $continue=true;
      _checkGD($url['extension']);
    }
  }

}


if($continue){

  /*nacteni obrazku*/
  switch(mb_strtolower($url['extension'])){
  case "jpg": case "jpeg": $source=@imagecreatefromjpeg($fname); break;
  case "png": $source=@imagecreatefrompng($fname); break;
  case "gif": $source=@imagecreatefromgif($fname); break;
  }

  /*vypocitani nove sirky*/
  list($width, $height)=getimagesize($fname);
  $newwidth=round(($width/$height)*$newheight);
  if($newwidth==0){$newwidth=1;}

  /*vytvoreni miniatury*/
  $result=imagecreatetruecolor($newwidth, $newheight);
  $bg=imagecolorallocate($result, 255, 255, 255);
  imagecopyresampled($result, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

}
else{
  die;
}

/*--- odeslani obrazku ---*/
header("Content-type: image/JPEG");
header("Expires: ".gmdate("D, d M Y H:i:s", time()+86400)." GMT");
imagejpeg($result, null, 100);
imagedestroy($result);

exit;

?>