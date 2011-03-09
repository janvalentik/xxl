<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_gallery2($cesta="", $strankovani=null){

  //priprava
  $result="";
  $cesta=_indexroot.$cesta;
  $cesta_noroot=$cesta;
  if(mb_substr($cesta, -1, 1)!="/"){$cesta.="/";}
  if(isset($strankovani) and $strankovani>0){
    $strankovat=true;
    $strankovani=intval($strankovani);
    if($strankovani<=0){$strankovani=1;}
  } else{$strankovat=false;}
  
  if(@file_exists($cesta) and @is_dir($cesta) and @file_exists($cesta."prev/") and @is_dir($cesta."prev/") and @file_exists($cesta."full/") and @is_dir($cesta."full/")){
  $handle=@opendir($cesta."prev/");
  
    //nacteni polozek
    $items=array();
    while($item=@readdir($handle)){
      $ext=pathinfo($item);
      if(isset($ext['extension'])){$ext=mb_strtolower($ext['extension']);}else{$ext="";}
        if(@is_dir($item) or $item=="." or $item==".." or !in_array($ext, $GLOBALS['__image_ext']) or !@file_exists($cesta."full/".$item)){continue;}
        $items[]=$item;
    }
    @closedir($handle);
    natsort($items);
  
    //priprava strankovani
    if($strankovat){
    $count=count($items);
    $paging=_resultPaging(_indexOutput_url, $strankovani, $count, "", "#hcm_gal".$GLOBALS['__hcm_uid'], "hcm_gal".$GLOBALS['__hcm_uid']."p");
    }
  
    //vypis
    $result="<div class='anchor'><a name='hcm_gal".$GLOBALS['__hcm_uid']."'></a></div>\n<div class='gallery'>\n";
      $counter=0;
      foreach($items as $item){
        if($strankovat and $counter>$paging[6]){break;}
        if(!$strankovat or ($strankovat and _resultPagingIsItemInRange($paging, $counter))){$result.="<a href='".$cesta."full/"._htmlStr($item)."' target='_blank' rel='lightbox[hcm".$GLOBALS['__hcm_uid']."]' title='".$item."'><img src='".$cesta."prev/"._htmlStr($item)."' alt='".$item."' /></a>\n";}
        $counter++;
      }
    $result.="</div>\n";
    if($strankovat){$result.=$paging[0];}
  
  }
  
  return $result;
  
}

?>