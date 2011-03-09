<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_articles($typ=1, $pocet=null, $perex=true, $info=true, $kategorie=null){
  
  //priprava
  $result="";
  $typ=intval($typ); if($typ<1 or $typ>9){$typ=1;}
  $pocet=intval($pocet); if($pocet<1){$pocet=1;}
  $perex=_boolean($perex);
  $info=_boolean($info);
  
  //limitovani na kategorie
  $rcats=_sqlArticleWhereCategories($kategorie);
  
  //priprava casti sql dotazu
  switch($typ){
  case 1: $rorder="time DESC"; $rcond=""; break;
  case 2: $rorder="readed DESC"; $rcond="readed!=0"; break;
  case 3: $rorder="ratesum/ratenum DESC"; $rcond="ratenum!=0"; break;
  case 4: $rorder="ratenum DESC"; $rcond="ratenum!=0"; break;
  case 5: $rorder="RAND()"; $rcond=""; break;
  case 6: $rorder="(SELECT time FROM `"._mysql_prefix."-iplog` WHERE type=2 AND var=`"._mysql_prefix."-articles`.id AND `"._mysql_prefix."-articles`.visible=1 AND `"._mysql_prefix."-articles`.time<=".time()." AND `"._mysql_prefix."-articles`.confirmed=1 ORDER BY id DESC LIMIT 1) DESC"; $rcond="readed!=0"; break;
  case 7: $rorder="(SELECT time FROM `"._mysql_prefix."-iplog` WHERE type=3 AND var=`"._mysql_prefix."-articles`.id AND `"._mysql_prefix."-articles`.visible=1 AND `"._mysql_prefix."-articles`.time<=".time()." AND `"._mysql_prefix."-articles`.confirmed=1 ORDER BY id DESC LIMIT 1) DESC"; $rcond="ratenum!=0"; break;
  case 8: $rorder="(SELECT time FROM `"._mysql_prefix."-posts` WHERE home=`"._mysql_prefix."-articles`.id AND type=2 ORDER BY time DESC LIMIT 1) DESC"; $rcond="(SELECT COUNT(id) FROM `"._mysql_prefix."-posts` WHERE home=`"._mysql_prefix."-articles`.id AND type=2)!=0"; break;
  case 9: $rorder="(SELECT COUNT(id) FROM `"._mysql_prefix."-posts` WHERE home=`"._mysql_prefix."-articles`.id AND type=2) DESC"; $rcond="(SELECT COUNT(id) FROM `"._mysql_prefix."-posts` WHERE home=`"._mysql_prefix."-articles`.id AND type=2)!=0"; break;
  }
  
    //pripojeni casti
    if($rcond!=""){$rcond=" AND ".$rcond;}
    $rcond=" WHERE "._sqlArticleFilter(true).$rcond;
    if($rcats!=""){$rcond.=" AND ".$rcats;}
  
  //vypis
  $query=mysql_query("SELECT id,title,perex,author,time,readed,comments FROM `"._mysql_prefix."-articles`".$rcond." ORDER BY ".$rorder." LIMIT ".$pocet);
  while($item=mysql_fetch_array($query)){
    $result.=_articlePreview($item, $info, $perex);
  }
  
  return $result;

}

?>