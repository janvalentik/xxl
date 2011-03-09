<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_img($cesta="", $vyska_nahledu=null, $titulek=null, $lightbox=null){
  if(isset($vyska_nahledu) and $vyska_nahledu>0){$vyska_nahledu=intval($vyska_nahledu);}else{$vyska_nahledu=96;}
  if(isset($titulek) and $titulek!=""){$titulek=_htmlStr($titulek);}
  if(!isset($lightbox)){$lightbox='hcm'.$GLOBALS['__hcm_uid'];}
  return "<a href='"._htmlStr($cesta)."' target='_blank' rel='lightbox[hcm".$lightbox."]'".(($titulek!="")?' title=\''.$titulek.'\'':'')."><img src='"._indexroot."remote/imgprev.php?id=".urlencode(base64_encode($cesta))."&amp;c&amp;h=".$vyska_nahledu."' alt='".(($titulek!="")?$titulek:'img')."' /></a>\n";
}

?>