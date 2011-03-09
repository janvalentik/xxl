<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- vystup ---*/
$output.="
<br />
<fieldset>
<legend>".$_lang['admin.other.backup.backup']."</legend>
<form class='cform' action='remote/backup_db.php' method='post'>
<p>".$_lang['admin.other.backup.backup.p']."</p>
<input type='submit' value='".$_lang['global.do']."' />&nbsp;
<label><input type='checkbox' name='compress' value='1'"._checkBoxActivate(function_exists("gzdeflate"))._inputDisable(function_exists("gzdeflate"))." /> ".$_lang['admin.other.backup.compress']."</label>
</form>
</fieldset>
<br />

<fieldset>
<legend>".$_lang['admin.other.backup.restore']."</legend>
<form class='cform' action='remote/restore_db.php' method='post' enctype='multipart/form-data'>
<p>".$_lang['admin.other.backup.restore.p']."</p>
<input type='file' name='backup' /> <input type='submit' value='".$_lang['global.do']."' onclick='return _sysConfirm();' /><br /><br />
</form>
</fieldset>
";

?>