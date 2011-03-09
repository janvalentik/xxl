<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*-- vystup --*/
$content="";

  /*-- navigace--*/
  if($query['visible']==1){
  $content.="<div class='article-navigation'>".$_lang['article.category'].": ";
    for($i=1; $i<=3; $i++){
      if($query['home'.$i]!=-1){
      $hometitle=mysql_fetch_array(mysql_query("SELECT title FROM `"._mysql_prefix."-root` WHERE id=".$query['home'.$i]));
      $content.="<a href='"._linkRoot($query['home'.$i])."'>".$hometitle['title']."</a>";
      }
      if($i!=3 and $query['home'.($i+1)]!=-1){$content.=", ";}
    }
  $content.="</div>\n";
  }

  /*-- titulek --*/
  $title=$query['title'];
  if(_template_autoheadings){
  $content.="<h1>".$title."</h1>\n";
  }

/*-- perex --*/
$content.="<p class='article-perex'>";
// volba pripony
$path = _indexroot.'upload/img/perex/'.$id.'.';
if(file_exists($path.'png'))
{$path .= 'png';}
elseif(file_exists($path.'jpg'))
{$path .= 'jpg';}
elseif(file_exists($path.'gif'))
{$path .= 'gif';}
else {$path = null;}

// vystup
$content.= (!is_null($path)?'<a href="'.$path.'" rel="lightbox"><img src="'.$path.'" class="article-image" title="'.$title.'" alt="'.$title.'" /></a>':'');
$content.=_parseHCM($query['perex']);
$content.="</p><br clear='all' />\n";



  /*-- obsah --*/
  $content.=_parseHCM($query['content']);

  /*-- informacni tabulka --*/

    //priprava
    $info=array(
    "basicinfo"=>null,
    "idlink"=>null,
    "rateresults"=>null,
    "rateform"=>null,
    "infobox"=>null
    );

      //zakladni informace
      if($query['showinfo']==1){
        $info['basicinfo']="
        <strong>".$_lang['article.author'].":</strong> "._linkUser($query['author'])."<br />
        <strong>".$_lang['article.posted'].":</strong> "._formatTime($query['time'])."<br />
        <strong>".$_lang['article.readed'].":</strong> ".$query['readed']."x
        ";
      }

      //ID clanku
      if(_loginright_adminart){
      $info['idlink']=(($info['basicinfo']!=null)?"<br />":'')."<strong>".$_lang['global.id'].":</strong> <a href='admin/index.php?p=content-articles-edit&amp;id=".$id."&amp;returnid=load&amp;returnpage=1'>".$id." <img src='"._templateImage("icons/edit.gif")."' alt='edit' class='icon' /></a>";
      }

      //vysledky hodnoceni
      if($query['rateon']==1 and _ratemode!=0){

        if($query['ratenum']!=0){
          /*procenta*/  if(_ratemode==1){$rate=(round($query['ratesum']/$query['ratenum']))."%";}
          /*znamka*/    else{$rate=round(-0.04*($query['ratesum']/$query['ratenum'])+5);}
          $rate.=" (".$_lang['article.rate.num']." ".$query['ratenum']."x)";
        }
        else{
          $rate=$_lang['article.rate.nodata'];
        }

        $info['rateresults']=(($info['basicinfo']!=null or $info['idlink']!=null)?"<br />":'')."<strong>".$_lang['article.rate'].":</strong> ".$rate;
      }

      //formular hodnoceni
      $rateform_used=false;
      if($query['rateon']==1 and _ratemode!=0 and _loginright_artrate and _iplogCheck(3, $query['id'])){
$info['rateform']="
<strong>".$_lang['article.rate.title'].":</strong>
<form action='"._indexroot."remote/artrate.php' method='post'>
<input type='hidden' name='id' value='".$query['id']."' />
";

        if(_ratemode==1){
          //procenta
          $info['rateform'].="<select name='r'>\n";
            for($x=0; $x<=100; $x+=10){
              if($x==50){$selected=" selected='selected'";}else{$selected="";}
              $info['rateform'].="<option value='".$x."'".$selected.">".$x."%</option>\n";
            }
          $info['rateform'].="</select>&nbsp;\n<input type='submit' value='".$_lang['article.rate.submit']."' />";
        }
        else{
          //znamky
          $info['rateform'].="<table class='ratetable'>\n";
          for($i=0; $i<2; $i++){
            $info['rateform'].="<tr class='r".$i."'>\n";
            if($i==0){$info['rateform'].="<td rowspan='2'><img src='"._templateImage("icons/rate-good.gif")."' alt='good' class='icon' /></td>\n";}
              for($x=1; $x<6; $x++){
                if($i==0){$info['rateform'].="<td><input type='radio' name='r' value='".((5-$x)*25)."' /></td>\n";}
                else{$info['rateform'].="<td>".$x."</td>\n";}
              }
            if($i==0){$info['rateform'].="<td rowspan='2'><img src='"._templateImage("icons/rate-bad.gif")."' alt='bad' class='icon' /></td>\n";}
            $info['rateform'].="</tr>\n";
          }
$info['rateform'].="
<tr><td colspan='7'><input type='submit' value='".$_lang['article.rate.submit']." &gt;' /></td></tr>
</table>
";
        }

      $info['rateform'].="</form>\n";
      }

      //infobox
      if($query['infobox']!=""){
      $info['infobox']=_parseHCM($query['infobox']);
      }

    //sestaveni kodu
    if(count(_arrayRemoveValue($info, null))!=0){
$path=_indexroot."upload/article_".$id."/";
$handler = @opendir($path);
while ($file = @readdir($handler)) {
  if ($file != "." && $file != "..") {
    $files[] = $file;
  }
}
@closedir($handler);
if (count($files)>0) {
	$content.="<div id='attachments'><h3>Soubory přiložené k článku</h3>";
  if (is_array($files)){
    $content.="<table>";
    foreach ($files as $key=>$value) {
      //ikona
      $iteminfo=pathinfo($value);
      if(!isset($iteminfo['extension'])){$iteminfo['extension']="";}
      $ext=mb_strtolower($iteminfo['extension']);
      $image=false;
      if(in_array($ext, array("rar", "zip", "tar", "gz", "tgz", "7z", "cab", "xar", "xla", "777", "alz", "arc", "arj", "bz", "bz2", "bza", "bzip2", "dz", "gza", "gzip", "lzma", "lzs", "lzo", "s7z", "taz", "tbz", "tz", "tzip"))){$icon="archive";}
      elseif(in_array($ext, array("jpg", "jpeg", "png", "gif", "bmp", "jp2", "tga", "pcx", "tif", "ppf", "pct", "pic", "ai", "ico"))){$icon="image"; $image=true;}
      elseif(in_array($ext, array("pdf"))){$icon="pdf";}
      elseif(in_array($ext, array("doc", "docx", "rtf"))){$icon="doc";}
      elseif(in_array($ext, array("csv", "dif", "xls", "rtf", "xlsb", "xlam", "xla", "xlsx"))){$icon="excel";}
      elseif(in_array($ext, array("otf", "tff"))){$icon="font";}
      elseif(in_array($ext, array("pdd", "psd", "eps", "psb", "pxr"))){$icon="psd";}
      elseif(in_array($ext, array("ai", "ait", "svg"))){$icon="illustrator";}
      elseif(in_array($ext, array("ppt", "pptx", "ppsx", "pps", "potx"))){$icon="ppt";}
      elseif(in_array($ext, array("accdb"))){$icon="access";}
      elseif(in_array($ext, array("xml"))){$icon="xml";}
      elseif(in_array($ext, array("sql", "py", "asp", "cgi", "htaccess", "txt", "nfo", "css", "ini", "bat", "inf", "me", "slam", "inc"))){$icon="editable";}
      elseif(in_array($ext, array("php", "php3", "php4", "php5"))){$icon="php";}
      elseif(in_array($ext, array("html", "htm", "xhtml", "shtml", "phtml"))){$icon="html";}
      elseif(in_array($ext, array("wav", "mp3", "mid", "rmi", "wma", "m4a", "xac", "aif", "au", "voc", "snd", "vox", "ogg", "flac", "aac", "amr", "asf", "rm", "ra", "ac3"))){$icon="media";}
      elseif(in_array($ext, array("3gp", "mp4", "mpeg", "mpg", "wmv", "avi", "mov", "xvid", "vob"))){$icon="movie";}
      elseif(in_array($ext, array("flv", "swf"))){$icon="flash";}
      elseif(in_array($ext, array("exe", "com", "bat", "dll"))){$icon="executable";}
      elseif(in_array($ext, array("sld", "slam", "js",))){$icon="sl";}
      elseif(in_array($ext, array("deb", "tar.gz"))){$icon="linux";}
      elseif(in_array($ext, array("vcf"))){$icon="vcf";}
      else{$icon="other";}
    	$content.="<tr".(is_int($y/2) ? " class='hl'":"")."><td class='icon'><img src=\"admin/images/icons/fman/".$icon.".gif\" alt=\"file\" /></td><td><a href=\""._htmlStr($path.$value)."\">".$value."</a></td></tr>";
      $y++;
    }
    $content.="</table>";
  }
  $content.="</div>";
}

      //zacatek tabulky
$content.="
<div class='anchor'><a name='ainfo'></a></div>
<table class='article-info'>
<tr valign='top'>
";

        //prvni bunka
        if($info['basicinfo']!=null or $info['idlink']!=null or $info['rateresults']!=null or ($info['infobox']!=null and $info['rateform']!=null)){
        $content.="<td>".$info['basicinfo'].$info['idlink'].$info['rateresults'];

          //vlozeni formulare pro hodnoceni, pokud je infobox obsazen
          if($info['rateform']!=null and ($info['infobox']!=null or $info['basicinfo']==null)){
          $content.=(($info['basicinfo']!=null)?"<br />":'')."<br />".$info['rateform'];
          $rateform_used=true;
          }

        $content.="\n</td>\n";

        }

        //druha bunka
        if($info['infobox']!=null or ($rateform_used==false and $info['rateform']!=null)){
        $content.="<td>";
          if($info['infobox']!=null){$content.=$info['infobox'];}
          if($rateform_used==false){$content.=$info['rateform'];}
        $content.="</td>";
        }

      //konec tabulky
      $content.="\n</tr>\n</table>\n";

    }

// Facebook - Líbí se mi
if (_fcbbutton){
  $content.="<iframe allowtransparency=\"true\" src=\"http://www.facebook.com/widgets/like.php?href="._url."/"._indexOutput_url."\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; width:450px; height:80px\"></iframe>\n";
}
// Facebook.com Share
if (_socialfcb){
  $content.="<a href='http://www.facebook.com/share.php?u="._url."/"._indexOutput_url."&amp;t="._title."' title='Sdílet na Facebooku' target='_blank'><img src=\"remote/share/facebook.png\" class=\"share\" alt=\"Sdílej na facebooku\" /></a>\n";
}
// Topclanky.cz Share
if (_socialtopclanky){
  $content.="<a href='http://www.topclanky.cz/pridat-odkaz/?kde="._url."/"._indexOutput_url."&amp;nadpis="._title."' title='topclanky.cz' target='_blank'><img src=\"remote/share/topclanky.png\" class=\"share\" alt=\"Přidej na Topčlánky\" /></a>\n";
}
// Twitter.com Share
if (_socialtwitter){
  $content.="<a href='http://twitter.com/home?status=Teď čtu pěkný članek: "._url."/"._indexOutput_url."' target='_blank'><img src=\"remote/share/twitter.png\" class=\"share\" alt=\"Sdílej na twitteru\" /></a>\n";
}

// Delicio.us Share
if (_socialdelicio){
  $content.="<a href='http://del.icio.us/post?url="._url."/"._indexOutput_url.";title="._title."' target='_blank'><img src=\"remote/share/delicio.png\" class=\"share\" alt=\"Sdílej na Delico\" /></a>\n";
}

// Jagg.cz Share
if (_socialjagg){
  $content.=" <a href='http://www.jagg.cz/bookmarks.php?action=add&amp;address="._url."/"._indexOutput_url."&amp;title="._title."' target='_blank' title='Jaggni to !'><img src=\"remote/share/jagg.png\" class=\"share\" alt=\"Sdílej na Jagg.cz\" /></a>\n";
}

// Zalinkuj.cz Share
if (_sociallinkuj){
  $content.=" <a href='http://linkuj.cz/?id=linkuj&amp;url="._url."/"._indexOutput_url."&amp;title="._title."' target='_blank' title='Linkuj si !'><img src=\"remote/share/linkuj.png\" class=\"share\" alt=\"Zalinkuj\" /></a>\n";
}

// Vybrali.sme.sk Share
if (_socialvybralisme){
  $content.="<a href='http://vybrali.sme.sk/submit.php?url="._url."/"._indexOutput_url."' title='vybrali.sme.sk' target='_blank'><img src=\"remote/share/vybralismesk.png\" class=\"share\" alt=\"vybrali.sme.sk\" /></a>\n";
}






  //odkaz na tisk
  if(_printart){$content.="\n<p><a href='"._indexroot."printart.php?id=".$id."' target='_blank'><img src='"._templateImage("icons/print.gif")."' alt='print' class='icon' /> ".$_lang['article.print']."</a></p>\n";}

  //komentare
  if($query['comments']==1 and _comments){
    	require_once(_indexroot.'require/functions-posts.php');
      $content.=_postsOutput(2, $id, $query['commentslocked']);
  }

  //zapocteni precteni
  if($query['confirmed']==1 and $query['time']<=time() and _iplogCheck(2, $id)){
  mysql_query("UPDATE `"._mysql_prefix."-articles` SET readed=".($query['readed']+1)." WHERE id=".$id);
  _iplogUpdate(2, $id);
  }

?>