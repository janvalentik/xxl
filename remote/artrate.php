<?php

/*--- inicializace jadra ---*/
define('_indexroot', '../');
require(_indexroot."core.php");
if(_ratemode==0){exit;}

/*--- hodnoceni ---*/

  //nacteni promennych
  _checkKeys('_POST', array('id'));
  $id=intval($_POST['id']);
  
  
  $article_exists=false;
  
  //kontrola promennych a pristupu
  $continue=false;
  $query=mysql_query("SELECT id,time,confirmed,public,home1,home2,home3,rateon FROM `"._mysql_prefix."-articles` WHERE id=".$id);
  if(mysql_num_rows($query)!=0){
    $article_exists=true;
      if(isset($_POST['r'])){
        $r=round($_POST['r']/10)*10;
        $query=mysql_fetch_array($query);
        if(_iplogCheck(3, $id) and $query['rateon']==1 and _articleAccess($query)==1 and $r<=100 and $r>=0){$continue=true;}
      }
  }
    
  //zapocteni hodnoceni
  if($continue){
  mysql_query("UPDATE `"._mysql_prefix."-articles` SET ratenum=ratenum+1,ratesum=ratesum+".$r." WHERE id=".$id);
  _iplogUpdate(3, $id);
  }
  
  //presmerovani
  if($article_exists){$aurl=_linkArticle($id)."#ainfo";}else{$aurl="";}
  header("location: "._indexroot.$aurl);

?>