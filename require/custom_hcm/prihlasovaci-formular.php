<?php
if (_loginindicator==0){
$output.="<form action='./remote/login.php' name='login_form' onsubmit=\"if(login_form.username.value=='' || login_form.password.value==''){_sysAlert(1); return false;}\" method='post'>";
$output.="<table><tr><td>".$GLOBALS['_lang']['xxl.name_form']."</td><td> ";
$output.="<input maxlength='24' name='username' value='".$GLOBALS['_lang']['xxl.name_form']."' onfocus=\"if(this.value=='".$GLOBALS['_lang']['xxl.name_form']."'){this.value=''}\" onblur=\"if(this.value==''){this.value='".$GLOBALS['_lang']['xxl.name_form']."'}\"/>";
$output.="<input type='hidden' value='mod' name='redir' />";
$output.="</td></tr><tr><td>".$GLOBALS['_lang']['xxl.pass_form']."</td><td>";
$output.="<input  type='password' value='' name='password' /></td></tr><tr><td>&nbsp;</td><td>";
$output.="<input type='submit'  value='".$GLOBALS['_lang']['xxl.login_form']."' /><label> <input name='persistent' value='1' type='checkbox'> ".$GLOBALS['_lang']['xxl.persit_form']."</label></td></tr></table>";
$output.="</form><ul>";
$output.="<li><a href='index.php?m=reg'>".$GLOBALS['_lang']['xxl.reg_form']."</a> »</li>";
$output.="<li><a href='index.php?m=lostpass'>".$GLOBALS['_lang']['xxl.lostpass_form']."</a> »</li>";
$output.="</ul>";
}
else{
$output.= _templateUserMenu (true);
}
?> 