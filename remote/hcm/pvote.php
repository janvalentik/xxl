<?php

/*--- incializace jadra ---*/
define('_indexroot', '../../');
require(_indexroot."core.php");

/*--- hlasovani ---*/

//nacteni promennych
if(isset($_POST['pid']) and isset($_POST['option'])){
$pid=intval($_POST['pid']);
$option=intval($_POST['option']);

  //ulozeni hlasu
  $query=mysql_query("SELECT locked,answers,votes FROM `"._mysql_prefix."-polls` WHERE id=".$pid);
  if(mysql_num_rows($query)!=0){
    $query=mysql_fetch_array($query);
    $answers=explode("#", $query['answers']);
    $votes=explode("-", $query['votes']);
      if(_loginright_pollvote and $query['locked']==0 and _iplogCheck(4, $pid) and isset($votes[$option])){
      $votes[$option]+=1;
      $votes=implode("-", $votes);
      mysql_query("UPDATE `"._mysql_prefix."-polls` SET votes='".$votes."' WHERE id=".$pid);
      _iplogUpdate(4, $pid);
      }
  }

}


//presmerovani
_returnHeader();


?>