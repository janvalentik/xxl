<?php

//kontrola jadra
if(!defined('_core')){exit;}

//titulek, obsah
$title=$query['title'];
$content="";
if(_template_autoheadings){
  $content.="<h1>".$query['title']."</h1>\n"._parseHCM($query['content'])."\n<div class='hr'><hr /></div>\n\n";
}
else{
  if($query['content']!=""){
  $content.=_parseHCM($query['content'])."<div class='hr'><hr /></div>";
  }
}

//obrazky
$content.='<a name="gallery"></a>';
$paging=_resultPaging(_indexOutput_url, $query['var2'], "images", "home=".$id, "#gallery");
$images=mysql_query("SELECT * FROM `"._mysql_prefix."-images` WHERE home=".$id." ORDER BY ord ".$paging[1]);
$images_number=mysql_num_rows($images);

  if($images_number!=0){

    $usetable=$query['var1']!=-1;
    if(_pagingmode==1 or _pagingmode==2){$content.=$paging[0];}
    if($usetable){$content.="<table class='gallery'>\n";}else{$content.="<div class='gallery'>\n";}

    //obrazky
    $counter=0;
    $cell_counter=0;
    while($img=mysql_fetch_array($images)){
      if($usetable and $cell_counter==0){$content.="<tr>\n";}

        //bunka
        if($usetable){$content.="<td>";}
        $content.=_galleryImage($img, $id);
        if($usetable){$content.="</td>";}

      $cell_counter++;
      if($usetable and ($cell_counter==$query['var1'] or $counter==$images_number-1)){$cell_counter=0; $content.="\n</tr>";}
      $content.="\n";
    $counter++;
    }

    if($usetable){$content.="</table>";}else{$content.="</div>";}
    if(_pagingmode==2 or _pagingmode==3){$content.=$paging[0];}

  }
  else{
    $content.=$_lang['misc.gallery.noimages'];
  }

if($query['var4']==1 and _comments){
  require_once(_indexroot.'require/functions-posts.php');
  $content.=_postsOutput(7, $id, $query['var5']);
}

?>