<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- pripava promennych ---*/
$message="";
$query=mysql_fetch_array(mysql_query("SELECT * FROM `"._mysql_prefix."-users` WHERE id="._loginid));
if($query['icq']==0){$query['icq']="";}

  //cesta k avataru
  if(_uploadavatar){
    $avatar_path=_indexroot."avatars/"._loginid.".jpg";
    if(!@file_exists($avatar_path)){$avatarsrc=_templateImage("system/no-avatar.gif");}
    else{$avatarsrc=$avatar_path;}
  }
  else{
    if($query['avatar']==""){$avatarsrc=_templateImage("system/no-avatar.gif");}
    else{$avatarsrc=$query['avatar'];}
  }

/*--- ulozeni ---*/
if(isset($_POST['username'])){

$errors=array();

  /*-- nacteni a kontrola promennych --*/
  
    //sebedestrukce
    if(_loginright_selfdestruction and _checkboxLoad("selfremove")){
    $selfremove_confirm=_md5Salt($_POST['selfremove-confirm'], $query['salt']);
      if($selfremove_confirm==$query['password']){
        if(_loginid!=0){
        _deleteUser(_loginid);
        $_SESSION=array();
        session_destroy();
        define('_tmp_redirect', 'index.php?m=login&_mlr=4');
        $errors[]=0; //zablokuje ukladani
        }
        else{
        $errors[]=$_lang['mod.settings.selfremove.denied'];
        }
      }
      else{
      $errors[]=$_lang['mod.settings.selfremove.failed'];
      }
    }
  
    //username
    $username=$_POST['username'];
    if(mb_strlen($username)>24){$username=mb_substr($username, 0, 24);}
    $username=_safeStr(_anchorStr($username, false));
      if($username==""){$errors[]=$_lang['admin.users.edit.badusername'];}
      else{
        $usernamechange=false;
        if($username!=_loginname){
          if(_loginright_changeusername or mb_strtolower($username)==mb_strtolower(_loginname)){
            if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users` WHERE (username='".$username."' OR publicname='".$username."') AND id!="._loginid), 0)==0){
              $usernamechange=true;
            }
            else{
              $errors[]=$_lang['admin.users.edit.userexists'];
            }
          }
          else{
          $errors[]=$_lang['mod.settings.error.usernamechangedenied'];
          }
        }
      }
      
    //publicname
    $publicname=$_POST['publicname'];
    if(mb_strlen($publicname)>24){$publicname=mb_substr($publicname, 0, 24);}
    $publicname=_safeStr(_htmlStr(_wsTrim($publicname)));
    if($publicname!=$query['publicname'] and $publicname!=""){if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users` WHERE (publicname='".$publicname."' OR username='".$publicname."') AND id!="._loginid), 0)!=0){$errors[]=$_lang['admin.users.edit.publicnameexists'];}}

    //email
    $email=_safeStr(trim($_POST['email']));
    if(!_validateEmail($email)){$errors[]=$_lang['admin.users.edit.bademail'];}
    else{if($email!=_loginemail){if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users` WHERE email='".$email."' AND id!="._loginid), 0)!=0){$errors[]=$_lang['admin.users.edit.emailexists'];}}}

    //massemail, wysiwyg, icq
    $massemail=_checkboxLoad("massemail");
    if(_loginright_administration){$wysiwyg=_checkboxLoad("wysiwyg");}
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
      
      //smazani avataru
      if(_checkboxLoad("removeavatar")){
        if(@file_exists($avatar_path)){@unlink($avatar_path);}
      }
      
      //upload avataru
      if(_checkGD("jpg", true)){
        if(isset($_FILES['avatar'])){
          $tmp_image=$_FILES['avatar']['tmp_name'];
          $tmp_image_info=pathinfo($_FILES['avatar']['name']);
          if(!isset($tmp_image_info['extension'])){$tmp_image_info['extension']="";}
            if(in_array(mb_strtolower($tmp_image_info['extension']), $__image_ext)){

              //nacteni obrazku
              switch(mb_strtolower($tmp_image_info['extension'])){
              case "jpg": case "jpeg": $avatar_source=@imagecreatefromjpeg($tmp_image); break;
              case "png": $avatar_source=@imagecreatefrompng($tmp_image); break;
              case "gif": $avatar_source=@imagecreatefromgif($tmp_image); break;
              }

              if($avatar_source){
                //vypocet rozmeru
                list($width, $height)=getimagesize($tmp_image);
                $newwidth=round(($width/$height)*128);
                if($newwidth==0){$newwidth=1;}

                //zmenseni a orezani
                $avatar_result=imagecreatetruecolor(96, 128);
                $bg=imagecolorallocate($avatar_result, 255, 255, 255);
                imagecopyresampled($avatar_result, $avatar_source, 0, 0, 0, 0, $newwidth, 128, $width, $height);
                if(@file_exists($avatar_path)){@unlink($avatar_path);}
                imagejpeg($avatar_result, $avatar_path, 75);
                imagedestroy($avatar_result);
              }
              else{
                _systemFailure("Invalid file.");
              }
              
            }
        }
      }

    }
    else{
      $avatar=_htmlStr(trim($_POST['avatar']));
      if(!_validateURL($avatar)){$avatar="";}
      else{$avatar=_safeStr($avatar);}
    }

    //password
    $passwordchange=false;
    if($_POST['currentpassword']!="" or $_POST['newpassword']!="" or $_POST['newpassword-confirm']!=""){
    $currentpassword=_md5Salt($_POST['currentpassword'], $query['salt']);
    $newpassword=$_POST['newpassword'];
    $newpassword_confirm=$_POST['newpassword-confirm'];
      if($currentpassword==$query['password']){
        if($newpassword==$newpassword_confirm){
          if($newpassword!=""){
          $passwordchange=true;
          $newpassword=_md5Salt($newpassword);
          }
          else{
          $errors[]=$_lang['mod.settings.error.badnewpass'];
          }
        }
        else{
        $errors[]=$_lang['mod.settings.error.newpassnosame'];
        }
      }
      else{
      $errors[]=$_lang['mod.settings.error.badcurrentpass'];
      }
    }

    //note
    $note=_safeStr(_htmlStr(_wsTrim(mb_substr($_POST['note'], 0, 1024))));
    
    //language
    if(_language_allowcustom){
      $language=_safeStr(_anchorStr($_POST['language'], false));
      if(!@file_exists(_indexroot."languages/".$language.".php")){$language="";}
    }
    
  /*-- ulozeni nebo seznam chyb --*/
  if(count($errors)==0){
  mysql_query("UPDATE `"._mysql_prefix."-users` SET email='".$email."',avatar='".$avatar."',web='".$web."',skype='".$skype."',msn='".$msn."',jabber='".$jabber."',icq=".$icq.",massemail=".$massemail.",note='".$note."',publicname='".$publicname."' WHERE id="._loginid);
  if(_loginright_administration){mysql_query("UPDATE `"._mysql_prefix."-users` SET wysiwyg=".$wysiwyg." WHERE id="._loginid);}
  if(_language_allowcustom){mysql_query("UPDATE `"._mysql_prefix."-users` SET language='".$language."' WHERE id="._loginid);}
  if($passwordchange==true){mysql_query("UPDATE `"._mysql_prefix."-users` SET password='".$newpassword[0]."', salt='".$newpassword[1]."' WHERE id="._loginid); $_SESSION[_sessionprefix."password"]=$newpassword[0];}
  if($usernamechange==true){mysql_query("UPDATE `"._mysql_prefix."-users` SET username='".$username."' WHERE id="._loginid);}
  define('_tmp_redirect', 'index.php?m=settings&saved');
  }
  else{
  $message=_formMessage(2, _eventList($errors, 'errors'));
  }

}

/*--- modul ---*/
if(isset($_GET['saved'])){$message=_formMessage(1, $_lang['global.saved']);}
if(_template_autoheadings==1){$module="<h1>".$_lang['mod.settings']."</h1>";}else{$module="";}

  //vyber jazyka
  if(_language_allowcustom){
    $language_select='
    <tr>
    <td><strong>'.$_lang['admin.settings.main.language'].'</strong></td>
    <td><select name="language" class="inputsmall"><option value="">'.$_lang['global.original'].'</option>';
      $handle=@opendir(_indexroot."languages");
      while($item=@readdir($handle)){
      if($item=="." or $item==".." or @is_dir(_indexroot.$item) or $item==_language.".php"){continue;}
      
        //kontrola polozky
        $item=pathinfo($item);
        if(!isset($item['extension']) or $item['extension']!="php"){continue;}
        $item=mb_substr($item['basename'], 0, mb_strrpos($item['basename'], "."));
      
      if($item==_loginlanguage){$selected=' selected="selected"';}else{$selected="";}
      $language_select.='<option value="'.$item.'"'.$selected.'>'.$item.'</option>';
      }
      closedir($handle);
    $language_select.='</select></td></tr>';
  }
  else{
    $language_select="";
  }
  
  //wysiwyg
  if(_loginright_administration){
  $admin="
  
  
  
  <tr>
  <td><strong>".$_lang['mod.settings.wysiwyg']."</strong></td>
  <td><label><input type='checkbox' name='wysiwyg' value='1'"._checkboxActivate($query['wysiwyg'])." /> ".$_lang['mod.settings.wysiwyg.label']."</label></td>
  </tr>
  
  ";
  }
  else{
  $admin="";
  }

$module.="
<p><a href='index.php?m=profile&amp;id="._loginname."'>".$_lang['mod.settings.profilelink']." &gt;</a></p>
<p>".$_lang['mod.settings.p']."</p>".$message."
<form action='index.php?m=settings' method='post' name='setform' enctype='multipart/form-data'>

"._jsLimitLength(1024, "setform", "note")."

  <fieldset>
  <legend>".$_lang['mod.settings.userdata']."</legend>
  <table class='profiletable'>

  <tr>
  <td><strong>".$_lang['login.username']."</strong> <span class='important'>*</span></td>
  <td><input type='text' name='username' value='"._loginname."' class='inputsmall' maxlength='24' />".(!_loginright_changeusername?"<span class='hint'>(".$_lang['mod.settings.namechangenote'].")</span>":'')."</td>
  </tr>

  <tr>
  <td><strong>".$_lang['mod.settings.publicname']."</strong></td>
  <td><input type='text' name='publicname' value='".$query['publicname']."' class='inputsmall' maxlength='24' /></td>
  </tr>

  <tr valign='top'>
  <td><strong>".$_lang['global.email']."</strong> <span class='important'>*</span></td>
  <td><input type='text' name='email' value='"._htmlStr($query['email'])."' class='inputsmall'/></td>
  </tr>

  ".$language_select."
  
  <tr>
  <td><strong>".$_lang['mod.settings.massemail']."</strong></td>
  <td><label><input type='checkbox' name='massemail' value='1'"._checkboxActivate($query['massemail'])." /> ".$_lang['mod.settings.massemail.label']."</label></td>
  </tr>
  
  ".$admin."
  </table>
  </fieldset>
  
  
  <fieldset>
  <legend>".$_lang['mod.settings.password']."</legend>
  <p class='minip'>".$_lang['mod.settings.password.hint']."</p>
  <table class='profiletable'>

  <tr>
  <td><strong>".$_lang['mod.settings.password.current']."</strong></td>
  <td><input type='password' name='currentpassword' class='inputsmall' /></td>
  </tr>

  <tr>
  <td><strong>".$_lang['mod.settings.password.new']."</strong></td>
  <td><input type='password' name='newpassword' class='inputsmall' /></td>
  </tr>

  <tr>
  <td><strong>".$_lang['mod.settings.password.new']." (".$_lang['global.check'].")</strong></td>
  <td><input type='password' name='newpassword-confirm' class='inputsmall' /></td>
  </tr>

  </table>
  </fieldset>
  
  
  <fieldset>
  <legend>".$_lang['mod.settings.info']."</legend>

  <table class='profiletable'>
  
  <tr>
  <td><strong>".$_lang['global.icq']."</strong></td>
  <td><input type='text' name='icq' value='".$query['icq']."' class='inputsmall' /></td>
  </tr>

  <tr>
  <td><strong>".$_lang['global.skype']."</strong></td>
  <td><input type='text' name='skype' value='".$query['skype']."' class='inputsmall' /></td>
  </tr>

  <tr>
  <td><strong>".$_lang['global.msn']."</strong></td>
  <td><input type='text' name='msn' value='".$query['msn']."' class='inputsmall' /></td>
  </tr>

  <tr>
  <td><strong>".$_lang['global.jabber']."</strong></td>
  <td><input type='text' name='jabber' value='".$query['jabber']."' class='inputsmall' /></td>
  </tr>

  <tr>
  <td><strong>".$_lang['global.web']."</strong></td>
  <td><input type='text' name='web' value='".$query['web']."' class='inputsmall' /><span class='hint'>".$_lang['mod.settings.web.hint']."</span></td>
  </tr>

  <tr valign='top'>
  <td><strong>".$_lang['global.note']."</strong></td>
  <td><textarea name='note' class='areasmall' rows='9' cols='33'>".$query['note']."</textarea></td>
  </tr>

  <tr><td></td>
  <td>"._getPostFormControls("setform", "note")."</td>
  </tr>

  </table>

  </fieldset>


  <fieldset>
  <legend>".$_lang['mod.settings.avatar']."</legend>
  <p>".(_uploadavatar?"<strong>".$_lang['mod.settings.avatar.upload'].":</strong> <input type='file' name='avatar' />":"<input type='text' name='avatar' class='inputmedium' value='".$query['avatar']."' />")."</p>
    <table>
    <tr valign='top'>
    <td width='106'><div class='avatar'><img src='".$avatarsrc."' alt='avatar' /></div></td>
    <td><p class='minip'>".$_lang['mod.settings.avatar.hint'.(_uploadavatar?'2':'')]."</p>".(_uploadavatar?"<p><label><input type='checkbox' name='removeavatar' value='1' /> ".$_lang['mod.settings.avatar.remove']."</label></p>":'')."</td>
    </tr>
    </table>
  </fieldset>
";

if(_loginright_selfdestruction and _loginid!=0){
$module.="

  <fieldset>
  <legend>".$_lang['mod.settings.selfremove']."</legend>
  <label><input type='checkbox' name='selfremove' value='1' onclick='if(checked==true){return _sysConfirm();}' /> ".$_lang['mod.settings.selfremove.box']."</label><br /><br />
  <div class='lpad'><strong>".$_lang['mod.settings.selfremove.confirm'].":</strong> <input type='password' name='selfremove-confirm' class='inputsmall' /></div>
  </fieldset>

";
}

$module.="
<br />
<input type='submit' value='".$_lang['mod.settings.submit']."' />
<input type='reset' value='".$_lang['global.reset']."' onclick='return _sysConfirm();' />

</form>
";

?>