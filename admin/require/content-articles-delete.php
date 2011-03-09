<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- nacteni promennych ---*/
$continue=false;
$path=_indexroot."upload/img/perex/";
if(isset($_GET['id']) and isset($_GET['returnid']) and isset($_GET['returnpage'])){
$id=intval($_GET['id']);
$returnid=intval($_GET['returnid']);
$returnpage=intval($_GET['returnpage']);
$query=mysql_query("SELECT title FROM `"._mysql_prefix."-articles` WHERE id=".$id._tmp_artAccess());
  if(mysql_num_rows($query)!=0){
  $query=mysql_fetch_array($query);
  $continue=true;
  }
}


/*--- ulozeni ---*/
if(isset($_POST['confirm'])){

  //smazani komentaru
  mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE type=2 AND home=".$id);
  
  //smazani clanku
  mysql_query("DELETE FROM `"._mysql_prefix."-articles` WHERE id=".$id);

  if(file_exists($path.$id.".jpg")){unlink($path.$id.".jpg");}
  if(file_exists($path.$id.".gif")){unlink($path.$id.".gif");}
  if(file_exists($path.$id.".png")){unlink($path.$id.".png");}
  recursive_remove_directory(_indexroot."upload/article_".$id);

  //presmerovani
  define('_tmp_redirect', 'index.php?p=content-articles-list&cat='.$returnid.'&page='.$returnpage.'&artdeleted');

}


/*--- vystup ---*/
if($continue){

$output.="
<a href='index.php?p=content-articles-list&amp;cat=".$returnid."&amp;page=".$returnpage."' class='backlink'>&lt; ".$_lang['global.return']."</a>
<h1>".$_lang['admin.content.articles.delete.title']."</h1>
<p class='bborder'>".str_replace("*arttitle*", $query['title'], $_lang['admin.content.articles.delete.p'])."</p>
<form class='cform' action='index.php?p=content-articles-delete&amp;id=".$id."&amp;returnid=".$returnid."&amp;returnpage=".$returnpage."' method='post'>
<input type='hidden' name='confirm' value='1' />
<input type='submit' value='".$_lang['admin.content.articles.delete.confirmbox']."' />
</form>
";

}
else{
$output.=_formMessage(3, $_lang['global.badinput']);
}




?>