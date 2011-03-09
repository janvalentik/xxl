<?php

/*--- incializace jadra ---*/
define('_indexroot', '../');
require(_indexroot."core.php");

/*--- prihlaseni ---*/
_checkKeys('_POST', array('username', 'password', 'redir'));
$result=0;
$username="";
if(!_loginindicator){

  if(_iplogCheck(1)){

    //nacteni promennych
    $username=_safeStr(_anchorStr($_POST['username'], false));
    $password=$_POST['password'];
    $persistent=_checkboxLoad('persistent');

    //nalezeni uzivatele
    $query=mysql_query("SELECT * FROM `"._mysql_prefix."-users` WHERE username='".$username."'");

      if(mysql_num_rows($query)!=0){

        $query=mysql_fetch_array($query);
        $password=_md5Salt($password, $query['salt']);
        $groupblock=mysql_fetch_array(mysql_query("SELECT blocked FROM `"._mysql_prefix."-groups` WHERE id=".$query['group']));
        if($query['blocked']==0 and $groupblock['blocked']==0){
          if($password==$query['password']){
          
            //vytvoreni saltu pro verze starsi nez 7.3.0
            if($query['salt']==""){
              $newpass=_md5Salt($_POST['password']);
              mysql_query("UPDATE `"._mysql_prefix."-users` SET password='".$newpass[0]."', salt='".$newpass[1]."' WHERE id=".$query['id']);
              $password=$newpass[0];
            }
            
            //navyseni poctu prihlaseni
            mysql_query("UPDATE `"._mysql_prefix."-users` SET logincounter=logincounter+1 WHERE id=".$query['id']);
            
            //zaslani cookie pro stale prihlaseni
            if($persistent){
                setcookie(_sessionprefix."persistent_key", $query['id']."-".md5($password._userip), (time()+2592000), "/");
            }
          
            //ulozeni dat pro session
            $_SESSION[_sessionprefix."user"]=$query['id'];
            $_SESSION[_sessionprefix."password"]=$password;
            $_SESSION[_sessionprefix."ip"]=_userip;
            $result=1;
          
          }
          else{
          _iplogUpdate(1);
          }
        }
        else{
        $result=2;
        }

      }

  }
  else{
  $result=5;
  }

}

/*--- presmerovani ---*/
if($result!=1 and $_POST['redir']==-1){$_POST['redir']="mod";}
switch($_POST['redir']){

  case "admin":
  $url=_indexroot."admin/index.php?_mlr=".$result;
  if($result!=1){$url=_addFdGetToLink($url, array("username"=>$username));}
  header("location: ".$url);
  break;

  case "mod":
  $url=_indexroot."index.php?m=login&_mlr=".$result;
  if($result!=1){$url=_addFdGetToLink($url, array("username"=>$username));}
  header("location: ".$url);
  break;

  default:
  _returnHeader();
  break;

}

?>