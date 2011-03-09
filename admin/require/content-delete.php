<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- pripava promennych ---*/
$continue=false;
$done=false;
$path=_indexroot."upload/img/perex/";
if(isset($_GET['id'])){
$id=intval($_GET['id']);
$query=mysql_query("SELECT id,title,type,ord FROM `"._mysql_prefix."-root` WHERE id=".$id);
  if(mysql_num_rows($query)!=0){
  $query=mysql_fetch_array($query);
    if(constant('_loginright_admin'.$type_array[$query['type']])){
    $continue=true;
    }
  }
}

if($continue){

  /*--- odstraneni ---*/
  if(isset($_POST['confirm'])){

    //souvisejici polozky
    switch($query['type']){

      //komentare v sekcich
      case 1:
      mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE type=1 AND home=".$id);
      break;
      
      //clanky v kategoriich a jejich komentare
      case 2:
      $rquery=mysql_query("SELECT id,home1,home2,home3 FROM `"._mysql_prefix."-articles` WHERE home1=".$id." OR home2=".$id." OR home3=".$id);
        while($item=mysql_fetch_array($rquery)){
          if(file_exists($path.$item['id'].".jpg")){unlink($path.$item['id'].".jpg");}
          if(file_exists($path.$item['id'].".gif")){unlink($path.$item['id'].".gif");}
          if(file_exists($path.$item['id'].".png")){unlink($path.$item['id'].".png");}
          recursive_remove_directory(_indexroot."upload/article_".$item['id']);
          if($item['home1']==$id and $item['home2']==-1 and $item['home3']==-1){mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE type=2 AND home=".$item['id']); mysql_query("DELETE FROM `"._mysql_prefix."-articles` WHERE id=".$item['id']); continue;} //delete
          if($item['home1']==$id and $item['home2']!=-1 and $item['home3']==-1){mysql_query("UPDATE `"._mysql_prefix."-articles` SET home1=home2 WHERE id=".$item['id']); mysql_query("UPDATE `"._mysql_prefix."-articles` SET home2=-1 WHERE id=".$item['id']); continue;} //2->1
          if($item['home1']==$id and $item['home2']!=-1 and $item['home3']!=-1){mysql_query("UPDATE `"._mysql_prefix."-articles` SET home1=home2 WHERE id=".$item['id']); mysql_query("UPDATE `"._mysql_prefix."-articles` SET home2=home3 WHERE id=".$item['id']); mysql_query("UPDATE `"._mysql_prefix."-articles` SET home3=-1 WHERE id=".$item['id']); continue;} //2->1,3->2
          if($item['home1']==$id and $item['home2']==-1 and $item['home3']!=-1){mysql_query("UPDATE `"._mysql_prefix."-articles` SET home1=home3 WHERE id=".$item['id']); mysql_query("UPDATE `"._mysql_prefix."-articles` SET home3=-1 WHERE id=".$item['id']); continue;} //3->1
          if($item['home1']!=-1 and $item['home2']==$id){mysql_query("UPDATE `"._mysql_prefix."-articles` SET home2=-1 WHERE id=".$item['id']); continue;} //2->x
          if($item['home1']!=-1 and $item['home3']==$id){mysql_query("UPDATE `"._mysql_prefix."-articles` SET home3=-1 WHERE id=".$item['id']); continue;} //3->x
        }
      break;
      
      //prispevky v knihach
      case 3:
      mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE type=3 AND home=".$id);
      break;
      
      //obrazky v galerii
      case 5:
      mysql_query("DELETE FROM `"._mysql_prefix."-images` WHERE home=".$id);
      break;
      
      //polozky v rozcestniku
      case 7:
      $rquery=mysql_query("SELECT id,ord FROM `"._mysql_prefix."-root` WHERE intersection=".$id);
      while($item=mysql_fetch_array($rquery)){
      mysql_query("UPDATE `"._mysql_prefix."-root` SET intersection=-1,ord=".($query['ord'].".".intval($item['ord']))." WHERE id=".$item['id']);
      }
      break;
    
      //prispevky ve forech
      case 8:
      mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE type=5 AND home=".$id);
      break;

    }

  mysql_query("DELETE FROM `"._mysql_prefix."-root` WHERE id=".$id);
  define('_tmp_redirect', 'index.php?p=content&done');
  $done=true;
  }

  if($done!=true){
  
    /*--- vystup ---*/

      //pole souvisejicich polozek
      $content_array=array();
      switch($query['type']){
      case 1: $content_array[]=mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-posts` WHERE type=1 AND home=".$id), 0)." ".$_lang['admin.content.delete.comments']; break;
      case 2: $content_array[]=mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-articles` WHERE home1=".$id." AND home2=-1 AND home3=-1"), 0)." ".$_lang['admin.content.delete.articles']; break;
      case 3: $content_array[]=mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-posts` WHERE type=3 AND home=".$id), 0)." ".$_lang['admin.content.delete.posts']; break;
      case 5: case 3: $content_array[]=mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-images` WHERE home=".$id), 0)." ".$_lang['admin.content.delete.images']; break;
      case 8: $content_array[]=mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-posts` WHERE type=5 AND home=".$id), 0)." ".$_lang['admin.content.delete.posts']; break;
      default: $content_array[]=$_lang['admin.content.delete.norelated'];
      }

    $output.="
    <p class='bborder'>".$_lang['admin.content.delete.p']."</p>
    <h2>".$_lang['global.item']." <em>".$query['title']."</em></h2><br />
    <p>".$_lang['admin.content.delete.contentlist'].":</p>
    "._eventList($content_array)."
    <div class='hr'><hr /></div>

    <form class='cform' action='index.php?p=content-delete&amp;id=".$id."' method='post'>
    <input type='hidden' name='confirm' value='1' />
    <input type='submit' value='".$_lang['admin.content.delete.confirm']."' />
    </form>
    ";

  }

}
else{
$output.=_formMessage(3, $_lang['global.badinput']);
}

?>