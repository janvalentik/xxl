<?php

/*--- inicializace jadra ---*/
define('_indexroot', '../../');
require(_indexroot."core.php");

/*--- vystup ---*/
if(!_loginright_adminfman){exit;}

/*---- priprava promennych ----*/
if(!_loginright_adminfmanlimit){$defdir="../../upload/";}
else{$defdir="../../upload/"._loginname."/";}

//adresar
if(isset($_GET['dir'])){
  $dir=str_replace("\\", "/", $_GET['dir']);
  if(mb_substr($dir, -1, 1)!="/"){$dir.="/";}
  $dir=_parsePath($dir);
  if((!_loginright_adminfmanplus and mb_substr_count($dir, "..")>2) or mb_substr_count($dir, "..")>mb_substr_count(_indexroot, "..")){$dir=$defdir;}
  if(!_loginright_adminfmanplus or _loginright_adminfmanlimit){if(mb_substr($dir, 0, mb_strlen($defdir))!=$defdir){$dir=$defdir;}}
  if(!@file_exists($dir) or !@is_dir($dir)){$dir=$defdir;}
}
else{
$dir=$defdir;
}

//vytvoreni vychoziho adresare
if(!(@file_exists($defdir) and @is_dir($defdir))){
$test=@mkdir($defdir, 0777, true);
  if(!$test){$continue=false; print _formMessage(3, $_lang['admin.fman.msg.defdircreationfailure']);}
  else{@chmod($defdir, 0777);}
}

$highlight=false;

//vypis adresaru
$handle=@opendir($dir);
$items=array();
while($item=@readdir($handle)){if(@is_dir($dir.$item) and $item!="." and $item!=".."){$items[]=$item;}}
natsort($items); $items=array_merge(array(".."), $items);

print '
<div id="gallery-browser-actdir"><strong>'.$_lang['admin.fman.currentdir'].':</strong> /'.mb_substr($dir, mb_strlen(_indexroot)).'</div>
<table id="fman-list">
';
foreach($items as $item){

  //adresar
  if($item==".."){
  	if(mb_substr($dir, mb_strlen($dir)-3, 3)!="../" and $dir!="./"){
    	$dirhref=mb_substr($dir, 0, mb_strrpos($dir, '/'));
    	$dirhref=mb_substr($dirhref, 0, mb_strrpos($dirhref, '/'));
  	}
  	else{
  	    //nejit za korenovy adresar systemu
    	continue;
  	}
  }
  else{
    $dirhref=$dir.$item;
  }

if($highlight){$hl_class=" class='hl'";}else{$hl_class="";}

print "
<tr".$hl_class.">
<td colspan='2'><a href='#' onclick=\"return _sysGalBrowse('"._htmlStr($dirhref)."/');\"><img src='images/icons/fman/directory.gif' alt='dir' class='icon' />"._htmlStr(_cutStr($item, 64, false))."</a></td>
</tr>
";

$highlight=!$highlight;
}

// vypis souboru
rewinddir($handle);
$items=array();
while($item=@readdir($handle)){if(!@is_dir($dir.$item) and $item!=".."){$items[]=$item;}}
natsort($items);
foreach($items as $item){

  //ikona
  $iteminfo=pathinfo($item);
  $image=false;
  if(!isset($iteminfo['extension'])){$iteminfo['extension']="";}
  $ext=mb_strtolower($iteminfo['extension']);
  if(in_array($ext, array("rar", "zip", "tar", "gz", "tgz", "7z", "cab", "xar", "xla", "777", "alz", "arc", "arj", "bz", "bz2", "bza", "bzip2", "dz", "gza", "gzip", "lzma", "lzs", "lzo", "s7z", "taz", "tbz", "tz", "tzip"))){$icon="archive";}
  elseif(in_array($ext, array("jpg", "jpeg", "png", "gif", "bmp", "jp2", "tga", "pcx", "tif", "ppf", "pct", "pic", "ai", "ico"))){$icon="image"; $image=true;}
  elseif(in_array($ext, array("sql", "php", "php3", "php4", "php5", "phtml", "py", "asp", "cgi", "shtml", "htaccess", "txt", "nfo", "rtf", "html", "htm", "xhtml", "css", "js", "ini", "bat", "inf", "me", "slam", "inc"))){$icon="editable";}
  elseif(in_array($ext, array("wav", "mp3", "mid", "rmi", "wma", "mpeg", "mpg", "wmv", "3gp", "mp4", "m4a", "xac", "aif", "au", "avi", "voc", "snd", "vox", "ogg", "flac", "mov", "aac", "vob", "amr", "asf", "rm", "ra", "ac3", "swf", "flv"))){$icon="media";}
  elseif(in_array($ext, array("exe", "com", "bat", "dll"))){$icon="executable";}
  elseif(in_array($ext, array("sld", "slam"))){$icon="sl";}
  else{$icon="other";}

if($highlight){$hl_class=" class='hl'";}else{$hl_class="";}

print "
<tr".$hl_class.">
<td".(!$image?' class="noimage" colspan="2"':'')."><a".($image?' href="../'._htmlStr(mb_substr($dir.$item, mb_strlen(_indexroot))).'" rel="lightbox[galbr]" title="'.round(@filesize($dir.$item)/1024).'kB" target="_blank"':'')."><img src='images/icons/fman/".$icon.".gif' alt='file' class='icon' />"._htmlStr(_cutStr($item, 32, false))."</a></td>
".($image?'<td><a href="#" onclick="return _sysGalSelect(\''._htmlStr(mb_substr($dir.$item, mb_strlen(_indexroot))).'\')">'.$_lang['admin.content.manageimgs.insert.browser.use'].'</a></td>':'')."
</tr>
";

$highlight=!$highlight;
}

print '</table>';



?>