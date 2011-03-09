<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- nastaveni a vlozeni skriptu pro upravu stranky --*/
$type=3;
require("require/sub/content-editscript-init.php");
if($continue){
  $custom_settings="
  <label><input type='checkbox' name='var3' value='1'"._checkboxActivate($query['var3'])." /> ".$_lang['admin.content.form.locked']."</label>&nbsp;&nbsp;
  <label><input type='checkbox' name='var1' value='1'"._checkboxActivate($query['var1'])." /> ".$_lang['admin.content.form.unregpost']."</label>
  ";
  if(!$new){$custom_settings.="&nbsp;&nbsp;<label><input type='checkbox' name='delposts' value='1' /> ".$_lang['admin.content.form.delposts']."</label> <small>(".mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-posts` WHERE home=".$id." AND type=3"), 0).")</small>";}
  $custom_settings.="&nbsp;&nbsp;<input type='text' name='var2' value='".$query['var2']."' class='inputmini' /> ".$_lang['admin.content.form.postsperpage'];
  $custom_array=array(array("var1", true, 0, false), array("var2", false, 2, false), array("var3", true, 0, false), array("delposts", true, 0, false));
}
require("require/sub/content-editscript.php");
?>