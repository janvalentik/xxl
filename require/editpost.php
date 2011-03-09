<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- pripava promennych ---*/
$message="";
$continue=false;
$scriptbreak=false;
$backlink=_indexroot;
if(isset($_GET['id'])){
$id=intval($_GET['id']);
$query=mysql_query("SELECT * FROM `"._mysql_prefix."-posts` WHERE id=".$id);
  if(mysql_num_rows($query)!=0){
  $query=mysql_fetch_array($query);
    if(_postAccess($query)){
    $continue=true;

      $nobbcode=false;
      switch($query['type']){
      case 1: $backlink=_addGetToLink(_linkRoot($query['home']), "page="._resultPagingGetItemPage(_commentsperpage, "posts", "id>".$query['id']." AND type=1 AND xhome=-1 AND home=".$query['home']))."#posts"; break;
      case 2: $backlink=_addGetToLink(_linkArticle($query['home']), "page="._resultPagingGetItemPage(_commentsperpage, "posts", "id>".$query['id']." AND type=2 AND xhome=-1 AND home=".$query['home']))."#posts"; break;
      case 3: $postsperpage=mysql_fetch_array(mysql_query("SELECT var2 FROM `"._mysql_prefix."-root` WHERE id=".$query['home'])); $backlink=_addGetToLink(_linkRoot($query['home']), "page="._resultPagingGetItemPage($postsperpage['var2'], "posts", "id>".$query['id']." AND type=3 AND xhome=-1 AND home=".$query['home']))."#posts"; break;
      case 4: $nobbcode=true; break;

      case 5:
      if($query['xhome']==-1){
        if(!_checkboxLoad("delete")){$backlink="index.php?m=topic&amp;id=".$query['id'];}
        else{$backlink=_linkRoot($query['home']);}
      }
      else{
        $backlink=_addGetToLink("index.php?m=topic&amp;id=".$query['xhome'], "page="._resultPagingGetItemPage(_commentsperpage, "posts", "id<".$query['id']." AND type=5 AND xhome=".$query['xhome']." AND home=".$query['home']))."#posts";
      }
      break;

      }
    
    }
  }
}

/*--- ulozeni ---*/
if(isset($_POST['text']) and $continue){

if(!_checkboxLoad("delete")){

/*- uprava -*/

  //nacteni promennych

    //jmeno hosta
    if($query['guest']!=""){
      $guest=$_POST['guest'];
      if(mb_strlen($guest)>24){$guest=mb_substr($guest, 0, 24);}
      $guest=_anchorStr($guest, false);
    }
    else{
      $guest="";
    }

  $text=_safeStr(_htmlStr(_wsTrim(_cutStr($_POST['text'], 3072, false))));
  if($query['xhome']==-1 and $query['type']!=4){$subject=_safeStr(_htmlStr(_wsTrim(_cutStr($_POST['subject'], 22, false))));}else{$subject="";}

    //vyplneni prazdnych poli
    if($subject=="" and $query['xhome']==-1 and $query['type']!=4){$subject="-";}
    if($guest==null and $query['guest']!=""){$guest=$_lang['posts.anonym'];}

  //ulozeni
  if($text!=""){
  if($guest!=null){mysql_query("UPDATE `"._mysql_prefix."-posts` SET guest='".$guest."' WHERE id=".$id);}
  mysql_query("UPDATE `"._mysql_prefix."-posts` SET text='".$text."',subject='".$subject."' WHERE id=".$id);
  define('_tmp_redirect', 'index.php?m=editpost&id='.$id.'&saved');
  $scriptbreak=true; $continue=false;
  }
  else{
  $message=_formMessage(2, $_lang['mod.editpost.failed']);
  }
    
}
else{

  /*- odstraneni -*/
  mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE id=".$id);
  if($query['xhome']==-1){mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE xhome=".$id." AND home=".$query['home']." AND type=".$query['type']);}
  $message=_formMessage(1, $_lang['mod.editpost.deleted']);
  $scriptbreak=true; $continue=false;

}

}

/*--- vystup ---*/

  //titulek, zprava
  if(_template_autoheadings==1){$module="<h1>".$_lang['mod.editpost']."</h1><div class='hr'><hr /></div>";}else{$module="";}
  if(isset($_GET['saved']) and $message==""){$message=_formMessage(1, $_lang['global.saved']);}
  $module.=$message;

  //formular
  if($continue){

  //pole
  $inputs=array();
  $module.=_jsLimitLength(3072, "postform", "text");
    if($query['guest']!=""){$inputs[]=array($_lang['posts.guestname'], "<input type='text' name='guest' class='inputsmall' value='".$query['guest']."' />");}
    if($query['xhome']==-1 and $query['type']!=4){$inputs[]=array($_lang[(($query['type']!=5)?'posts.subject':'posts.topic')], "<input type='text' name='subject' class='inputsmall' maxlength='22' value='".$query['subject']."' />");}
    $inputs[]=array($_lang['posts.text'], "<textarea name='text' class='areamedium' rows='5' cols='33'>".$query['text']."</textarea>", true);

  //formoutput
  $module.=_formOutput('postform', 'index.php?m=editpost&amp;id='.$id, $inputs, null, $_lang['global.save'], _getPostformControls("postform", "text", $nobbcode)."<br /><br /><label><input type='checkbox' name='delete' value='1' /> ".$_lang['mod.editpost.delete']."</label>");

  }
  else{
    /*neplatny vstup*/ if(!$scriptbreak){$module.=_formMessage(3, $_lang['global.badinput']);}
  }

  //zpetny odkaz
  $module.="<p><a href='".$backlink."'>&lt; ".$_lang['global.return']."</a></p>";

?>