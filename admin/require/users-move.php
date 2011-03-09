<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- ulozeni ---*/
$message="";
if(isset($_POST['sourcegroup'])){
$source=intval($_POST['sourcegroup']);
$target=intval($_POST['targetgroup']);
$source_data=mysql_query("SELECT level FROM `"._mysql_prefix."-groups` WHERE id=".$source);
$target_data=mysql_query("SELECT level FROM `"._mysql_prefix."-groups` WHERE id=".$target);

  if(mysql_num_rows($source_data)!=0 and mysql_num_rows($target_data)!=0 and $source!=2 and $target!=2){
    if($source!=$target){
      $source_data=mysql_fetch_array($source_data);
      $target_data=mysql_fetch_array($target_data);
        if(_loginright_level>$source_data['level'] and _loginright_level>$target_data['level']){
        mysql_query("UPDATE `"._mysql_prefix."-users` SET `group`=".$target." WHERE `group`=".$source." AND id!=0");
        $message=_formMessage(1, $_lang['global.done']);
        }
        else{
        $message=_formMessage(2, $_lang['admin.users.move.failed']);
        }
    }
    else{
    $message=_formMessage(2, $_lang['admin.users.move.same']);
    }
  }
  else{
  $message=_formMessage(3, $_lang['global.badinput']);
  }

}


/*--- vystup ---*/

$output.="<p class='bborder'>".$_lang['admin.users.move.p']."</p>\n".$message."
<form class='cform' action='index.php?p=users-move' method='post'>
".$_lang['admin.users.move.text1']." "._tmp_authorSelect("sourcegroup", -1, "id!=2", null, null, true)." ".$_lang['admin.users.move.text2']." "._tmp_authorSelect("targetgroup", -1, "id!=2", null, null, true)." <input type='submit' value='".$_lang['global.do']."' onclick='return _sysConfirm();' />
</form>
";




?>