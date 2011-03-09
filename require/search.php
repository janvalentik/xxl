<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- pripava ---*/
function _tmp_checkboxLoadGet($input){if(isset($_GET[$input])){return 1;}else{return 0;}}

if(isset($_GET['q'])){
$q=trim(_stripSlashes($_GET['q']));
$root=_tmp_checkboxLoadGet("root");
$art=_tmp_checkboxLoadGet("art");
$post=_tmp_checkboxLoadGet("post");
$image=_tmp_checkboxLoadGet("img");
}
else{
$q=""; $root=1; $art=1; $post=0; $image=0;
}

/*--- modul ---*/
if(_template_autoheadings==1){$module="<h1>".$_lang['mod.search']."</h1>";}else{$module="";}

$module.="
<p class='bborder'>".$_lang['mod.search.p']."</p>

<form action='index.php' method='get'>
<input type='hidden' name='m' value='search' />
<input type='text' name='q' class='inputmedium' value='"._htmlStr($q)."' /> <input type='submit' value='".$_lang['mod.search.submit']."' /><br />
".$_lang['mod.search.where'].":&nbsp;
<label><input type='checkbox' name='root' value='1'"._checkboxActivate($root)." /> ".$_lang['mod.search.where.root']."</label>&nbsp;
<label><input type='checkbox' name='art' value='1'"._checkboxActivate($art)." /> ".$_lang['mod.search.where.articles']."</label>&nbsp;
<label><input type='checkbox' name='post' value='1'"._checkboxActivate($post)." /> ".$_lang['mod.search.where.posts']."</label>&nbsp;
<label><input type='checkbox' name='img' value='1'"._checkboxActivate($image)." /> ".$_lang['mod.search.where.images']."</label>
</form>";

  /*- vyhledavani -*/
  if($q!=""){
   

  if(mb_strlen($q)>=3){
   /*- zápis do mysql není-li úprazdná proměnná q -*/
  $time = Time();
  if (_loginindicator==0){
  $users = "-1";}
  else{
  $users =_loginid;}
  if (mysql_query("INSERT INTO `"._mysql_prefix."-statssearch` VALUES (null,'"._htmlStr($q)."','".$time."','".$users."')")); 

  
  $q=_safeStr("%".$q."%");
  $results=array();
  
    if($root){
    $query=mysql_query("SELECT id,title,content,intersectionperex FROM `"._mysql_prefix."-root` WHERE visible=1".(!_loginindicator?" AND public=1":'')." AND (title LIKE '".$q."' OR content LIKE '".$q."' OR intersectionperex LIKE '".$q."')");
      while($item=mysql_fetch_array($query)){
      $results[]=array(_linkRoot($item['id']), $item['title'], strip_tags($item['intersectionperex']));
      }
    }
    
    if($art){
    if(!_loginindicator){$public=" AND ((SELECT public FROM `"._mysql_prefix."-root` WHERE id=`"._mysql_prefix."-articles`.home1)=1 OR (SELECT public FROM `"._mysql_prefix."-root` WHERE id=`"._mysql_prefix."-articles`.home2)=1 OR (SELECT public FROM `"._mysql_prefix."-root` WHERE id=`"._mysql_prefix."-articles`.home3)=1)";}else{$public="";}
    $query=mysql_query("SELECT id,title,perex FROM `"._mysql_prefix."-articles` WHERE "._sqlArticleFilter()." AND (title LIKE '".$q."' OR content LIKE '".$q."' OR infobox LIKE '".$q."' OR perex LIKE '".$q."')".$public);
      while($item=mysql_fetch_array($query)){
      $results[]=array(_linkArticle($item['id']), $item['title'], _cutStr(strip_tags($item['perex']), 255, false));
      }
    }
    
    if($post){
    $query=mysql_query("SELECT id,type,home,xhome,text,subject FROM `"._mysql_prefix."-posts` WHERE type!=4 AND (subject LIKE '".$q."' OR text LIKE '".$q."')");
      while($item=mysql_fetch_array($query)){
      
        //preskoceni polozek, ke kterym neni umoznen pristup
        switch($item['type']){
        case 1: case 3: case 5: $homedata=mysql_fetch_array(mysql_query("SELECT title,public,visible,var2 FROM `"._mysql_prefix."-root` WHERE id=".$item['home'])); break;
        case 2: $homedata=mysql_fetch_array(mysql_query("SELECT title,public,visible FROM `"._mysql_prefix."-articles` WHERE id=".$item['home'])); break;
        }
        
        if(!_publicAccess($homedata['public']) or $homedata['visible']!=1){continue;}
        
        //odkaz na stranku
        switch($item['type']){
        case 1: $homelink=_addGetToLink(_linkRoot($item['home']), "page="._resultPagingGetItemPage(_commentsperpage, "posts", "id>".$item['id']." AND type=1 AND xhome=-1 AND home=".$item['home']))."#posts"; break;
        case 2: $homelink=_addGetToLink(_linkArticle($item['home']), "page="._resultPagingGetItemPage(_commentsperpage, "posts", "id>".$item['id']." AND type=2 AND xhome=-1 AND home=".$item['home']))."#posts"; break;
        case 3: $homelink=_addGetToLink(_linkRoot($item['home']), "page="._resultPagingGetItemPage($homedata['var2'], "posts", "id>".$item['id']." AND type=3 AND xhome=-1 AND home=".$item['home']))."#posts"; break;
        case 5: if($item['xhome']==-1){$homelink="index.php?m=topic&amp;id=".$item['id'];}else{$homelink=_addGetToLink("index.php?m=topic&amp;id=".$item['xhome'], "page="._resultPagingGetItemPage(_commentsperpage, "posts", "id<".$item['id']." AND type=5 AND xhome=".$item['xhome']." AND home=".$item['home']))."#posts";} break;
        }
        
        //vyplneni prazdeho predmetu
        if($item['subject']==""){$item['subject']=$_lang['mod.messages.nosubject'];}
      
      $results[]=array($homelink, $item['subject']." (".$homedata['title'].")", _cutStr(strip_tags(_parsePost($item['text'])), 255));
      }
    }
    
    if($image){
    $query=mysql_query("SELECT id,prev,full,ord,home,title FROM `"._mysql_prefix."-images` WHERE title LIKE '%".$q."%'");
      while($item=mysql_fetch_array($query)){
      
        //nacteni data domovske galerie, preskoceni nedostupnych
        $homedata=mysql_fetch_array(mysql_query("SELECT title,public,visible,var2 FROM `"._mysql_prefix."-root` WHERE id=".$item['home']));
        if(!_publicAccess($homedata['public']) or $homedata['visible']!=1){continue;}
        $homelink=_addGetToLink(_linkRoot($item['home']), "page="._resultPagingGetItemPage($homedata['var2'], "images", "ord<".$item['ord']." AND home=".$item['home']));
      
      $results[]=array($homelink, $item['title']." (".$homedata['title'].")", _galleryImage($item, "search", 96));
      }
    }
    
    if(count($results)!=0){
    
    
      foreach($results as $item){
      
      //zapsání do mysql
      $module.="
      <h2 class='list-title'><a href='".$item[0]."'>".$item[1]."</a></h2>
      <p class='list-perex'>".$item[2]."</p>";
      }
      }
    else{
      $module.="<br />"._formMessage(1, $_lang['mod.search.noresult']);
    }

  }
  else{
  $module.="<br />"._formMessage(2, $_lang['mod.search.minlength']);
  }
  }





?>