<?php

/*---- inicializace jadra ----*/

define('_indexroot', '../');
define('_administration', '1');
require(_indexroot."core.php");


/*---- vystup ----*/

$admintitle=$_lang['admin.title'];
if(isset($_GET['p'])){$getp=_anchorStr($_GET['p']);}else{$getp="index";}
$output="";

/*--- hlavicka ---*/

  /*-- vlozeni funcki administrace --*/
  require(_indexroot."admin/acore.php");

  //priprava uzivatelskeho menu
  $usermenu='<span>';
  if(_loginindicator and _loginright_administration){$usermenu.=_loginpublicname.' [<a href="'._indexroot.'index.php?m=settings">'.$_lang['usermenu.settings'].'</a>, <a href="'._indexroot.'remote/logout.php?_return=admin">'.$_lang['usermenu.logout'].'</a>]';}
  else{$usermenu.='<a href="./">'.$_lang['usermenu.guest'].'</a>';}
  $usermenu.='</span>';

  //priprava pole modulu a menu
  $menu="<div id='menu'><div id='menu-padding'>\n";
  
  if(_loginindicator and _loginright_administration){

    //pole modulu (jmeno, 0-titulek, 1-prava ke vstupu, 2-nadrazeny modul, 3-podrazene moduly, [4-vlastni titulek a zpetny odkaz?], [5-ikona napovedy v dokumentaci?])
    $modules=array(
    "index"=>           array($_lang['admin.menu.index'],             true,                                              null,                       array(), true),

    "content"=>         array($_lang['admin.menu.content'],           _loginright_admincontent,                   null,                       array("content-move","content-titles","content-convert","content-articles","content-confirm","content-movearts","content-polls","content-polls-edit","content-boxes","content-editsection","content-editcategory","content-delete","content-editintersection","content-articles-list","content-articles-edit","content-articles-delete","content-boxes-edit","content-boxes-new","content-editbook","content-editseparator","content-editlink","content-editgallery","content-manageimgs","content-artfilter"), false, "content"),
      "content-move"=>           array($_lang['admin.content.move.title'],            _loginright_admincontent,       "content",                  array()),
      "content-titles"=>         array($_lang['admin.content.titles.title'],          _loginright_admincontent,       "content",                  array()),
      "content-convert"=>           array($_lang['admin.content.convert.title'],         _loginright_admincontent,       "content",                  array()),
      "content-articles"=>       array($_lang['admin.content.articles.title'],        _loginright_adminart,           "content",                  array()),
      "content-articles-list"=>  array($_lang['admin.content.articles.list.title'],   _loginright_adminart,           "content-articles",         array()),
      "content-articles-edit"=>  array($_lang['admin.content.articles.edit.title'],   _loginright_adminart,           "content-articles",         array(), true),
      "content-articles-delete"=>array($_lang['admin.content.articles.delete.title'], _loginright_adminart,           "content-articles",         array(), true),
      "content-confirm"=>        array($_lang['admin.content.confirm.title'],         _loginright_adminconfirm,       "content",                  array()),
      "content-movearts"=>       array($_lang['admin.content.movearts.title'],        _loginright_admincategory,      "content",                  array()),
      "content-artfilter"=>      array($_lang['admin.content.artfilter.title'],       _loginright_admincategory,      "content",                  array()),
      "content-polls"=>          array($_lang['admin.content.polls.title'],           _loginright_adminpoll,          "content",                  array()),
      "content-polls-edit"=>     array($_lang['admin.content.polls.edit.title'],      _loginright_adminpoll,          "content-polls",            array()),
      "content-sboxes"=>         array($_lang['admin.content.sboxes.title'],          _loginright_adminsbox,          "content",                  array()),
      "content-boxes"=>          array($_lang['admin.content.boxes.title'],           _loginright_adminbox,           "content",                  array()),
     
     //!!!!!!!!!!!!!
     
      "download-manager"=>         array($_lang['xxl.admin.download.manager'],           _loginright_download,           "content",                  array()),
      "newsletter-manager"=>       array($_lang['xxl.admin.newsletter'],               _loginright_newsletter,           "content",                  array()),
      "sitemap"=>                  array($_lang['xxl.admin.sitemap'],                     _loginright_sitemap,           "content",                  array()),
      "statsword"=>               array($_lang['xxl.statsword'],                         _loginright_statsword,          "content",                  array()),
  
     
      
      "content-boxes-edit"=>     array($_lang['admin.content.boxes.edit.title'],      _loginright_adminbox,           "content-boxes",            array()),
      "content-boxes-new"=>      array($_lang['admin.content.boxes.new.title'],       _loginright_adminbox,           "content-boxes",            array(), true),

      "content-delete"=>           array($_lang['admin.content.delete.title'],          true,                           "content",                  array()),
      "content-editsection"=>      array($_lang['admin.content.editsection.title'],     _loginright_adminsection,       "content",                  array(), false, "editroot"),
      "content-editcategory"=>     array($_lang['admin.content.editcategory.title'],    _loginright_admincategory,      "content",                  array(), false, "editroot"),
      "content-editintersection"=> array($_lang['admin.content.editintersection.title'],_loginright_adminintersection,  "content",                  array(), false, "editroot"),
      "content-editbook"=>         array($_lang['admin.content.editbook.title'],        _loginright_adminbook,          "content",                  array(), false, "editroot"),
      "content-editseparator"=>    array($_lang['admin.content.editseparator.title'],   _loginright_adminseparator,     "content",                  array(), false, "editroot"),
      "content-editlink"=>         array($_lang['admin.content.editlink.title'],        _loginright_adminlink,          "content",                  array(), false, "editroot"),
      "content-editgallery"=>      array($_lang['admin.content.editgallery.title'],     _loginright_admingallery,       "content",                  array(), false, "editroot"),
      "content-editforum"=>        array($_lang['admin.content.editforum.title'],       _loginright_adminforum,         "content",                  array(), false, "editroot"),
      "content-manageimgs"=>      array($_lang['admin.content.manageimgs.title'],     _loginright_admingallery,       "content",                  array(), true),




    "users"=>           array($_lang['admin.menu.users'],               _loginright_adminusers or _loginright_admingroups,        null,                       array("users-editgroup","users-delgroup","users-edit","users-delete","users-list","users-move")),
      "users-editgroup"=> array($_lang['admin.users.groups.edittitle'], _loginright_admingroups,            "users",                    array(), false, "editgroup"),
      "users-delgroup"=>  array($_lang['admin.users.groups.deltitle'],  _loginright_admingroups,            "users",                    array()),
      "users-edit"=>      array($_lang['admin.users.edit.title'],       _loginright_adminusers,             "users",                    array()),
      "users-delete"=>    array($_lang['admin.users.deleteuser'],       _loginright_adminusers,             "users",                    array()),
      "users-list"=>      array($_lang['admin.users.list'],             _loginright_adminusers,             "users",                    array()),
      "users-move"=>      array($_lang['admin.users.move'],             _loginright_adminusers,             "users",                    array()),

    "fman"=>            array($_lang['admin.menu.fman'],              _loginright_adminfman,                             null,                       array()),
    "settings"=>        array($_lang['admin.menu.settings'],          _loginright_adminsettings,                         null,                       array()),

    "other"=>           array($_lang['admin.menu.other'],             _loginright_adminbackup or _loginright_adminmassemail or _loginright_adminbans,  null,                       array("other-backup","other-massemail","other-bans","other-cleanup","other-transm")),
      "other-backup"=>       array($_lang['admin.other.backup.title'],    _loginright_adminbackup,                             "other",                     array()),
      "other-cleanup"=>      array($_lang['admin.other.cleanup.title'],   (_loginright_level==10001),                            "other",                     array()),
      "other-massemail"=>    array($_lang['admin.other.massemail.title'], _loginright_adminmassemail,                          "other",                     array()),
      "other-bans"=>         array($_lang['admin.other.bans.title'],      _loginright_adminbans,                               "other",                     array()),
      "other-transm"=>       array($_lang['admin.other.transm.title'],    (_loginid==0),                                         "other",                     array())
   
   
    );

    if(isset($modules[$getp][0])){$admintitle=$modules[$getp][0];}

    //seznam modulu s odkazem v menu
    $menuitems=array(
    "index",
    "content",
    "users",
    "fman",
    "settings",
    "other"
    );


    //kod menu
    
    foreach($menuitems as $item){
    if($modules[$item][1]==true){
      if($getp==$item or in_array($getp, $modules[$item][3])){$class=" class='act'";}else{$class="";}
      $menu.="<a href='index.php?p=".$item."'$class>".$modules[$item][0]."</a> \n";
    }
    }
    $menu=trim($menu);

  }
  else{
    $menu.="<a href='./'>".$_lang['global.login']."</a>\n";
  }
  
  //dokonceni menu
  $menu.="</div></div>\n<hr class='hidden' />";

/*--- xhtml hlavicka ---*/

$output.='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="generator" content="SunLight CMS '._systemversion.'" />
<link href="remote/style.php?s='._adminscheme.'" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="remote/javascript.php"></script>
<script type="text/javascript" src="'._indexroot.'remote/javascript.php"></script>
<script type="text/javascript" src="'._indexroot.'libs/jquery/jquery-1.4.2.min.js"></script>
'.(_lightbox?'<script type="text/javascript" src="'._indexroot.'libs/colorbox/jquery.colorbox.js"></script>
<script type="text/javascript" src="'._indexroot.'libs/tr-hover/tr-hover.js"></script>
<script type="text/javascript" src="'._indexroot.'remote/colorbox.php"></script>
<link href="'._indexroot.'libs/colorbox/colorbox.css" rel="stylesheet" type="text/css" />':'').(_codemirror ? '
<link rel="stylesheet" href="modules/codemirror/editor.css" type="text/css" media="screen" />
<script type="text/javascript" src="modules/codemirror/codemirror.js"></script>' : '').'
<title>'._title.' - '.$_lang['admin.title'].' &gt; '.$admintitle.'</title>
</head>

<body>';

/*--- hlavicka a menu ---*/
$output.='
<div id="header">'.$usermenu._title.' - '.$_lang['admin.title'].'</div>
<hr class="hidden" />

'.$menu;

$output.="\n\n<div id='content'>\n";

/*--- zprava o odeprenem pristupu ---*/
if(_loginindicator and _loginright_administration!=1){
$output.="<h1>".$_lang['global.error']."</h1>"._formMessage(3, $_lang['admin.denied']);
}

/*--- prihlaseni nebo obsah ---*/
if(_loginindicator and _loginright_administration){

  //vlozeni modulu
  if(array_key_exists($getp, $modules)){
    if($modules[$getp][1]==true and ($modules[$getp][2]==null or $modules[$modules[$getp][2]][1]==true)){
      /*zpetny odkaz*/  if($modules[$getp][2]!=null and !(isset($modules[$getp][4]) and $modules[$getp][4]==true)){$output.="<a href='index.php?p=".$modules[$getp][2]."' class='backlink'>&lt; ".$_lang['global.return']."</a>";}
      /*titulek*/       if(!(isset($modules[$getp][4]) and $modules[$getp][4]==true)){$output.="<h1>".$modules[$getp][0]; if(isset($modules[$getp][5])){$output.=" "._tmp_docsIcon($modules[$getp][5], true);} $output.="</h1>";}
      $file="require/".$getp.".php";
      if(@file_exists($file)){require($file);}
      else{$output.=_formMessage(2, $_lang['admin.moduleunavailable']);}
    }
    else{
      $output.="<h1>".$_lang['global.error']."</h1>"._formMessage(3, $_lang['global.accessdenied']);
    }
  }
  else{
  $output.="<h1>".$_lang['global.error404.title']."</h1>"._formMessage(2, $_lang['global.error404']);
  }

}
else{
$login=_uniForm("login", array('return'=>'admin', 'returnpath'=>null));
$output.=$login[0];
}

/*--- paticka, vypis vystupu ---*/
$output.='
</div>

<hr class="hidden" />
<div id="copyright">
<div>'.((_loginindicator and _loginright_administration)?'<a href="../" target="_blank">'.$_lang['admin.link.site'].'</a> &nbsp;&bull;&nbsp; <a href="./" target="_blank">'.$_lang['admin.link.newwin'].'</a>':'<a href="../">&lt; '.$_lang['admin.link.home'].'</a>').'</div>
Copyright &copy; '.date("Y").' <a href="http://sunlight.shira.cz/" target="_blank">SunLight CMS</a> by <a href="http://shira.cz/" target="_blank">ShiraNai7</a>
</div>


</body>
</html>';

  if(!defined('_tmp_redirect')){echo $output;}
  else{header("location: "._tmp_redirect); exit;}

?>