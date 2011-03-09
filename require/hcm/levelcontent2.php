<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_levelcontent2($min_uroven=0, $max_uroven=10000, $vyhovujici_text="", $nevyhovujici_text=""){
  if(_loginright_level>=intval($min_uroven) and _loginright_level<=intval($max_uroven)){return $vyhovujici_text;}
  else{return $nevyhovujici_text;}
}

?>