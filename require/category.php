<?php

//kontrola jadra
if(!defined('_core')){exit;}

//nastaveni strankovani
$artsperpage=$query['var2'];
switch($query['var1']){
case 1: $artorder="time DESC"; break;
case 2: $artorder="id DESC"; break;
case 3: $artorder="title"; break;
case 4: $artorder="title DESC"; break;
}

//titulek, obsah
$title=$query['title'];
$content="";
if(_template_autoheadings){
  $content.="<h1>".$query['title']._linkRSS($id, 4)."</h1>\n"._parseHCM($query['content'])."\n<div class='hr'><hr /></div>\n\n";
}
else{
  $content.=_linkRSS($id, 4);
    if($query['content']!=""){
    $content.=_parseHCM($query['content'])."<div class='hr'><hr /></div>";
    }
}

//vypis clanku
 if (_selectart){
$content.="<form action='' method='post'>
<select name='ord'>
<option value='1'>".$_lang['xxl.a-z']."</option>
<option value='2'>".$_lang['xxl.z-a']."</option>
<option value='3'>".$_lang['xxl.art-new']."</option>
<option value='4'>".$_lang['xxl.art-old']."</option>
</select>
<input type='submit' value='".$_lang['xxl.go']."' />
</form>";

if (isset($_POST['ord'])) {
switch($_POST['ord']){
case 1: $artorder="title"; break;
case 2: $artorder="title DESC"; break;
case 3: $artorder="time DESC"; break;
case 4: $artorder="time"; break;
}}}


$arts_cond="(home1=".$id." OR home2=".$id." OR home3=".$id.") AND "._sqlArticleFilter()." ORDER BY ".$artorder;
$paging=_resultPaging(_indexOutput_url, $artsperpage, "articles", $arts_cond);
$arts=mysql_query("SELECT id,title,author,perex,time,comments,readed FROM `"._mysql_prefix."-articles` WHERE ".$arts_cond." ".$paging[1]);

  if(mysql_num_rows($arts)!=0){
    if(_pagingmode==1 or _pagingmode==2){$content.=$paging[0];}
    while($art=mysql_fetch_array($arts)){$content.=_articlePreview($art, $query['var3']==1);}
    if(_pagingmode==2 or _pagingmode==3){$content.='<br />'.$paging[0];}
  }
  else{
    $content.=$_lang['misc.category.noarts'];
  }

?>