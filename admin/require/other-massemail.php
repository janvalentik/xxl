<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- odeslani ---*/
if(isset($_POST['text'])){

  //nacteni promennych
  $text=_stripSlashes($_POST['text']);
  $subject=_stripSlashes($_POST['subject']);
  $sender=$_POST['sender'];
  if(isset($_POST['receivers'])){$receivers=$_POST['receivers'];}else{$receivers=array();}
  $ctype=$_POST['ctype'];
  $maillist=_checkboxLoad("maillist");
  
  //kontrola promennych
  $errors=array();
  if($text=="" and !$maillist){$errors[]=$_lang['admin.other.massemail.notext'];}
  if(@count($receivers)==0){$errors[]=$_lang['admin.other.massemail.noreceivers'];}
  if($subject=="" and !$maillist){$errors[]=$_lang['admin.other.massemail.nosubject'];}
  if(!_validateEmail($sender) and !$maillist){$errors[]=$_lang['admin.other.massemail.badsender'];}
  
  if(count($errors)==0){
  
    //sestaveni casti sql dotazu - 'where'
    $groups=_sqlWhereColumn("`group`", implode("-", $receivers));

    //vytvoreni pole s ID prijemcu
    $query=mysql_query("SELECT email FROM `"._mysql_prefix."-users` WHERE massemail=1 AND (".$groups.")");
    $counter=0; $packet_id=0; $receivers_packets=array();
    while($item=mysql_fetch_array($query)){
      $receivers_packets[$packet_id][]=$item['email'];
      if($counter==10){$counter=0; $packet_id++;}else{$counter++;}
    }

    //typ obsahu
    if($ctype==1){$ctype="text/plain";}
    else{$ctype="text/html";}
    
    //hlavicky
    $headers="Content-Type: ".$ctype."; charset=utf-8\n".(_mailerusefrom?"From: ".mb_substr($sender, 0, mb_strpos($sender, "@"))." <".$sender.">":"Reply-To: ".$sender."")."\n";
    
    if(!$maillist){
      //odeslani emailu
      $done=0;
      foreach($receivers_packets as $packet){

        //seznam emailovych adres
        $emails="";
        $emails_counter=0;
          foreach($packet as $item){
            $emails.=$item.",";
            $emails_counter++;
          }
        $emails=trim($emails, ",");

        //odeslani emailu
        if(@mail(null, $subject, $text, "Bcc: ".$emails."\n".$headers)){$done+=$emails_counter;}

      }

      //zprava
      if($done!=0){$output.=_formMessage(1, str_replace("*counter*", $done, $_lang['admin.other.massemail.send'])); $_POST=array();}
      else{$output.=_formMessage(2, $_lang['admin.other.massemail.noreceiversfound']);}
    }
    else{
      //vypis adres
      $emails="";
      $emails_counter=0;
      foreach($receivers_packets as $packet){
        foreach($packet as $item){
          $emails.=$item.",";
          $emails_counter++;
        }
      }
      $emails=trim($emails, ",");
      
      if($emails_counter!=0){$output.=_formMessage(1, "<textarea class='areasmall' rows='9' cols='33' name='list'>"._htmlStr($emails)."</textarea>"); $_POST=array();}
      else{$output.=_formMessage(2, $_lang['admin.other.massemail.noreceiversfound']);}
    }

  }
  else{
  $output.=_formMessage(2, _eventList($errors, 'errors'));
  }

}

/*--- vystup ---*/

$output.="
<br />
<form class='cform' action='index.php?p=other-massemail' method='post'>
<table>

<tr>
<td class='rpad'><strong>".$_lang['admin.other.massemail.sender']."</strong></td>
<td><input type='text' name='sender'"._restorePostValue("sender")." class='inputbig' /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['posts.subject']."</strong></td>
<td><input type='text' name='subject' class='inputbig'"._restorePostValue("subject")." /></td>
</tr>

<tr valign='top'>
<td class='rpad'><strong>".$_lang['admin.other.massemail.receivers']."</strong></td>
<td>"._tmp_authorSelect("receivers", -1, "1", "selectbig", null, true, 4)."</td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['admin.other.massemail.ctype']."</strong></td>
<td>
  <select name='ctype' class='selectbig'>
  <option value='1'>".$_lang['admin.other.massemail.ctype.1']."</option>
  <option value='2'".((isset($_POST['ctype']) and $_POST['ctype']==2)?" selected='selected'":'').">".$_lang['admin.other.massemail.ctype.2']."</option>
  </select>
</td>
</tr>

<tr valign='top'>
<td class='rpad'><strong>".$_lang['admin.other.massemail.text']."</strong></td>
<td><textarea name='text' class='areabig' rows='9' cols='94'>"._restorePostValue("text", null, true)."</textarea></td>
</tr>

<tr><td></td>
<td><input type='submit' value='".$_lang['global.send']."' />&nbsp;&nbsp;<label><input type='checkbox' name='maillist' value='1'"._checkboxActivate(_checkboxLoad("maillist"))." /> ".$_lang['admin.other.massemail.maillist']."</label></td>
</tr>

</table>
</form>
";


?>