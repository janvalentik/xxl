<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_levelcontent($min_uroven=0, $vyhovujici_text="", $nevyhovujici_text=""){
  if(_loginright_level>=intval($min_uroven)){return $vyhovujici_text;}
  else{return $nevyhovujici_text;}
}

?>