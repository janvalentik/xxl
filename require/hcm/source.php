<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_source($kod=""){
  return "<div class='pre'>".nl2br(_htmlStr(trim($kod)))."</div>";
}

?>