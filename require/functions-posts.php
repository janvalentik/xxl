<?php

/*---- kontrola jadra ----*/
if(!defined('_core')){exit;}






/*---- vratit vypis prispevku / komentaru ----*/

function _postsOutput($type, $home, $vars, $force_locked=false){
if (_fcborsystemcomments==1) {
  global $_lang;

    /*--- typ ---*/
    $desc="DESC ";
    $ordercol = 'id';
    $countcond="type=".$type." AND xhome=-1 AND home=".$home;
    switch($type){

      //komentare v sekci
      case 1:
      $posttype=1;
      $xhome=-1;
      $subclass="comments";
      $title=$_lang['posts.comments'];
      $addlink=$_lang['posts.addcomment'];
      $nopostsmessage=$_lang['posts.nocomments'];
      $postsperpage=_commentsperpage;
      $canpost=_loginright_postcomments;
      $locked=_boolean($vars);
      $replynote=true;
      break;

      //komentare u clanku
      case 2:
      $posttype=2;
      $xhome=-1;
      $subclass="comments";
      $title=$_lang['posts.comments'];
      $addlink=$_lang['posts.addcomment'];
      $nopostsmessage=$_lang['posts.nocomments'];
      $postsperpage=_commentsperpage;
      $canpost=_loginright_postcomments;
      $locked=_boolean($vars);
      $replynote=true;
      break;

      //prispevky v knize
      case 3:
      $posttype=3;
      $xhome=-1;
      $subclass="book";
      $title=null;
      $addlink=$_lang['posts.addpost'];
      $nopostsmessage=$_lang['posts.noposts'];
      $postsperpage=$vars[0];
      $canpost=$vars[1];
      $locked=_boolean($vars[2]);
      $replynote=true;
      break;

      //temata ve foru
      case 5:
      $posttype=5;
      $xhome=-1;
      $subclass="book";
      $title=null;
      $addlink=$_lang['posts.addtopic'];
      $nopostsmessage=$_lang['posts.notopics'];
      $postsperpage=$vars[0];
      $canpost=$vars[1];
      $locked=_boolean($vars[2]);
      $replynote=true;
      $ordercol = 'bumptime';
      break;

      //odpovedi v tematu
      case 6:
      $posttype=5;
      $xhome=$vars[3];
      $subclass="book";
      $title=null;
      $addlink=$_lang['posts.addanswer'];
      $nopostsmessage=$_lang['posts.noanswers'];
      $postsperpage=$vars[0];
      $canpost=$vars[1];
      $locked=_boolean($vars[2]);
      $replynote=false;
      $desc="";
      $countcond="type=5 AND xhome=".$xhome." AND home=".$home;
      break;

      //komentare v galerii
      case 7:
      $posttype=7;
      $xhome=-1;
      $subclass="comments";
      $title=$_lang['posts.comments'];
      $addlink=$_lang['posts.addcomment'];
      $nopostsmessage=$_lang['posts.nocomments'];
      $postsperpage=_commentsperpage;
      $canpost=_loginright_postcomments;
      $locked=_boolean($vars);
      $replynote=true;
      break;

    }

    if($force_locked) $locked = true;

    /*--- vystup ---*/
    $output="
    <div class='anchor'><a name='posts'></a></div>
    <div class='posts-".$subclass."'>
    ";

    if($title!=null){$output.="<h2>".$title._linkRss($home, $posttype)."</h2>\n";}

    $output.="<div class='posts-form'>\n";

    /*--- priprava strankovani ---*/
    $paging=_resultPaging(_indexOutput_url, $postsperpage, "posts", $countcond, "#posts");

    /*--- formular nebo odkaz na pridani ---*/
    if(!$locked and (isset($_GET['addpost']) or isset($_GET['replyto']))){

      //nacteni cisla prispevku pro odpoved
      if($xhome==-1){
        if(isset($_GET['replyto']) and $_GET['replyto']!=-1){$reply=intval($_GET['replyto']); if($replynote){$output.="<p>".$_lang['posts.replynote']." (<a href='"._indexOutput_url."#posts'>".$_lang['global.cancel']."</a>).</p>";}}
        else{$reply=-1;}
      }
      else{
        $reply=$xhome;
      }

      //formular nebo prihlaseni
      if($canpost){
        $form=_uniForm("postform", array('posttype'=>$type, 'posttarget'=>$home, 'xhome'=>$reply));
        $output.=$form[0];
      }
      else{
        $loginform=_uniForm("login", array('return'=>-1, 'returnpath'=>_indexOutput_url), true);
        $output.="<p>".$_lang['posts.loginrequired']."</p>".$loginform[0];
      }

    }
    else{
      if(!$locked){$output.="<a href='"._addGetToLink(_indexOutput_url, "addpost&amp;page=".$paging[2])."#posts'><strong>".$addlink." &gt;</strong></a>";}
      else{$output.="<img src='"._templateImage("icons/lock.png")."' alt='stop' class='icon' /> <strong>".$_lang['posts.locked'.(($type==5)?'3':'')]."</strong>";}
    }

    $output.="</div>";

      //zprava
      if(isset($_GET['r'])){
        switch($_GET['r']){
        case 0: $output.=_formMessage(2, $_lang['posts.failed']); break;
        case 1: $output.=_formMessage(1, $_lang[(($type!=5)?'posts.added':'posts.topicadded')]); break;
        case 2: $output.=_formMessage(2, str_replace("*postsendexpire*", _postsendexpire, $_lang['misc.requestlimit'])); break;
        }
      }

    $output.="\n<div class='hr'><hr /></div>\n\n";

    /*--- vypis ---*/
    if(_pagingmode==1 or _pagingmode==2){$output.=$paging[0];}

    $query=mysql_query("SELECT ".(($type!=5)?"*":"id,author,guest,subject,time,ip,locked,bumptime")." FROM `"._mysql_prefix."-posts` WHERE type=".$posttype." AND xhome=".$xhome." AND home=".$home." ORDER BY ".$ordercol.' '.$desc.$paging[1]);
    if(mysql_num_rows($query)!=0){

      //vypis prispevku nebo temat
      if($type!=5){
        while($item=mysql_fetch_array($query)){

        //nacteni autora
        if(_uploadavatar){

        $avatar_path=_indexroot."avatars/".$item['author'].".jpg";
        if(!@file_exists($avatar_path)){$avatar=_templateImage("system/no-avatar.gif");}
        else{$avatar=$avatar_path;}
        }

        else{
        $avt=mysql_fetch_array(mysql_query("SELECT avatar FROM `"._mysql_prefix."-users` where id=".$item['author']));
        if($avt['avatar']==""){$avatar=_templateImage("system/no-avatar.gif");}else{$avatar=$avt['avatar'];}
        }
        if($item['guest']==""){$author=_linkUser($item['author'], "post-author");}
        else{$author="<span class='post-author-guest' title='".$item['ip']."'>".$item['guest']."</span>";}

          //nacteni autora
          if($item['guest']==""){$author=_linkUser($item['author'], "post-author");}
          else{$author="<span class='post-author-guest' title='".$item['ip']."'>".$item['guest']."</span>";}

          //odkazy pro spravu
          $post_access=_postAccess($item);
          if($type!=6 or $post_access){
            $actlinks=" <span class='post-actions'>";
              if($type!=6){$actlinks.="<a href='"._addGetToLink(_indexOutput_url, "replyto=".$item['id'])."#posts'>".$_lang['posts.reply']."</a>";}
              if($post_access){$actlinks.=(($type!=6)?" ":'')."<a href='index.php?m=editpost&amp;id=".$item['id']."'>".$_lang['global.edit']."</a>";}
            $actlinks.="</span>";
          }
          else{
            $actlinks="";
          }

          //prispevek

         $output.="<div class='post-box'><div class='post-head'>";
         // Zapínání a vypínání avatarů
         if (_avatars){
         $output.="<img src='".$avatar."' class='post-avatar' alt='avatar' />";
         }
         //Konec
          $output.="".$author.",";
          if($type!=6){$output.="<span class='post-subject'>".$item['subject']."</span> ";}
          $output.="<span class='post-info'>("._formatTime($item['time']).")</span>".$actlinks."";

         if (_postlinks){
          $output.="<span class='post-actions'><a name='".$item['id']."'></a>
          <a href='#".$item['id']."'>".$_lang['xxl.post.link']."</a></span>";
          }
          $output.="</div><div class='post-body'>"._parsePost($item['text'])."</div></div>\n";

            //odpovedi
            if($type!=6){
              $answers=mysql_query("SELECT * FROM `"._mysql_prefix."-posts` WHERE type=".$type." AND home=".$home." AND xhome=".$item['id']." ORDER BY id");
              while($answer=mysql_fetch_array($answers)){

              //avatarfce
              if(_uploadavatar){
                $avatar_path=_indexroot."avatars/".$answer['author'].".jpg";
                if(!@file_exists($avatar_path)){$avatar=_templateImage("system/no-avatar.gif");}
              else{$avatar=$avatar_path;}
              }
              //Query
              else{
                $avt_person=mysql_fetch_array(mysql_query("SELECT avatar FROM `"._mysql_prefix."-users` where id=".$answer['author']));
                if($avt_person['avatar']==""){$avatar_one=_templateImage("system/no-avatar.gif");}else{$avatar=$avt_person['avatar'];}
              }

                  //jmeno autora
                  if($answer['guest']==""){$author=_linkUser($answer['author'], "post-author");}
                  else{$author="<span class='post-author-guest' title='".$answer['ip']."'>".$answer['guest']."</span>";}

                  //odkazy pro spravu
                  if(_postAccess($answer)){$actlinks=" <span class='post-actions'><a href='index.php?m=editpost&amp;id=".$answer['id']."'>".$_lang['global.edit']."</a></span>";}
                  else{$actlinks="";}

              $output.="<div class='post-answer'><div class='post-head'>";
              // Zapínání a vypínání avatarů
              if (_avatars){
              $output.="<img src='".$avatar."' class='post-avatar' alt='avatar' />";
               }
             //Konec
             $output.="".$author." ".$_lang['posts.replied']." <span class='post-info'>("._formatTime($answer['time']).")</span>".$actlinks."";

          if (_postlinks){
          $output.="<span class='post-actions'><a name='".$item['id']."'></a>
          <a href='#".$item['id']."'>".$_lang['xxl.post.link']."</a></span>";
          }

           $output.="</div><div class='post-body'>"._parsePost($answer['text'])."</div></div>\n";
              }
            }

        }
      if(_pagingmode==2 or _pagingmode==3){$output.="<br />".$paging[0];}
      }
      else{

        //tabulka s tematy
        $output.="\n<table class='widetable2'>\n<tr><td><strong>".$_lang['posts.topic']."</strong></td><td><strong>".$_lang['article.author']."</strong></td><td><strong>".$_lang['global.posttime']."</strong></td><td><strong>".$_lang['global.lastanswer']."</strong></td><td><strong>".$_lang['global.answersnum']."</strong></td></tr>\n";
          while($item=mysql_fetch_array($query)){

            //nacteni autora
            if($item['guest']==""){$author=_linkUser($item['author'], "post-author", false, false, 16);}
            else{$author="<span class='post-author-guest' title='".$item['ip']."'>"._cutStr($item['guest'], 16)."</span>";}


            //nacteni jmena autora posledniho prispevku
            $lastpost=mysql_query("SELECT author,guest FROM `"._mysql_prefix."-posts` WHERE xhome=".$item['id']." ORDER BY id DESC LIMIT 1");
            if(mysql_num_rows($lastpost)!=0){
              $lastpost=mysql_fetch_array($lastpost);
                if($lastpost['author']!=-1){$lastpost=_linkUser($lastpost['author'], "post-author", false, false, 16);}
                else{$lastpost="<span class='post-author-guest'>"._cutStr($lastpost['guest'], 16)."</span>";}
            }
            else{
              $lastpost="-";
            }

          $output.="<tr".($item['locked'] ? ' class="topic-locked"' : '')."><td>".($item['locked'] ? '<img src="'._templateImage('icons/lock.png').'" class="icon" alt="locked" /> ' : '')."<a href='index.php?m=topic&amp;id=".$item['id']."'>".$item['subject']."</a></td><td>".$author."</td><td>"._formatTime($item['time'])."</td><td>".$lastpost."</td><td>".mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-posts` WHERE type=5 AND xhome=".$item['id']), 0)."</td></tr>\n";
          }
        $output.="</table><br />\n\n";
        if(_pagingmode==2 or _pagingmode==3){$output.=$paging[0]."<br />";}

        //posledni odpovedi
        $output.="\n<div class='hr'><hr /></div><br />\n<h3>".$_lang['posts.forum.lastact']."</h3>\n";
        $query=mysql_query("SELECT xhome,author,guest,time FROM `"._mysql_prefix."-posts` WHERE type=5 AND home=".$home." AND xhome!=-1 ORDER BY id DESC LIMIT "._extratopicslimit);
          if(mysql_num_rows($query)!=0){
          $output.="<ul>\n";
            while($item=mysql_fetch_array($query)){
              $topic=mysql_fetch_array(mysql_query("SELECT id,subject FROM `"._mysql_prefix."-posts` WHERE id=".$item['xhome']));
              if($item['guest']==""){$author=_linkUser($item['author']);}else{$author="<span class='post-author-guest'>".$item['guest']."</span>";}
              $output.="<li><a href='index.php?m=topic&amp;id=".$topic['id']."'>".$topic['subject']."</a>&nbsp;&nbsp;<small>(".$_lang['global.postauthor']." ".$author." "._formatTime($item['time']).")</small></li>\n";
            }
          $output.="</ul>\n\n";
          }
          else{
            $output.="<p>".$_lang['global.nokit']."</p>";
          }

      }

    }
    else{
    $output.="<p>".$nopostsmessage."</p>";
    }
  $output.="</div>";
}
else{
  $output="<div id=\"fb-root\" style=\"width:100%\"></div>
    <script>
    window.fbAsyncInit = function() {
      FB.init({appId: '".md5(_url)."_".$home."', status: true, cookie: true,
               xfbml: true});
    };
    (function() {
      var e = document.createElement('script'); e.async = true;
      e.src = document.location.protocol +
        '//connect.facebook.net/cs_CZ/all.js';
      document.getElementById('fb-root').appendChild(e);
    }());
    </script>
    <fb:comments xid=\"".md5(_url)."_".$home."\" numposts=\"10\" />";
}

return $output;
}

?>