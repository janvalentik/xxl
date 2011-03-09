<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- priprava promennych ---*/
if(isset($_GET['a'])){
$a=intval($_GET['a']);
}
else{
$a=1;
}

/*--- modul ---*/
$module="";

  //titulek
  if(_template_autoheadings==1){$module.="<h1>".$_lang['mod.messages']."</h1>\n";}
  
  //menu
  $menu_items=array(
  1=>$_lang['mod.messages.inbox'],
  2=>$_lang['mod.messages.outbox'],
  3=>$_lang['mod.messages.new']
  );
  
  $module.="<p class='messages-menu'>";
    foreach($menu_items as $ma=>$mn){
    if($ma==$a){$mclass=" class='active'";}else{$mclass="";}
    $module.="<a href='index.php?m=messages&amp;a=".$ma."'".$mclass.">".$mn."</a>\n";
    }
  $module.="</p>\n\n";
  
  //obsah
  switch($a){

    /*--- novy vzkaz ---*/
    case 3:
    
      //odeslani vzkazu
      if(isset($_POST['text'])){
      
        //nacteni promennych
        $subject=_safeStr(_htmlStr(mb_substr($_POST['subject'], 0, 32)));
        $receiver=_anchorStr($_POST['receiver'], false);
        $text=_safeStr(_htmlStr(mb_substr($_POST['text'], 0, 4096)));
        
        //kontrola promennych
        $errors=array();
        if($subject==""){$subject=$_lang['mod.messages.nosubject'];}
        if($text==""){$errors[]=$_lang['mod.messages.error.notext'];}

        $receiver=mysql_query("SELECT id,blocked,`group` FROM `"._mysql_prefix."-users` WHERE username='".$receiver."'");
        if(mysql_num_rows($receiver)!=0){
          $receiver=mysql_fetch_array($receiver);
          $groupblock=mysql_fetch_array(mysql_query("SELECT blocked FROM `"._mysql_prefix."-groups` WHERE id=".$receiver['group']));
            if($receiver['blocked']==1 or $groupblock['blocked']==1){$errors[]=$_lang['mod.messages.error.blockedreceiver'];}
          $receiver=$receiver['id'];
        }
        else{
          $errors[]=$_lang['mod.messages.error.badreceiver'];
        }
        
        if(!_iplogCheck(5)){$errors[]=str_replace("*postsendexpire*", _postsendexpire, $_lang['misc.requestlimit']);}
        
        //ulozeni
        if(count($errors)==0){
          $newid=_getNewID("messages");
          mysql_query("INSERT INTO `"._mysql_prefix."-messages` (id,sender,receiver,readed,subject,text,time) VALUES (".$newid.","._loginid.",".$receiver.",0,'".$subject."','".$text."',".time().")");
          $module.=_formMessage(1, $_lang['mod.messages.sent']);
          if(!_loginright_unlimitedpostaccess){_iplogUpdate(5);}
          $_POST=array();
        }
        else{
          $module.=_formMessage(2, _eventList($errors, 'errors'));
        }
      
      }
    
      //predvyplneni prijemce
      if(isset($_GET['receiver'])){$get_receiver=_anchorStr($_GET['receiver'], false);}
      else{$get_receiver="";}

      //predvyplneni predmetu
      if(isset($_GET['subject'])){$get_subject=((mb_substr($_GET['subject'], 0, 3)!="Re:")?"Re: ":'').$_GET['subject'];}
      else{$get_subject="";}

    $module.="
    <div class='hr'><hr /></div>
    "._jsLimitLength(4096, "newmessage", "text")._formOutput(
    "newmessage",
    "index.php?m=messages&amp;a=3",
    array(
      array($_lang['mod.messages.receiver'], "<input type='text' name='receiver' class='inputmedium' maxlength='24'"._restorePostValue("receiver", $get_receiver)." />"),
      array($_lang['posts.subject'], "<input type='text' name='subject' class='inputmedium'"._restorePostValue("subject", $get_subject)." maxlength='32' />"),
      array($_lang['posts.text'], "<textarea class='areamedium' rows='5' cols='33' name='text'>"._restorePostValue("text", null, true)."</textarea>", true)
    ),
    array("text", "receiver"),
    null,
    _getPostformControls("newmessage", "text")
    );
    break;

    /*--- cteni vzkazu ---*/
    case 4:

      //nacteni id
      $continue=false;
      if(isset($_GET['id']) and isset($_GET['r'])){
      if($_GET['r']==1){$getway="receiver"; $author="sender"; $return=1;}else{$getway="sender"; $author="receiver"; $return=2;}
      $id=intval($_GET['id']);
      $message=mysql_query("SELECT * FROM `"._mysql_prefix."-messages` WHERE id=".$id." AND ".$getway."="._loginid);
        if(mysql_num_rows($message)!=0){
        $message=mysql_fetch_array($message);
        if($message['receiver']==_loginid and $message['readed']==0){mysql_query("UPDATE `"._mysql_prefix."-messages` SET readed=1 WHERE id=".$id);}
            // odkaz na odpoved
            if($return==1){
                $sender_name=mysql_query("SELECT `username` FROM `"._mysql_prefix."-users` WHERE id=".$message['sender']);
                if(mysql_num_rows($sender_name)!=0){$sender_name=mysql_fetch_assoc($sender_name); $sender_name=$sender_name['username'];}
                else{$sender_name='';}
                $answerlink="<br /><a href='index.php?m=messages&amp;a=3&amp;receiver=".$sender_name."&amp;subject=".urlencode($message['subject'])."'>".$_lang['mod.messages.answer']." &gt;</a>";
            }
            else{
                $answerlink="";
            }    
        $continue=true;
        }
      }
    
      //obsah
      if($continue){
        $module.="
        <div class='hr'><hr /></div>
        <a href='index.php?m=messages&amp;a=".$return."&amp;page="._resultPagingGetItemPage(_messagesperpage, "messages", "id>".$id." AND ".$getway."="._loginid)."' class='backlink'>&lt; ".$_lang['global.return']."</a>
        <p>
        <strong>".$_lang['mod.messages.'.$author].": "._linkUser($message[$author])."</strong><br />
        <strong>".$_lang['posts.subject'].":</strong> ".$message['subject']."<br />
        <strong>".$_lang['global.posttime'].":</strong> "._formatTime($message['time']).
        $answerlink."
        </p>
        <div class='hr'><hr /></div>
        <p>
        "._parsePost($message['text'])."
        </p>
        ";
      }
      else{
        $module.=_formMessage(2, $_lang['global.badinput']);
      }
    break;

    /*--- prichozi / odchozi vzkazy ---*/
    default:

      //smazani vzkazu
      if($a!=2 and isset($_POST['which'])){

        switch($_POST['which']){

          //oznacene
          case 1:
          foreach($_POST as $key=>$value){
          if($key=="which"){continue;}
          $key=intval($key);
          mysql_query("DELETE FROM `"._mysql_prefix."-messages` WHERE id=".$key." AND receiver="._loginid);
          }
          break;

          //prectene
          case 2:
          mysql_query("DELETE FROM `"._mysql_prefix."-messages` WHERE receiver="._loginid." AND readed=1");
          break;

          //vsechny
          case 3:
          mysql_query("DELETE FROM `"._mysql_prefix."-messages` WHERE receiver="._loginid);
          break;

        }

        //zprava
        $module.=_formMessage(1, $_lang['global.done']);

      //email
       if(@mail($email, $_lang['xxl.act.mail.subject'].$username, "".$_lang['xxl.act.mail.body'].$username.$_lang['xxl.act.mail.body1']._url.$_lang['xxl.act.mail.body2'].$code.$_lang['xxl.act.mail.body3'].$email."</p></div>", "Content-Type: text/html; charset=utf-8\n"));
      }
    
    if($a!=2){$filter="receiver";}else{$filter="sender";}
    $paging=_resultPaging("index.php?m=messages&amp;a=".$a, _messagesperpage, "messages", $filter."="._loginid, "&amp;a=".$a);

    if($a==2){$module.="<p>".$_lang['mod.messages.outbox.p']."</p>";}
    if(_pagingmode==1 or _pagingmode==2){$module.=$paging[0];}
    $module.=(($a!=2)?"<form action='index.php?m=messages&amp;a=".$a."&amp;page=".$paging[2]."' method='post'>":'')."
    <table class='messages-table'>
    <tr><td width='58%'><strong>".$_lang['mod.messages.message']."</strong></td><td width='20%'><strong>".$_lang['mod.messages.'.(($a!=2)?"sender":"receiver")]."</strong></td><td width='22%'><strong>".$_lang['global.posttime']."</strong></td></tr>\n";

      $items=mysql_query("SELECT id,sender,receiver,time,subject,readed FROM `"._mysql_prefix."-messages` WHERE ".$filter."="._loginid." ORDER BY id DESC ".$paging[1]);
      if(mysql_num_rows($items)!=0){
        while($item=mysql_fetch_array($items)){
        if($item['readed']==0){$iclass=" class='notreaded'";}else{$iclass="";}
        $module.="<tr><td>".(($a!=2)?"<input type='checkbox' name='".$item['id']."' value='1' /> ":'')."<a href='index.php?m=messages&amp;a=4&amp;id=".$item['id']."&amp;r=".$a."'".$iclass.">"._cutStr($item['subject'], 24)."</a></td><td>"._linkUser($item[(($a!=2)?"sender":"receiver")], null, false, false, 15)."</td><td>"._formatTime($item['time'])."</td></tr>\n";
        }
      }
      else{
        $module.="<tr><td colspan='3'>".$_lang['mod.messages.nokit']."</td></tr>\n";
      }

    $module.="</table>";
    
    if(_pagingmode==2 or _pagingmode==3){$module.=$paging[0];}
    
    if($a!=2){
    $module.="
    <br />
    <strong>".$_lang['mod.messages.delete'].":</strong>&nbsp;
      <select name='which'>
      <option value='1'>".$_lang['mod.messages.delete.selected']."</option>
      <option value='2'>".$_lang['mod.messages.delete.readed']."</option>
      <option value='3'>".$_lang['mod.messages.delete.all']."</option>
      </select>
    <input type='submit' value='".$_lang['global.do']."' onclick='return _sysConfirm();' />
    </form>
    ";
    }
    
    break;

  }

?>