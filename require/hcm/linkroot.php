<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_linkroot($id=null, $text=null, $nove_okno=false){
  $id=intval($id);
  $query=mysql_query("SELECT title FROM `"._mysql_prefix."-root` WHERE id=".$id);
  if(isset($nove_okno) and _boolean($nove_okno)){$target=" target='_blank'";}else{$target="";}
  if(mysql_num_rows($query)!=0){
    $query=mysql_fetch_array($query);
    if(isset($text) and $text!=""){$query['title']=$text;}
    return "<a href='"._linkRoot($id)."'".$target.">".$query['title']."</a>";
  }
}

?>