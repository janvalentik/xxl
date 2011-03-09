<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- priprava ---*/
if(isset($_GET['c'])){
$c=intval($_GET['c']);
$returntolist=true;
}
else{
$c=1;
$returntolist=false;
}

/*--- ulozeni ---*/
if(isset($_POST['title'])){

  //nacteni promennych
  $title=_safeStr(_htmlStr($_POST['title']));
  $column=intval($_POST['column']);
  $ord=floatval($_POST['ord']);
  $content=_safeStr(_filtrateHCM(_stripSlashes($_POST['content'])), false);
  $visible=_checkboxLoad('visible');
  $public=_checkboxLoad('public');
  
  //vlozeni
  $newid=_getNewID("boxes");
  mysql_query("INSERT INTO `"._mysql_prefix."-boxes` (id,ord,title,content,visible,public,`column`) VALUES (".$newid.",".$ord.",'".$title."','".$content."',".$visible.",".$public.",".$column.")");
  define('_tmp_redirect', 'index.php?p=content-boxes-edit&c='.$column.'&created');

}

/*--- vystup ---*/
$output.="
<a href='index.php?p=".($returntolist?"content-boxes-edit&amp;c=".$c:"content-boxes")."' class='backlink'>&lt; ".$_lang['global.return']."</a>
<h1>".$_lang['admin.content.boxes.new.title']."</h1>
<p class='bborder'></p>

<form class='cform' action='index.php?p=content-boxes-new&amp;c=".$c."' method='post'>

<table>

<tr>
<td class='rpad'><strong>".$_lang['admin.content.form.title']."</strong></td>
<td><input type='text' name='title' class='inputmedium' /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['admin.content.boxes.column']."</strong></td>
<td><input type='text' name='column' value='".$c."' class='inputmedium' /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['admin.content.form.ord']."</strong></td>
<td><input type='text' name='ord' value='1' class='inputmedium' /></td>
</tr>

<tr valign='top'>
<td class='rpad'><strong>".$_lang['admin.content.form.content']."</strong></td>
<td><textarea name='content' class='areasmall_100pwidth' rows='9' cols='33'></textarea></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['admin.content.form.settings']."</strong></td>
<td>
<label><input type='checkbox' name='visible' value='1' checked='checked' /> ".$_lang['admin.content.form.visible']."</label>&nbsp;&nbsp;
<label><input type='checkbox' name='public' value='1' checked='checked' /> ".$_lang['admin.content.form.public']."</label>
</td>
</tr>

<tr>
<td></td>
<td><input type='submit' value='".$_lang['global.create']."' /></td>
</tr>

</table>

</form>

";

?>