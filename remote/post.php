<?php

/*--- incializace jadra ---*/
define('_indexroot', '../');
require(_indexroot."core.php");

/*--- zpracovani ---*/

  /*-- nacteni promennych --*/
  
    //kontrola zvoleni
    _checkKeys('_POST', array('_posttarget', '_posttype', 'text'));
    _checkKeys('_GET', array('_return'));
  
    //jmeno hosta nebo ID uzivatele
    if(_loginindicator){
      $guest="";
      $author=_loginid;
    }
    else{
      if(isset($_POST['guest'])){
        $guest=$_POST['guest'];
        if(mb_strlen($guest)>24){$guest=mb_substr($guest, 0, 24);}
        $guest=_anchorStr($guest, false);
      }
      else{
        $guest="";
      }
      
      $author=-1;
    }
    
    //typ, domov, text
    $posttarget=intval($_POST['_posttarget']);
    $posttype=intval($_POST['_posttype']);
    $text=_safeStr(_htmlStr(_wsTrim(_cutStr($_POST['text'], (($posttype!=4)?3072:255), false))));

    //domovsky prispevek
    if($posttype!=4){
      _checkKeys('_POST', array('_xhome'));
      $xhome=intval($_POST['_xhome']);
    }
    else{
      $xhome=-1;
    }

    //predmet
    if($xhome==-1 and $posttype!=4){
      _checkKeys('_POST', array('subject'));
      $subject=_safeStr(_htmlStr(_wsTrim(_cutStr($_POST['subject'], 22, false))));
    }
    else{
      $subject="";
    }

    //vyplneni prazdnych poli
    if($subject=="" and $xhome==-1 and $posttype!=4){$subject="-";}
    if($guest=="" and !_loginindicator){$guest=$_lang['posts.anonym'];}
    
    //typ 6 je zpracovanim stejny jako 5
    if($posttype==6){$posttype=5;}


  /*-- kontrola cile --*/
  $continue=false;
  switch($posttype){

    //sekce
    case 1:
    $tdata=mysql_query("SELECT public,var1,var3 FROM `"._mysql_prefix."-root` WHERE id=".$posttarget." AND type=1");
      if(mysql_num_rows($tdata)!=0){
      $tdata=mysql_fetch_array($tdata);
        if(_publicAccess($tdata['public']) and $tdata['var1']==1 and $tdata['var3']!=1){
          $continue=true;
        }
      }
    break;

    //clanek
    case 2:
    $tdata=mysql_query("SELECT id,time,confirmed,public,home1,home2,home3,comments,commentslocked FROM `"._mysql_prefix."-articles` WHERE id=".$posttarget);
      if(mysql_num_rows($tdata)!=0){
      $tdata=mysql_fetch_array($tdata);
        if(_articleAccess($tdata)==1 and $tdata['comments']==1 and $tdata['commentslocked']==0){
          $continue=true;
        }
      }
    break;

    //kniha
    case 3:
    $tdata=mysql_query("SELECT public,var1,var3 FROM `"._mysql_prefix."-root` WHERE id=".$posttarget." AND type=3");
      if(mysql_num_rows($tdata)!=0){
      $tdata=mysql_fetch_array($tdata);
        if(_publicAccess($tdata['public']) and _publicAccess($tdata['var1']) and $tdata['var3']!=1){
          $continue=true;
        }
      }

    break;

    //shoutbox
    case 4:
    $tdata=mysql_query("SELECT public,locked FROM `"._mysql_prefix."-sboxes` WHERE id=".$posttarget);
      if(mysql_num_rows($tdata)!=0){
      $tdata=mysql_fetch_array($tdata);
        if(_publicAccess($tdata['public']) and $tdata['locked']!=1){
          $continue=true;
        }
      }
    break;

    //forum
    case 5:
    $tdata=mysql_query("SELECT public,var2,var3 FROM `"._mysql_prefix."-root` WHERE id=".$posttarget." AND type=8");
      if(mysql_num_rows($tdata)!=0){
      $tdata=mysql_fetch_array($tdata);
        if(_publicAccess($tdata['public']) and _publicAccess($tdata['var3']) and $tdata['var2']!=1){
          $continue=true;
        }
      }

    break;

    //galerie
    case 7:
    $tdata=mysql_query("SELECT public,var4,var5 FROM `"._mysql_prefix."-root` WHERE id=".$posttarget." AND type=5");
      if(mysql_num_rows($tdata)!=0){
      $tdata=mysql_fetch_array($tdata);
        if(_publicAccess($tdata['public']) and $tdata['var4']==1 and $tdata['var5']!=1){
          $continue=true;
        }
      }
    break;

  }
  
  /*-- kontrola prispevku pro odpoved --*/
  if($xhome!=-1){
    $continue2=false;
    $tdata=mysql_query("SELECT xhome FROM `"._mysql_prefix."-posts` WHERE id=".$xhome." AND home=".$posttarget);
      if(mysql_num_rows($tdata)!=0){
        $tdata=mysql_fetch_array($tdata);
        if($tdata['xhome']==-1){$continue2=true;}
      }
  }
  else{
    $continue2=true;
  }
  
  /*-- ulozeni prispevku --*/
  if($continue and $continue2 and $text!="" and _captchaCheck()){
    if($posttype==4 or _iplogCheck(5)){
      $newid=_getNewID("posts");
      mysql_query("INSERT INTO `"._mysql_prefix."-posts` (id,type,home,xhome,subject,text,author,guest,time,ip) VALUES (".$newid.",".$posttype.",".$posttarget.",".$xhome.",'".$subject."','".$text."',".$author.",'".$guest."',".time().",'"._userip."')");
      if(!_loginright_unlimitedpostaccess and $posttype!=4){_iplogUpdate(5);}
      $return=1;
      
        //shoutboxy - odstraneni prispevku za hranici limitu
        if($posttype==4){
          $pnum=mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-posts` WHERE type=4 AND home=".$posttarget), 0);
          if($pnum>_sboxmemory){
            $dnum=$pnum-_sboxmemory;
            $dposts=mysql_query("SELECT id FROM `"._mysql_prefix."-posts` WHERE type=4 AND home=".$posttarget." ORDER BY id LIMIT ".$dnum);
              while($dpost=mysql_fetch_array($dposts)){
                mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE id=".$dpost['id']);
              }
          }
        }
      
    }
    else{
      $return=2;
    }
  }
  else{
    $return=0;
  }

/*--- presmerovani ---*/
if($posttype!=4){
  if(!isset($_POST['subject'])){$_POST['subject']="";}
  if($return!=1){$returnurl=_addFdGetToLink(_addGetToLink($_GET['_return'], "replyto=".$xhome, false), array("guest"=>$guest, "subject"=>$_POST['subject'], "text"=>$_POST['text']))."&addpost";}
  else{$returnurl=$_GET['_return'];}
  $_GET['_return']=_addGetToLink($returnurl, "r=".$return."#posts", false);
}
_returnHeader();

?>