<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_galimg($galerie="", $typ=1, $vyska_nahledu=null, $limit=null){
  
  //nacteni parametru
  $result="";
  $galerie=_sqlWhereColumn("home", $galerie);
  if(isset($vyska_nahledu) and $vyska_nahledu>0 and $vyska_nahledu<1024){$vyska_nahledu=intval($vyska_nahledu);}else{$vyska_nahledu=128;}
  if(isset($limit)){$limit=abs(intval($limit));}else{$limit=1;}
  
  //urceni razeni
  switch($typ){
  case 2: $razeni="RAND()"; break;
  default: $razeni="id DESC";
  }
  
  //vypis obrazku
  $rimgs=mysql_query("SELECT id,title,prev,full FROM `"._mysql_prefix."-images` WHERE ".$galerie." ORDER BY ".$razeni." LIMIT ".$limit);
  while($rimg=mysql_fetch_array($rimgs)){
    $result.=_galleryImage($rimg, "hcm".$GLOBALS['__hcm_uid'], $vyska_nahledu);
  }
  
  return $result;
  
}

?>