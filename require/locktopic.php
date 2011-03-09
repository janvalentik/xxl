<?php
/*--- kontrola jadra ---*/
if(!defined('_core')) {
    exit;
}

/*--- pripava promennych ---*/
$message = "";
$continue = false;
$scriptbreak = false;
$backlink = _indexroot;
$unlock = '';
if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = mysql_query("SELECT id,author,time,subject,locked FROM `"._mysql_prefix."-posts` WHERE id=".$id." AND type=5 AND xhome=-1");
    if(mysql_num_rows($query) != 0) {
        $query = mysql_fetch_assoc($query);
        if(_postAccess($query) && _loginright_locktopics) {
            
            $continue = true;
            $backlink = "index.php?m=topic&amp;id=".$query['id'];
            if($query['locked']) $unlock = '2';

        }
    }
}

/*--- ulozeni ---*/
if($continue && isset($_POST['doit'])) {

    mysql_query("UPDATE `"._mysql_prefix."-posts` SET locked=".(($query['locked'] == 1) ? 0 : 1)." WHERE id=".$id);
    $message = _formMessage(1, $_lang['mod.locktopic.ok'.$unlock]);
    $continue = false;
    $scriptbreak = true;

}

/*--- vystup ---*/

//titulek
if(_template_autoheadings == 1) {
    $module = "<h1>".$_lang['mod.locktopic'.$unlock]."</h1><div class='hr'><hr /></div>";
} else {
    $module = "";
}

//zpetny odkaz
$module .= "<p><a href='".$backlink."'>&lt; ".$_lang['global.return']."</a></p>";

// zprava
$module .= $message;

//formular
if($continue) {

    $furl = 'index.php?m=locktopic&amp;id='.$id;

    $module .= '
<form action="'.$furl.'" method="post">
'._formMessage(2, sprintf($_lang['mod.locktopic.text'.$unlock], $query['subject'])).'
<input type="submit" name="doit" value="'.$_lang['mod.locktopic.submit'.$unlock].'" />
</form>
';

} else {
    /*neplatny vstup*/
    if(!$scriptbreak) {
        $module .= _formMessage(3, $_lang['global.badinput']);
    }
}

?>