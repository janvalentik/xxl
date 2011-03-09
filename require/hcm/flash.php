<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_flash($cesta="", $sirka=null, $vyska=null){
  
  //prednastavene rozmery
  $defwidth="320"; $defheight="240";
  
  //nacteni parametru
  $cesta=_htmlStr($cesta);
  if(!_isAbsolutePath($cesta)){$cesta=_url."/".$cesta;}
  if(!isset($sirka)){$sirka=$defwidth; $sirka_def=true;}else{$sirka=intval($sirka); $sirka_def=false;}
  if(!isset($vyska)){
    if(!$sirka_def){$vyska=round(0.75*$sirka);}
    else{$vyska=$defheight;}
  } else{$vyska=intval($vyska);}
    
  //sestaveni kodu
  return "
<!--[if !IE]> -->
<object type='application/x-shockwave-flash' data='".$cesta."' width='".$sirka."' height='".$vyska."'>
<!-- <![endif]-->

<!--[if IE]>
<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0' width='$sirka' height='$vyska'>
<param name='movie' value='".$cesta."' />
<!--><!---->
<param name='loop' value='true' />
<param name='menu' value='false' />

".$GLOBALS['_lang']['hcm.player.alt']."
</object>
<!-- <![endif]-->
";
  
}

?>