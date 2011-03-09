<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_custom($modul=null){
  if(isset($modul) and $modul!=""){
    $filename=_anchorStr($modul, false);
    $customfile=_indexroot."require/custom_hcm/".$filename.".php";
    if(file_exists($customfile)){
      $_params=func_get_args();
      $_uid=$GLOBALS['__hcm_uid'];
      global $_lang, $__image_ext;
      $output="";
      require($customfile);
      return $output;
    }
    else{
      return str_replace("*name*", $filename, $_lang['hcm.notfound']);
    }
  }
}

?>