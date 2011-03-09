<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*-- akce --*/
session_start();
$result=0;

  //pole konstant opravneni
  $rights_array=_getRightsArray();
  
  //pouziti cookie persistentniho prihlaseni, pokud existuje
  $persistent_cookie_found=false;
  if(isset($_COOKIE[_sessionprefix."persistent_key"])) {
    //nacist data z cookie nebo ji znicit
    if(!isset($_SESSION[_sessionprefix."persistent_key_destroy"])){
        //nacist data
        $persistent_cookie=$_COOKIE[_sessionprefix."persistent_key"];
        $persistent_cookie=explode("-", $persistent_cookie);
        if(count($persistent_cookie)==2){
            $persistent_cookie[0]=intval($persistent_cookie[0]);
            $persistent_cookie_found=true;
        }
    }else{
        //zlikvidovat cookie
        setcookie(_sessionprefix."persistent_key", "", (time()-3600), "/");
        $_SESSION[_sessionprefix."persistent_key_destroy"]=null;
        unset($_SESSION[_sessionprefix."persistent_key_destroy"]);
    }
  }

  //kontrola existence session
  if($persistent_cookie_found or (isset($_SESSION[_sessionprefix."user"]) and isset($_SESSION[_sessionprefix."password"]) and isset($_SESSION[_sessionprefix."ip"]))){

    //pouziti cookie pro nastaveni dat session (pokud neexistuji)
    $persistent_cookie_used=false;
    $persistent_cookie_bad=false;
    if($persistent_cookie_found and _iplogCheck(1) and !(isset($_SESSION[_sessionprefix."user"]) and isset($_SESSION[_sessionprefix."password"]) and isset($_SESSION[_sessionprefix."ip"]))){
        $persistent_cookie_bad=true;
        $uquery=mysql_query("SELECT * FROM `"._mysql_prefix."-users` WHERE id=".$persistent_cookie[0]);
        if(mysql_num_rows($uquery)!=0){
            $uquery=mysql_fetch_array($uquery);
            $persistent_cookie_used=true;
            if($persistent_cookie[1]==md5($uquery['password']._userip)){
                //platna cooke
                $_SESSION[_sessionprefix."user"]=$persistent_cookie[0];
                $_SESSION[_sessionprefix."password"]=$uquery['password'];
                $_SESSION[_sessionprefix."ip"]=_userip;
                $persistent_cookie_bad=false;
            } else {
                //neplatna cookie - zaznam v ip logu
                _iplogUpdate(1);
            }
        }
    }

    //kontroly
    $continue=false;
    if(!$persistent_cookie_bad){
        $id=intval($_SESSION[_sessionprefix."user"]);
        $pass=$_SESSION[_sessionprefix."password"];
        $ip=$_SESSION[_sessionprefix."ip"];
        if(!$persistent_cookie_used){$uquery=mysql_query("SELECT * FROM `"._mysql_prefix."-users` WHERE id=".$id);}
    
          if($persistent_cookie_used or mysql_num_rows($uquery)!=0){
              if(!$persistent_cookie_used){$uquery=mysql_fetch_array($uquery);}
              $gquery=mysql_fetch_array(mysql_query("SELECT * FROM `"._mysql_prefix."-groups` WHERE id=".$uquery['group']));
              if($uquery['password']==$pass and $uquery['blocked']==0 and $gquery['blocked']==0 and $ip==_userip){
                $continue=true;
              }
          }
    }

      //zabiti neplatne session
      if($continue!=true){
      _userLogout();
      if(_administration!=1){header("location: "._indexroot."index.php?m=login&_mlr=3");}
      else{header("location: "._indexroot."admin/index.php?_mlr=3");}
      }

    //definovani konstant
    if($continue){
    
    $result=1;
    define('_loginid', $uquery['id']);
    define('_loginname', $uquery['username']);
      if($uquery['publicname']!=""){define('_loginpublicname', $uquery['publicname']);}
      else{define('_loginpublicname', $uquery['username']);}
    define('_loginemail', $uquery['email']);
    define('_loginwysiwyg', $uquery['wysiwyg']);
    define('_loginlanguage', $uquery['language']);
    define('_logincounter', $uquery['logincounter']);

      //konstanty skupiny
      define('_loginright_group', $gquery['id']);
      define('_loginright_groupname', $gquery['title']);

            //zvyseni levelu pro superuzivatele
            if($uquery['levelshift']==1){$gquery['level']+=1;}

        foreach($rights_array as $item){
        define('_loginright_'.$item, $gquery[$item]);
        }

      //zaznamenani casu aktivity
      mysql_query("UPDATE `"._mysql_prefix."-users` SET activitytime='".time()."', ip='".$_SERVER['REMOTE_ADDR']."' WHERE id="._loginid);

    }

  }
  else{

  //konstanty hosta
  define('_loginid', -1);
  define('_loginname', '');
  define('_loginpublicname', '');
  define('_loginemail', '');
  define('_loginwysiwyg', 0);
  define('_loginlanguage', '');
  define('_logincounter', 0);

    //konstanty skupiny
    $gquery=mysql_fetch_array(mysql_query("SELECT * FROM `"._mysql_prefix."-groups` WHERE id=2"));
    define('_loginright_group', $gquery['id']);
    define('_loginright_groupname', $gquery['title']);

      foreach($rights_array as $item){
      define('_loginright_'.$item, $gquery[$item]);
      }
  }

  //konstanta pro indikaci prihlaseni
  define('_loginindicator', $result);

?>