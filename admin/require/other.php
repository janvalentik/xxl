<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- vystup ---*/
$output.="
<p class='bborder'>".$_lang['admin.other.p']."</p>
<ul class='ex-list'>
";

  //pole polozek (nazev, pravo(null=loginright), remote skript?)
  $items=array(
  array("backup",     null,                       false),
  array("bans",       null,                       false),
  array("massemail",  null,                       false),
  array("cleanup",    _loginright_level==10001,   false),
  array("sqlex",      _loginright_level==10001,   true),
  array("php",        _loginright_level==10001,   true),
  array("transm",     _loginid==0,                false)
  );

  //vypis
  foreach($items as $item){
    if($item[1]==true or ($item[1]===null and constant('_loginright_admin'.$item[0]))){
      $output.="<li><a href='".($item[2]?"remote/".$item[0].".php' target='_blank":"index.php?p=other-".$item[0])."'>".$_lang['admin.other.'.$item[0].'.title']."</a></li>\n";
    }
    
  }

$output.="</ul>";

?>