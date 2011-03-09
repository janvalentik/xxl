<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_ximg($cesta='', $extrakod=null){
  //alternativni text
  $ralt=basename($cesta);
  if(mb_substr_count($ralt, ".")!=0){$ralt=mb_substr($ralt, 0, mb_strrpos($ralt, "."));}
  //kod
  if(isset($extrakod)){$rpluscode=" ".$extrakod;}else{$rpluscode="";}
  return "<img src='"._htmlStr($cesta)."' alt='".$ralt."'".$rpluscode." />";
}

?>