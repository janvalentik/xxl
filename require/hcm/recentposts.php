<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_recentposts($limit=null, $stranky="", $typ=null){

  //priprava
  $result="";
  if(isset($limit) and intval($limit)>=1){$limit=abs(intval($limit));}else{$limit=10;}
   
  //filtr cisel sekci, knih nebo clanku
  if(isset($stranky) and isset($typ)){
    $rtype=intval($typ);
    if($rtype<1 or $rtype>3){$rtype=1;}
    $rroots="("._sqlWhereColumn("home", $stranky).") AND type=".$rtype;
  }
  else{
    $rroots="type!=4";
  }
   
  $query=mysql_query("SELECT id,type,home,xhome,subject,author,guest,time,text FROM `"._mysql_prefix."-posts` WHERE ".$rroots." ORDER BY id DESC LIMIT ".$limit);
  while($item=mysql_fetch_array($query)){
  
    //nacteni titulku a odkazu na stranku
    switch($item['type']){
    case 1: case 3: $homelink=_linkRoot($item['home']); $hometitle=mysql_fetch_array(mysql_query("SELECT title FROM `"._mysql_prefix."-root` WHERE id=".$item['home'])); break;
    case 2: $homelink=_linkArticle($item['home']); $hometitle=mysql_fetch_array(mysql_query("SELECT title FROM `"._mysql_prefix."-articles` WHERE id=".$item['home'])); break;
  
      case 5:
      if($item['xhome']==-1){
        $tid=$item['id'];
        $hometitle=array("title"=>$item['subject']);
      }
      else{
        $tid=$item['xhome'];
        $hometitle=mysql_fetch_array(mysql_query("SELECT subject FROM `"._mysql_prefix."-posts` WHERE id=".$item['xhome']));
        $hometitle=array("title"=>$hometitle['subject']);
      }
        
      $homelink="index.php?m=topic&amp;id=".$tid;
      break;
      
    }
  
    //nacteni jmena autora
    if($item['author']!=-1){$authorname=_linkUser($item['author'], null, true, true);}
    else{$authorname=$item['guest'];}
  
    $hometitle=$hometitle['title'];
  
    $result.="
<h2 class='list-title'><a href='"._url."/".$homelink."'>".$hometitle."</a> <small>".$GLOBALS['_lang']['global.postauthor'].": ".$authorname." | "._formatTime($item['time'])." </small> </h2>
<p class='list-perex'>"._cutStr(strip_tags(_parsePost($item['text'])), 70)."</p>

";
  
  }
  
  return $result;
 
}

?>