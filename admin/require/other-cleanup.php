<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- pripava ---*/
$message="";

function _tmp_selectTime($name){
global $_lang;
return "<select name='".$name."'>
<option value='1'>".$_lang['admin.other.cleanup.time.1']."</option>
<option value='2'>".$_lang['admin.other.cleanup.time.2']."</option>
<option value='4'>".$_lang['admin.other.cleanup.time.4']."</option>
<option value='8'>".$_lang['admin.other.cleanup.time.8']."</option>
<option value='25' selected='selected'>".$_lang['admin.other.cleanup.time.25']."</option>
<option value='52'>".$_lang['admin.other.cleanup.time.52']."</option>
<option value='104'>".$_lang['admin.other.cleanup.time.104']."</option>
</select>";
}

/*--- akce ---*/
if(isset($_POST['action'])){

  switch($_POST['action']){

  //cistka
  case 1:
  
    //vzkazy
    $messages=$_POST['messages'];
    switch($messages){
    
      case 1:
      $messages_time=time()-($_POST['messages-time']*7*24*60*60);
      mysql_query("DELETE FROM `"._mysql_prefix."-messages` WHERE readed=1 AND time<".$messages_time);
      break;
      
      case 2:
      mysql_query("TRUNCATE TABLE `"._mysql_prefix."-messages`");
      break;
    
    }
    
    //komentare, prispevky, iplog
    if(_checkboxLoad("comments")){mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE type=1 OR type=2");}
    if(_checkboxLoad("posts")){mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE type>2");}
    if(_checkboxLoad("iplog")){mysql_query("TRUNCATE TABLE `"._mysql_prefix."-iplog`");}
    
    //uzivatele
    if(_checkboxLoad("users")){
      $users_time=time()-($_POST['users-time']*7*24*60*60);
      $users_group=intval($_POST['users-group']);
      if($users_group==-1){$users_group="";}else{$users_group=" AND `group`=".$users_group;}
      $userids=mysql_query("SELECT id FROM `"._mysql_prefix."-users` WHERE id!=0 AND activitytime<".$users_time.$users_group);
      while($userid=mysql_fetch_array($userids)){_deleteUser($userid['id']);}
    }
    
    
    
    //zprava
    $message=_formMessage(1, $_lang['global.done']);
  
  break;

  //deinstalace
  case 2:
  $pass=$_POST['pass'];
  $confirm=_checkboxLoad("confirm");
    if($confirm){
    $right_pass=mysql_fetch_array(mysql_query("SELECT password,salt FROM `"._mysql_prefix."-users` WHERE id=0"));
      if(_md5Salt($pass, $right_pass['salt'])==$right_pass['password']){

        //odstraneni tabulek
        $tables=array('articles', 'boxes', 'groups', 'images', 'iplog', 'messages', 'polls', 'posts', 'root', 'sboxes', 'settings', 'users');
        foreach($tables as $table){
        mysql_query("DROP TABLE `"._mysql_prefix."-".$table."`");
        }
        
        //zprava
        _userLogout();
        echo "<h1>".$_lang['global.done']."</h1>\n<p>".$_lang['admin.other.cleanup.uninstall.done']."</p>";
        exit;

      }
      else{
        $message=_formMessage(2, $_lang['admin.other.cleanup.uninstall.badpass']);
      }
    }
  break;

  }

}

/*--- vystup ---*/
$output.=$message."
<br />
<fieldset>
<legend>".$_lang['admin.other.cleanup.cleanup']."</legend>
<form class='cform' action='index.php?p=other-cleanup' method='post'>
<input type='hidden' name='action' value='1' />
<p>".$_lang['admin.other.cleanup.cleanup.p']."</p>

<table>
<tr valign='top'>

<td>
  <fieldset>
  <legend>".$_lang['mod.messages']."</legend>
  <label><input type='radio' name='messages' value='0' checked='checked' /> ".$_lang['global.noaction']."</label><br />
  <label><input type='radio' name='messages' value='1' /> ".$_lang['admin.other.cleanup.messages.1']."</label> "._tmp_selectTime("messages-time")."<br />
  <label><input type='radio' name='messages' value='2' /> ".$_lang['admin.other.cleanup.messages.2']."</label>
  </fieldset>
</td>

<td>
  <fieldset>
  <legend>".$_lang['global.other']."</legend>
  <label><input type='checkbox' name='comments' value='1' /> ".$_lang['admin.other.cleanup.other.comments']."</label><br />
  <label><input type='checkbox' name='posts' value='1' /> ".$_lang['admin.other.cleanup.other.posts']."</label><br />
  <label><input type='checkbox' name='iplog' value='1' /> ".$_lang['admin.other.cleanup.other.iplog']."</label>
  </fieldset>
</td>

</tr>

<tr>

<td>
  <fieldset>
  <legend>".$_lang['admin.users.users']."</legend>
  <p class='bborder'><label><input type='checkbox' name='users' value='1' /> ".$_lang['admin.other.cleanup.users']."</label></p>
  <table>

  <tr>
  <td><strong>".$_lang['admin.other.cleanup.users.time']."</strong></td>
  <td>"._tmp_selectTime("users-time")."</td>
  </tr>

  <tr>
  <td><strong>".$_lang['admin.other.cleanup.users.group']."</strong></td>
  <td>"._tmp_authorSelect("users-group", -1, "1", null, $_lang['global.all'], true)."</td>
  </tr>

  </table>
  </fieldset>
</td>

<td align='center'>
<input type='submit' value='".$_lang['global.do2']." &gt;' onclick='return _sysConfirm();' />
</td>

</tr>

</table>

</form>
</fieldset>
<br />

<fieldset>
<legend>".$_lang['admin.other.cleanup.uninstall']."</legend>
<form class='cform' action='index.php?p=other-cleanup' method='post'>
<input type='hidden' name='action' value='2' />
<p class='bborder'>".$_lang['admin.other.cleanup.uninstall.p']."</p>
<strong>".$_lang['admin.other.cleanup.uninstall.pass'].":</strong> <input type='password' class='inputsmall' name='pass' /><br />
<label><input type='checkbox' name='confirm' value='1' /> ".$_lang['admin.other.cleanup.uninstall.confirm']."</label><br /><br />
<input type='submit' value='".$_lang['global.do']."' onclick='return _sysConfirm();' />
</form>
</fieldset>
";
?>