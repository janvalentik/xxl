<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- schvaleni zvoleneho clanku ---*/
$message="";
if(isset($_GET['id'])){
mysql_query("UPDATE `"._mysql_prefix."-articles` SET confirmed=1 WHERE id=".intval($_GET['id']));
$message=_formMessage(1, $_lang['global.done']);
}

/*--- vystup ---*/

  //nacteni filtru
  if(isset($_GET['limit'])){
  $catlimit=intval($_GET['limit']);
  $condplus=" AND (home1=".$catlimit." OR home2=".$catlimit." OR home3=".$catlimit.")";
  }
  else{
  $catlimit=-1;
  $condplus="";
  }

$output.="
<p class='bborder'>".$_lang['admin.content.confirm.p']."</p>

<form class='cform' action='index.php' method='get'>
<input type='hidden' name='p' value='content-confirm' />
".$_lang['admin.content.confirm.filter'].": "._tmp_rootSelect("limit", 2, $catlimit, true, $_lang['global.all'])." <input type='submit' value='".$_lang['global.do']."' />
</form>
<div class='hr'><hr /></div>

".$message."

<table class='list'>
<tr><td><strong>".$_lang['global.article']."</strong></td><td><strong>".$_lang['article.category']."</strong></td><td><strong>".$_lang['article.posted']."</strong></td><td><strong>".$_lang['article.author']."</strong></td><td><strong>".$_lang['global.action']."</strong></td></tr>
";

  //vypis
  $query=mysql_query("SELECT id,title,home1,home2,home3,author,time,visible,confirmed,public FROM `"._mysql_prefix."-articles` WHERE confirmed=0".$condplus." ORDER BY time DESC");
  if(mysql_num_rows($query)!=0){
    while($item=mysql_fetch_array($query)){

      //seznam kategorii
      $cats="";
      for($i=1; $i<=3; $i++){
      if($item['home'.$i]!=-1){
      $hometitle=mysql_fetch_array(mysql_query("SELECT title FROM `"._mysql_prefix."-root` WHERE id=".$item['home'.$i]));
      $cats.=$hometitle['title'];
      }
      if($i!=3 and $item['home'.($i+1)]!=-1){$cats.=", ";}
      }

    if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-articles` WHERE id=".$item['id']._tmp_artAccess()), 0)!=0){$editlink=" / <a href='index.php?p=content-articles-edit&amp;id=".$item['id']."&amp;returnid=load&amp;returnpage=1' class='small'>".$_lang['global.edit']."</a>";}else{$editlink="";}
    $output.="<tr><td>"._tmp_articleEditLink($item, false)."</td><td>".$cats."</td><td>"._formatTime($item['time'])."</td><td>"._linkUser($item['author'])."</td><td><a href='index.php?p=content-confirm&amp;id=".$item['id']."&amp;limit=".$catlimit."' class='small'>".$_lang['admin.content.confirm.confirm']."</a>".$editlink."</td></tr>\n";
    }
  }
  else{
  $output.="<tr><td colspan='5'>".$_lang['global.nokit']."</td></tr>";
  }

$output.="</table>";

?>