<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_notpublic($pro_prihlasene="", $pro_neprihlasene=""){
  if(_loginindicator){return $pro_prihlasene;}
  else{return $pro_neprihlasene;}
}

?>