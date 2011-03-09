<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_countart($kategorie=null){
  if(isset($kategorie) and $kategorie!=""){$cond=" AND "._sqlArticleWhereCategories($kategorie);}else{$cond="";}
  return mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-articles` WHERE "._sqlArticleFilter().$cond), 0);
}

?>