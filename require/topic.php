<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- priprava ---*/
$topictitle="";
$forumtitle="";
$module="";
$continue=false;

  if(isset($_GET['id'])){
    $id=intval($_GET['id']);
    $query=mysql_query("SELECT * FROM `"._mysql_prefix."-posts` WHERE id=".$id." AND type=5 AND xhome=-1");
      if(mysql_num_rows($query)!=0){
        $query=mysql_fetch_array($query);
        $topictitle=$query['subject'];
        $homedata=mysql_fetch_array(mysql_query("SELECT id,title,public,var1,var2,var3 FROM `"._mysql_prefix."-root` WHERE id=".$query['home']));
        $forumtitle=$homedata['title'];
        $continue=true;
        if(_publicAccess($homedata['public'])){$need2login=false;}
        else{$need2login=true;}
      }
  }

/*--- vystup ---*/
if($continue){
define('_indexOutput_url', "index.php?m=topic&amp;id=".$id);

if(!$need2login){



    
//zpetny odkaz, titulek
$module="<a href='"._addGetToLink(_linkRoot($homedata['id']), "page="._resultPagingGetItemPage($homedata['var1'], "posts", "bumptime>".$query['bumptime']." AND xhome=-1 AND type=5 AND home=".$homedata['id']))."' class='backlink'>&lt; ".$_lang['global.return']."</a>\n";
if(_template_autoheadings){$module.="<h1>".$homedata['title']."</h1>\n<div class='hr'><hr /></div>\n";}
if(_postAccess($query)){$editlink=" <a href='index.php?m=editpost&amp;id=".$id."'><img src='"._templateImage("icons/edit.gif")."' alt='edit' class='icon' /></a>".(_loginright_locktopics ? "&nbsp;<a href='index.php?m=locktopic&amp;id=".$id."'><img src='"._templateImage("icons/".(($query['locked'] == 1) ? 'un' : '')."lock.png")."' alt='lock' class='icon' /></a>": '');}else{$editlink="";}
if($query['guest']==""){$author=_linkUser($query['author'], "post-author");}else{$author="<span class='post-author-guest' title='".$query['ip']."'>".$query['guest']."</span>";}
$module.="
<h2>".$_lang['posts.topic'].": ".$query['subject']._linkRSS($id, 6).$editlink."</h2>
<p><small>".$_lang['global.postauthor']." ".$author." "._formatTime($query['time'])."</small></p>";


//Avatar - existence
if(_uploadavatar){
$avatar_path=_indexroot."avatars/".$query['author'].".jpg";
if(!@file_exists($avatar_path)){$avatar_one=_templateImage("system/no-avatar.gif");}
else{$avatar_one=$avatar_path;}
}
//Query
else{
$avt_person=mysql_fetch_array(mysql_query("SELECT avatar FROM `"._mysql_prefix."-users` where id=".$query['author']));
if($avt_person['avatar']==""){$avatar_one=_templateImage("system/no-avatar.gif");}else{$avatar=$avt_person['avatar'];}
}

if (_avatars){
$module.="<img src='".$avatar_one."' class='post-avatar' alt='avatar' />";
}

$module.="<p>"._parsePost($query['text'])."</p><br clear='all' />";

//odpovedi
require_once(_indexroot.'require/functions-posts.php');
$module.=_postsOutput(6, $homedata['id'], array(_commentsperpage, _publicAccess($homedata['var3']), $homedata['var2'], $id),($query['locked'] == 1));

}
else{

$form=_uniForm("notpublic");
$module.=$form[0];

}


}
else{
define('_indexOutput_url', "index.php?m=topic");
if(_template_autoheadings){$module.="<h1>".$_lang['global.error404.title']."</h1>\n";}
$module.=_formMessage(2, $_lang['posts.topic.notfound']);
}

?>