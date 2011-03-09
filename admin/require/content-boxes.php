<?php

/*--- kontrola jadra---*/
if(!defined('_core')){exit;}

/*--- priprava, odstraneni sloupce ---*/
$message="";
if(isset($_GET['delcolumn'])){
mysql_query("DELETE FROM `"._mysql_prefix."-boxes` WHERE `column`=".intval($_GET['delcolumn']));
$message=_formMessage(1, $_lang['global.done']);
}

/*--- vystup ---*/
$output.="<p class='bborder'>".$_lang['admin.content.boxes.p']."</p>
<p><a href='index.php?p=content-boxes-new'><img src='images/icons/new.gif' alt='new' class='icon' />".$_lang['admin.content.boxes.create']."</a></p>".$message."

<table class='widetable'>
<tr><td><strong>".$_lang['admin.content.boxes.column']."</strong></td><td><strong>".$_lang['admin.content.boxes.totalboxes']."</strong></td><td><strong>".$_lang['global.action']."</strong></td></tr>
";

  $query=mysql_query("SELECT DISTINCT `column` FROM `"._mysql_prefix."-boxes` ORDER BY `column`");
  while($item=mysql_fetch_array($query)){
  $output.="<tr><td><a href='index.php?p=content-boxes-edit&amp;c=".$item['column']."' class='block'><img src='images/icons/dir.gif' alt='col' class='icon' /><strong>".$item['column']."</strong></a></td><td>".mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-boxes` WHERE `column`=".$item['column']), 0)."</td><td><a href='index.php?p=content-boxes&amp;delcolumn=".$item['column']."' onclick='return _sysConfirm();'><img src='images/icons/delete.gif' alt='del' class='icon' />".$_lang['global.delete']."</a></td></tr>\n";
  }
  
$output.="</table>";

?>