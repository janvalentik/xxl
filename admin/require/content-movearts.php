<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- priprava promennych ---*/
$message="";

/*--- akce ---*/
if(isset($_POST['source'])){

  //nacteni promennych
  $source=intval($_POST['source']);
  $target=intval($_POST['target']);
  $fullmove=_checkboxLoad("fullmove");
  
  //kontrola promennych
  $error_log=array();
  if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-root` WHERE id=".$source." AND type=2"), 0)==0){$error_log[]=$_lang['admin.content.movearts.badsource'];}
  if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-root` WHERE id=".$target." AND type=2"), 0)==0){$error_log[]=$_lang['admin.content.movearts.badtarget'];}
  if($source==$target){$error_log[]=$_lang['admin.content.movearts.samecats'];}
  
  //aplikace
  if(count($error_log)==0){

      if(!$fullmove){
      $query=mysql_query("SELECT id,home1,home2,home3 FROM `"._mysql_prefix."-articles` WHERE home1=".$source." OR home2=".$source." OR home3=".$source);
      $counter=0;
      while($item=mysql_fetch_array($query)){
        if($item['home1']==$source){$homeid=1; $homecheck=array(2, 3);}
        if($item['home2']==$source){$homeid=2; $homecheck=array(1, 3);}
        if($item['home3']==$source){$homeid=3;  $homecheck=array(1, 2);}
        mysql_query("UPDATE `"._mysql_prefix."-articles` SET home".$homeid."=".$target." WHERE id=".$item['id']);
          foreach($homecheck as $hc){
            if($item['home'.$hc]==$target){
            if($hc!=1){mysql_query("UPDATE `"._mysql_prefix."-articles` SET home".$hc."=-1 WHERE id=".$item['id']);}
            else{mysql_query("UPDATE `"._mysql_prefix."-articles` SET home".$homeid."=-1 WHERE id=".$item['id']);}
            }
          }
        $counter++;
      }
    }
    else{
      mysql_query("UPDATE `"._mysql_prefix."-articles` SET home1=".$target.",home2=-1,home3=-1 WHERE home1=".$source." OR home2=".$source." OR home3=".$source);
      $counter=mysql_affected_rows();
    }
    
  $message=_formMessage(1, str_replace("*moved*", $counter, $_lang['admin.content.movearts.done']));
  }
  else{
  $message=_formMessage(2, _eventList($error_log, 'errors'));
  }

}

/*--- vystup ---*/
$output.="
<p class='bborder'>".$_lang['admin.content.movearts.p']."</p>
".$message."
<form class='cform' action='index.php?p=content-movearts' method='post'>
".$_lang['admin.content.movearts.text1']." "._tmp_rootSelect("source", 2, -1, false)." ".$_lang['admin.content.movearts.text2']." "._tmp_rootSelect("target", 2, -1, false)." <input type='submit' value='".$_lang['global.do']."' />
<br /><br />
<label><input type='checkbox' name='fullmove' value='1' /> ".$_lang['admin.content.movearts.fullmove']."</label>
</form>
";

?>