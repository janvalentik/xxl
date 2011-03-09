<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- nastaveni a vlozeni skriptu pro upravu stranky --*/
$type=2;
require("require/sub/content-editscript-init.php");
if($continue){

    //vyber zpusobu razeni clanku
    $artorder_select="";
    for($x=1; $x<=4; $x++){
    if($x==$query['var1']){$selected=" selected='selected'";}else{$selected="";}
    $artorder_select.="<option value='".$x."'".$selected.">".$_lang['admin.content.form.artorder.'.$x]."</option>";
    }

  $custom_settings=
  $_lang['admin.content.form.artorder']." <select name='var1'>".$artorder_select."</select>&nbsp;&nbsp;".
  $_lang['admin.content.form.artsperpage']." <input type='text' name='var2' value='".$query['var2']."' class='inputmini' /></span>
  </span>&nbsp;&nbsp;<span class='customsettings'>
  <label><input type='checkbox' name='var3' value='1'"._checkboxActivate($query['var3'])." /> ".$_lang['admin.content.form.showinfo']."</label>
  ";
  
  $custom_array=array(
  array("var1", false, 2, false),
  array("var2", false, 2, false),
  array("var3", true,  0, false)
  );
}
require("require/sub/content-editscript.php");

?>