<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- priprava promennych ---*/
$message="";
$continue=false;
if(isset($_GET['c'])){
$c=intval($_GET['c']);
$continue=true;
}

/*--- ulozeni ---*/
if(isset($_POST['do'])){

  foreach($_POST as $var=>$val){
    if($var!="do"){
    $var=@explode("-", $var);
      if(count($var)==2){
      $id=intval($var[0]);
      $var=_safeStr($var[1]);
      $skip=false;
      $quotes="";
        switch($var){
        case "title": $val=_safeStr(_htmlStr($val)); $quotes="'"; break;
        case "column": $val=intval($val); break;
        case "ord": $val=floatval($val); break;
        case "content": $val=_safeStr(_filtrateHCM(_stripSlashes($val)), false); $quotes="'"; break;
        case "visible": case "public": $val=_checkboxLoad($id.'-'.$var.'new'); break;
        default: $skip=true;
        }
          if($skip!=true){
          mysql_query("UPDATE `"._mysql_prefix."-boxes` SET `".$var."`=".$quotes.$val.$quotes." WHERE id=".$id);
          }
      }
    }
  }

}

/*--- odstraneni ---*/
if(isset($_GET['del'])){
$del=intval($_GET['del']);
  if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-boxes` WHERE id=".$del), 0)!=0){
  mysql_query("DELETE FROM `"._mysql_prefix."-boxes` WHERE id=".$del);
  $message=_formMessage(1, $_lang['global.done']);
  }
  else{
  $message=_formMessage(2, $_lang['global.badinput']);
  }
}

/*--- vystup ---*/
if($continue){

  //zprava
  if(isset($_GET['saved'])){$message=_formMessage(1, $_lang['global.saved']);}
  if(isset($_GET['created'])){$message=_formMessage(1, $_lang['global.created']);}

$output.="<br />".$message."<div class='hr'><hr /></div>
<form class='cform' action='index.php?p=content-boxes-edit&amp;c=".$c."&amp;saved' method='post'>
<input type='hidden' name='do' value='1' />
<p class='bborder'><input type='submit' value='".$_lang['admin.content.boxes.saveboxeschanges']."' />&nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?p=content-boxes-new&amp;c=".$c."'><img src='images/icons/new.gif' alt='new' class='icon' />".$_lang['admin.content.boxes.create']."</a></p>
<br />
<table id='boxesedit'>
";

$query=mysql_query("SELECT * FROM `"._mysql_prefix."-boxes` WHERE `column`=".$c." ORDER BY ord");
if(mysql_num_rows($query)!=0){
$isfirst=true;
  while($item=mysql_fetch_array($query)){
  if($isfirst){$output.="\n\n\n\n<tr>\n\n\n\n";}
    $output.="
    <td class='cell'>
    <div>
    <table>

    <tr>
    <td class='rpad'><strong>".$_lang['admin.content.form.title']."</strong></td>
    <td><input type='text' name='".$item['id']."-title' value='".$item['title']."' class='inputmedium' /></td>
    </tr>

    <tr>
    <td class='rpad'><strong>".$_lang['admin.content.boxes.column']."</strong></td>
    <td><input type='text' name='".$item['id']."-column' value='".$item['column']."' class='inputmedium' /></td>
    </tr>

    <tr>
    <td class='rpad'><strong>".$_lang['admin.content.form.ord']."</strong></td>
    <td><input type='text' name='".$item['id']."-ord' value='".$item['ord']."' class='inputmedium' /></td>
    </tr>

    <tr valign='top'>
    <td class='rpad'><strong>".$_lang['admin.content.form.content']."</strong></td>
    <td><textarea name='".$item['id']."-content' class='areasmall_100pwidth codemirror xml' rows='9' cols='33'>"._htmlStr($item['content'])."</textarea></td>
    </tr>

    <tr>
    <td class='rpad'><strong>".$_lang['admin.content.form.settings']."</strong></td>
    <td>
    <label><input type='checkbox' name='".$item['id']."-visiblenew' value='1'"._checkboxActivate($item['visible'])." /> ".$_lang['admin.content.form.visible']."</label>&nbsp;&nbsp;
    <label><input type='checkbox' name='".$item['id']."-publicnew' value='1'"._checkboxActivate($item['public'])." /> ".$_lang['admin.content.form.public']."</label>
    <input type='hidden' name='".$item['id']."-visible' value='1' />
    <input type='hidden' name='".$item['id']."-public' value='1' />
    &nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?p=content-boxes-edit&amp;c=".$c."&amp;del=".$item['id']."' onclick='return _sysConfirm();'><img src='images/icons/delete.gif' alt='del' class='icon' />".$_lang['admin.content.boxes.delete']."</a>
    </td>
    </tr>

    </table>
    </div>
    </td>
    ";
  if(!$isfirst){$output.="\n\n\n\n</tr>\n\n\n\n";}
  $isfirst=!$isfirst;
  }
  
    //dodatecne uzavreni radku tabulky (pri lichem poctu boxu)
    if(!$isfirst){$output.="\n\n\n\n</tr>\n\n\n\n";}
}
else{
$output.=$_lang['global.nokit'];
}
  
$output.="</table><input type='submit' value='".$_lang['admin.content.boxes.saveboxeschanges']."' /></form>";

}
else{
$output.=_formMessage(3, $_lang['global.badinput']);
}

?>