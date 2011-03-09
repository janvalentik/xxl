<?php
/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}



/*--- definice funkce modulu ---*/
function _HCM_jv_newsletter(){

  if(isset($_POST['newsletter'])){
    $ip=$_SERVER["REMOTE_ADDR"];
    $email=_safeStr($_POST["newsletter"]);
    $newid=_getNewID('users');
    $message=$GLOBALS['_lang']['xxl.admin.newsletter.hcm.send.ok'];
    if (_validateEmail($email)) {
	    $query=mysql_query("SELECT * FROM `"._mysql_prefix."-users` WHERE email='".$email."' LIMIT 1");
      $user=mysql_fetch_array($query);
      if (mysql_num_rows($query)==0) {
  	    mysql_query("INSERT INTO `"._mysql_prefix."-users` (id,ip,email,massemail,`group`) VALUES ('".$newid."','".$ip."','".$email."','1','6')");
        if (mysql_error()) {
      	 $message=$GLOBALS['_lang']['xxl.admin.newsletter.hcm.send.no'];
        }
      }
      else{
      	if($user['username']!=''){$message=$GLOBALS['_lang']['xxl.admin.newsletter.hcm.send.exist'].$user['username'];}
        else{$message=$GLOBALS['_lang']['xxl.admin.newsletter.hcm.send.existnoname'];}
      }
    }else{
      $message=$GLOBALS['_lang']['admin.users.edit.bademail'];
    }

    $return.="<script type=\"text/javascript\">/* <![CDATA[ */alert (\"".$message."\");/* ]]> */</script><noscript><div class=\"message2\">".$message."</div></noscript>";
  }

  $return.="<form action='' method='post'>
            <input type='text' name='newsletter' value='".$GLOBALS['_lang']['xxl.admin.newsletter.hcm.1']."' onfocus=\"if(this.value=='".$GLOBALS['_lang']['xxl.admin.newsletter.hcm.1']."'){this.value=''}\" onblur=\"if(this.value==''){this.value='".$GLOBALS['_lang']['xxl.admin.newsletter.hcm.1']."'}\" />
            <input type='submit'  value='".$GLOBALS['_lang']['xxl.admin.newsletter.hcm.send']."' name='vlozeni_emailu' />
            </form>";

  return $return;
}
?>