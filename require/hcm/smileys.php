<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_smileys($text=""){
    return _parsePost($text, true, false, false);
}

?>