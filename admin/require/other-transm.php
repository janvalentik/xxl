<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- akce ---*/
$message="";
if(isset($_POST['user'])){

$user=_safeStr(_anchorStr(trim($_POST['user'])));
$query=mysql_query("SELECT id,password FROM `"._mysql_prefix."-users` WHERE username='".$user."'");
  if(mysql_num_rows($query)!=0){
    $query=mysql_fetch_array($query);
    _userLogout();
    $_SESSION[_sessionprefix."user"]=$query['id'];
    $_SESSION[_sessionprefix."password"]=$query['password'];
    $_SESSION[_sessionprefix."ip"]=_userip;
    define('_tmp_redirect', _indexroot.'index.php?m=login');
  }
  else{
    $message=_formMessage(2, $_lang['global.baduser']);
  }

}

/*--- vystup ---*/

$output.="
<p class='bborder'>".$_lang['admin.other.transm.p']."</p>
".$message."
<form action='index.php?p=other-transm' method='post'>
<strong>".$_lang['global.user'].":</strong> <input type='text' name='user' class='inputsmall' /> <input type='submit' value='".$_lang['global.login']."' />
</form>
";


?>