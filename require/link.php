<?php

//kontrola jadra
if(!defined('_core')){exit;}

//vystup
$title=$query['title']; $content="";

  //odkazani podle ID
  if(mb_substr($query['content'], 0, 1)=="*"){
    //stranka
    $lid=intval(mb_substr($query['content'], 1));
    $query['content']="";
      if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-root` WHERE id=".$lid), 0)!=0){
        $query['content']=_linkRoot($lid);
      }
    }
  else{
    //clanek
    if(mb_substr($query['content'], 0, 1)=="%"){
    $lid=intval(mb_substr($query['content'], 1));
    $query['content']="";
      if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-articles` WHERE id=".$lid), 0)!=0){
        $query['content']=_linkArticle($lid);
      }
    }
  }

  //aktivace presmerovani
  if($query['content']!=""){
  define('_tmp_redirect', $query['content']);
  }

?>