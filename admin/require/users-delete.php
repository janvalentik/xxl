<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- pripava promennych ---*/
$levelconflict=false;

  //id
  $continue=false;
  if(isset($_GET['id'])){
    $id=_safeStr(_anchorStr($_GET['id'], false));
    $query=mysql_query("SELECT id FROM `"._mysql_prefix."-users` WHERE username='".$id."'");
      if(mysql_num_rows($query)!=0){
        $query=mysql_fetch_array($query);
        if(_levelCheck($query['id'])){$continue=true;}else{$continue=false; $levelconflict=true;}
        $id=$query['id'];
      }
  }


if($continue){

/*--- odstraneni ---*/
if($query['id']!=0 and $query['id']!=_loginid){
  _deleteUser($id);
  $output.=_formMessage(1, $_lang['global.done']);
}
else{
  if($query['id']==0){$output.=_formMessage(2, $_lang['global.rootnote']);}
  else{$output.=_formMessage(2, $_lang['admin.users.deleteuser.selfnote']);}
}



}
else{
if($levelconflict==false){$output.=_formMessage(3, $_lang['global.baduser']);}
else{$output.=_formMessage(3, $_lang['global.disallowed']);}
}

?>