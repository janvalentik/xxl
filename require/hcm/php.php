<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_php($kod="", $ze_souboru=false){
  if(_boolean($ze_souboru)){
      //ze souboru
      $soubor=_indexroot.$kod;
      if(@file_exists($soubor)){
          $_params=func_get_args();
          array_slice($_params, 2);
          $output=null;
          $reqoutput=require $soubor;
          if(is_string($reqoutput)){return $reqoutput;}
          else{return $output;}
      }
  }else{
      //kod
      return _evalBox($kod);
  }
}

?>