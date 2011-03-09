<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- priprava, kontrola pristupovych prav ---*/
$message="";
if(!(_loginright_adminsection or _loginright_admincategory or _loginright_adminbook or _loginright_adminseparator or _loginright_admingallery or _loginright_adminintersection)){$continue=false; $output.=_formMessage(3, $_lang['global.accessdenied']);}
else{$continue=true;}

/*--- akce ---*/
if(isset($_POST['do'])){

  foreach($_POST as $id=>$title){
  if($id=="do"){continue;}
  $id=intval($id);
  $title=_safeStr(_htmlStr(trim($title)));
  if($title==""){$title=$_lang['global.novalue'];}
  mysql_query("UPDATE `"._mysql_prefix."-root` SET title='".$title."' WHERE id=".$id);
  }
  
  $message=_formMessage(1, $_lang['global.saved']);

}

/*--- vystup ---*/
if($continue){
$output.="<p class='bborder'>".$_lang['admin.content.titles.p']."</p>".$message."

<form action='index.php?p=content-titles' method='post'>
<input type='hidden' name='do' value='1' />

<table>
<tr><td><strong>".$_lang['global.item']."</strong></td><td class='lpad'><strong>".$_lang['global.type']."</strong></td></tr>
";

  //funkce
  function _tmp_titleListItem($item, $ipad=false){
  global $_lang;
  $type_array=_tmp_getTypeArray();
  if($ipad==true){$ipad=" class='intersecpad'";}else{$ipad="";}
  return "<tr><td".$ipad."><input type='text' name='".$item['id']."' value='".$item['title']."' /></td><td class='lpad'>".$_lang['admin.content.'.$type_array[$item['type']]]."</td></tr>\n";
  }

  //vypis
  $query=mysql_query("SELECT id,title,type FROM `"._mysql_prefix."-root` WHERE intersection=-1 ORDER BY ord");
  while($item=mysql_fetch_array($query)){
  $output.=_tmp_titleListItem($item);
    if($item['type']==7){
    $iquery=mysql_query("SELECT id,title,type FROM `"._mysql_prefix."-root` WHERE intersection=".$item['id']." ORDER BY ord");
      while($iitem=mysql_fetch_array($iquery)){
      $output.=_tmp_titleListItem($iitem, true);
      }
    }
  }

$output.="
<tr>
<td><br /><input type='submit' value='".$_lang['global.save']."' /> <input type='reset' value='".$_lang['global.reset']."' onclick='return _sysConfirm();' /></td>
<td></td>
</tr>

</table>

</form>";
}



?>