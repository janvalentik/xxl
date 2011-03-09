<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_gallery($cesta="", $vyska_nahledu=null, $strankovani=null){
  
  //priprava
  $result="";
  $cesta=_indexroot.$cesta;
  $cesta_noroot=$cesta;
  if(mb_substr($cesta, -1, 1)!="/"){$cesta.="/";}
  if(mb_substr($cesta_noroot, -1, 1)!="/"){$cesta_noroot.="/";}
  $vyska_nahledu=abs(intval($vyska_nahledu));
  if(isset($strankovani) and $strankovani>0){
    $strankovat=true;
    $strankovani=intval($strankovani);
    if($strankovani<=0){$strankovani=1;}
  } else{$strankovat=false;}
  
  if(@file_exists($cesta) and @is_dir($cesta)){
  $handle=@opendir($cesta);
  
    //nacteni polozek
    $items=array();
    while($item=@readdir($handle)){
      $ext=pathinfo($item);
      if(isset($ext['extension'])){$ext=mb_strtolower($ext['extension']);}else{$ext="";}
        if(@is_dir($item) or $item=="." or $item==".." or !in_array($ext, $GLOBALS['__image_ext'])){continue;}
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
        if(!$strankovat or ($strankovat and _resultPagingIsItemInRange($paging, $counter))){$result.="<a href='".$cesta._htmlStr($item)."' target='_blank' rel='lightbox[hcm".$GLOBALS['__hcm_uid']."]' title='".$item."'><img src='"._indexroot."remote/imgprev.php?id=".urlencode(base64_encode($cesta_noroot.$item))."&amp;c&amp;h=".$vyska_nahledu."' alt='".$item."' /></a>\n";}
        $counter++;
      }
    $result.="</div>\n";
    if($strankovat){$result.=$paging[0];}
  
  }
  
  return $result;

}

?>