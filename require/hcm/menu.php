<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_menu($od=null, $do=null){
  if(!is_null($od) and !is_null($do)){
    $start=intval($od);
    $length=intval($do);
  }
  else{
    $start=null;
    $length=null;
  }
 return _templateMenu($start, $length);
}

?>