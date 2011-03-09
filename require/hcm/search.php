<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_search(){
  if(_search){
  return "<form action='index.php' method='get' class='searchform'>
<input type='hidden' name='m' value='search' />
<input type='hidden' name='root' value='1' />
<input type='hidden' name='art' value='1' />
<input type='text' name='q' class='q' /> <input type='submit' value='".$GLOBALS['_lang']['mod.search.submit']."' />
</form>
";
  }
}

?>