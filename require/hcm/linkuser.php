<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_linkuser($jmeno=""){
  $name=_safeStr(_anchorStr($jmeno, false));
  $query=mysql_query("SELECT id FROM `"._mysql_prefix."-users` WHERE username='".$name."'");
  if(mysql_num_rows($query)!=0){
    $query=mysql_fetch_array($query);
    return _linkUser($query['id']);
  }
}

?>