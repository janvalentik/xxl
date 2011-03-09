<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- priprava ---*/
$list=false;
$message="";
$id=null;

  if(isset($_GET['id'])){
  $id=_safeStr(_anchorStr($_GET['id'], false));
  $query=mysql_query("SELECT id FROM `"._mysql_prefix."-users` WHERE username='".$id."'");
    if(mysql_num_rows($query)!=0){
    $query=mysql_fetch_array($query);
    $list=true;
    }
    else{
    $message=_formMessage(2, $_lang['global.baduser']);
    }
  }

/*--- modul ---*/

  //titulek
  if(_template_autoheadings==1){$module="<h1>".$_lang['mod.profile.arts']."</h1><br />";}else{$module="";}

  //vyhledavaci pole
  
    //odkaz zpet na profil
    if($list){$module.="\n<a href='index.php?m=profile&amp;id=".$id."' class='backlink'>&lt; ".$_lang['global.return']."</a>\n";}
  
  $module.="
  <form action='index.php' method='get'>
  <input type='hidden' name='m' value='profile-arts' />
  <input type='text' name='id'".(($id!=null)?" value='".$id."'":'')." class='inputmedium' /> <input type='submit' value='".$_lang['global.open']."' />
  </form><br />
  ".$message;
  
  //tabulka
  if($list==true){
  
  $cond="author=".$query['id']." AND "._sqlArticleFilter(true);
  $paging=_resultPaging("index.php?m=profile-arts&amp;id=".$id, 15, "articles", $cond);
  if(_pagingmode==1 or _pagingmode==2){$module.=$paging[0];}
  
  $arts=mysql_query("SELECT id,title,author,perex,time,comments,public,readed FROM `"._mysql_prefix."-articles` WHERE ".$cond." ORDER BY time DESC ".$paging[1]);
  if(mysql_num_rows($arts)!=0){
    while($art=mysql_fetch_array($arts)){
      $module.=_articlePreview($art);
    }
    if(_pagingmode==2 or _pagingmode==3){$module.='<br />'.$paging[0];}
  }
  else{
    $module.=$_lang['global.nokit'];
  }
  
  }

?>