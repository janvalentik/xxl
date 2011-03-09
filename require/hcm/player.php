<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_player($soubor="", $sirka=null, $vyska=null, $autoplay=false){
  
  //prednastavene hodnoty
  $extension=pathinfo($soubor);
  if(isset($extension['extension'])){$extension=$extension['extension'];}
  if($extension=="mp3"){$defvyska="19";}else{$defvyska="240";}
  $defsirka="320";

  //nacteni parametru
  $soubor=_htmlStr($soubor);
  if(!_isAbsolutePath($soubor)){$soubor=_url."/".$soubor;}
  if(!isset($sirka)){$sirka=$defsirka;}else{$sirka=intval($sirka);}
  if(!isset($vyska)){$vyska=$defvyska;}else{$vyska=intval($vyska);}
  $autoplay=_booleanStr(_boolean($autoplay));

  //sestaveni kodu
  return '
<div id="player_'.$GLOBALS['__hcm_uid'].'"><div class="message2">'.$GLOBALS['_lang']['hcm.player.alt'].'</div></div>
<script type="text/javascript">
// <![CDATA[
		var so = new SWFObject("'._indexroot.'remote/hcm/player.swf", "player_embed_'.$GLOBALS['__hcm_uid'].'", "'.$sirka.'", "'.$vyska.'", "9", "#000000");
		so.addParam("allowfullscreen","true");
		so.addVariable("file", "'.$soubor.'");
		so.addVariable("link", "'.$soubor.'");
		so.addVariable("autostart", "'.$autoplay.'");
		so.write("player_'.$GLOBALS['__hcm_uid'].'");
// ]]>
</script>
';

}

?>