<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- nastaveni a vlozeni skriptu pro upravu stranky --*/
$type=1;
require("require/sub/content-editscript-init.php");
if($continue){
  $custom_array=array(array("var1", true, 0, false), array("var2", true, 0, false), array("var3", true, 0, false), array("delcomments", true, 0, false));
  $custom_settings="
  <label><input type='checkbox' name='var2' value='1'"._checkboxActivate($query['var2'])." /> ".$_lang['admin.content.form.autotitle']."</label>&nbsp;&nbsp;
  <label><input type='checkbox' name='var1' value='1'"._checkboxActivate($query['var1'])." /> ".$_lang['admin.content.form.comments']."</label>&nbsp;&nbsp;
  <label><input type='checkbox' name='var3' value='1'"._checkboxActivate($query['var3'])." /> ".$_lang['admin.content.form.commentslocked']."</label>
  ";
  if(!$new){$custom_settings.="&nbsp;&nbsp;<label><input type='checkbox' name='delcomments' value='1' /> ".$_lang['admin.content.form.delcomments']."</label> <small>(".mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-posts` WHERE home=".$id." AND type=1"), 0).")</small>";}
}
require("require/sub/content-editscript.php");

?>