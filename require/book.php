<?php

//kontrola jadra
if(!defined('_core')){exit;}

//titulek, obsah
$title=$query['title'];
$content="";
if(_template_autoheadings){
$content.="<h1>".$query['title']._linkRSS($id, 3)."</h1>\n"._parseHCM($query['content']);
}
else{
$content.=_linkRSS($id, 4);
  if($query['content']!=""){
  $content.=_parseHCM($query['content']);
  }
}

//prispevky
require_once(_indexroot.'require/functions-posts.php');
$content.=_postsOutput(3, $id, array($query['var2'], _publicAccess($query['var1']), $query['var3']));

?>