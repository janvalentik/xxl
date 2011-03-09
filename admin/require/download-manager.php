<?php



/*--- kontrola jadra ---*/

if(!defined('_core')){exit;}

$folder = "../upload/download";

/*---------------------------------Ruční přidávání-----------------------------------------------------*/



$output.="<fieldset><legend>".$_lang['xxl.admin.download.manager.prefix']."</legend>";
$output.="<form action='' method='post' name='formular'><table cellspacing='10'> <tr><td><strong> ".$_lang['xxl.admin.download.manager.link']."</strong></td> <td> "._url."/upload/download/<input type='text' name='cesta' class='inputbig' value='"._safeStr($_POST['cesta'])."'/></td></tr>
          <tr><td><strong> ".$_lang['xxl.admin.download.manager.public']."</strong></td> <td><input type='checkbox' checked='checked' name='public' value='1' /></td></tr>
         <tr><td></td><td><input type='submit' value='".$_lang['xxl.admin.download.manager.ok']."' name='formular' /></td></tr> </table></form> <p>".$_lang['xxl.admin.download.manager.text']."</p></fieldset>";





/*--- safeStr ---*/

$cesta = _anchorStr($_POST["cesta"]);

@rename($folder."/".$_POST["cesta"], $folder."/".$cesta);



/*--- Podmínky---*/

if(isset($_POST['formular'])){

    if(!empty($_POST["cesta"])) {



/*--- Zápis---*/

    if (mysql_query("INSERT INTO `"._mysql_prefix."-download` VALUES (null,'0','".$cesta."','"._checkboxload('public')."')"))

        $output.=_formMessage(1, "".$_lang['xxl.admin.download.manager.add']."");} 



    else{

    $output.=_formMessage(2, "".$_lang['xxl.admin.download.manager.no']."");}   

}





/*---------------------------------Automatické přidávání-----------------------------------------------------*/



$output.="<fieldset><legend>".$_lang['xxl.admin.download.manager.upload.5']."</legend>";

$output.="<form action='' method='post' enctype='multipart/form-data'>
<table cellspacing='10'>
<tr><td><strong> ".$_lang['admin.fman.file']."</strong></td> <td><input type='file' name='manager-down' /></td></tr>
<tr><td><strong> ".$_lang['xxl.admin.download.manager.public']."</strong></td> <td><input type='checkbox' checked='checked' name='public' value='1' /></td></tr>
<tr><td>&nbsp;</td><td><input type='submit' value='".$_lang['xxl.admin.download.manager.upload.1']."' /></td></tr></table></form>";

$output.="<p>".$_lang['xxl.admin.download.manager.upload.6']."</p>";

$output.="</fieldset>";

           

/*--- Sestavení aif, copy ---*/

if (isset($_FILES['manager-down'])){

    if($_FILES['manager-down']['type']=="application/octet-stream"){  // Jsi PHP? Táhni.

          $output.=_formMessage(2, "".$_lang['xxl.admin.download.manager.upload.2']."");

    } 

    else{           

      $type_rename = _anchorStr($_FILES['manager-down']['name']);

      $target = $folder."/".$type_rename;

      $type_name = $_FILES['manager-down']['tmp_name'];

      if (!file_exists($target)) {

        $copy = move_uploaded_file($type_name, $target);

        chmod ($target, 0777);

        mysql_query("INSERT INTO `"._mysql_prefix."-download` VALUES (null,'0','".$type_rename.",'"._checkboxload('public')."')");

        $output.=_formMessage(1, "".$_lang['xxl.admin.download.manager.upload.3']."");

      }else{

      $output.=_formMessage(2, "".$_lang['xxl.admin.download.manager.upload.4']."");

    }

  }  

}



// --- Mazání ---

if (isset($_POST['mazat'], $_POST['selected'])) {

  foreach ($_POST['path'] as $key=>$value) {

    if (in_array($key,array_keys($_POST['selected']))){@unlink($folder.'/'.$value);}}

    mysql_query ("DELETE FROM `"._mysql_prefix."-download` WHERE id in(".implode(',',array_keys($_POST['selected'])).")");}

// --- Kontrola prazdneho sloupce ---

    $row = mysql_query("SELECT *  FROM `"._mysql_prefix."-download`"); 

    if (!mysql_num_rows($row)) {

    $output.=_formMessage(2, "".$_lang['xxl.admin.nowhile']."");}

    else {

$output.="<fieldset><legend>".$_lang['xxl.admin.download.manager.delete']."</legend>";

$output.="<form action='' method='post' name='mazat'><table><tr>";

$output.="<td class=\"rpad\">&nbsp;</td>";

$output.="<td class=\"rpad\"><strong>".$_lang['xxl.admin.download.manager.type']."</strong></td>";

$output.="<td class=\"rpad\"><strong>".$_lang['xxl.admin.download.manager.link_down']."</strong></td>";

$output.="<td class=\"rpad\"><strong>".$_lang['xxl.admin.download.manager.ratted_num']."</strong></td>";

$output.="<td class=\"rpad\"><strong>".$_lang['xxl.admin.download.manager.anchor_html']."</strong></td>";

$output.="<td class=\"rpad\"><strong>".$_lang['xxl.admin.download.manager.hcm']."</strong></td>";

$output.="<td class=\"rpad\"><strong>".$_lang['xxl.admin.download.manager.hcm.not_public']."</strong></td></tr>";





/*--- Sestavení ---*/

    $vysledek = mysql_query("SELECT *,ID  FROM `"._mysql_prefix."-download`"); 

    while ($zaznam = mysql_fetch_array($vysledek)) {

    $cesta = $zaznam["cesta"];

    $stazeno = $zaznam["stazeno"];

    $id = $zaznam["id"];



    $output.="<tr>\n

    <td class=\"rpad\"><input type='checkbox' name='selected[$id]' /><input type='hidden' name='path[".$id."]' value='".$cesta."' /></td>\n

    <td class=\"rpad\">".$cesta."</td>\n

    <td class=\"rpad\"> <a href='"._url."/download.php?dl=".$id."'>".$_lang['xxl.admin.download.manager.download']."</a></td>\n

    <td class=\"rpad\"><strong>".$stazeno ." x</strong></td>\n

    <td class=\"rpad\">&lt;a href=&quot;"._url."/download.php?dl=".$id."&quot;&gt;&lt;/a&gt;</td>\n

    <td class=\"rpad\">[hcm]custom,dp,".$id."[/hcm]</td>\n

    <td class=\"rpad\">[hcm]custom,dnp,".$id."[/hcm]</td>\n 

    </tr>";}

    

$output.="<tr><td><input type='submit' name='mazat' value='Smazat' onclick='return _sysConfirm();' /></td></tr></table></form>

</fieldset>";}



if ($mazani) {

    $output.=_formMessage(1, "".$_lang['xxl.admin.download.manager.delete.ok']."");}

?>