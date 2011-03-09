<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- pripava promennych ---*/
$message="";
$errno=0;

  //id
  $continue=false;
  if(isset($_GET['id'])){
    $id=_safeStr(_anchorStr($_GET['id'], false));
    $query=mysql_query("SELECT * FROM `"._mysql_prefix."-users` WHERE username='".$id."'");
    if(mysql_num_rows($query)!=0){
    $query=mysql_fetch_array($query);
    
      //test pristupu
      if($query['id']!=_loginid){
        if(_levelCheck($query['id'])){
          if($query['id']!=0){$continue=true;}
          else{$errno=2;}
        }
      }
      else{
        define('_tmp_redirect', _indexroot.'index.php?m=settings');
      }
      
    }else{
      $errno=1;
    }
  }
  else{
  $continue=true;
  $id=null;
  $query=array(
  "id"=>"-1",
  "group"=>_defaultgroup,
  "levelshift"=>0,
  "username"=>"",
  "publicname"=>"",
  "blocked"=>0,
  "email"=>"@",
  "avatar"=>"",
  "web"=>"",
  "skype"=>"",
  "msn"=>"",
  "jabber"=>"",
  "icq"=>"",
  "note"=>""
  );
  }

  //vyber skupiny
  $group_select=_tmp_authorSelect("group", $query['group'], "id!=2 AND level<"._loginright_level, null, null, true);

if($continue){

/*--- ulozeni ---*/
if(isset($_POST['username'])){

$errors=array();

  //nacteni a kontrola promennych
  
    //username
    $username=$_POST['username'];
    if(mb_strlen($username)>24){$username=mb_substr($username, 0, 24);}
    $username=_safeStr(_anchorStr($username, false));
      if($username==""){$errors[]=$_lang['admin.users.edit.badusername'];}
      else{
        $usernamechange=false;
        if($username!=$query['username']){
            if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users` WHERE (username='".$username."' OR publicname='".$username."') AND id!=".$query['id']), 0)==0){
              $usernamechange=true;
            }
            else{
              $errors[]=$_lang['admin.users.edit.userexists'];
            }
        }
      }

    //publicname
    $publicname=$_POST['publicname'];
    if(mb_strlen($publicname)>24){$publicname=mb_substr($publicname, 0, 24);}
    $publicname=_safeStr(_htmlStr(_wsTrim($publicname)));
    if($publicname!=$query['publicname'] and $publicname!=""){if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users` WHERE (publicname='".$publicname."' OR username='".$publicname."') AND id!=".$query['id']), 0)!=0){$errors[]=$_lang['admin.users.edit.publicnameexists'];}}

    //email
    $email=_safeStr(trim($_POST['email']));
    if(!_validateEmail($email)){$errors[]=$_lang['admin.users.edit.bademail'];}
    else{if($email!=$query['email']){if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users` WHERE email='".$email."' AND id!=".$query['id']), 0)!=0){$errors[]=$_lang['admin.users.edit.emailexists'];}}}

    //icq
    $icq=intval(str_replace("-", "", $_POST['icq']));

    //skype
    $skype=trim($_POST['skype']);
    if($skype!="" and !preg_match('|[a-zA-Z0-9._-]{6,62}|', $skype)){$errors[]=$_lang['global.skype.bad'];}
    $skype=_safeStr($skype);

    //msn
    $msn=trim($_POST['msn']);
    if($msn!="" and !_validateEmail($msn)){$errors[]=$_lang['global.msn.bad'];}
    $msn=_safeStr($msn);

    //jabber
    $jabber=trim($_POST['jabber']);
    if($jabber!="" and !_validateEmail($jabber)){$errors[]=$_lang['global.jabber.bad'];}
    $jabber=_safeStr($jabber);

    //web
    $web=_htmlStr(trim($_POST['web']));
    if(mb_strlen($web)>255){$web=mb_substr($web, 0, 255);}
    if($web!="" and !_validateURL("http://".$web)){$web="";}
    else{$web=_safeStr($web);}

    //avatar
    if(_uploadavatar){
      $avatar="";
      if(_checkboxLoad("removeavatar")){
        $avatar_path=_indexroot."avatars/".$query['id'].".jpg";
        if(@file_exists($avatar_path)){@unlink($avatar_path);}
      }
    }
    else{
      $avatar=_htmlStr(trim($_POST['avatar']));
      if(!_validateURL($avatar)){$avatar="";}
      else{$avatar=_safeStr($avatar);}
    }

    //password
    $passwordchange=false;
    $password=$_POST['password'];
    if($id==null and $password==""){$errors[]=$_lang['admin.users.edit.passwordneeded'];}
    if($password!=""){$passwordchange=true; $password=_md5Salt($password);}

    //note
    $note=_safeStr(_htmlStr(_wsTrim(mb_substr($_POST['note'], 0, 1024))));

    //blocked
    $blocked=_checkboxLoad("blocked");

    //group
    if(isset($_POST['group'])){
      $group=intval($_POST['group']);
      $group_test=mysql_query("SELECT level FROM `"._mysql_prefix."-groups` WHERE id=".$group." AND id!=2 AND level<"._loginright_level);
      if(mysql_num_rows($group_test)!=0){
        $group_test=mysql_fetch_array($group_test);
        if($group_test['level']>_loginright_level){$errors[]=$_lang['global.badinput'];}
      }
      else{
        $errors[]=$_lang['global.badinput'];
      }
    }
    else{
      $group=$query['group'];
    }
    
    //levelshift
    if(_loginid==0){$levelshift=_checkboxLoad("levelshift");}
    else{$levelshift=$query['levelshift'];}

  //ulozeni / vytvoreni anebo seznam chyb
  if(count($errors)==0){

  if($id!=null){
    //ulozeni
    mysql_query("UPDATE `"._mysql_prefix."-users` SET email='".$email."',avatar='".$avatar."',web='".$web."',skype='".$skype."',msn='".$msn."',jabber='".$jabber."',icq=".$icq.",note='".$note."',publicname='".$publicname."',`group`=".$group.",blocked=".$blocked.",levelshift=".$levelshift." WHERE id=".$query['id']);
    if($passwordchange==true){mysql_query("UPDATE `"._mysql_prefix."-users` SET password='".$password[0]."', salt='".$password[1]."' WHERE id=".$query['id']);}
    if($usernamechange==true){mysql_query("UPDATE `"._mysql_prefix."-users` SET username='".$username."' WHERE id=".$query['id']);}
    define('_tmp_redirect', 'index.php?p=users-edit&r=1&id='.$username);
  }
  else{
    //vytvoreni
    $newid=_getNewID("users");
    mysql_query("INSERT INTO `"._mysql_prefix."-users` (id,`group`,levelshift,username,publicname,password,salt,logincounter,registertime,activitytime,blocked,massemail,wysiwyg,ip,email,avatar,web,skype,msn,jabber,icq,note) VALUES (".$newid.",".$group.",".$levelshift.",'".$username."','".$publicname."','".$password[0]."','".$password[1]."',0,".time().",0,".$blocked.",1,1,'','".$email."','".$avatar."','".$web."','".$skype."','".$msn."','".$jabber."',".$icq.",'".$note."')");
    define('_tmp_redirect', 'index.php?p=users-edit&r=2&id='.$username);
  }

  }
  else{
    $message=_eventList($errors, 'errors');
  }

}

/*--- vystup ---*/

  //zpravy
  $messages_code="";
  
  if(isset($_GET['r'])){
  switch($_GET['r']){
  case 1: $messages_code.=_formMessage(1, $_lang['global.saved']); break;
  case 2: $messages_code.=_formMessage(1, $_lang['global.created']); break;
  }
  }
  
  if($message!=""){
  $messages_code.=_formMessage(2, $message);
  }

$output.="
<p class='bborder'>".$_lang['admin.users.edit.p']."</p>
".$messages_code."
<form action='index.php?p=users-edit".(($id!=null)?"&amp;id=".$id:'')."' method='post' name='userform'"._jsCheckForm("userform", ($id!=null?array("username","email"):array("username","email","password"))).">
<table>

<tr>
<td class='rpad'><strong>".$_lang['login.username']."</strong></td>
<td><input type='text' name='username' class='inputsmall' value='".$query['username']."' maxlength='24' /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['mod.settings.publicname']."</strong></td>
<td><input type='text' name='publicname' class='inputsmall' value='".$query['publicname']."' maxlength='24' /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang[(($id==null)?'login.password':'mod.settings.password.new')]."</strong></td>
<td><input type='text' name='password' class='inputsmall' /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['global.group']."</strong></td>
<td>".$group_select."</td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['login.blocked']."</strong></td>
<td><input type='checkbox' name='blocked' value='1'"._checkboxActivate($query['blocked'])." /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['global.levelshift']."</strong></td>
<td><input type='checkbox' name='levelshift' value='1'"._checkboxActivate($query['levelshift'])._inputDisable(_loginid==0)." /> "._tmp_docsIcon("levelshift")."</td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['global.email']."</strong></td>
<td><input type='text' name='email' class='inputsmall' value='"._htmlStr($query['email'])."' /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['global.icq']."</strong></td>
<td><input type='text' name='icq' class='inputsmall' value='".(($query['icq']!=0)?$query['icq']:'')."' /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['global.skype']."</strong></td>
<td><input type='text' name='skype' class='inputsmall' value='".$query['skype']."' /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['global.msn']."</strong></td>
<td><input type='text' name='msn' class='inputsmall' value='".$query['msn']."' /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['global.jabber']."</strong></td>
<td><input type='text' name='jabber' class='inputsmall' value='".$query['jabber']."' /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['global.web']."</strong></td>
<td><input type='text' name='web' class='inputsmall' value='".$query['web']."' /> <small>".$_lang['mod.settings.web.hint']."</small></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['global.avatar']."</strong></td>
<td>".(_uploadavatar?"<input type='checkbox' name='removeavatar' value='1' /> ".$_lang['mod.settings.avatar.remove']."</label>":"<input type='text' name='avatar' class='inputmedium' value='".$query['avatar']."' />")."</td>
</tr>

<tr valign='top'>
<td class='rpad'><strong>".$_lang['global.note']."</strong></td>
<td><textarea name='note' class='areasmall' rows='9' cols='33'>".$query['note']."</textarea></td>
</tr>

<tr><td></td>
<td><input type='submit' value='".$_lang[(isset($_GET['id'])?'global.save':'global.create')]."' />".(($id!=null)?"&nbsp;&nbsp;<small>".$_lang['admin.content.form.thisid']." ".$query['id']."</small>":'')."</td>
</tr>

</table>
</form>
";

  //odkaz na profil a zjisteni ip
  if($id!=null){
  $output.="
  <p>
  <a href='"._indexroot."index.php?m=profile&amp;id=".$query['username']."' target='_blank'>".$_lang['mod.settings.profilelink']." &gt;</a>
  ".(_loginright_adminbans?"<br /><a href='index.php?p=other-bans&amp;getip=".$query['username']."'>".$_lang['admin.other.bans.getuserip']." &gt;</a>":'')."
  </p>
  ";
  }

}
else{
  switch($errno){
  case 1: $output.=_formMessage(2, $_lang['global.baduser']); break;
  case 2: $output.=_formMessage(2, $_lang['global.rootnote']); break;
  default: $output.=_formMessage(3, $_lang['global.disallowed']); break;
  }
}

?>