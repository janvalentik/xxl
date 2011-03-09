<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_filelist($cesta="", $velikosti=false){
  
  $result="";
  $cesta=_indexroot.$cesta;
  $velikosti=_boolean($velikosti);
  if(mb_substr($cesta, -1, 1)!="/"){$cesta.="/";}

  if(@file_exists($cesta) and @is_dir($cesta)){
  $handle=@opendir($cesta);
    while($item=@readdir($handle)){
      if(@is_dir($cesta.$item) or $item=="." or $item==".."){continue;}
      $items[]=$item;
    }
    natsort($items);
    $result="<ul>\n";
      foreach($items as $item){
        $result.="<li>";
        $result.="<a href='".$cesta._htmlStr($item)."' target='_blank'>".$item."</a>";
        if($velikosti){$result.=" (".round(@filesize($cesta.$item)/1024)."kB)";}
        $result.="</li>\n";
      }
    $result.="</ul>\n";
  @closedir($handle);
  }
  
  return $result;
  
}

?>