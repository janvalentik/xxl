<?php

/*---- kontrola jadra ----*/
if(!defined('_core')){exit;}






/*---- vlozeni GET promenne do odkazu ----*/

function _addGetToLink($link, $params, $entity=true){

  //oddelovaci znak
  if(mb_substr_count($link, "?")==0){$link.="?";}
  else{if($entity){$link.="&amp;";}else{$link.="&";}}

return $link.$params;
}






/*---- vlozeni dat formulare pro obnovu do url ----*/

function _addFdGetToLink($url, $array){

  //oddelovaci znak
  if(mb_substr_count($url, "?")==0){$url.="?";}
  else{$url.="&";}

  //seznam
  foreach($array as $key=>$item){
  $url.="_formData[".$key."]=".urlencode($item)."&";
  }

return mb_substr($url, 0, mb_strlen($url)-1);
}






/*---- formatovani retezce pro uzivatelska jmena, mod rewrite atd. ----*/

function _anchorStr($input, $lower=true){

  //diakritika
  $input=str_replace(
  array("é", "ě", "É", "Ě", "ř", "Ř", "ť", "Ť", "ž", "Ž", "ú", "Ú", "ů", "Ů", "ü", "Ü", "í", "Í", "ó", "Ó", "á", "Á", "š", "Š", "ď", "Ď", "ý", "Ý", "č", "Č", "ň", "Ň", "ä", "Ä", "ĺ", "Ĺ", "ľ", "Ľ", "ŕ", "Ŕ", "ö", "Ö"),
  array("e", "e", "E", "E", "r", "R", "t", "T", "z", "Z", "u", "U", "u", "U", "u", "U", "i", "I", "o", "O", "a", "A", "s", "S", "d", "D", "y", "Y", "c", "C", "n", "N", "a", "A", "l", "L", "l", "L", "r", "R", "o", "O"),
  $input
  );

  //mezery
  $input=str_replace(" ", "-", $input);

  //odfiltrovani nepovolenych znaku
  $output="";
  $len=mb_strlen($input);
  for($x=0; $x<$len; $x++){
  $char=mb_substr($input, $x, 1);
  if(in_array($char, array("A","a","B","b","C","c","D","d","E","e","F","f","G","g","H","h","I","i","J","j","K","k","L","l","M","m","N","n","O","o","P","p","Q","q","R","r","S","s","T","t","U","u","V","v","W","w","X","x","Y","y","Z","z","0","1","2","3","4","5","6","7","8","9",".","-"))){$output.=$char;}
  }

  //dvojite symboly
  /*$output=preg_replace('|--+|', '-', $output);
  $output=preg_replace('|\.\.+|', '.', $output);
  $output=preg_replace('|\.-+|', '.', $output);
  $output=preg_replace('|-\.+|', '-', $output);*/
  $from=array('|--+|', '|\.\.+|', '|\.-+|', '|-\.+|');
  $to=array('-', '.', '.', '-');
  $output=preg_replace($from, $to, $output);
  
  //orezani
  $output=trim($output, "-_.");

  //prevod na mala pismena
  if($lower==true){
  $output=mb_strtolower($output);
  }

  //return
  return $output;

}






/*---- vraceni md5 hashe se zvolenym nebo nahodne vygenerovanym saltem  (pri generovani je vysledkem pole s klici 0-hash, 1-salt, 2-vstup) ----*/

function _md5Salt($str, $usesalt=null){
  if($usesalt===null){$salt=_wordGen(8,3);}else{$salt=$usesalt;}
  $hash=md5($salt.$str.$salt);
  if($usesalt===null){return array($hash, $salt, $str);}else{return $hash;}
}






/*---- definovani nedefinovanych klicu v poli ----*/

function _arrayDefineKeys($array, $keys){
if(is_array($array)){
  foreach($keys as $key=>$value){
    if(!isset($array[$key])){$array[$key]=$value;}
  }
return $array;
}
else{return array();}
}






/*---- odstraneni klicu z pole ----*/

function _arrayRemoveKey($array, $key_remove, $preserve_keys=false){
$output=array();
if(is_array($array)){
  foreach($array as $key=>$value){
    if($key!=$key_remove){
      if(!$preserve_keys){$output[]=$value;}
      else{$output[$key]=$value;}
    }
  }
}
return $output;
}






/*---- odstraneni dane hodnoty z pole ----*/

function _arrayRemoveValue($array, $value_remove, $preserve_keys=false){
$output=array();
if(is_array($array)){
  foreach($array as $key=>$value){
    if($value!=$value_remove){
      if(!$preserve_keys){$output[]=$value;}
      else{$output[$key]=$value;}
    }
  }
}
return $output;
}





/*---- nahled clanku pro vypis ----*/

function _articlePreview($art, $info=true, $perex=true){
global $_lang;

  //titulek
  $output="<h2 class='list-title'><a href='"._linkArticle($art['id'])."'>".$art['title']."</a></h2>";

  //perex
if($perex==true){
$output.="<p class='list-perex'>";
// volba pripony
$path = _indexroot.'upload/img/perex/'.($art['id']).'.';
if(file_exists($path.'png')) {
$path .= 'png';
} elseif(file_exists($path.'jpg')) {
$path .= 'jpg';
} elseif(file_exists($path.'gif')) {
$path .= 'gif';
} else {
$path = null;
}

// vystup
$output.="<a href='"._linkArticle($art['id'])."' title='".$art['title']."'>";
$output .= (!is_null($path)?'<img src="'.$path.'" class="list-image" title="'.$art['title'].'" alt="'.$art['title'].'" />':'');
$output.="</a>";
$output.=_parseHCM($art['perex']);
$output.="</p>";
}

  
  
  
  
  //info

  if($info==true){
  
      //pocet komentaru
      if($art['comments']==1 and _comments){
      $info_comments=_template_listinfoseparator."<span>".$_lang['article.comments'].":</span> ".mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-posts` WHERE home=".$art['id']." AND type=2"), 0);
      }
      else{
      $info_comments="";
      }
  
  $output.="
  <div class='list-info'>
  <span>".$_lang['article.author'].":</span> "._linkUser($art['author'], null, true)._template_listinfoseparator.
  "<span>".$_lang['article.posted'].":</span> "._formatTime($art['time'])._template_listinfoseparator.
  "<span>".$_lang['article.readed'].":</span> ".$art['readed']."x 
  <a href='"._linkArticle($art['id'])."#posts' title='".$art['title']."'>".$info_comments."</a></div>";
  }

return $output."\n";
}






/*---- vratit kod obrazku galerie ----*/

function _galleryImage($img, $lightboxid, $height=0){
$content="<a href='".(!_isAbsolutePath($img['prev'])?_indexroot:'').$img['full']."' rel=\"lightbox\" ".(($img['title']!="")?" title='".$img['title']."'":'').">";
if($img['prev']!=""){$content.="<img src='".(!_isAbsolutePath($img['prev'])?_indexroot:'').$img['prev']."' alt='".(($img['title']!="")?$img['title']:"img")."' />";}
else{$content.="<img src='"._indexroot."remote/imgprev.php?id=".$img['id'].(($height!=0)?"&amp;h=".$height:'')."' alt='".(($img['title']!="")?$img['title']:"img")."' />";}
$content.="</a>\n";
return $content;
}






/*---- vratit boolen hodnotu cisla ----*/

function _boolean($input){
if($input==1){return true;}else{return false;}
}






/*---- vratit textovou reprezentaci boolean hodnoty cisla ----*/

function _booleanStr($input){
if($input==true){return "true";}else{return "false";}
}






/*---- inicializace captcha ----*/

function _captchaInit(){

if(_captcha and !_loginindicator){
  global $_lang, $__captcha_counter;
  $__captcha_counter++;
  if(!isset($_SESSION[_sessionprefix.'captcha_code']) or !is_array($_SESSION[_sessionprefix.'captcha_code'])) {$_SESSION[_sessionprefix.'captcha_code'] = array();}
  $_SESSION[_sessionprefix.'captcha_code'][$__captcha_counter]=mb_strtoupper(_wordGen(5, 2));
  return array($_lang['captcha.input'], "<input type='text' name='_cp' class='inputc' /><img src='"._indexroot."remote/cimage.php?n=".$__captcha_counter."' alt='captcha' title='".$_lang['captcha.help']."' class='cimage' /><input type='hidden' name='_cn' value='".$__captcha_counter."' />");
}
else{
  return array("", "");
}

}






/*---- kontrola captcha ----*/

function _captchaCheck(){

if(_captcha and !_loginindicator){
  if(isset($_POST['_cp']) and isset($_POST['_cn']) and isset($_SESSION[_sessionprefix.'captcha_code'][$_POST['_cn']])){
    if(str_replace("O", "0", $_SESSION[_sessionprefix.'captcha_code'][$_POST['_cn']])==str_replace("O", "0", mb_strtoupper($_POST['_cp']))){$return=true;}
    else{$return=false;}
    $_SESSION[_sessionprefix.'captcha_code'][$_POST['_cn']]=null;
    return $return;
  }
  else{
    return false;
  }
}
else{
  return true;
}

}






/*---- zaskrtnuti checkboxu ----*/

function _checkboxActivate($input){
if($input==1){return " checked='checked'";}
}






/*---- nacteni odeslaneho checkboxu formularem ----*/

function _checkboxLoad($name){
if(isset($_POST[$name])){return 1;}else{return 0;}
}






/*---- kontrola kompatibility ----*/

function _checkVersion($type, $version, $get=false){

  //pole verzi podle typu
  switch($type){
  case "database": $cmp_versions=array("1.0.1"); break;
  case "database_backup": $cmp_versions=array("1.0.1"); break;
  case "language_file": $cmp_versions=array("1.0.1"); break;
  case "template": $cmp_versions=array("2.0"); break;
  default: $cmp_versions=array(); break;
  }

  //vystup
  if(!$get){return in_array($version, $cmp_versions);}
  else{return $cmp_versions;}

}






/*---- orezat text na pozadovanou delku ----*/

function _cutStr($string, $length, $convert_entities=true){
if($length>0){
  if($convert_entities){$string=_htmlStrUndo($string);}
  if(mb_strlen($string)>$length){$string=mb_substr($string, 0, $length-3)."...";}
  if($convert_entities){$string=_htmlStr($string);}
  return $string;
}
else{
  return $string;
}
}






/*---- odstraneni uzivatele ----*/

function _deleteUser($id){
if($id!=0){
  $username=mysql_fetch_array(mysql_query("SELECT username FROM `"._mysql_prefix."-users` WHERE id=".$id));
  mysql_query("DELETE FROM `"._mysql_prefix."-users` WHERE id=".$id);
  mysql_query("DELETE FROM `"._mysql_prefix."-messages` WHERE receiver=".$id." OR sender=".$id);
  mysql_query("UPDATE `"._mysql_prefix."-posts` SET guest='".$username['username']."',author=-1 WHERE author=".$id);
  mysql_query("UPDATE `"._mysql_prefix."-articles` SET author=0 WHERE author=".$id);
  mysql_query("UPDATE `"._mysql_prefix."-polls` SET author=0 WHERE author=".$id);

    //odstraneni uploadovaneho avataru
    $avatar=_indexroot."avatars/".$id.".jpg";
    if(@file_exists($avatar)){@unlink($avatar);}
    
}
}






/*---- vyhodnoceni php kodu uvnitr funkce ----*/

function _evalBox($code){
$output="";
eval($code);
return $output;
}







/*---- vratit formatovany seznam udalosti (chyb) ----*/

function _eventList($events, $intro=null){

  //text
  if($intro!=null){
    if($intro!="errors"){$output=$intro;}
    else{global $_lang; $output=$_lang['misc.errorlog.intro'];}
    $output.="\n";
  }
  else{
  $output="";
  }

$output.="<ul>\n";

  foreach($events as $item){
  $output.="<li>".$item."</li>\n";
  }

$output.="</ul>";
return $output;
}






/*---- vratit textovou podobu casove znamky ----*/

function _formatTime($timestamp){
return date(_time_format, $timestamp);
}






/*---- systemova zprava ----*/

function _formMessage($type, $string){
return "\n<div class='message".$type."'>$string</div>\n";
}






/*---- vytvoreni struktury formulare ----*/

function _formOutput($name, $action, $cells, $check=null, $submittext=null, $codenexttosubmit=null){
global $_lang;

/*--- kontrola poli javascriptem, text odesilaciho tlacidla ---*/
if($check!=null){$checkcode=_jsCheckForm($name, $check);}
else{$checkcode="";}

if($submittext!=null){$submit=$submittext;}
else{$submit=$_lang['global.send'];}

/*--- vystup ---*/
$output="
<form action='".$action."' method='post' name='".$name."'".$checkcode.">
<table>";

  //bunky
  foreach($cells as $cell){
  if($cell[0]!=""){
    $output.="
    <tr".((isset($cell[2]) and $cell[2]==true)?" valign='top'":'').">
    <td class='rpad'".(($cell[1]=="")?" colspan='2'":'').">".(($cell[1]!="")?"<strong>":'').$cell[0].(($cell[1]!="")?"</strong>":'')."</td>
    ".(($cell[1]!="")?"<td>".$cell[1]."</td>":'')."
    </tr>";
    $lastcell=$cell;
  }
  }

//odesilaci tlacidlo, konec tabulky
$output.="
  <tr>
  ".((isset($lastcell[1]) and $lastcell[1]!="")?"<td></td><td>":"<td colspan='2'>")."
  <input type='submit' value='".$submit."' />".$codenexttosubmit."</td>
  </tr>

</table>
</form>";

return $output;
}






/*---- ziskat ID pro novy zaznam v databazi ----*/

function _getNewID($table){
$newid=mysql_query("SELECT id FROM `"._mysql_prefix."-".$table."` ORDER BY id DESC LIMIT 1");
if(mysql_num_rows($newid)==0){return 1;}
else{$newid=mysql_fetch_array($newid); return ($newid['id']+1);}
}






/*---- ziskat pole prav ----*/

function _getRightsArray(){
return array(
"level",
"administration",
"adminsettings",
"adminusers",
"admingroups",
"admincontent",
"adminsection",
"admincategory",
"adminbook",
"adminseparator",
"admingallery",
"adminlink",
"adminintersection",
"adminforum",
"adminart",
"adminallart",
"adminchangeartauthor",
"adminpoll",
"adminpollall",
"adminsbox",
"adminbox",
"admindownload",
"adminnewsletter",
"adminsitemap",
"adminconfirm",
"adminneedconfirm",
"adminfman",
"adminfmanlimit",
"adminfmanplus",
"adminhcmphp",
"adminbackup",
"adminmassemail",
"adminstatsword",
"adminbans",
"adminposts",
"changeusername",
"unlimitedpostaccess",
"locktopics",
"postcomments",
"artrate",
"pollvote",
"selfdestruction"
);
}






/*---- zjistit, jestli uzivatel NEMA dane pravo ----*/

function _userHasNotRight($name){
if(mb_substr($name, 0, 1)!="-"){
  $negations=array("adminfmanlimit", "adminneedconfirm");
  if(!in_array($name, $negations)){return !constant('_loginright_'.$name);}
  else{return constant('_loginright_'.$name);}
}
else{return true;}
}






/*---- vratit kod smajlu a BBCode tlacitek ----*/

function _getPostformControls($form, $area, $ignorebbcode=false){
$output="";

  //bbcode
  if(_bbcode and _template_bbcode_buttons and !$ignorebbcode){
  $bbcode_array=array("b", "i", "url", "img", "code");
    foreach($bbcode_array as $item){
    $output.="<a href=\"#\" onclick=\"return _sysAddBBCode('".$form."','".$area."','".$item."');\"><img src=\""._templateImage("bbcode/".$item.".gif")."\" alt=\"".$item."\" /></a> ";
    }
  }

  //smajly
  if(_smileys){
  if(_bbcode and !$ignorebbcode){$output.="&nbsp;&nbsp;";}
    for($x=1; $x<=_template_smileys; $x++){
    $output.="<a href=\"#\" onclick=\"return _sysAddSmiley('".$form."','".$area."',".$x.");\"><img src=\""._templateImage("smileys/".$x.".gif")."\" alt=\"".$x."\" title=\"".$x."\" /></a> ";
    }
    
      //odkaz na vypis dalsich smajlu
      if(_template_smileys_list){
      global $_lang;
      $output.="&nbsp;<a href=\""._indexroot."remote/smileys.php\" target=\"_blank\" onclick=\"return _sysOpenWindow('"._indexroot."remote/smileys.php', 320, 475);\">".$_lang['misc.smileys.list.link']."</a>";
      }
    
  }
  
return "<span class='posts-form-buttons'>".trim($output)."</span>";
}






/*---- nahrazeni 'nebezpecnych' znaku ----*/

function _htmlStr($input){
return str_replace(array("&", "<", ">", "\"", "'"), array("&amp;", "&lt;", "&gt;", "&quot;", "&#39;"), $input);
}






/*---- navraceni 'nebezpecnych' znaku ----*/

function _htmlStrUndo($input, $double=false){
$output=str_replace(array("&lt;", "&gt;", "&quot;", "&#39;", "&amp;"), array("<", ">", "\"", "'", "&"), $input);
if($double){$output=_htmlStrUndo($output);}
return $output;
}






/*---- zakazani pole formulare, pokud vstupni promenna NENI true ----*/

function _inputDisable($cond){
if($cond!=true){return " disabled='disabled'";}
}






/*---- kontrola iplogu ----*/

function _iplogCheck($type, $var=null){
$return=true;
$querybasic="SELECT * FROM `"._mysql_prefix."-iplog` WHERE ip='"._userip."' AND type=".$type;

  switch($type){
  
    //pokusy o prihlaseni
    case 1:
    $query=mysql_query($querybasic);
    if(mysql_num_rows($query)!=0){
      $query=mysql_fetch_array($query);
      if($query['var']>=_maxloginattempts){$return=false;}
    }
    break;
    
    //precteni clanku, hodnoceni clanku, hlasovani v ankete
    case 2: case 3: case 4:
    $query=mysql_query($querybasic." AND var=".$var);
    if(mysql_num_rows($query)!=0){$return=false;}
    break;
    
    //zaslani komentare, prispevku nebo vzkazu
    case 5:
    $query=mysql_query($querybasic);
    if(mysql_num_rows($query)!=0){$return=false;}
    break;


  }

return $return;
}






/*---- aktualizace iplogu ----*/

function _iplogUpdate($type, $var=null){
$querybasic="SELECT * FROM `"._mysql_prefix."-iplog` WHERE ip='"._userip."' AND type=".$type;
$newid=_getNewID("iplog");

  switch($type){
  
    //prihlaseni
    case 1:
    $query=mysql_query($querybasic);
      if(mysql_num_rows($query)!=0){
      $query=mysql_fetch_array($query);
      mysql_query("UPDATE `"._mysql_prefix."-iplog` SET var=".($query['var']+1)." WHERE id=".$query['id']);
      }
      else{
      mysql_query("INSERT INTO `"._mysql_prefix."-iplog` (id,ip,type,time,var) VALUES (".$newid.",'"._userip."',1,".time().",1)");
      }
    break;
    
    //precteni clanku
    case 2:
    mysql_query("INSERT INTO `"._mysql_prefix."-iplog` (id,ip,type,time,var) VALUES (".$newid.",'"._userip."',2,".time().",".$var.")");
    break;
    
    //hodnoceni clanku
    case 3:
    mysql_query("INSERT INTO `"._mysql_prefix."-iplog` (id,ip,type,time,var) VALUES (".$newid.",'"._userip."',3,".time().",".$var.")");
    break;

    //hlasovani v ankete
    case 4:
    mysql_query("INSERT INTO `"._mysql_prefix."-iplog` (id,ip,type,time,var) VALUES (".$newid.",'"._userip."',4,".time().",".$var.")");
    break;

    //odeslani komentare, prispevku nebo vzkazu
    case 5:
    mysql_query("INSERT INTO `"._mysql_prefix."-iplog` (id,ip,type,time,var) VALUES (".$newid.",'"._userip."',5,".time().",0)");
    break;

  }

}






/*---- rozpoznani absolutniho tvaru cesty ----*/

function _isAbsolutePath($path){
$path=parse_url($path);
return isset($path['scheme']);
}







/*---- kontrola poli formulare javascriptem ----*/

function _jsCheckForm($form, $inputs){
global $_lang;

//kod
$output=" onsubmit=\"if(";
$count=count($inputs);
for($x=1; $x<=$count; $x++){
$output.=$form.".".$inputs[$x-1].".value==''";
if($x!=$count){$output.=" || ";}
}
$output.="){_sysAlert(1); return false;}\"";

//return
return $output;

}






/*---- limitovat delku textarey javascriptem ----*/

function _jsLimitLength($maxlength, $form, $name){
$fid="_sysLimitLength_".mb_substr(md5($maxlength.$form.$name), 0, 8);
return "
<script type='text/javascript'>
//<![CDATA[
document.onkeyup=".$fid.";
document.onmouseup=".$fid.";
document.onmousedown=".$fid.";

function ".$fid."(){
value=document.$form.$name.value;
len=$maxlength-value.length;
  if(len<0){
  st=document.$form.$name.scrollTop;
  document.$form.$name.value=document.$form.$name.value.substring(0,$maxlength);
  document.$form.$name.scrollTop=st;
  _sysAlert(2);
  }
}

//]]>
</script>
";
}






/*---- generovani nahodneho slova ----*/

function _wordGen($len=10, $numlen=3,$codes=''){
$codes_arr=explode(';',$codes);
if($len>$numlen){$wordlen=$len-$numlen;}
else{$wordlen=$len; $numlen=0;}
$output="";

  //priprava poli
  $letters1=array("a", "e", "i", "o", "u", "y");
  $letters2=array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w", "x", "z");

  //textova cast
  $t=true;
  for($x=0; $x<$wordlen; $x++){
    if($t){$r=array_rand($letters2); $output.=$letters2[$r];}
    else{$r=array_rand($letters1); $output.=$letters1[$r];}
  $t=!$t;
  }

  //ciselna cast
  if($numlen!=0){
    $output.=mt_rand(pow(10, $numlen-1), pow(10, $numlen)-1);
  }
  if (!in_array($output,$codes_arr)) {
  	return $output;
  }
  else{
    _wordGen(6,2,$codes);
  }
}






/*---- vyhodnotit pravo pristupu k cilovemu uzivateli ----*/

function _levelCheck($targetuserid){

if(_loginindicator){

  //nacteni urovne
  $user=mysql_fetch_array(mysql_query("SELECT `group`,id FROM `"._mysql_prefix."-users` WHERE id=".$targetuserid));
  $level=mysql_fetch_array(mysql_query("SELECT level FROM `"._mysql_prefix."-groups` WHERE id=".$user['group']));

  //kontrola
  if(_loginright_level>$level['level'] or $user['id']==_loginid){return true;}
  else{return false;}
  
}
else{
  return false;
}

}






/*---- adresa k clanku ----*/

function _linkArticle($id){

if(_modrewrite!=1){
  return "index.php?a=".$id;
}
else{
  $anchor=mysql_fetch_array(mysql_query("SELECT title,seotitle FROM `"._mysql_prefix."-articles` WHERE id=".$id));
  if (_urlgenerate and $anchor['seotitle']!='') {
  	return _anchorStr($anchor['seotitle']).".a".$id._rewrite_ext;
  }
  else{
    return _anchorStr($anchor['title']).".a".$id._rewrite_ext;
  }
}

}






/*---- adresa ke strance ----*/

function _linkRoot($id){

  if(_modrewrite!=1){
    return "index.php?p=".$id;
  }
  else{
    $anchor=mysql_fetch_array(mysql_query("SELECT title,seotitle FROM `"._mysql_prefix."-root` WHERE id=".$id));
    if (_urlgenerate and $anchor['seotitle']!='') {
    	return _anchorStr($anchor['seotitle']).".p".$id._rewrite_ext;
    }
    else{
      return _anchorStr($anchor['title']).".p".$id._rewrite_ext;
    }
  }

}






/*---- adresa k rss zdroji ----*/

function _linkRSS($id, $type, $space=true){
global $_lang;
  if(_rss){
    if($space){$space=" ";}else{$space="";}
    return $space."<a href='"._indexroot."remote/rss.php?tp=".$type."&amp;id=".$id."' target='_blank' title='".$_lang['rss.linktitle']."'><img src='"._templateImage("icons/rss.gif")."' alt='rss' class='icon' /></a>";
  }
  else{
    return "";
  }
}






/*---- odkaz na uzivatele ----*/

function _linkUser($id, $class=null, $noicon=false, $onlyname=false, $namelengthlimit=null, $namesuffix=""){

if($class!=null){$class=" class='".$class."'";}else{$class="";}

  //nacteni dat
  $userdata=mysql_fetch_array(mysql_query("SELECT publicname,username,`group` FROM `"._mysql_prefix."-users` WHERE id=".$id));
  
  if($onlyname==false){
    $groupdata=mysql_fetch_array(mysql_query("SELECT icon FROM `"._mysql_prefix."-groups` WHERE id=".$userdata['group']));

    //ikona
    if($noicon==false){
    $icon=(($groupdata['icon']!="")?"<img src='"._indexroot."groupicons/".$groupdata['icon']."' alt='icon' class='icon' /> ":'');
    }
    else{
    $icon="";
    }
    
    //vyber zobrazovaneho jmena
    if($userdata['publicname']!=""){$publicname=$userdata['publicname'];}
    else{$publicname=$userdata['username'];}
    
    //kod odkazu
    if($namelengthlimit!=null){$publicname=_cutStr($publicname, $namelengthlimit);}
    $link="<a href='"._indexroot."index.php?m=profile&amp;id=".$userdata['username']."'".$class.(_administration?" target='_blank'":'').">".$publicname.$namesuffix."</a>";
  }
  else{
  $icon="";
  if($userdata['publicname']!=""){$link=$userdata['publicname'].$namesuffix;}else{$link=$userdata['username'].$namesuffix;}
  }

return $icon.$link;
}






/*---- odkaz na email ----*/

function _mailto($email){
$email=str_replace("@", _atreplace, $email);
return "<a href='#' onclick='return _sysMai_lto(this);'>".$email."</a>";
}






/*---- rozebrat retezec na parametry a vratit jako pole ----*/

function _parseStr($input){

  //nastaveni funkce
  $sep=",";
  $quote="\"";
  $quote2="'";

  //priprava
  $output=array();
  $input=trim($input, $sep." \n");
  $len=mb_strlen($input);

  //rozebrani retezce
  $key=0;
  $fill=false;
  $quoted=false;
  $used_quote="";
  for($pos=0; $pos<$len; $pos++){
    $char=mb_substr($input, $pos, 1);
      if($fill){
        //plneni parametru
        if(($quoted and ($char!=$used_quote or mb_substr($input, $pos-1, 1)=="\\")) or (!$quoted and $char!=$sep)){$output[$key].=$char;}
        else{if($quoted){$output[$key]=str_replace("\\".$used_quote, $used_quote, $output[$key]);}else{$output[$key]=trim($output[$key]);} $key++; $fill=false;}
      }
      else{
        //hledani zacatku parametru
        if($char!=" " and $char!="\n" and $char!="\r" and $char!=$sep){
          $pos--;
          $output[$key]="";
          $fill=true;
            //detekce uvozovek
            if($char==$quote or $char==$quote2){$quoted=true; if($char==$quote){$used_quote=$quote;}else{$used_quote=$quote2;} $pos++;}
            else{$quoted=false;}
        }
      }
  }

return $output;
}







/*---- HCM moduly ----*/

function _parseHCM_loadmodule($module){
  $module=basename($module);
  $file=_indexroot.'require/hcm/'.$module.'.php';
  if(file_exists($file)){
    require($file);
    return function_exists('_HCM_'.$module);
  }
}

function _parseHCM_module($match){
  $GLOBALS['__hcm_uid']++;
  $params=_parseStr($match[1]);
  if(isset($params[0])){
    if(function_exists('_HCM_'.$params[0]) or _parseHCM_loadmodule($params[0])){
      return call_user_func_array('_HCM_'.$params[0], array_splice($params, 1));
    }
  }
}

function _parseHCM_filter($match){
global $__input;

/*

Mozne hodnoty promenne $__input:

null - vsechny HCM moduly budou odstraneny
array(true, array('one', 'two')) - jen hcm moduly 'one' a 'two' budou odstraneny
array(false, array('one', 'two')) - jen hcm moduly 'one' a 'two' budou zachovany

*/

$paramarray=_parseStr($match[1]);
$mresult=$match[0];
  if(isset($paramarray[0])){
  $paramarray[0]=mb_strtolower($paramarray[0]);
    if($__input==null or ($__input[0] and in_array($paramarray[0], $__input[1])) or (!$__input[0] and !in_array($paramarray[0], $__input[1]))){
    $mresult="";
    }
  }
return $mresult;
}

function _parseHCM($input, $handler='_parseHCM_module'){
return preg_replace_callback('|\[hcm\](.*?)\[/hcm\]|s', $handler, $input);
}






/*---- filtrovani hcm modulu na zaklade opravneni ----*/

function _filtrateHCM($content){

  //odstraneni HCM php modulu
  if(!_loginright_adminhcmphp){
  $GLOBALS['__input']=array(true, array('php'));
  $content=_parseHCM($content, '_parseHCM_filter');
  unset($GLOBALS['__input']);
  }

return $content;
}






/*---- vyhodnoceni cesty ----*/

function _parsePath($path){
$path=_arrayRemoveValue(explode("/", trim($path, "/")), ".");
$loop=true;

  while($loop){

  $moverindex=-1;

    for($i=count($path)-1; $i>=0; $i--){
    if($path[$i]==".."){$moverindex=$i; break;}
    }

    if($moverindex!=-1){

      $collision=-1;

        for($i=$moverindex-1; $i>=0; $i--){
        if($path[$i]!=".."){$collision=$i; break;}
        }

        if($collision!=-1){
        $path=_arrayRemoveKey($path, $moverindex, true);
        $path=_arrayRemoveKey($path, $collision, true);
        $path=_arrayRemoveKey($path, -1);
        }
        else{
        $loop=false;
        }

    }
    else{
      $loop=false;
    }

  }

$output=implode("/", $path)."/";
if($output=="/"){$output="./";}
return $output;
}






/*---- kontrola url na vlozene skripty ----*/

function _isSafeUrl($url){
if(mb_substr($url, 0, 11)=="javascript:" or mb_substr($url, 0, 5)=="data:"){return false;}
else{return true;}
}






/*---- vyhodnoceni BBCode tagu ----*/
function _parseBBCode_tag($match){
global $_lang;

  //identifikovani typu, vyhodnoceni
  $type=preg_replace('|^\[(.*?)\](.+)|s', '$1', $match[0], 1);
  $match[1]=trim(str_replace('[', '&#91;', $match[1]));
  $output=$match[1];

  switch($type){

    case 'b':
    $output='<strong>'.$match[1].'</strong>';
    break;
    
    case 'i':
    $output='<em>'.$match[1].'</em>';
    break;
    
    case 'u':
    $output='<u>'.$match[1].'</u>';
    break;

    case 'q':
    $output='&quot;'.$match[1].'&quot;';
    break;
    
    case 'img':
    if(_isSafeUrl($match[1])){$output='<img src="'.$match[1].'" alt="img" />';}
    break;
    
    case 'code':
    $output='<span class="pre">'.$match[1].'</span>';
    break;
    
    case 'url':
    if(mb_substr($match[1], 0, 7)!="http://"){$match[1]="http://".$match[1];}
    if(_isSafeUrl($match[1])){$output='<a href="'.$match[1].'" target="_blank" rel="nofollow">'._cutStr(mb_substr($match[1], 7), 48).'</a>';}
    break;

  }

return $output;
}

function _parseBBCode($input){
$tags=array('b', 'i', 'u', 'q', 'img', 'code', 'url');
$search=array();
foreach($tags as $tag){$search[]='|\['.$tag.'\](.*?)\[/'.$tag.'\]|s';}
return preg_replace_callback($search, '_parseBBCode_tag', $input, 56);
}






/*---- vyhodnoceni textu prispevku ----*/

function _parsePost($input, $smileys=true, $bbcode=true, $nl2br=true){

$output=$input;

//vyhodnoceni BBCode
if(_bbcode and $bbcode){
  $output=_parseBBCode($output);
}

//vyhodnoceni smajlu
if(_smileys and $smileys){
  $output=preg_replace('/\*(\d{1,3})\*/s', '<img src=\''._indexroot.'templates/'._template.'/images/smileys/$1.gif\' alt=\'$1\' class=\'post-smiley\' />', $output, 32);
}

//prevedeni novych radku
if($nl2br){
  $output=nl2br($output);
}

//navrat vystupu
return $output;

}






/*---- vyhodnotit pravo k pristupu ke clanku ----*/

/*

Navratove hodnoty:
0 - pristup odepren
1 - pristup povolen
2 - vyzadovano prihlaseni

Potrebne klice v poli $res:
id,time,confirmed,public,home1,home2,home3

*/

function _articleAccess($res){
$return=0;
  if(($res['time']<=time() or _loginright_adminconfirm) and ($res['confirmed']==1 or _loginright_adminconfirm)){
    $home1_public=mysql_fetch_array(mysql_query("SELECT public FROM `"._mysql_prefix."-root` WHERE id=".$res['home1'])); $home1_public=$home1_public['public'];
    if($res['home2']!=-1){$home2_public=mysql_fetch_array(mysql_query("SELECT public FROM `"._mysql_prefix."-root` WHERE id=".$res['home2'])); $home2_public=$home2_public['public'];}else{$home2_public=1;}
    if($res['home3']!=-1){$home3_public=mysql_fetch_array(mysql_query("SELECT public FROM `"._mysql_prefix."-root` WHERE id=".$res['home3'])); $home3_public=$home3_public['public'];}else{$home3_public=1;}
      if(_publicAccess($res['public']) and _publicAccess($home1_public) and _publicAccess($home2_public) and _publicAccess($home3_public)){
        $return=1;
      }
      else{
        $return=2;
      }
  }
return $return;
}






/*---- testovani pristupu k prispevku ----*/

/*
Potrebne klice v poli $post: author,time
*/

function _postAccess($post){

//nacteni dat
if($post['author']!=-1){
$author=mysql_fetch_array(mysql_query("SELECT id,`group` FROM `"._mysql_prefix."-users` WHERE id=".$post['author']));
$level=mysql_fetch_array(mysql_query("SELECT level FROM `"._mysql_prefix."-groups` WHERE id=".$author['group']));
$level=$level['level'];
}
else{
$level=0;
$author=array('id'=>-1);
}

//return
if(_loginindicator and ((_loginright_level>$level and _loginright_adminposts) or _loginid==$author['id']) and ($post['time']+_postadmintime>time() or _loginright_unlimitedpostaccess)){return true;}
else{return false;}


}






/*---- pristup na zaklade verejnosti a stavu prihlaseni ----*/

function _publicAccess($public){
  if($public==1 or ($public==0 and _loginindicator)){return true;}
  else{return false;}
}






/*---- odstraneni vsech lomitek z konce retezce ----*/

function _removeSlashesFromEnd($string){
while(mb_substr($string, -1)=="/"){$string=mb_substr($string, 0, mb_strlen($string)-1);}
return $string;
}







/*---- obnoveni hodnoty pole formulare z POST ----*/

function _restorePostValue($name, $else=null, $noautoparam=false){

if(!$noautoparam){$autoparam_start=" value='"; $autoparam_end="'";}
else{$autoparam_start=""; $autoparam_end="";}

if(isset($_POST[$name]) and $_POST[$name]!=""){return $autoparam_start._htmlStr(_stripSlashes($_POST[$name])).$autoparam_end;}
else{if($else!=null){return $autoparam_start._htmlStr($else).$autoparam_end;}}

}






/*---- obnoveni hodnoty pole formulare z GET ----*/

function _restoreGetValue($name, $else=null){
if(isset($_GET[$name]) and $_GET[$name]!=""){return " value='"._htmlStr(_stripSlashes($_GET[$name]))."'";}
else{if($else!=null){return " value='"._htmlStr($else)."'";}}
}






/*---- obnoveni hodnoty pole formulare z _formData v GET poli ----*/

function _restoreGetFdValue($name, $else=null, $noautoparam=false){

if(!$noautoparam){$autoparam_start=" value='"; $autoparam_end="'";}
else{$autoparam_start=""; $autoparam_end="";}

if(isset($_GET['_formData'][$name])){return $autoparam_start._htmlStr(_stripSlashes($_GET['_formData'][$name])).$autoparam_end;}
else{if($else!=null){return $autoparam_start._htmlStr($else).$autoparam_end;}}

}






/*---- strankovani ----*/

/*
Pokud je $table cislo, bude toto cislo pouzito namisto zjistovani poctu z tabulky v databazi.
Funkce vraci pole s temito klici:

array(
  0 => xhtml kod seznamu stran,
  1 => cast sql dotazu - limit,
  2 => aktualni strana,
  3 => celkovy pocet stran,
  4 => pocet polozek,
  5 => cislo prvni zobrazene polozky,
  6 => cislo posledni zobrazene polozky,
  7 => pocet polozek na jednu stranu
)

Cisla polozek zacinaji od nuly.
*/

function _resultPaging($url, $limit, $table, $conditions="1", $linksuffix="", $param="page"){
global $_lang;

//priprava promennych
if(is_string($table)){$count=mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-".$table."` WHERE ".$conditions), 0);}
else{$count=$table;}
if($count==0){$count=1;}
if(isset($_GET[$param])){$s=abs(intval($_GET[$param])-1);}else{$s=0;}
$pages=ceil($count/$limit);
if($s+1>$pages){$s=0;}
$start=$s*$limit;
$beginpage=$s+1-_showpages; if($beginpage<1){$endbonus=abs($beginpage)+1; $beginpage=1;}else{$endbonus=0;}
$endpage=$s+1+_showpages+$endbonus; if($endpage>$pages){$beginpage-=$endpage-$pages; if($beginpage<1){$beginpage=1;} $endpage=$pages;}

//vypis stran

  //oddelovaci symbol v url
  if(mb_substr_count($url, "?")==0){$url.="?";}
  else{$url.="&amp;";}

if($pages>1){
  $paging="<span>";
    for($x=$beginpage; $x<=$endpage; $x++){
      if($x==$s+1){$class=" class='act'";}else{$class="";}
      $paging.="<a href='".$url.$param."=".$x.$linksuffix."'".$class.">".$x."</a>\n";
      if($x!=$endpage){$paging.=" ";}
    }
  $paging.="</span>";

    //ovladaci prvky

      //minus
      if($s+1!=1){$paging="<a href='".$url.$param."=".($s).$linksuffix."'>&laquo; ".$_lang['global.previous']."</a>&nbsp;&nbsp;".$paging;}
      if($beginpage>1){$paging="<a href='".$url.$param."=1".$linksuffix."' title='".$_lang['global.first']."'>1</a> ... ".$paging;}

      //plus
      if($s+1!=$pages){$paging.="&nbsp;&nbsp;<a href='".$url.$param."=".($s+2).$linksuffix."'>".$_lang['global.next']." &raquo;</a>";}
      if($endpage<$pages){$paging.=" ... <a href='".$url.$param."=".$pages.$linksuffix."' title='".$_lang['global.last']."'>".$pages."</a>";}

  $paging="\n<div class='paging'>\n".$_lang['global.paging'].":&nbsp;&nbsp;".$paging."\n</div>\n\n";
}
else{
 $paging="";
}

//return
$end_item=($start+$limit-1);
return array($paging, "LIMIT ".$start.", ".$limit, ($s+1), $pages, $count, $start, (($end_item>$count-1)?$count-1:$end_item), $limit);

}






/*---- zjistit stranku, na ktere se polozka nachazi pri danem strankovani a podmince razeni ----*/

function _resultPagingGetItemPage($limit, $table, $conditions="1"){
$count=mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-".$table."` WHERE ".$conditions), 0);
return intval($count/$limit)+1;
}






/*---- zjisteni, zda je polozka s urcitym cislem v rozsahu aktualni strany strankovani ----*/

function _resultPagingIsItemInRange($pagingdata, $itemnumber){
if($itemnumber>=$pagingdata[5] and $itemnumber<=$pagingdata[6]){return true;}
else{return false;}
}






/*---- navrat na predchozi stranku ----*/

function _returnHeader(){

  //odeslani headeru
  if(isset($_GET['_return']) and $_GET['_return']!=""){
    $_GET['_return']=_htmlStrUndo($_GET['_return']);
    if(preg_match("/^index.php\?m=(settings|editpost|messages)/", $_GET['_return'])){$_GET['_return']="";}
    header("location: "._indexroot.urldecode($_GET['_return'])); exit;
  }
  else{
    if(isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER']!=""){header("location: ".$_SERVER['HTTP_REFERER']); exit;}
    else{header("location: "._indexroot); exit;}
  }

}






/*---- escapovani retezce pro pouziti v sql dotazech ----*/

function _safeStr($input, $stripslashes=true){
  if(is_string($input)){
    return mysql_escape_string(_stripSlashes($input, $stripslashes));
  }
  else{
    return $input;
  }
}





/*---- odescapovani retezce pri aktivnim magic_quotes_gpc ----*/

function _stripSlashes($input, $cond=true){
if(get_magic_quotes_gpc()==1 and $cond){return stripslashes($input);}
else{return $input;}
}






/*---- ulozeni vsech stavajicich post-dat do skrytych poli formulare a vratit jako xhtml vystup nebo neformatovane pole ----*/

function _getPostdata($array=false, $filter=null){
$output=array();
$counter=0;
  foreach($_POST as $key=>$value){
    if($filter!=null and mb_substr($key, 0, mb_strlen($filter))!=$filter){continue;}
    if(!$array){$output[]="<input type='hidden' name='"._htmlStr($key)."' value='"._htmlStr($value)."' />";}
    else{$output[]=array($key, _stripSlashes($value));}
    $counter++;
  }
if(!$array){$output=implode("\n", $output);}
return $output;
}






/*---- zakodovat retezec ----*/

function _slcEncode($string, $key=1){

  //priprava
  $output="";

  //zakodovani
  $len=strlen($string);
  for($x=0; $x<$len; $x++){
    $output.=chr(ord(substr($string, $x, 1))+$key-$x);
  }

  //vystup
  return base64_encode($output);

}






/*---- dekodovat retezec ----*/

function _slcDecode($string, $key=1){

  //priprava
  $string=base64_decode($string);
  $output="";

  //dekodovani
  $len=strlen($string);
  for($x=0; $x<$len; $x++){
    $output.=chr(ord(substr($string, $x, 1))-$key+$x);
  }

  //vystup
  return $output;

}






/*---- vratit cast sql dotazu 'where' pro vyhledavani clanku v urcitych kategorii ----*/

function _sqlArticleWhereCategories($ids){
  if($ids!=null){
    $ids=_arrayRemoveValue(@explode("-", $ids), "");
    $sql_code="(";
    $sql_count=count($ids);
    $counter=1;
      foreach($ids as $rcat){
      $rcat=intval($rcat);
      $sql_code.="(home1=".$rcat." OR home2=".$rcat." OR home3=".$rcat.")";
      if($counter!=$sql_count){$sql_code.=" OR ";}
      $counter++;
      }
    $sql_code.=")";
    return $sql_code;
  }
  else{
    return "";
  }
}






/*---- vratit cast sql dotazu 'where' pro filtrovani clanku podle verejnosti, viditelnosti, stavu schvaleni a casu vydani ----*/

function _sqlArticleFilter($check_category_public=false, $exclude_invisible=true){
$output="confirmed=1 AND time<=".time();
if($exclude_invisible){$output.=" AND visible=1";}
if(!_loginindicator){$output.=" AND public=1";}
if($check_category_public and !_loginindicator){$output.=" AND ((SELECT public FROM `"._mysql_prefix."-root` WHERE id=`"._mysql_prefix."-articles`.home1)=1 OR (SELECT public FROM `"._mysql_prefix."-root` WHERE id=`"._mysql_prefix."-articles`.home2)=1 OR (SELECT public FROM `"._mysql_prefix."-root` WHERE id=`"._mysql_prefix."-articles`.home3)=1)";}
return $output;
}






/*---- vratit cast sql dotazu 'where' pro limitovani na urcite hodnoty sloupce ----*/
function _sqlWhereColumn($column, $values, $isnumeric=true){

if($values!="all"){
  //priprava promennych
  $values=_arrayRemoveValue(explode("-", $values), "");
  $values_count=count($values);
  $sql="";
  $counter=1;

  //cyklus
  foreach($values as $value){
  
    //zpracovani hodnoty
    if($isnumeric){$value=intval($value);}
    else{$value="'"._safeStr($value)."'";}

    //prirazeni k dotazu
    $sql.=$column."=".$value;
    if($counter!=$values_count){$sql.=" OR ";}

  $counter++;
  }
}
else{
  $sql="1";
}

return $sql;
}






/*---- zobrazeni zpravy o selhani systemu a konec ----*/

function _systemFailure($msg){
_systemMessage("Selhání systému", "<p>".$msg."</p>");
exit;
}






/*---- zobrazeni systemove zpravy ----*/

function _systemMessage($title, $message){
echo "

<div style='text-align: center !important; margin: 10px !important;'>
<div style='text-align: left !important; margin: 0 auto !important; width: 600px !important; font-family: monospace !important; font-size: 13px !important; color: #000000 !important; background-color: #ffffff !important; border: 1px solid #ffffff !important; position: relative; z-index: 999;'>
<div style='border: 1px solid #000000 !important; padding: 10px; overflow: auto !important;'>
<h1 style='color: #000000 !important; font-size: 20px !important; border-bottom: 2px solid #ff6600 !important;'>".$title."</h1>
".$message."
</div>
</div>
</div>

";
}







/*---- vratit kod systemoveho formulare ----*/

function _uniForm($id, $vars=array(), $notitle=false){

//priprava
global $_lang;
$content="";
$title="";

//typ
switch($id){


  /*--- prihlaseni (return{admin,mod,-1},returnpath) ---*/
  case "login":
  
    //titulek
    $title=$_lang['login.title'];
  
    //zpravy
    if(isset($_GET['_mlr'])){
      switch($_GET['_mlr']){
      case 0: $content.=_formMessage(2, $_lang['login.failure']); break;
      case 1: if(_loginindicator and !_administration){$content.=_formMessage(1, $_lang['login.success']);} break;
      case 2: if(!_loginindicator){$content.=_formMessage(2, $_lang['login.blocked.message']);} break;
      case 3: if(!_loginindicator){$content.=_formMessage(3, $_lang['login.securitylogout']);} break;
      case 4: if(!_loginindicator){$content.=_formMessage(1, $_lang['login.selfremove']);} break;
      case 5: if(!_loginindicator){$content.=_formMessage(2, str_replace(array("*1*", "*2*"), array(_maxloginattempts, _maxloginexpire/60), $_lang['login.attemptlimit']));} break;
      }
    }

    //obsah
    if(!_loginindicator){
    
      $redirparam="<input type='hidden' name='redir' value='".$vars['return']."' />";

      //kod formulare
      $content.=_formOutput(
      "login_form",
      _indexroot."remote/login.php".(($vars['return']!="admin" and $vars['return']!="mod")?"?_return=".urlencode($vars['returnpath']):''),
      array(
        array($_lang['login.username'], "<input type='text' name='username' class='inputsmall'"._restoreGetFdValue("username")." maxlength='24' />".$redirparam),
        array($_lang['login.password'], "<input type='password' name='password' class='inputsmall' />")
      ),
      array("username", "password"),
      $_lang['global.login'],
      "&nbsp;&nbsp;<label><input type='checkbox' name='persistent' value='1' /> ".$_lang['login.persistent']."</label>"
      );
      
        //odkazy
        if(_registration or _lostpass){
        $content.="\n\n<p>\n".
        ((_registration and !_administration)?"<a href='"._indexroot."index.php?m=reg'>".$_lang['mod.reg']." &gt;</a>\n":'').
        (_lostpass?((_registration and !_administration)?"<br />":'')."<a href='"._indexroot."index.php?m=lostpass'>".$_lang['mod.lostpass']." &gt;</a>\n":'').
        "</p>";
        }

    }
    else{
    $content.="<p>".$_lang['login.ininfo']." <em>"._loginname."</em> - <a href='"._indexroot."remote/logout.php'>".$_lang['usermenu.logout']."</a>.</p>";
    }
  
  break;
  
  
  /*--- zprava o neverejnosti obsahu (0-notpublicsite) ---*/
  case "notpublic":
  $form=_uniForm("login", array('return'=>-1, 'returnpath'=>urlencode(_indexOutput_url)), true);
  if(!isset($vars[0])){$vars[0]=false;}
  $content="<p>".$_lang['notpublic.p'.(($vars[0]==true)?'2':'')]."</p>".$form[0];
  $title=$_lang['notpublic.title'];
  break;
  
  
  /*--- formular pro zaslani prispevku / komentare (posttype,posttarget,xhome) ---*/
  case "postform":
  $title="";
  $notitle=true;

  //pole
  $inputs=array();
  $captcha=_captchaInit();
  $content=_jsLimitLength(3072, "postform", "text");
    if(_loginindicator==0){$inputs[]=array($_lang['posts.guestname'], "<input type='text' name='guest' maxlength='24' class='inputsmall'"._restoreGetFdValue("guest")." />");}
    if($vars['xhome']==-1){$inputs[]=array($_lang[(($vars['posttype']!=5)?'posts.subject':'posts.topic')], "<input type='text' name='subject' class='inputsmall' maxlength='22'"._restoreGetFdValue("subject")." />");}
    $inputs[]=$captcha;
    $inputs[]=array($_lang['posts.text'], "<textarea name='text' class='areamedium' rows='5' cols='33'>"._restoreGetFdValue("text", null, true)."</textarea><input type='hidden' name='_posttype' value='".$vars['posttype']."' /><input type='hidden' name='_posttarget' value='".$vars['posttarget']."' /><input type='hidden' name='_xhome' value='".$vars['xhome']."' />", true);

  //formular
  if(isset($_GET['page'])){$pagingpage=intval($_GET['page']);}
  else{$pagingpage=1;}
  $content.=_formOutput('postform', _addGetToLink(_indexroot."remote/post.php", "_return=".urlencode(_addGetToLink(_indexOutput_url, "page=".$pagingpage))), $inputs, array("text"), null, _getPostformControls("postform", "text"));

  break;


}
  
//return
if((_template_autoheadings==1 or _administration==1) and $notitle==false){$content="<h1>$title</h1>\n".$content;}
return array($content, $title);

}






/*---- validace emailove adresy ----*/

function _validateEmail($email){
  $isValid = true;
   $atIndex = mb_strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = mb_substr($email, $atIndex+1);
      $local = mb_substr($email, 0, $atIndex);
      $localLen = mb_strlen($local);
      $domainLen = mb_strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if(function_exists("checkdnsrr")){
        if ($isValid && !(checkdnsrr($domain,"MX") ||
   checkdnsrr($domain,"A")))
        {
           // domain not found in DNS
           $isValid = false;
        }
      }
   }
   return $isValid;
}






/*---- validace url v absolutnim tvaru ----*/

function _validateURL($url){
return preg_match('|^http:\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,6}((:[0-9]{1,5})?\/.*)?$|i', $url);
}






/*---- odstraneni nezadoucich odradkovani a mezer z retezce ----*/

function _wsTrim($string){
$from=array("|(\r\n){3,}|s", "|  +|s");
$to=array("\r\n\r\n", " ");
return preg_replace($from, $to, trim($string));
}






/*---- odhlaseni uzivatele ----*/

function _userLogout(){
if(!defined('_loginindicator') or _loginindicator==1){
$_SESSION[_sessionprefix."user"]=null;
$_SESSION[_sessionprefix."password"]=null;
$_SESSION[_sessionprefix."ip"]=null;
unset($_SESSION[_sessionprefix."user"]);
unset($_SESSION[_sessionprefix."password"]);
unset($_SESSION[_sessionprefix."ip"]);
if(isset($_COOKIE[_sessionprefix."persistent_key"])){
    $_SESSION[_sessionprefix."persistent_key_destroy"]=true;
}
}
}






/*---- kontrolovat pritomnost a podporu formatu gd knihovnou ----*/

function _checkGD($check_format_support=null, $returnbool=false){
$img=null;

  //otestovani podpory
  if(!function_exists("gd_info")){$img="zElEQVR4nGP538jyiYGFj4HlP8sClgSW/4f2szDYATEDUIShkYXBwpPl9R8GFtEzjWAxEP5z+YPDQRbmDQ4OQLEGhgUPQaIKYBKsi4HhIMvfhU4KQLbAywWOQHHuCY4sjO9/GDCwMCcjqX8n9IGB5XfCn2cToXpZN4H0/meAgI8sDB/qeyF2MvxlYHhwxVoUyGIG8v+DxQQmMqio/IO4CgiYwKz/v9kY/jMCWY8erw1V6ugHsni/de1ntgPL8jT4KCwFsQQtLRm+ADUBAA7YQI0qsisiAAAAAElFTkSuQmCC";}
  elseif($check_format_support!=null){
    $info=gd_info();
    $support=false;
      switch(mb_strtolower($check_format_support)){
      case "png": if(isset($info['PNG Support']) and $info['PNG Support']==true){$support=true;} break;
      case "jpg": case "jpeg": if((isset($info['JPG Support']) and $info['JPG Support']==true) or (isset($info['JPEG Support']) and $info['JPEG Support']==true)){$support=true;} break;
      case "gif": if(isset($info['GIF Read Support']) and $info['GIF Read Support']==true){$support=true;} break;
      }
    if($support==false){$img="2klEQVR4nGP538jyh4HlOwPLf5Y/LEdY/mtEsjDcaGRhYACKMDSy/NMC8h0YWJgmdLJsWyPP4nUKKH7BgOEgC4PeVm8GFgYmb6DKt+sYhIVZOCVe/ADpYgDJLkhocGRhkO4DyjL8CVgEJIH2AElXhoaDINP/MbB8285VPJ2FkVF1yWeWD+3Nn8RZJE8yAfWCTACBj0B1HwQYGA4A7Wdg+HJcdZETyAQGHqAwA5gl4MkwCcKCAijrgQwDy7+dHLINQnkJU4FiFk+Bgg1AX3gyKLQoQExmYFEAKQUAOQ48juT2O7oAAAAASUVORK5CYII=";}
  }

  //odeslani chyboveho obrazku
  if($img!=null and $returnbool==false){
    header("Content-Type: image/PNG");
    echo base64_decode("iVBORw0KGgoAAAANSUhEUgAAAEQAAAAWAQAAAAEmKTd1AAAACXBIWXMAAAsSAAALEgHS3X78AAAA".$img);
    exit;
  }
  else{
    if($img==null){return true;}
    else{return false;}
  }
}






/*---- kontrolovat existenci vsech klicu v globalnim poli, jinak konec ----*/

function _checkKeys($array_name, $keys){
  global $$array_name;
  if(!is_array($$array_name)){exit;}
  foreach($keys as $key){if(!isset(${$array_name}[$key])){exit;}}
}

/* XXL */
/** Vrácení rozměrů obrázku po zmenšení
* @param string název zmenšovaného souboru
* @param int maximální šířka výsledného obrázku, 0 pokud na ní nezáleží
* @param int maximální výška výsledného obrázku, 0 pokud na ní nezáleží
* @return array ($width, $height) výsledná šířka a výška
* @copyright Jakub Vrána, http://php.vrana.cz/
*/
function _image_shrink_size($file_in, $max_x = 0, $max_y = 0) {
    list($width, $height) = getimagesize($file_in);
    if (!$width || !$height) {
        return array(0, 0);
    }
    if ($max_x && $width > $max_x) {
        $height = round($height * $max_x / $width);
        $width = $max_x;
    }
    if ($max_y && $height > $max_y) {
        $width = round($width * $max_y / $height);
        $height = $max_y;
    }
    return array($width, $height);
}

/** Převzorkování obrázku GIF, PNG nebo JPG
* @param string název zmenšovaného souboru
* @param string název výsledného souboru
* @param int šířka výsledného obrázku
* @param int výška výsledného obrázku
* @return bool true, false v případě chyby
* @copyright Jakub Vrána, http://php.vrana.cz/
*/
function _image_resize($file_in, $file_out, $width, $height) {
    $imagesize = getimagesize($file_in);
    if ((!$width && !$height) || !$imagesize[0] || !$imagesize[1]) {
        return false;
    }
    if ($imagesize[0] == $width && $imagesize[1] == $height) {
        return copy($file_in, $file_out);
    }
    switch ($imagesize[2]) {
        case 1: $img = imagecreatefromgif($file_in); break;
        case 2: $img = imagecreatefromjpeg($file_in); break;
        case 3: $img = imagecreatefrompng($file_in); break;
        default: return false;
    }
    if (!$img) {
        return false;
    }
    $img2 = imagecreatetruecolor($width, $height);
    imagecopyresampled($img2, $img, 0, 0, 0, 0, $width, $height, $imagesize[0], $imagesize[1]);
    if ($imagesize[2] == 2) {
        return imagejpeg($img2, $file_out);
    } elseif ($imagesize[2] == 1 && function_exists("imagegif")) {
        imagetruecolortopalette($img2, false, 256);
        return imagegif($img2, $file_out);
    } else {
        return imagepng($img2, $file_out);
    }
}

function recursive_remove_directory($directory, $empty=FALSE)
 {
     if(substr($directory,-1) == '/')
     {
         $directory = substr($directory,0,-1);
     }
     if(!file_exists($directory) || !is_dir($directory))
     {
         return FALSE;
     }elseif(is_readable($directory))
     {
         $handle = opendir($directory);
         while (FALSE !== ($item = readdir($handle)))
         {
             if($item != '.' && $item != '..')
             {
                 $path = $directory.'/'.$item;
                 if(is_dir($path))
                 {
                     recursive_remove_directory($path);
                 }else{
                     unlink($path);
                 }
             }
         }
         closedir($handle);
         if($empty == FALSE)
         {
             if(!rmdir($directory))
             {
                 return FALSE;
             }
         }
     }
     return TRUE;
 }
?>