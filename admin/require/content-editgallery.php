<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- nastaveni a vlozeni skriptu pro upravu stranky --*/
$type=5;
require("require/sub/content-editscript-init.php");
if($continue){

  $custom_settings="
  <input type='text' name='var1' value='".$query['var1']."' class='inputmicro' /> ".$_lang['admin.content.form.imgsperrow'].",&nbsp;&nbsp;
  <input type='text' name='var2' value='".$query['var2']."' class='inputmicro' /> ".$_lang['admin.content.form.imgsperpage']."
  </span>&nbsp;&nbsp;<span class='customsettings'>
  <input type='text' name='var3' value='".$query['var3']."' class='inputmini' /> ".$_lang['admin.content.form.prevheight']."&nbsp;&nbsp;
  <label><input type='checkbox' name='var4' value='1'"._checkboxActivate($query['var4'])." /> ".$_lang['admin.content.form.comments']."</label>&nbsp;&nbsp;
  <label><input type='checkbox' name='var5' value='1'"._checkboxActivate($query['var5'])." /> ".$_lang['admin.content.form.commentslocked']."</label>
  ";
  if(!$new){
    $custom_settings.="&nbsp;&nbsp;<label><input type='checkbox' name='delcomments' value='1' /> ".$_lang['admin.content.form.delcomments']."</label> <small>(".mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-posts` WHERE home=".$id." AND type=7"), 0).")</small>";
    $custom_settings.="</span>&nbsp;&nbsp;<span class='customsettings'><a href='index.php?p=content-manageimgs&amp;g=".$id."'><img src='images/icons/edit.gif' alt='edit' class='icon' /><strong>".$_lang['admin.content.form.manageimgs']." &gt;</strong></a>";
  }
  
  $custom_array=array(
  array("var1", false, 2, false),
  array("var2", false, 2, false),
  array("var3", false, 2, false),
  array("var4", false, 2, false),
  array("var5", false, 2, false),
  array("delcomments", true, 0, false)
  );
}
require("require/sub/content-editscript.php");

?>