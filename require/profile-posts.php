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
  if(_template_autoheadings==1){$module="<h1>".$_lang['mod.profile.posts']."</h1><br />";}else{$module="";}

  //vyhledavaci pole
  
    //odkaz zpet na profil
    if($list){$module.="\n<a href='index.php?m=profile&amp;id=".$id."' class='backlink'>&lt; ".$_lang['global.return']."</a>\n";}
  
  $module.="
  <form action='index.php' method='get'>
  <input type='hidden' name='m' value='profile-posts' />
  <input type='text' name='id'".(($id!=null)?" value='".$id."'":'')." class='inputmedium' /> <input type='submit' value='".$_lang['global.open']."' />
  </form><br />
  ".$message;
  
  //tabulka
  if($list==true){
  
  $cond="author=".$query['id']." AND `type`!=4";
  $paging=_resultPaging("index.php?m=profile-posts&amp;id=".$id, 15, "posts", $cond);
  if(_pagingmode==1 or _pagingmode==2){$module.=$paging[0];}
  

  $posts=mysql_query("SELECT id,type,home,xhome,subject,text,author,time FROM `"._mysql_prefix."-posts` WHERE ".$cond." ORDER BY time DESC ".$paging[1]);
  if(mysql_num_rows($posts)!=0){
    while($post=mysql_fetch_array($posts)){
        switch($post['type']){
            case 1: case 3: $homelink=_linkRoot($post['home']); $hometitle=mysql_fetch_array(mysql_query("SELECT title FROM `"._mysql_prefix."-root` WHERE id=".$post['home'])); $hometitle=$hometitle['title']; break;
            case 2: $homelink=_linkArticle($post['home']); $hometitle=mysql_fetch_array(mysql_query("SELECT title FROM `"._mysql_prefix."-articles` WHERE id=".$post['home'])); $hometitle=$hometitle['title']; break;
            case 5: $homelink='index.php?m=topic&amp;id='.$post[(($post['xhome']=='-1')?'id':'xhome')]; if($post['xhome']=='-1'){$hometitle=$post['subject'];}else{$hometitle=mysql_fetch_array(mysql_query("SELECT subject FROM `"._mysql_prefix."-posts` WHERE id=".$post['xhome'])); $hometitle=$hometitle['subject'];} break;
        }
        $module.="<div class='post-head'><a href='".$homelink."#posts' class='post-author'>".$hometitle."</a> <span class='post-info'>("._formatTime($post['time']).")</span></div><p class='post-body'>"._parsePost($post['text'])."</p>\n";
    }
    if(_pagingmode==2 or _pagingmode==3){$module.='<br />'.$paging[0];}
  }
  else{
    $module.=$_lang['global.nokit'];
  }
  
  }

?>