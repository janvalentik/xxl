<?php

/*--- incializace jadra ---*/
if(!defined('_core')){
define('_indexroot', '../');
require(_indexroot."core.php");
}

/*--- odhlaseni a presmerovani ---*/
_userLogout();
_returnHeader();

?>