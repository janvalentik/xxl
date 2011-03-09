<?php

/*---- inicializace jadra ----*/

define('_indexroot', './');
require(_indexroot."core.php");
require(_indexroot."/require/functions-template.php");


/*---- vystup ----*/

$notpublic_form=false;
$notpublic_form_wholesite=false;
$is_homepage=false;

/*-- aplikovani nastaveni neverejnych stranek --*/
if(_publicAccess(!_notpublicsite) or (isset($_GET['m']) and in_array($_GET['m'], array("login", "reg", "lostpass")))){


  /*-- modul --*/
  if(isset($_GET['m'])){
    if(_loginindicator or !in_array($_GET['m'], array("settings", "editpost", "messages"))){
      switch($_GET['m']){


      //prihlaseni
      case "login":
      define('_indexOutput_url', "index.php?m=login");
      require(_indexroot."require/login.php");
      define('_indexOutput_content', $module);
      define('_indexOutput_title', $_lang['login.title']);
      break;


      //seznam uzivatelu
      case "ulist":
      if(_ulist){
        define('_indexOutput_url', "index.php?m=ulist");
        require(_indexroot."require/ulist.php");
        define('_indexOutput_content', $module);
        define('_indexOutput_title', $_lang['admin.users.list']);
      }
      break;

      //registrace
      case "reg":
      if(_registration and !_loginindicator){
        define('_indexOutput_url', "index.php?m=reg");
        require(_indexroot."require/reg.php");
        define('_indexOutput_content', $module);
        define('_indexOutput_title', $_lang['mod.reg']);
      }
      break;

      //ztracene heslo
      case "lostpass":
      if(_lostpass and !_loginindicator){
        define('_indexOutput_url', "index.php?m=lostpass");
        require(_indexroot."require/lostpass.php");
        define('_indexOutput_content', $module);
        define('_indexOutput_title', $_lang['mod.lostpass']);
      }
      break;

      //profil
      case "profile":
        define('_indexOutput_url', "index.php?m=profile");
        require(_indexroot."require/profile.php");
        define('_indexOutput_content', $module);
        define('_indexOutput_title', $_lang['mod.profile']);
      break;

      //clanky autora
      case "profile-arts":
        define('_indexOutput_url', "index.php?m=profile-arts");
        require(_indexroot."require/profile-arts.php");
        define('_indexOutput_content', $module);
        define('_indexOutput_title', $_lang['mod.profile.arts']);
      break;
      
      //prispevky uzivatele
      case "profile-posts":
        define('_indexOutput_url', "index.php?m=profile-posts");
        require(_indexroot."require/profile-posts.php");
        define('_indexOutput_content', $module);
        define('_indexOutput_title', $_lang['mod.profile.posts']);
      break;

      //nastaveni
      case "settings":
        define('_indexOutput_url', "index.php?m=settings");
        require(_indexroot."require/settings.php");
        define('_indexOutput_content', $module);
        define('_indexOutput_title', $_lang['mod.settings']);
      break;

      //uprava prispevku
      case "editpost":
        define('_indexOutput_url', "index.php?m=editpost");
        require(_indexroot."require/editpost.php");
        define('_indexOutput_content', $module);
        define('_indexOutput_title', $_lang['mod.editpost']);
      break;

      // uzamknuti prispevku (forum topic)
      case "locktopic":
        define('_indexOutput_url', "index.php?m=locktopic");
        require (_indexroot."require/locktopic.php");
        define('_indexOutput_content', $module);
        define('_indexOutput_title', $_lang['mod.locktopic']);
        break;

      //vzkazy
      case "messages":
      if(_messages){
        define('_indexOutput_url', "index.php?m=messages");
        require(_indexroot."require/messages.php");
        define('_indexOutput_content', $module);
        define('_indexOutput_title', $_lang['mod.messages']);
      }
      break;

      //vyhledavani
      case "search":
      if(_search){
        define('_indexOutput_url', "index.php?m=search");
        require(_indexroot."require/search.php");
        define('_indexOutput_content', $module);
        define('_indexOutput_title', $_lang['mod.search']);
      }
      break;

      //tema
      case "topic":
        //_indexOutput_url je definovano uvnitr:
        require(_indexroot."require/topic.php");
        define('_indexOutput_content', $module);
          if($forumtitle!="" and $topictitle!=""){define('_indexOutput_title', $forumtitle." "._titleseparator." ".$topictitle);}
          else{define('_indexOutput_title', $_lang['mod.topic']);}
      break;

      }
    }
    else{
      $notpublic_form=true;
    }
    define('_pagetype','system');
  }
  
  /*-- clanek --*/
  elseif(isset($_GET['a'])){
  $id=intval($_GET['a']);

    //clanek
    $query=mysql_query("SELECT * FROM `"._mysql_prefix."-articles` WHERE id=".$id);
    if(mysql_num_rows($query)!=0){

      //rozebrani dat, test pristupu
      $query=mysql_fetch_array($query);
      $access=_articleAccess($query);
      define('_indexOutput_url', _linkArticle($id));

      //vlozeni modulu
      if($access==1){
        require(_indexroot."require/article.php");
        define('_indexOutput_content', $content);
        define('_indexOutput_title', $title);
      }
      elseif($access==2){
        $notpublic_form=true;
      }

    }
    define('_pagetype','article');
  }
  
  /*-- stranka nebo hlavni strana --*/
  else{

    //nacteni ID stranky nebo hlavni strany
    $continue=true;
    if(isset($_GET['p'])){
      $id=intval($_GET['p']);
    }
    else{
    $query=mysql_query("SELECT id FROM `"._mysql_prefix."-root` WHERE intersection=-1 AND type!=4 ORDER BY ord LIMIT 1");
      if(mysql_num_rows($query)!=0){
        $query=mysql_fetch_array($query);
        $id=$query['id'];
      }
      else{
        $continue=false;
      }
    }

    //vypis stranky
    if($continue){

      $query=mysql_query("SELECT * FROM `"._mysql_prefix."-root` WHERE id=".$id);
      if(mysql_num_rows($query)!=0){

        //rozebrani dat, test pristupu
        $query=mysql_fetch_array($query);
        define('_indexOutput_url', _linkRoot($id));
        define('_indexOutput_pid', $id);

        //priprava pro vystup
        if(_publicAccess($query['public'])){

          //dedicnost nastaveni verejnosti po rozcestniku
          if($query['intersection']!=-1){
            $intersection_data=mysql_fetch_array(mysql_query("SELECT public,var2 FROM `"._mysql_prefix."-root` WHERE id=".$query['intersection']));
            $intersection_public=$intersection_data['public'];
          }
          else{
            $intersection_public=1;
          }

          //zpetny odkaz na rozcestnik
          $backlink="";
          if($query['intersection']!=-1 and $query['visible']==1 and _template_intersec_backlink and $intersection_data['var2']!=1){
            $backlink="<a href='"._linkRoot($query['intersection'])."' class='backlink'>&lt; ".$_lang['global.return']."</a>";
          }

          //vlozeni modulu
          if(_publicAccess($intersection_public)){
          
            switch($query['type']){
            case 1: require(_indexroot."require/section.php");define('_pagetype','section'); break;
            case 2: require(_indexroot."require/category.php");define('_pagetype','category'); break;
            case 3: require(_indexroot."require/book.php");define('_pagetype','book'); break;
            case 5: require(_indexroot."require/gallery.php");define('_pagetype','gallery'); break;
            case 6: require(_indexroot."require/link.php");define('_pagetype','link'); break;
            case 7: require(_indexroot."require/intersection.php");define('_pagetype','intersection'); break;
            case 8: require(_indexroot."require/forum.php");define('_pagetype','forum'); break;
            }
            
            define('_indexOutput_content', $backlink.$content);
            define('_indexOutput_title', $title);
            
          }
          else{
            $notpublic_form=true;
          }

        }
        else{
          $notpublic_form=true;
        }

      }

    }
    else{
      //neni co zobrazit
      define('_indexOutput_content', (_template_autoheadings?"<h1>".$_lang['global.error404.title']."</h1>":'')._formMessage(2, $_lang['global.nokit']));
      define('_indexOutput_title', $_lang['global.error404.title']);
    }

  }


}
else{
  $notpublic_form=true;
  $notpublic_form_wholesite=true;
}


/*-- definovani nedefinovanych konstant --*/
if(!defined('_indexOutput_pid')){define('_indexOutput_pid', -1);}
if(!defined('_indexOutput_url')){define('_indexOutput_url', _indexroot);}


/*-- nenalezeno nebo pozadovani prihlaseni pro neverejny obsah --*/
if(!defined('_indexOutput_content')){
  if(!$notpublic_form){
    define('_indexOutput_content', (_template_autoheadings?"<h1>".$_lang['global.error404.title']."</h1>":'')._formMessage(2, $_lang['global.error404']));
    define('_indexOutput_title', $_lang['global.error404.title']);
  }
  else{
    $form=_uniForm("notpublic", array($notpublic_form_wholesite));
    define('_indexOutput_content', $form[0]);
    define('_indexOutput_title', $form[1]);
  }
}


/*-- vlozeni sablony motivu nebo presmerovani --*/
if(!defined('_tmp_redirect')){
if (isset($_POST['template'])) {
$_SESSION['jv_template']=_safeStr(_htmlStr(trim($_POST['template'])));
}
if (isset($_SESSION['jv_template'])) {
$usertemplate=$_SESSION['jv_template'];
}
if(!@file_exists(_indexroot."templates/".$usertemplate."/template.php") or !@file_exists(_indexroot."templates/".$usertemplate."/config.php")){$usertemplate=_template;}
require(_indexroot."templates/".$usertemplate."/template.php");
}
else{
header("HTTP/1.1 301 Moved Permanently");
header("Location: "._tmp_redirect); exit;
}


?>