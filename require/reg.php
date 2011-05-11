<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- registrace ---*/
$done=false;
$message="";
if(isset($_POST['username'])){

$errors=array();

  //kontrola iplogu
  if(!_iplogCheck(5)){
  $errors[]=str_replace("*postsendexpire*", _postsendexpire, $_lang['misc.requestlimit']);
  }

  //nacteni a kontrola promennych
  $username=$_POST['username'];
    if(mb_strlen($username)>24){$username=mb_substr($username, 0, 24);}
    $username=_safeStr(_anchorStr($username, false));
    if($username==""){$errors[]=$_lang['admin.users.edit.badusername'];}
    elseif(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users` WHERE username='".$username."' OR publicname='".$username."'"), 0)!=0){$errors[]=$_lang['admin.users.edit.userexists'];}
  
  $password=$_POST['password'];
  $password2=$_POST['password2'];
    if($password!=$password2){$errors[]=$_lang['mod.reg.nosame'];}
    if($password!=""){$password=_md5Salt($password);}
    else{$errors[]=$_lang['mod.reg.passwordneeded'];}

  $email=_safeStr(trim($_POST['email']));
    if(!_validateEmail($email)){$errors[]=$_lang['admin.users.edit.bademail'];}
    if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users` WHERE email='".$email."'"), 0)!=0){$errors[]=$_lang['admin.users.edit.emailexists'];}

  if(!_captchaCheck()){
  $errors[]=$_lang['captcha.failure'];
  }
  
  $massemail=_checkboxLoad('massemail');
  
  if(_registration_grouplist and isset($_POST['group'])){
    $group=intval($_POST['group']);
    $groupdata=mysql_query("SELECT id FROM `"._mysql_prefix."-groups` WHERE id=".$group." AND blocked=0 AND reglist=1");
    if(mysql_num_rows($groupdata)==0){$errors[]=$_lang['global.badinput'];}
  }
  else{
    $group=_defaultgroup;
  }
  
  if(_rules!="" and !_checkboxLoad("agreement")){
  $errors[]=$_lang['mod.reg.rules.disagreed'];
  }
    
  //vlozeni do databaze nebo seznam chyb
  if(count($errors)==0){
  $newid=_getNewID("users");
  $codes=array();
  $query=mysql_query("SELECT code FROM `"._mysql_prefix."-users`");
  while ($result=mysql_fetch_array($query)) {
  	$codes[]=$result['code'];
  }
  $codes=implode(';',$codes);
  $code=_wordGen(6,2,$codes);
  $blocked=(_activation ? 1:0);
  mysql_query("INSERT INTO `"._mysql_prefix."-users` (id,`group`,levelshift,username,password,salt,logincounter,registertime,activitytime,blocked,massemail,wysiwyg,language,ip,email,avatar,web,skype,msn,jabber,icq,note,code) VALUES ($newid,".$group.",0,'".$username."','".$password[0]."','".$password[1]."',0,".time().",".time().",".$blocked.",".$massemail.",0,'','"._userip."','".$email."','','','','','',0,'','".$code."')");
  _iplogUpdate(5);
  $done=true;
  }
  else{
  $message=_formMessage(2, _eventList($errors, 'errors'));
  }

}

/*--- modul ---*/
if(_template_autoheadings==1){$module="<h1>".$_lang['mod.reg']."</h1>";}else{$module="";}

if($done==false){

  //priprava vyberu skupiny
  $groupselect=array(null);
  if(_registration_grouplist){
   $groupselect_items=mysql_query("SELECT id,title FROM `"._mysql_prefix."-groups` WHERE `blocked`=0 AND reglist=1 ORDER BY title");
    if(mysql_num_rows($groupselect_items)!=0){
    $groupselect_content="";
      while($groupselect_item=mysql_fetch_array($groupselect_items)){
        $groupselect_content.="<option value='".$groupselect_item['id']."'".(($groupselect_item['id']==_defaultgroup)?" selected='selected'":'').">".$groupselect_item['title']."</option>\n";
      }
      $groupselect=array($_lang['global.group'], "<select name='group'>".$groupselect_content."</select>");
    }
  }
  
  //priprava podminek
  if(_rules!=""){
    $rules=array("<div class='hr'><hr /></div><h2>".$_lang['mod.reg.rules']."</h2>"._rules."<br /><label><input type='checkbox' name='agreement' value='1' /> ".$_lang['mod.reg.rules.agreement']."</label><div class='hr'><hr /></div><br />", "", true);
  }
  else{
    $rules=array(null);
  }

  //formular
  $captcha=_captchaInit();

  $module.=
  "<p class='bborder'>".$_lang['mod.reg.p']."</p>".$message.
  _formOutput(
  "regform",
  "index.php?m=reg",
    array(
    array($_lang['login.username'], "<input type='text' name='username' class='inputsmall' maxlength='24'"._restorePostValue('username')." />"),
    array($_lang['login.password'], "<input type='password' name='password' class='inputsmall' />"),
    array($_lang['login.password']." (".$_lang['global.check'].")", "<input type='password' name='password2' class='inputsmall' />"),
    array($_lang['global.email'], "<input type='text' name='email' class='inputsmall' "._restorePostValue('email', '@')." />"),
    array($_lang['mod.settings.massemail'], "<input type='checkbox' name='massemail' value='1' checked='checked' /> ".$_lang['mod.settings.massemail.label']),
    $groupselect,
    $captcha,
    $rules,
    ),
  array("username", "email", "password", "password2"),
  $_lang['mod.reg.submit']
  );

}
else{
if(_activation){
 $sender=mysql_fetch_array(mysql_query("SELECT email FROM `"._mysql_prefix."-users` WHERE id=0"));
 if(@mail($email, "=?utf-8?B?".base64_encode($_lang['xxl.act.mail.subject'])."?=".$username, "".nl2br(_actmail)."<hr /><a href='"._url."/index.php?m=login&code=".$code."'>"._url."/index.php?m=login&code=".$code."</a><hr />", "Content-Type: text/html; charset=utf-8\nFrom:"._title." <".$sender['email'].">\n")){
    $module.=_formMessage(1, $_lang['xxl.actmail.send']);
 }
 else{
  $module.=_formMessage(3, $_lang['xxl.actmail.send.error']);
 }
}
else{
  $module.=_formMessage(1, str_replace("*username*", $username, $_lang['mod.reg.done']));
}
}

?>