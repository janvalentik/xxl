<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- pripava promennych ---*/
$levelconflict=false;
$sysgroups_array=array(1,2/*,3 is not necessary*/);
$unregistered_useable=array(
"postcomments",
"artrate",
"pollvote"
);

  //id
  $continue=false;
  if(isset($_GET['id'])){
    $id=intval($_GET['id']);
    $query=mysql_query("SELECT * FROM `"._mysql_prefix."-groups` WHERE id=".$id);
    if(mysql_num_rows($query)!=0){
      $query=mysql_fetch_array($query);
      $systemitem=in_array($query['id'], $sysgroups_array);
      if(_loginright_level>$query['level']){$continue=true;}else{$levelconflict=true;}
    }
  }

if($continue){

    //pole prav
    $rights_array=array(
    "changeusername",
    "postcomments",
    "locktopics",
    "unlimitedpostaccess",
    "artrate",
    "pollvote",
    "adminposts",
    "selfdestruction",
    "-".$_lang['admin.users.groups.adminrights'],
    "administration",
    "adminsettings",
    "adminusers",
    "admingroups",
    "adminfman",
    "adminfmanlimit",
    "adminfmanplus",
    "adminhcmphp",
    "adminbackup",
    "adminmassemail",
    "adminstatsword", 
    "adminbans",
    "-".$_lang['admin.users.groups.admincontentrights'],
    "admincontent",
    "adminconfirm",
    "adminneedconfirm",
    "adminart",
    "adminallart",
    "adminchangeartauthor",
    "adminsection",
    "admincategory",
    "adminbook",
    "adminseparator",
    "admingallery",
    "adminlink",
    "adminintersection",
    "adminforum",
    "adminpoll",
    "adminpollall",
    "adminsbox",
    "adminbox",
    "admindownload",
    "adminsitemap"
    );

    $rights="";
      foreach($rights_array as $item){
      if(($id==2 and !in_array($item, $unregistered_useable)) or (mb_substr($item, 0, 1)!="-" and _userHasNotRight($item))){continue;}
        if(mb_substr($item, 0, 1)!="-"){
        $rights.="
        <tr>
        <td><strong>".$_lang['admin.users.groups.'.$item]."</strong></td>
        <td><input type='checkbox' name='$item' value='1'"._checkboxActivate($query[$item])._inputDisable($id!=1)." /></td>
        <td class='lpad'>".$_lang['admin.users.groups.'.$item.'.help']."</td>
        </tr>
        ";
        }
        else{
        $rights.="</table></fieldset><fieldset><legend>".mb_substr($item, 1)."</legend><table>";
        }
      }

  /*--- ulozeni ---*/
  if(isset($_POST['title'])){

  $title=_safeStr(_htmlStr(trim($_POST['title'])));
  if($title==""){$title=$_lang['global.novalue'];}
  if($id!=2){$icon=_safeStr(_htmlStr(trim($_POST['icon'])));}
  if($id>2){$blocked=_checkboxLoad("blocked");}
  if($id!=2){$reglist=_checkboxLoad("reglist");}

  mysql_query("UPDATE `"._mysql_prefix."-groups`SET title='".$title."' WHERE id=".$id);
  if($id!=2){mysql_query("UPDATE `"._mysql_prefix."-groups`SET icon='".$icon."' WHERE id=".$id);}

    //uroven, blokovani
    if($id>2){
      $level=intval($_POST['level']);
      if($level>_loginright_level){$level=_loginright_level-1;}
      if($level>=10000){$level=9999;}
      if($level<0){$level=0;}
      mysql_query("UPDATE `"._mysql_prefix."-groups` SET level=".$level.",blocked=".$blocked." WHERE id=".$id);
    }
  
    //prava
    if($id!=1){
      foreach($rights_array as $item){
      if(($id==2 and !in_array($item, $unregistered_useable)) or _userHasNotRight($item)){continue;}
      mysql_query("UPDATE `"._mysql_prefix."-groups` SET ".$item."="._checkboxLoad($item)." WHERE id=".$id);
      }
    }
    
    //seznam pri registraci
    if($id!=2){
    mysql_query("UPDATE `"._mysql_prefix."-groups` SET reglist=".$reglist." WHERE id=".$id);
    }

  define('_tmp_redirect', 'index.php?p=users-editgroup&id='.$id.'&saved');

  }


  /*--- vystup ---*/
  $output.=
  "
  <p class='bborder'>".$_lang['admin.users.groups.editp']."</p>
  ".(isset($_GET['saved'])?_formMessage(1, $_lang['global.saved']):'')."
  ".($systemitem?_tmp_smallNote($_lang['admin.users.groups.specialgroup.editnotice']):'')."
  <form action='index.php?p=users-editgroup&amp;id=".$id."' method='post'>
  <table>

  <tr>
  <td><strong>".$_lang['global.name']."</strong></td>
  <td><input type='text' name='title' class='inputmedium' value='".$query['title']."' /></td>
  </tr>

  <tr>
  <td class='rpad'><strong>".$_lang['admin.users.groups.level']."</strong></td>
  <td><input type='text' name='level' class='inputmedium' value='".$query['level']."'"._inputDisable(!$systemitem)." /></td>
  </tr>
  
  ".(($id!=2)?"
  <tr><td><strong>".$_lang['admin.users.groups.icon']."</strong></td><td><input type='text' name='icon' class='inputmedium' value='".$query['icon']."' /></td></tr>
  <tr><td class='rpad'><strong>".$_lang['admin.users.groups.reglist']."</strong></td><td><input type='checkbox' name='reglist' value='1'"._checkboxActivate($query['reglist'])." /></td></tr>
  ":'')."

  <tr>
  <td class='rpad'><strong>".$_lang['admin.users.groups.blocked']."</strong></td>
  <td><input type='checkbox' name='blocked' value='1'"._checkboxActivate($query['blocked'])._inputDisable($id!=1 and $id!=2)." /></td>
  </tr>

  </table><br />

  <fieldset>
  <legend>".$_lang['admin.users.groups.commonrights']."</legend>
  <table>

  ".$rights."

  
  </table></fieldset><br />


  <br />
  <input type='submit' value='".$_lang['global.save']."' />&nbsp;&nbsp;<small>".$_lang['admin.content.form.thisid']." ".$id."</small>

  </form>
  ";


}
else{
if($levelconflict==false){$output.=_formMessage(3, $_lang['global.badinput']);}
else{$output.=_formMessage(3, $_lang['global.disallowed']);}
}

?>