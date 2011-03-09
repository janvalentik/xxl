<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- vystup ---*/
$output.="
<p class='bborder'>".$_lang['admin.content.articles.p']."</p>
<p><a href='index.php?p=content-articles-edit'><img src='images/icons/new.gif' alt='new' class='icon' />".$_lang['admin.content.articles.create']."</a></p><br />

<table class='widetable'>
<tr><td><strong>".$_lang['article.category']."</strong></td><td><strong>".$_lang['global.articlesnum']."</strong></td></tr>
";

  //funkce
  function _tmp_catitemOutput($item, $pad=false){
  if($pad==true){$pad=" class='lpad'";}else{$pad="";}
  if($item['type']==2){$title="<a href='index.php?p=content-articles-list&amp;cat=".$item['id']."' class='block'><img src='images/icons/dir.gif' alt='col' class='icon' /><strong>".$item['title']."</strong></a>";}else{$title=$item['title'];}
  return "<tr><td".$pad.">".$title."</td><td>".mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-articles` WHERE (home1=".$item['id']." OR home2=".$item['id']." OR home3=".$item['id'].")"._tmp_artAccess()), 0)."</td></tr>\n";
  }

  //radky
  $query=mysql_query("SELECT title,id,type FROM `"._mysql_prefix."-root` WHERE (type=2 OR type=7) AND intersection=-1 ORDER BY ord");
  while($item=mysql_fetch_array($query)){
    if($item['type']==2){
    $output.=_tmp_catitemOutput($item);
    }
    else{
      $iquery=mysql_query("SELECT title,id,type FROM `"._mysql_prefix."-root` WHERE intersection=".$item['id']." AND type=2 ORDER BY ord");
      if(mysql_num_rows($iquery)!=0){
      $output.="<tr><td colspan='2'><a>".$item['title']."</a></td></tr>";
        while($iitem=mysql_fetch_array($iquery)){
        $output.=_tmp_catitemOutput($iitem, true);
        }
      }
    }
  }

$output.="
</table>

<br />
<form class='cform' action='index.php' method='get'>
<input type='hidden' name='p' value='content-articles-edit' />
<input type='hidden' name='returnid' value='load' />
<input type='hidden' name='returnpage' value='1' />
".$_lang['admin.content.articles.openid'].": <input type='text' name='id' class='inputmini' /> <input type='submit' value='".$_lang['global.open']."' />
</form>
";




?>