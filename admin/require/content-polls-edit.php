<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- priprava promennych ---*/
$continue=false;
$message="";
if(isset($_GET['id'])){
  $id=intval($_GET['id']);
  $query=mysql_query("SELECT * FROM `"._mysql_prefix."-polls` WHERE id=".$id._tmp_pollAccess());
    if(mysql_num_rows($query)!=0){
      $query=mysql_fetch_array($query);
      $new=false;
      $actionbonus="&amp;id=".$id;
      $submitcaption=$_lang['global.save'];
      $continue=true;
    }
}
else{
$id=-1;
$query=array(
'author'=>_loginid,
'question'=>"",
'answers'=>"",
'locked'=>0
);
$new=true;
$actionbonus="";
$submitcaption=$_lang['global.create'];
$continue=true;
}

/*--- ulozeni / vytvoreni ---*/
if(isset($_POST['question'])){

  //nacteni promennych
  $question=_safeStr(_htmlStr(trim($_POST['question'])));
  
    //odpovedi
    $answers=@explode("#", $_POST['answers']);
    $answers_new=array();
      foreach($answers as $answer){
      $answers_new[]=_htmlStr(trim($answer));
      }
    $answers=_arrayRemoveValue($answers_new, "");
    $answers_count=count($answers);
    $answers=@implode("#", $answers);
    
  if(_loginright_adminpollall){$author=intval($_POST['author']);}else{$author=_loginid;}
  $locked=_checkboxLoad("locked");
  $reset=_checkboxLoad("reset");
  
  //kontrola promennych
  $errors=array();
  if($question==""){$errors[]=$_lang['admin.content.polls.edit.error1'];}
  if($answers_count==0){$errors[]=$_lang['admin.content.polls.edit.error2'];}
  if(_loginright_adminpollall and mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users` WHERE id=".$author." AND (id="._loginid." OR (SELECT level FROM `"._mysql_prefix."-groups` WHERE id=`"._mysql_prefix."-users`.`group`)<"._loginright_level.")"), 0)==0){$errors[]=$_lang['admin.content.articles.edit.error3'];}
  
  //ulozeni
  if(count($errors)==0){

    if(!$new){
    mysql_query("UPDATE `"._mysql_prefix."-polls` SET question='".$question."',answers='".$answers."',author=".$author.",locked=".$locked." WHERE id=".$id);
    
      //korekce seznamu hlasu
      if(!$reset){
      $votes=explode("-", $query['votes']);
      $votes_count=count($votes);
      $newvotes="";
      
        //prilis mnoho polozek
        if($votes_count>$answers_count){
          for($i=0; $i<$votes_count-$answers_count; $i++){
          array_pop($votes);
          }
        $newvotes=implode("-", $votes);
        }
        
        //malo polozek
        if($votes_count<$answers_count){
        $newvotes=implode("-", $votes).str_repeat("-0", $answers_count-$votes_count);
        }
        
        //ulozeni korekci
        if($newvotes!=""){
        mysql_query("UPDATE `"._mysql_prefix."-polls` SET votes='".$newvotes."' WHERE id=".$id);
        }
        
      }
    
      //vynulovani
      if($reset){
      mysql_query("UPDATE `"._mysql_prefix."-polls` SET votes='".trim(str_repeat("0-", $answers_count), "-")."' WHERE id=".$id);
      mysql_query("DELETE FROM `"._mysql_prefix."-iplog` WHERE type=4 AND var=".$id);
      }
      
      //presmerovani
      define('_tmp_redirect', 'index.php?p=content-polls-edit&id='.$id.'&saved');
    
    }
    else{
    $newid=_getNewID("polls");
    mysql_query("INSERT INTO `"._mysql_prefix."-polls` (id,author,question,answers,locked,votes) VALUES (".$newid.",".$author.",'".$question."','".$answers."',".$locked.",'".trim(str_repeat("0-", $answers_count), "-")."')");
    define('_tmp_redirect', 'index.php?p=content-polls-edit&id='.$newid.'&created');
    }

  }
  else{
  $message=_formMessage(2, _eventList($errors, 'errors'));
  }

}

/*--- vystup ---*/
if($continue){

    //vyber autora
    if(_loginright_adminpollall){
    $author_select="
    <tr>
    <td class='rpad'><strong>".$_lang['article.author']."</strong></td>
    <td>"._tmp_authorSelect("author", $query['author'], "adminpoll=1", "selectmedium")."</td></tr>
    ";
    }
    else{
    $author_select="";
    }
    
    //zprava
    if(isset($_GET['saved'])){$message=_formMessage(1, $_lang['global.saved']);}
    if(isset($_GET['created'])){$message=_formMessage(1, $_lang['global.created']);}

  $output.="
  <p class='bborder'>".$_lang['admin.content.polls.edit.p']."</p>
  ".$message."
  <form action='index.php?p=content-polls-edit".$actionbonus."' method='post'>
  <table>
  
  <tr>
  <td class='rpad'><strong>".$_lang['admin.content.form.question']."</strong></td>
  <td><input type='text' name='question' class='inputmedium' value='".$query['question']."' /></td>
  </tr>
  
  ".$author_select."
  
  <tr valign='top'>
  <td class='rpad'><strong>".$_lang['admin.content.form.answers']."</strong></td>
  <td><textarea name='answers' class='areasmall' rows='9' cols='33'>".$query['answers']."</textarea></td>
  </tr>
  
  <tr>
  <td class='rpad'><strong>".$_lang['admin.content.form.settings']."</strong></td>
  <td>
  <label><input type='checkbox' name='locked' value='1'"._checkboxActivate($query['locked'])." /> ".$_lang['admin.content.form.locked']."</label>&nbsp;&nbsp;
  ".(!$new?"<label><input type='checkbox' name='reset' value='1' /> ".$_lang['admin.content.polls.reset']."</label>":'')."
  </td>
  </tr>
  
  <tr><td></td>
  <td><input type='submit' value='".$submitcaption."' />".(!$new?"&nbsp;&nbsp;<small>".$_lang['admin.content.form.thisid']." ".$id."</small>&nbsp;&nbsp;<span class='customsettings'><a href='index.php?p=content-polls&amp;del=".$id."' onclick='return _sysConfirm();'><span><img src='images/icons/delete.gif' class='icon' alt='del' /> ".$_lang['global.delete']."</span></a>":'')."</span></td>
  </tr>
  
  </table>
  </form>
  ";

}
else{
$output.=_formMessage(3, $_lang['global.badinput']);
}

?>