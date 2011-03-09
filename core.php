<?php

/*---- inicializace jadra ----*/

  //kontrola definovani cesty
  if(!defined('_indexroot')){exit;}

  //casove pasmo
  if(function_exists('putenv')){@putenv("TZ=Europe/Prague");}
  @setlocale(LC_TIME, 'czech', 'utf8', 'cz_CZ');
  
  //hlaseni chyb
  error_reporting(E_ERROR | E_WARNING | E_PARSE);

  //kontrola existence mbstring
  if(!function_exists("mb_substr")){
  _systemFailure("Na serveru není nainstalováno nebo aktivováno rozšíření PHP o <em>mbstring</em> (Multibyte String Functions), které je potřebné pro práci s řetězci v kódování UTF-8.");
  }

  //header a kodovani
  @mb_internal_encoding("UTF-8");
  if(!defined('_tmp_customheader')){$header="Content-Type: text/html; charset=utf-8";}
  else{$header=_tmp_customheader;}
  if($header!=""){header($header);}

  //pristupove udaje
  require(_indexroot."access.php");
  define('_mysql_prefix', $prefix);
  define('_mysql_db', $database);

  //konstanty
  define('_core', '1');
  define('_nl', "\n");
  define('_systemversion', 'XXL 1.0.0');
  define('_sessionprefix', md5($server.$database.$user.$prefix)."-");
  define('_rewrite_ext', '.html');
  define('_userip', $_SERVER['REMOTE_ADDR']);
  if(!defined('_administration')){define('_administration', 0);}
  
  //promenne
  $__image_ext=array("png", "jpeg", "jpg", "gif");
  $__shid_total=0;
  $__hcm_uid=0;
  $__captcha_counter=0;


/*---- vlozeni funkci ----*/

require(_indexroot."require/functions.php");
if(isset($_GET['___identify'])){echo "SunLight CMS "._systemversion; exit;}

/*---- pripojeni k mysql ----*/

$con=@mysql_connect($server, $user, $password);
if($con){$db=@mysql_select_db($database);}
if(!$con or !$db){_systemFailure("Připojení k databázi se nezdařilo. Důvodem je pravděpodobně výpadek serveru nebo chybné přístupové údaje.</p><hr />\n<pre>"._htmlStr(mysql_error())."</pre><hr /><p>Zkontrolujte přístupové údaje v souboru <em>access.php</em>.");}
mysql_query("SET NAMES `utf8`");


/*---- konstanty, jazykovy soubor, motiv, sessions, blokovani IP, ... ----*/
if(!defined('_tmp_litemode')){

  /*---- kontrola existence adresare install ----*/
  if(@file_exists(_indexroot."install") and @is_dir(_indexroot."install")){
  _systemFailure("Na serveru se stále nachází adresář <em>install</em>, který obsahuje skripty pro instalaci databáze. Smažte jej, pokud je databáze již nainstalovaná.");
  }


  /*---- definovani konstant nastaveni ----*/
  $query=mysql_query("SELECT * FROM `"._mysql_prefix."-settings`");
  if(mysql_error()!=false){_systemFailure("Připojení k databázi proběhlo úspěšně, ale dotaz na databázi selhal. Zkontrolujte, zda je databáze správně nainstalovaná.");}
  while($item=mysql_fetch_array($query)){

    switch($item['var']){
    case "template": if(!@file_exists(_indexroot."templates/".$item['val']."/template.php") or !@file_exists(_indexroot."templates/".$item['val']."/config.php")){$item['val']="default";} break;
    case "modrewrite": if(!@file_exists(_indexroot.".htaccess")){$item['val']=0;} break;
    case "wysiwyg": if(!@file_exists(_indexroot."admin/modules/tinymce.slam")){$item['val']=0;} break;
    }

  define('_'.$item['var'], $item['val']);
  }

    //kontrola verze databaze
    if(!defined("_dbversion") or !_checkVersion("database", _dbversion)){
    _systemFailure("Verze nainstalované databáze není kompatibilní s verzí systému. Pokud byl právě aplikován patch pro přechod na novější verzi, pravděpodobně jste zapoměl(a) spustit skript pro aktualizaci databáze.");
    }

    //inicializace sessions
    require(_indexroot."require/session.php");

    //jazyk

      //vychozi jazyk nebo individualni
      if(_loginindicator and _language_allowcustom and _loginlanguage!=""){$language=_loginlanguage;}
      else{$language=_language;}

    $_lang=array();
    $langfile=_indexroot."languages/".$language.".php";
    $langfile_default=_indexroot."languages/default.php";

      if(@file_exists($langfile)){
        require($langfile);
      }
      else{
        if(@file_exists($langfile_default)){require($langfile_default);}
        else{_systemFailure("Zvolený ani přednastavený jazykový soubor nebyl nalezen.");}
      }

        //kontrola verze jazykoveho souboru
        if(!_checkVersion("language_file", $_lang['main.version'])){
        @mysql_query("UPDATE `"._mysql_prefix."-settings` SET val='default' WHERE var='language'");
        _systemFailure("Zvolený jazykový soubor není kompatibilní s verzí systému.");
        }

    //motiv
    $template=_indexroot."templates/"._template."/template.php";
    $template_config=_indexroot."templates/default/config.php";

      if(!@file_exists($template) or !@file_exists($template_config)){
        _systemFailure("Nastavený ani přednastavený motiv <em>default</em> nebyl nalezen.");
      }

    require(_indexroot."templates/"._template."/config.php");

      //kontrola verze motivu
      if(!_checkVersion("template", _template_version) and !_administration){
      _systemFailure("Zvolený motiv není kompatibilní s verzí systému.");
      }


  /*---- kontrola existence souboru patch.php ----*/
  if(@file_exists(_indexroot."patch.php")){
  _systemFailure("Na serveru se stále nachází soubor <em>patch.php</em>. Smažte jej, pokud je databáze již aktualizovaná.");
  }


  /*---- kontrola blokace IP ----*/
  if(!_administration){
    if(_banned!=""){
    $banned=explode("\n", _banned);
      foreach($banned as $item){
      if(mb_substr(_userip, 0, mb_strlen($item))==$item){exit;}
      }
    }
  }


  /*---- vycisteni iplogu ----*/
  mysql_query("DELETE FROM `"._mysql_prefix."-iplog` WHERE (type=1 AND ".time()."-time>"._maxloginexpire.") OR (type=2 AND ".time()."-time>"._artreadexpire.") OR (type=3 AND ".time()."-time>"._artrateexpire.") OR (type=4 AND ".time()."-time>"._pollvoteexpire.") OR (type=5 AND ".time()."-time>"._postsendexpire.")");

}


/*---- smazani nepotrebnych promennych z pameti ----*/
unset(
$server,
$user,
$password,
$database,
$prefix,
$time_zone,
$time_format,
$header,
$con,
$db,
$item,
$query,
$uquery,
$gquery,
$language,
$langfile,
$langfile_default,
$template,
$template_config,
$banned
);

?>