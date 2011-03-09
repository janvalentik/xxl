<?php

//kontrola jadra
if(!defined('_core')){exit;}

//titulek, obsah
$title=$query['title'];
$content="";
if($query['var2']==1){$content.="<h1>".$title."</h1>";}
$content.=_parseHCM($query['content']);

// komentare
if($query['var1']==1 and _comments){
  require_once(_indexroot.'require/functions-posts.php');
  $content.=_postsOutput(1, $id, $query['var3']);
}

?>