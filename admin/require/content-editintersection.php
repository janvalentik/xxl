<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- nastaveni a vlozeni skriptu pro upravu stranky --*/
$type=7;
require("require/sub/content-editscript-init.php");
if($continue){
  $custom_settings="
  <label><input type='checkbox' name='var1' value='1'"._checkboxActivate($query['var1'])." /> ".$_lang['admin.content.form.showinfo']."</label>&nbsp;&nbsp;
  <label><input type='checkbox' name='var2' value='1'"._checkboxActivate($query['var2'])." /> ".$_lang['admin.content.form.flink']."</label>
  ";
  $custom_array=array(array("var1", true, 0, false), array("var2", true, 0, false));
}
require("require/sub/content-editscript.php");

?>