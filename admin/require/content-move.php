<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- priprava, kontrola pristupovych prav ---*/
$message="";
if(!(_loginright_adminsection or _loginright_admincategory or _loginright_adminbook or _loginright_adminseparator or _loginright_admingallery or _loginright_adminintersection)){$continue=false; $output.=_formMessage(3, $_lang['global.accessdenied']);}
else{$continue=true;}

/*--- akce ---*/
if(isset($_POST['action'])){

  //nacteni promennych
  $action=intval($_POST['action']);
  $zonedir=intval($_POST['zonedir']);
  $zone=floatval($_POST['zone']);
  $offset=floatval($_POST['offset']);
  
  //aplikace
  if($action==1){$sign="+";}else{$sign="-";}
  if($zonedir==1){$zonedir=">";}else{$zonedir="<";}
  mysql_query("UPDATE `"._mysql_prefix."-root` SET ord=ord".$sign.$offset." WHERE ord".$zonedir."=".$zone." AND intersection=-1");
  $message=_formMessage(1, $_lang['global.done']);

}

/*--- vystup ---*/
if($continue){
$output.="<p class='bborder'>".$_lang['admin.content.move.p']."</p>".$message."
<form class='cform' action='index.php?p=content-move' method='post'>
<select name='action'><option value='1'>".$_lang['admin.content.move.choice1']."</option><option value='2'>".$_lang['admin.content.move.choice2']."</option></select>&nbsp;
".$_lang['admin.content.move.text1']."&nbsp;
<select name='zonedir'><option value='1'>".$_lang['admin.content.move.choice3']."</option><option value='2'>".$_lang['admin.content.move.choice4']."</option></select>&nbsp;
".$_lang['admin.content.move.text2']."&nbsp;
<input type='text' name='zone' value='1' class='inputmini' maxlength='5' />&nbsp;,
".$_lang['admin.content.move.text3']."&nbsp;
<input type='text' name='offset' value='1' class='inputmini' maxlength='5' />.&nbsp;
<input type='submit' value='".$_lang['global.do']."' onclick='return _sysConfirm();' />
</form>
";
}



?>