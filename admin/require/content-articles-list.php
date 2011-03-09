<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- nacteni promennych ---*/
$continue=false;
if(isset($_GET['cat'])){
$cid=intval($_GET['cat']);
  if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-root` WHERE id=".$cid." AND type=2"), 0)!=0){
  $catdata=mysql_fetch_array(mysql_query("SELECT title,var1,var2 FROM `"._mysql_prefix."-root` WHERE id=".$cid));
  $continue=true;
  }
}


/*--- vystup---*/
if($continue){
$output.="
<p class='bborder'>".$_lang['admin.content.articles.list.p']."</p>
";

  //nastaveni strankovani podle kategorie
  $artsperpage=$catdata['var2'];
  switch($catdata['var1']){
  case 1: $artorder="time DESC"; break;
  case 2: $artorder="id DESC"; break;
  case 3: $artorder="title"; break;
  case 4: $artorder="title DESC"; break;
  }
  
  //titulek kategorie
  $output.="<h2>".$catdata['title']."</h2>\n";
  
  //vypis clanku

    //zprava
    $message="";
    if(isset($_GET['artdeleted'])){$message=_formMessage(1, $_lang['admin.content.articles.delete.done']);}
  
  $cond="(home1=".$cid." OR home2=".$cid." OR home3=".$cid.")"._tmp_artAccess();
  $paging=_resultPaging("index.php?p=content-articles-list&amp;cat=".$cid, $catdata['var2'], "articles", $cond);
  $s=$paging[2];
  $output.=$paging[0]."<div class='hr'><hr /></div>\n".$message."\n<table class='list'>\n<tr><td><strong>".$_lang['global.article']."</strong></td><td><strong>".$_lang['article.author']."</strong></td><td><strong>".$_lang['article.posted']."</strong></td><td><strong>".$_lang['global.action']."</strong></td></tr>\n";
  $arts=mysql_query("SELECT id,title,time,author,confirmed,visible,public FROM `"._mysql_prefix."-articles` WHERE ".$cond." ORDER BY ".$artorder." ".$paging[1]);
  if(mysql_num_rows($arts)!=0){
  while($art=mysql_fetch_array($arts)){$output.="<tr><td>"._tmp_articleEditLink($art)."</td><td>"._linkUser($art['author'])."</td><td>"._formatTime($art['time'])."</td><td><a href='index.php?p=content-articles-edit&amp;id=".$art['id']."&amp;returnid=".$cid."&amp;returnpage=".$s."'><img src='images/icons/edit.gif' alt='edit' class='icon' />".$_lang['global.edit']."</a>&nbsp;&nbsp;&nbsp;<a href='index.php?p=content-articles-delete&amp;id=".$art['id']."&amp;returnid=".$cid."&amp;returnpage=".$s."'><img src='images/icons/delete.gif' alt='del' class='icon' />".$_lang['global.delete']."</a></td></tr>\n";}
  }
  else{
  $output.="<tr><td colspan='4'>".$_lang['global.nokit']."</td></tr>";
  }
  $output.="</table>";

}
else{
$output.=_formMessage(3, $_lang['global.badinput']);
}




?>