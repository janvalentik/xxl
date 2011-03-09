<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- priprava ---*/
$message="";
$infopage=false;

function _tmp_boolSelect($name, $type2=false){
global $_lang;
return "
<select name='".$name."'>
<option value='-1'>".(($type2==false)?$_lang['admin.content.artfilter.f1.bool.doesntmatter']:$_lang['global.nochange'])."</option>
<option value='1'>".$_lang['admin.content.artfilter.f1.bool.mustbe']."</option>
<option value='0'>".$_lang['admin.content.artfilter.f1.bool.mustntbe']."</option>
</select>&nbsp;
";
}

/*--- akce ---*/
if(isset($_POST['category'])){

  //nacteni promennych
  $category=intval($_POST['category']);
  $author=intval($_POST['author']);
  $time=_tmp_loadTime(time());
  $ba=intval($_POST['ba']);
  $public=intval($_POST['public']);
  $visible=intval($_POST['visible']);
  $confirmed=intval($_POST['confirmed']);
  $comments=intval($_POST['comments']);
  $rateon=intval($_POST['rateon']);
  $showinfo=intval($_POST['showinfo']);
  $new_category=intval($_POST['new_category']);
  $new_author=intval($_POST['new_author']);
  $new_public=intval($_POST['new_public']);
  $new_visible=intval($_POST['new_visible']);
  if(_loginright_adminconfirm){$new_confirmed=intval($_POST['new_confirmed']);}
  $new_comments=intval($_POST['new_comments']);
  $new_rateon=intval($_POST['new_rateon']);
  $new_showinfo=intval($_POST['new_showinfo']);
  $new_delete=_checkboxLoad("new_delete");
  $new_resetrate=_checkboxLoad("new_resetrate");
  $new_delcomments=_checkboxLoad("new_delcomments");
  $new_resetread=_checkboxLoad("new_resetread");
  
  //kontrola promennych
  if($new_category!=-1){if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-root` WHERE id=".$new_category." AND type=2"), 0)==0){$new_category=-1;}}
  if($new_author!=-1){if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users` WHERE id=".$new_author), 0)==0){$new_author=-1;}}
  
  //sestaveni casti sql dotazu - 'where'
  $params=array("category", "author", "time", "public", "visible", "confirmed", "comments", "rateon", "showinfo");
  $cond="";
  
    //cyklus
    foreach($params as $param){

      $skip=false;
      if($param=="category" or $param=="author" or $param=="time"){
      
        switch($param){

        case "category":
          if($$param!="-1"){$cond.=_sqlArticleWhereCategories($$param);}
          else{$skip=true;}
        break;

        case "author":
          if($$param!="-1"){$cond.=$param."=".$$param;}
          else{$skip=true;}
        break;

        case "time":
          switch($ba){
          case 1: $operator=">"; break;
          case 2: $operator="="; break;
          case 3: $operator="<"; break;
          default: $skip=true; break;
          }
          if(!$skip){$cond.=$param.$operator.$$param;}
        break;

        }

      }
      else{
        //boolean
        switch($$param){
        case "1": $cond.=$param."=1"; break;
        case "0": $cond.=$param."=0"; break;
        default: $skip=true; break;
        }
      }

      if(!$skip){
       $cond.=" AND ";
      }
      
    }
    
    //vycisteni podminky
    if($cond==""){$cond=1;}
    else{$cond=mb_substr($cond, 0, mb_strlen($cond)-5);}
    
  //vyhledani clanku
  $query=mysql_query("SELECT id FROM `"._mysql_prefix."-articles` WHERE ".$cond);
  $found=mysql_num_rows($query);
    if($found!=0){
      if(!_checkboxLoad("_process")){
        $infopage=true;
      }
      else{
      $boolparams=array("public", "visible", "comments", "rateon", "showinfo");
      if(_loginright_adminconfirm){$boolparams[]="confirmed";}
        while($item=mysql_fetch_array($query)){

          //smazani komentaru
          if($new_delcomments or $new_delete){mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE type=2 AND home=".$item['id']);}

          //smazani clanku
          if($new_delete){mysql_query("DELETE FROM `"._mysql_prefix."-articles` WHERE id=".$item['id']); continue;}
          
          //vynulovani hodnoceni
          if($new_resetrate){
          mysql_query("UPDATE `"._mysql_prefix."-articles` SET ratenum=0, ratesum=0 WHERE id=".$item['id']);
          mysql_query("DELETE FROM `"._mysql_prefix."-iplog` WHERE type=3 AND var=".$item['id']);
          }
          
          //vynulovani poctu precteni
          if($new_resetread){mysql_query("UPDATE `"._mysql_prefix."-articles` SET readed=0 WHERE id=".$item['id']);}

          //zmena kategorie
          if($new_category!=-1){mysql_query("UPDATE `"._mysql_prefix."-articles` SET home1=".$new_category.", home2=-1, home3=-1 WHERE id=".$item['id']);}

          //zmena autora
          if($new_author!=-1){mysql_query("UPDATE `"._mysql_prefix."-articles` SET author=".$new_author." WHERE id=".$item['id']);}

          //konfigurace
          foreach($boolparams as $param){
          $paramvar="new_".$param; $paramval=$$paramvar;
           if($paramval==0 or $paramval==1){mysql_query("UPDATE `"._mysql_prefix."-articles` SET ".$param."=".$paramval." WHERE id=".$item['id']);}
          }

        }
        $message=_formMessage(1, $_lang['global.done']);
      }
    }
    else{
    $message=_formMessage(2, $_lang['admin.content.artfilter.f1.noresult']);
    }

}

/*--- vystup ---*/
$output.="
<p class='bborder'>".$_lang['admin.content.artfilter.p']."</p>
".$message."
<form action='index.php?p=content-artfilter' method='post'>
";

if(!$infopage){
$output.="
<h2>".$_lang['admin.content.artfilter.f1.title']."</h2>
<p>".$_lang['admin.content.artfilter.f1.p']."</p>
<table>

<tr>
<td class='rpad'><strong>".$_lang['article.category']."</strong></td>
<td>"._tmp_rootSelect("category", 2, -1, true, $_lang['global.any2'])."</td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['article.author']."</strong></td>
<td>"._tmp_authorSelect("author", -1, "adminart=1", "selectmedium", $_lang['global.any'])."</td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['article.posted']."</strong></td>
<td>

<select name='ba'>
<option value='0'>".$_lang['admin.content.artfilter.f1.time0']."</option>
<option value='1'>".$_lang['admin.content.artfilter.f1.time1']."</option>
<option value='2'>".$_lang['admin.content.artfilter.f1.time2']."</option>
<option value='3'>".$_lang['admin.content.artfilter.f1.time3']."</option>
</select>

"._tmp_editTime(time())."

</td>
</tr>

<tr valign='top'>
<td class='rpad'><strong>".$_lang['admin.content.form.settings']."</strong></td>
<td>
"._tmp_boolSelect("public").$_lang['admin.content.form.public']."<br />
"._tmp_boolSelect("visible").$_lang['admin.content.form.visible']."<br />
"._tmp_boolSelect("confirmed").$_lang['admin.content.form.confirmed']."<br />
"._tmp_boolSelect("comments").$_lang['admin.content.form.comments']."<br />
"._tmp_boolSelect("rateon").$_lang['admin.content.form.artrate']."<br />
"._tmp_boolSelect("showinfo").$_lang['admin.content.form.showinfo']."
</td>
</tr>

</table>

<br /><div class='hr'><hr /></div><br />

<h2>".$_lang['admin.content.artfilter.f2.title']."</h2>
<p>".$_lang['admin.content.artfilter.f2.p']."</p>
<table>

<tr>
<td class='rpad'><strong>".$_lang['article.category']."</strong></td>
<td>"._tmp_rootSelect("new_category", 2, -1, true, $_lang['global.nochange'])."</td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['article.author']."</strong></td>
<td>"._tmp_authorSelect("new_author", -1, "adminart=1", "selectmedium", $_lang['global.nochange'])."</td>
</tr>

<tr valign='top'>
<td class='rpad'><strong>".$_lang['admin.content.form.settings']."</strong></td>
<td>
"._tmp_boolSelect("new_public", true).$_lang['admin.content.form.public']."<br />
"._tmp_boolSelect("new_visible", true).$_lang['admin.content.form.visible']."<br />
".(_loginright_adminconfirm?_tmp_boolSelect("new_confirmed", true).$_lang['admin.content.form.confirmed']."<br />":'')."
"._tmp_boolSelect("new_comments", true).$_lang['admin.content.form.comments']."<br />
"._tmp_boolSelect("new_rateon", true).$_lang['admin.content.form.artrate']."<br />
"._tmp_boolSelect("new_showinfo", true).$_lang['admin.content.form.showinfo']."
</td>
</tr>

<tr valign='top'>
<td class='rpad'><strong>".$_lang['global.action']."</strong></td>
<td>
<label><input type='checkbox' name='new_delete' value='1' /> ".$_lang['global.delete']."</label><br />
<label><input type='checkbox' name='new_resetrate' value='1' /> ".$_lang['admin.content.form.resetartrate']."</label><br />
<label><input type='checkbox' name='new_delcomments' value='1' /> ".$_lang['admin.content.form.delcomments']."</label><br />
<label><input type='checkbox' name='new_resetread' value='1' /> ".$_lang['admin.content.form.resetartread']."</label>
</td>
</tr>

</table>

<br /><div class='hr'><hr /></div><br />

<input type='submit' value='".$_lang['mod.search.submit']."' />
";
}
else{
$output.=_getPostdata()."
<input type='hidden' name='_process' value='1' />
"._formMessage(1, str_replace("*found*", $found, $_lang['admin.content.artfilter.f1.infotext']))."
<input type='submit' value='".$_lang['global.do2']."' />&nbsp;&nbsp;<a href='index.php?p=content-artfilter'>".$_lang['global.cancel']."</a>
";
}

$output.="</form>";

?>