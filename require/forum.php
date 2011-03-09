<?php

//kontrola jadra
if(!defined('_core')){exit;}

//titulek, obsah
$title=$query['title'];
$content="";
if(_template_autoheadings){
$content.="<h1>".$query['title']._linkRSS($id, 5)."</h1>\n"._parseHCM($query['content']);
}
else{
$content.=_linkRSS($id, 5);
  if($query['content']!=""){
  $content.=_parseHCM($query['content']);
  }
}

//temata
require_once(_indexroot.'require/functions-posts.php');
$content.=_postsOutput(5, $id, array($query['var1'], _publicAccess($query['var3']), $query['var2']));

?>