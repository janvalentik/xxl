<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- pripava ---*/
if(isset($_GET['link'])){$mode=1;}else{$mode=0;}

/*--- vystup ---*/
if(_template_autoheadings==1){$module="<h1>".$_lang['mod.lostpass']."</h1>";}else{$module="";}

switch($mode){

case 1:

  //nacteni promennych
  if(_iplogCheck(1)){
    if(isset($_GET['user']) and isset($_GET['hash'])){
    $user=_anchorStr($_GET['user'], false);
    $hash=_safeStr($_GET['hash']);
    $done=false;

    //text
    $module.="<p class='bborder'>".$_lang['mod.lostpass.p2']."</p>";

    //zmena nebo zadani hesla
    $errors=array();
    if(isset($_POST['action'])){

        //uzivatelske jmeno and otisk
        $badlink=false;
        $userdata=mysql_query("SELECT id,email,password,salt,username FROM `"._mysql_prefix."-users` WHERE username='"._safeStr($user)."'");
        if(mysql_num_rows($userdata)==0){
          $errors[]=$_lang['mod.lostpass.badlink']; $badlink=true;
        }
        else{
           $userdata=mysql_fetch_array($userdata);
           if($hash!=md5($userdata['email'].$userdata['salt'].$userdata['password'])){$errors[]=$_lang['mod.lostpass.badlink']; $badlink=true;}
        }

        //zmena a odeslani emailu nebo vypis chyb
        if(count($errors)==0){
          $newpass=_md5Salt(_wordGen());
          $text_tags=array("*url*", "*username*", "*newpass*", "*date*", "*ip*");
          $text_contents=array(_url."/", $userdata['username'], $newpass[2], _formatTime(time()), _userip);
            if(@mail($userdata['email'], $_lang['mod.lostpass.mail.subject'], str_replace($text_tags, $text_contents, $_lang['mod.lostpass.mail.text2']), "Content-Type: text/plain; charset=utf-8\n")){
              mysql_query("UPDATE `"._mysql_prefix."-users` SET password='".$newpass[0]."', salt='".$newpass[1]."' WHERE id=".$userdata['id']);
              $module.=_formMessage(1, $_lang['mod.lostpass.generated']);
            }
            else{
              $module.=_formMessage(3, $_lang['hcm.mailform.msg.failure2']);
            }
          $done=true;
        }
        else{
          $module.=_formMessage(2, _eventList($errors, "errors"));
          if($badlink){_iplogUpdate(1);}
        }

    }

    //formular
    if(!$done and count($errors)==0){
      $module.=
      _formOutput(
      "lostpassform",
      "index.php?m=lostpass&amp;link&amp;user="._htmlStr($user)."&amp;hash="._htmlStr($hash),
      array(),
      array(),
      $_lang['mod.lostpass.generate'],
      "<input type='hidden' name='action' value='1' />"
      );
    }

    }
  }
  else{
    $module.=_formMessage(2, str_replace(array("*1*", "*2*"), array(_maxloginattempts, _maxloginexpire/60), $_lang['login.attemptlimit']));
  }

break;

default:

  $module.="<p class='bborder'>".$_lang['mod.lostpass.p']."</p>";

    //kontrola promennych, odeslani emailu
    if(isset($_POST['username'])){

      //nacteni promennych
      $username=_anchorStr($_POST['username'], false);
      $email=_safeStr($_POST['email']);

      //kontrola promennych
      if(_captchaCheck()){
        $userdata=mysql_query("SELECT email,password,salt,username FROM `"._mysql_prefix."-users` WHERE username='"._safeStr($username)."' AND email='".$email."'");
        if(mysql_num_rows($userdata)!=0){

          //odeslani emailu
          $userdata=mysql_fetch_array($userdata);
          $link=_url."/index.php?m=lostpass&link&user=".$username."&hash=".md5($userdata['email'].$userdata['salt'].$userdata['password']);
          $text_tags=array("*url*", "*username*", "*link*", "*date*", "*ip*");
          $text_contents=array(_url."/", $userdata['username'], $link, _formatTime(time()), _userip);
            if(@mail($userdata['email'], $_lang['mod.lostpass.mail.subject'], str_replace($text_tags, $text_contents, $_lang['mod.lostpass.mail.text']), "Content-Type: text/plain; charset=utf-8\n")){
              $module.=_formMessage(1, $_lang['mod.lostpass.cmailsent']);
            }
            else{
             $module.=_formMessage(3, $_lang['hcm.mailform.msg.failure2']);
            }

        }
        else{
          $module.=_formMessage(2, $_lang['mod.lostpass.notfound']);
        }
      }
      else{
        $module.=_formMessage(2, $_lang['captcha.failure2']);
      }

    }

    //formular
    $captcha=_captchaInit();

    $module.=
    _formOutput(
    "lostpassform",
    "index.php?m=lostpass",
      array(
      array($_lang['login.username'], "<input type='text' name='username' class='inputsmall' maxlength='24'"._restorePostValue('username')." />"),
      array($_lang['global.email'], "<input type='text' name='email' class='inputsmall' "._restorePostValue('email', '@')." />"),
      $captcha
      ),
    array("username", "email"),
    $_lang['global.send']
    );
  
break;

}

?>