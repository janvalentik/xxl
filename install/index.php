<?php

/*-- inicializace jadra --*/
define('_tmp_litemode', 1);
define('_indexroot', '../');
require(_indexroot."core.php");
$msg="";

/*-- instalace databaze --*/
if(isset($_POST['license'])){

  //nacteni promennych
  $url=_removeSlashesFromEnd($_POST['url']);
  $pass=$_POST['pass'];
  $pass2=$_POST['pass2'];
  $email=$_POST['email'];
  $rewrite=_checkboxLoad("rewrite");
  $title=_safeStr(_htmlStr($_POST['title']));
  $descr=_safeStr(_htmlStr($_POST['descr']));

  //kontrola promennych
  $errors=array();
  if($url=="" or $url=="http://"){$errors[]="Nebyla zadána adresa serveru.";}
  if($pass=="" or $pass2==""){$errors[]="Nebylo vyplněno heslo.";}
  if($pass!=$pass2){$errors[]="Zadaná hesla nejsou shodná.";}
  if(!_validateEmail($email)){$errors[]="E-mailová adresa není platná.";}

  //instalace
  if(count($errors)==0){

    //smazani existujicich tabulek
    if($rewrite){
      $tables=array('articles', 'boxes', 'download', 'emaily', 'groups', 'images', 'iplog', 'messages', 'polls', 'posts', 'root', 'sboxes', 'settings', 'statssearch', 'users');
      foreach($tables as $table){mysql_query("DROP TABLE IF EXISTS `"._mysql_prefix."-".$table."`");}
    }

    //vytvoreni nove struktury
    $pass=_md5Salt($pass);
    $url=_safeStr(_htmlStr($url));
    $email=_safeStr($email);
    require("data.php");

    //zprava
    if($sql_error==false){$msg="<font style='color:green;'>Zdá se, že databáze byla úspěšně nainstalována. Smažte adresář <em>install</em> ze serveru.</font>";}
    else{$msg="Během vyhodnocování SQL dotazů nastala chyba:<hr />"._htmlStr($sql_error);}

  }
  else{
    $msg=_eventList($errors);
  }
}

/*-- zprava --*/
if($msg!=""){
$msg="<div id='message'><div>".$msg."</div></div><br />";
}

//vlozeni zacatku hlavicky
require("../require/headstart.php");

?>
<title>Instalace databáze SunLight CMS <?php echo _systemversion; ?></title>

<style type="text/css">
body {font-family: Verdana, Helvetica, sans-serif; font-size: 13px; color: #000000; background-color: #ff6600; text-align: center; margin: 10px 0px 10px 0px;}
p,ol,ul {line-height: 24px;}
a {color: #ff6600}
h1 {font-size: 22px; margin: 0px 0px 15px 0px; padding: 0px 0px 8px 0px; border-bottom: 3px solid #ff6600;}
h2 {font-size: 17px;}
td {padding-right: 10px;}
#container {width: 980px; margin: 0 auto; text-align: left;}
#container div {padding: 10px 10px 25px 10px; background-color: #fff8f5; border: 1px solid #ff9859;}
#message {width: 980px; margin: 0 auto; text-align: left;}
#message div {padding: 10px; background-color: #ffffcc; border: 1px solid #f0f0f0; font-weight: bold;}
</style>

<script type="text/javascript">
function _checkForm(){
url=document.form.url.value;
pass=document.form.pass.value;
pass2=document.form.pass2.value;
email=document.form.email.value;
license=document.form.license.checked;
rewrite=document.form.rewrite.checked;

  if(url=="" || url=="http://"){alert("Nebyla zadána adresa serveru."); return false;}
  if(pass=="" || pass2==""){alert("Nebylo vyplněno heslo."); return false;}
  if(pass!=pass2){alert("Zadaná hesla nejsou shodná."); return false;}
  if(email=="" || email=="@"){alert("Nebyla zadána e-mailová adresa."); return false;}
  if(!license){alert("Musíte souhlasit s licencí pro spuštění instalace."); return false;}
  if(rewrite){return confirm("Opravdu chcete přepsat existující tabulky se stejnými jmény?");}

}

function _autoFill(){
eval("loc=\""+document.location+"\";");
if(loc.substring(loc.length-1, loc.length)=="/"){loc=loc.substring(0, loc.length-1);}

  //odrezani posledniho adresare
  spos=0;
  for(x=loc.length-1; x>=0; x-=1){
    if(loc.substring(x, x+1)=="/"){spos=x; break;}
  }
  
  //prepsani hodnoty
  if(spos!=0){
  loc=loc.substring(0, spos);
  document.form.url.value=loc;
  }


}
</script>

</head>

<body onload="_autoFill();">

<?php echo $msg; ?>

<form action="./" method="post" name="form">
<div id="container">
<div>


  <h1>Instalace databáze SunLight CMS <?php echo _systemversion; ?></h1>
  <p>Tato stránka slouží k instalaci databáze (vytvoření tabulek) pro redakční systém SunLight CMS <?php echo _systemversion; ?>. Před spuštěním instalace se ujistěte, že máte nastaveny správné přístupové údaje v souboru <em>access.php</em> a že není databáze již nainstalovaná (pro více instalací na jedné databázi je potřeba změnit prefix)! Pokud chcete databázi přeinstalovat, aktivujte možnost <em>Přepsat tabulky</em>.</p>


  <hr />
  <h2>Práva adresářů</h2>
  <p>Před započetím instalace je potřeba nastavit práva zapisu pro adresáře a soubory uvedené v tabulce, jinak systém nemusí vždy fungovat správně.</p>
  <table>
  <?php
  $dir_array=array('admin/modules/tinymce/plugins/ajaxfilemanager/session','admin/modules/tinymce/plugins/ajaxfilemanager/session/gc_log.ajax.php','admin/modules/tinymce/plugins/ajaxfilemanager/session/gc_counter.ajax.php','avatars', 'upload', 'upload/_thumbs', 'upload/download',  'upload/img', 'upload/img/article', 'upload/img/perex', 'sitemap.xml');
  $dir_error=array();
  foreach ($dir_array as $key=>$value) {
    $dir_error[]=(is_writable(_indexroot.$value) ? 1:0);
  	echo "<tr><td>".$value."</td><td><img src='../admin/images/icons/".(is_writable(_indexroot.$value) ? "new.gif":"delete.gif")."' /></td></tr>";
  }
  ?>
  </table>
  <?php
if (in_array(0,$dir_error)){echo "<div style='background-color:red;color:white;padding:3px;text-align:center;'><h2>POZOR VŠECHNY POTŘEBNÉ ADRESÁŘE A SOUBORY NEMAJÍ PRÁVA ZAPISU!!!</h2></div>";}else{echo "<div style='background-color:green;color:white;padding:3px;text-align:center;'><h2>PRÁVA ZAPISU U VŠECH ADRESÁŘŮ A SOUBORŮ JSOU V POŘÁDKU</h2></div>";}
  ?>
  <hr />

  <h2>Licenční ujednání</h2>
  <p>Použití a redistribuce redakčního systému SunLight CMS (dále jen <q>systém</q>), v původním i upraveném tvaru, jsou povoleny za následujících podmínek:</p>
  
  <ul>
    <li>administrace systému musí obsahovat viditelný nezměněný copyright s původními odkazy</li>
    <li>je povoleno upravovat zdrojové kódy systému, ne však zahrnovat (kopírovat) jejich části do jiných systémů</li>
    <li>není povoleno vydávat systém za vlastní dílo</li>
  </ul>
  
  <h3>Zřeknutí se odpovědnosti</h3>
  <p>Tento systém je poskytován autorem <q>jak stojí a leží</q> a jakékoliv výslovné nebo předpokládané záruky včetně, ale nejen, předpokládaných obchodních záruk a záruky vhodnosti pro jakýkoliv účel jsou popřeny. Autor nebude v žádném případě odpovědný za jakékoliv přímé, nepřímé, náhodné, zvláštní, příkladné nebo vyplývající škody (včetně, ale nejen, škod vzniklých narušením dodávek zboží nebo služeb; ztrátou použitelnosti, dat nebo zisků; nebo přerušením obchodní činnosti) jakkoliv způsobené na základě jakékoliv teorie o zodpovědnosti, ať už plynoucí z jiného smluvního vztahu, určité zodpovědnosti nebo přečinu (včetně nedbalosti) na jakémkoliv způsobu použití tohoto systému, i v případě, že byl autor upozorněn na možnost takových škod.</p>
  
  <h3>Platnost a změna znění licence</h3>
  <p>Tato licence je platná pro všechny verze systému. Autor si vyhrazuje právo na změnu znění licenčního ujednání. Změny vstupují v platnost dnem uveřejnění na oficiálních stránkách.</p>
  <br />

  <label><input type="checkbox" name="license" value="1" /> souhlasím s licenčním ujednáním</label>
  <br /><br />


  <hr />


  <h2>Nastavení instalace</h2>
  <table>
  
  <tr>
  <td><strong>Adresa serveru</strong></td>
  <td><input type="text" size="50" name="url" value="http://" /></td>
  <td><small>adresa ke kořenovému adresáři systému v absolutním tvaru (důležité)</small></td>
  </tr>

  <tr>
  <td><strong>Přístupové heslo</strong></td>
  <td><input type="password" size="50" name="pass" /></td>
  <td><small>heslo pro účet hlavního administrátora</small></td>
  </tr>
  
  <tr>
  <td><strong>Přístupové heslo</strong></td>
  <td><input type="password" size="50" name="pass2" /></td>
  <td><small>heslo pro účet hlavního administrátora (kontrola)</small></td>
  </tr>

  <tr>
  <td><strong>E-mail</strong></td>
  <td><input type="text" size="50" name="email" value="@" /></td>
  <td><small>e-mail pro účet hlavního administrátora (pro obnovování ztraceného hesla)</small></td>
  </tr>
  
  <tr>
  <td><strong>Titulek stránek</strong></td>
  <td><input type="text" size="50" name="title" value="Lorem ipsum" /></td>
  <td><small>titulek stránek</small></td>
  </tr>

  <tr>
  <td><strong>Popis stránek</strong></td>
  <td><input type="text" size="50" name="descr" value="bez popisu" /></td>
  <td><small>krátký popis stránek</small></td>
  </tr>

  <tr>
  <td><strong>Přepsat tabulky</strong></td>
  <td><input type="checkbox" name="rewrite" value="1" /></td>
  <td><small>tato volba způsobí, že existující tabulky se stejnými jmény budou přepsány</small></td>
  </tr>
  
  </table>
  <br />
  
  <input type="submit" value="Spustit instalaci databáze &gt;" onclick="return _checkForm();" />


</div>
</div>
</form>

</body>
</html>