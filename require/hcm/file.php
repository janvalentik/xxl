<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_file($soubor=""){
  $soubor=_indexroot.$soubor;
  if(in_array(pathinfo($soubor, PATHINFO_EXTENSION), array('txt', 'htm', 'html'))){
      if(@file_exists($soubor)){
          return @file_get_contents($soubor);
      }
  }
}

?>