<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}


/*--- ulozeni ---*/
if(isset($_POST['template'])){

  //pole vstupu (nazev, checkbox?, zpracovani: 0-nic 1-htmlstr 2-intval)
  $settings_array=array(
  array("template",               false, 0),
  array("time_format",            false, 1),
  array("language",               false, 0),
  array("language_allowcustom",   true,  0),
  array("showpages",              false, 2),
  array("pagingmode",             false, 2),
  array("notpublicsite",          true,  0),
  array("title",                  false, 1),
  array("titletype",              false, 2),
  array("description",            false, 1),
  array("keywords",               false, 1),
  array("author",                 false, 1),
  array("url",                    false, 1),
  array("favicon",                true,  0),
  array("footer",                 false, 0),
  array("titleseparator",         false, 1),
  array("modrewrite",             true,  0),
  array("wysiwyg",                true,  0),
  array("ulist",                  true,  0),
  array("registration",           true,  0),
  array("registration_grouplist", true,  0),
  array("rules",                  false, 0),
  array("actmail",                false, 0),
  array("lostpass",               true,  0),
  array("search",                 true,  0),
  array("comments",               true,  0),
  array("messages",               true,  0),
  array("captcha",                true,  0),
  array("bbcode" ,                true,  0),
  array("smileys",                true,  0),
  array("lightbox",               true,  0),
  array("codemirror",             true,  0),
  array("printart",               true,  0),
  array("ratemode",               false, 2),
  array("rss",                    true,  0),
  array("profileemail",           true,  0),
  array("atreplace",              false, 1),
  array("maxloginattempts",       false, 2),
  array("maxloginexpire",         false, 2),
  array("artreadexpire",          false, 2),
  array("artrateexpire",          false, 2),
  array("pollvoteexpire",         false, 2),
  array("postsendexpire",         false, 2),
  array("commentsperpage",        false, 2),
  array("messagesperpage",        false, 2),
  array("extratopicslimit",       false, 2),
  array("rsslimit",               false, 2),
  array("sboxmemory",             false, 2),
  array("mailerusefrom",          true,  0),
  array("uploadavatar",           true,  0),
  array("postadmintime",          false, 2),
  array("defaultgroup",           false, 2),
  array("adminintro",             false, 0),
  array("adminlinkprivate",       true,  0),
  array("adminscheme",            false, 2),
  array("fcbbutton",              false, 2),
  array("activation",             false, 2), 
  array("socialsend",             false, 2),
  array("socialfcb",              false, 2),
  array("socialtopclanky",        false, 2),
  array("socialtwitter",          false, 2),
  array("socialdelicio",          false, 2),
  array("socialjagg",             false, 2), 
  array("sociallinkuj",           false, 2),
  array("socialvybralisme",       false, 2),
  array("fcborsystemcomments",    false, 2),
  array("pocitadlo",              false, 0),
  array("avatars",                false, 2),
  array("postlinks",              false, 2),
  array("urlgenerate",            false, 2),
  array("linkimage",              false, 2),
  array("selectart",             false, 2),
  
  );
 
 
 
  
  //ulozeni
  foreach($settings_array as $item){

    //nacteni a zpracovani hodnoty
    if($item[1]==false){
      if(!isset($_POST[$item[0]])){$_POST[$item[0]]="0";}
        switch($item[2]){
        case 0: $val=_safeStr($_POST[$item[0]]); break;
        case 1: $val=_safeStr(_htmlStr($_POST[$item[0]])); break;
        case 2: $val=intval($_POST[$item[0]]); break;
        }
    }
    else{
    $val=_checkboxLoad($item[0]);
    }
    
    //individualni akce
    switch($item[0]){
    case "adminintro": case "rules": $val=trim($val); break;
    case "url": $val=_removeSlashesFromEnd($val); break;
    case "defaultgroup": if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-groups` WHERE id=".$val), 0)==0){$val=3;} break;
    case "showpages": $val=intval(abs($val-1)/2); if($val==0){$val=1;} break;
    case "pagingmode": if($val<1 or $val>3){$val=1;} break;
    case "commentsperpage": case "messagesperpage": case "extratopicslimit": case "rsslimit": case "sboxmemory": if($val<=0){$val=1;} break;
    case "postadmintime": $val=$val*60*60; if($val<=0){$val=3600;} break;
    case "maxloginexpire": case "artreadexpire": case "pollvoteexpire": case "artrateexpire": $val=$val*60; if($val<0){$val=0;} break;
    case "maxloginattempts": if($val<1){$val=1;} break;
    case "postsendexpire": if($val<0){$val=0;} break;
    }

    //ulozeni
    mysql_query("UPDATE `"._mysql_prefix."-settings` SET val='".$val."' WHERE var='".$item[0]."'");
    
  }
  
  //presmerovani
  define('_tmp_redirect', 'index.php?p=settings&r=1');

}


/*--- priprava promennych ---*/

  //vyber motivu
  $template_select='<select name="template">';
    $handle=@opendir(_indexroot."templates");
    while($item=@readdir($handle)){
    if($item=="." or $item==".."){continue;}
    if($item==_template){$selected=' selected="selected"';}else{$selected="";}
    $template_select.='<option value="'.$item.'"'.$selected.'>'.$item.'</option>';
    }
    closedir($handle);
  $template_select.='</select>';
  
  //vyber jazyka
  $language_select='<select name="language">';
    $handle=@opendir(_indexroot."languages");
    while($item=@readdir($handle)){
    if($item=="." or $item==".." or @is_dir(_indexroot.$item)){continue;}

      //kontrola polozky
      $item=pathinfo($item);
      if(!isset($item['extension']) or $item['extension']!="php"){continue;}
      $item=mb_substr($item['basename'], 0, mb_strrpos($item['basename'], "."));
    
    if($item==_language){$selected=' selected="selected"';}else{$selected="";}
    $language_select.='<option value="'.$item.'"'.$selected.'>'.$item.'</option>';
    }
    closedir($handle);
  $language_select.='</select>';
  
  //vyber vychozi skupiny
  $defaultgroup_select=_tmp_authorSelect("defaultgroup", _defaultgroup, "id!=2", null, null, true);
  
  //vyber zobrazeni strankovani
  $pagingmode_select='<select name="pagingmode">';
  for($x=1; $x<4; $x++){
  if($x==_pagingmode){$selected=" selected='selected'";}else{$selected="";}
  $pagingmode_select.="<option value='".$x."'".$selected.">".$_lang['admin.settings.mods.pagingmode.'.$x]."</option>";
  }
  $pagingmode_select.='</select>';
  
  //vyber schematu administrace
  $adminscheme_select='<select name="adminscheme">';
  for($x=0; $x<10; $x++){
    if($x==_adminscheme){$selected=" selected='selected'";}else{$selected="";}
    $adminscheme_select.="<option value='".$x."'".$selected.">".$_lang['admin.settings.admin.adminscheme.'.$x]."</option>";
  }
  $adminscheme_select.='</select>';
  
  //vyber zpusobu zobrazeni titulku
  $titletype_select='<select name="titletype" class="selectmedium">';
  for($x=1; $x<3; $x++){
  if($x==_titletype){$selected=" selected='selected'";}else{$selected="";}
  $titletype_select.="<option value='".$x."'".$selected.">".$_lang['admin.settings.info.titletype.'.$x]."</option>";
  }
  $titletype_select.='</select>';
  
  //vyber zpusobu hodnoceni clanku
  $ratemode_select='<select name="ratemode">';
  for($x=0; $x<3; $x++){
  if($x==_ratemode){$selected=" selected='selected'";}else{$selected="";}
  $ratemode_select.="<option value='".$x."'".$selected.">".$_lang['admin.settings.mods.ratemode.'.$x]."</option>";
  }
  $ratemode_select.='</select>';
  
  
    //Výběr typu komentáře
  $fcborsystemcomments_select='<select name="fcborsystemcomments">';
  for($x=1; $x<3; $x++){
  if($x==_fcborsystemcomments){$selected=" selected='selected'";}else{$selected="";}
  $fcborsystemcomments_select.="<option value='".$x."'".$selected.">".$_lang['xxl.admin.fcborsystemcomments_type.'.$x]."</option>";
  }
  $fcborsystemcomments_select.='</select>';
  
  

/*--- vystup ---*/
$output.='
<p>'.$_lang['admin.settings.p'].'</p>

'.(isset($_GET['r'])?_formMessage(1, $_lang['admin.settings.saved']):'').'

<form action="index.php?p=settings" method="post">



  <!-- *************** MAIN *************** -->
  <fieldset>
  <legend>'.$_lang['admin.settings.main'].'</legend>

  <table>

  <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.main.template'].'</strong></td>
  <td>'.$template_select.'</td>
  <td class="lpad">'.$_lang['admin.settings.main.template.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.main.time_format'].'</strong></td>
  <td><input type="text" name="time_format" size="10" value="'._time_format.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.main.time_format.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.main.language'].'</strong></td>
  <td>'.$language_select.'</td>
  <td class="lpad">'.$_lang['admin.settings.main.language.help'].'</td>
  </tr>
  
    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.main.language_allowcustom'].'</strong></td>
  <td><input type="checkbox" name="language_allowcustom" value="1"'._checkboxActivate(_language_allowcustom).' /></td>
  <td class="lpad">'.$_lang['admin.settings.main.language_allowcustom.help'].'</td>
  </tr>
  
    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.main.notpublicsite'].'</strong></td>
  <td><input type="checkbox" name="notpublicsite" value="1"'._checkboxActivate(_notpublicsite).' /></td>
  <td class="lpad">'.$_lang['admin.settings.main.notpublicsite.help'].'</td>
  </tr>
  
  </table>

  </fieldset>



  <!-- *************** INFO *************** -->
  <fieldset>
  <legend>'.$_lang['admin.settings.info'].'</legend>

  <table>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.info.title'].'</strong></td>
  <td><input type="text" name="title" class="inputmedium" value="'._title.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.info.title.help'].'</td>
  </tr>
  
    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.info.titletype'].'</strong></td>
  <td>'.$titletype_select.'</td>
  <td class="lpad">'.$_lang['admin.settings.info.titletype.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.info.description'].'</strong></td>
  <td><input type="text" name="description" class="inputmedium" value="'._description.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.info.description.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.info.keywords'].'</strong></td>
  <td><input type="text" name="keywords" class="inputmedium" value="'._keywords.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.info.keywords.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.info.author'].'</strong></td>
  <td><input type="text" name="author" class="inputmedium" value="'._author.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.info.author.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.info.titleseparator'].'</strong></td>
  <td><input type="text" name="titleseparator" class="inputmedium" value="'._titleseparator.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.info.titleseparator.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.info.url'].'</strong></td>
  <td><input type="text" name="url" class="inputmedium" value="'._url.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.info.url.help'].'</td>
  </tr>
  
    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.info.favicon'].'</strong></td>
  <td><input type="checkbox" name="favicon" value="1"'._checkboxActivate(_favicon).' /></td>
  <td class="lpad">'.$_lang['admin.settings.info.favicon.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.info.footer'].'</strong></td>
  <td colspan="2"><textarea name="footer" rows="9" cols="33" class="areasmallwide codemirror xml">'._footer.'</textarea></td>
  </tr>

  </table>

  </fieldset>
  
  
  
  <!-- *************** MODS *************** -->
  <fieldset>
  <legend>'.$_lang['admin.settings.mods'].'</legend>

  <table>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.modrewrite'].'</strong></td>
  <td><input type="checkbox" name="modrewrite" value="1"'._checkboxActivate(_modrewrite)._inputDisable(@file_exists(_indexroot.".htaccess")).' /> '._tmp_docsIcon('modrewrite').'</td>
  <td class="lpad">'.$_lang['admin.settings.mods.modrewrite.help'].'</td>
  </tr>
  
      <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);"><td colspan="3">&nbsp;</td></tr>
  
    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.registration'].'</strong></td>
  <td><input type="checkbox" name="registration" value="1"'._checkboxActivate(_registration).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.registration.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.registration_grouplist'].'</strong></td>
  <td><input type="checkbox" name="registration_grouplist" value="1"'._checkboxActivate(_registration_grouplist).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.registration_grouplist.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.defaultgroup'].'</strong></td>
  <td>'.$defaultgroup_select.'</td>
  <td class="lpad">'.$_lang['admin.settings.mods.defaultgroup.help'].'</td>
  </tr>
  
      <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);"><td colspan="3">&nbsp;</td></tr>
  
  <tr valign="top">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.rules'].'</strong></td>
  <td colspan="2">
  <p>'.$_lang['admin.settings.mods.rules.help'].'</p>
  <textarea name="rules" rows="9" cols="33" class="areasmallwide codemirror xml">'._htmlStr(_rules).'</textarea>
  </td>
  </tr>

      <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);"><td colspan="3">&nbsp;</td></tr>

  <tr valign="top">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.actmail'].'</strong></td>
  <td colspan="2">
  <p>'.$_lang['admin.settings.mods.actmail.help'].'</p>
  <textarea name="actmail" rows="9" cols="33" class="areasmallwide codemirror xml">'._actmail.'</textarea>
  </td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.rss'].'</strong></td>
  <td><input type="checkbox" name="rss" value="1"'._checkboxActivate(_rss).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.rss.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.lostpass'].'</strong></td>
  <td><input type="checkbox" name="lostpass" value="1"'._checkboxActivate(_lostpass).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.lostpass.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.ulist'].'</strong></td>
  <td><input type="checkbox" name="ulist" value="1"'._checkboxActivate(_ulist).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.ulist.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.search'].'</strong></td>
  <td><input type="checkbox" name="search" value="1"'._checkboxActivate(_search).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.search.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.comments'].'</strong></td>
  <td><input type="checkbox" name="comments" value="1"'._checkboxActivate(_comments).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.comments.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.messages'].'</strong></td>
  <td><input type="checkbox" name="messages" value="1"'._checkboxActivate(_messages).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.messages.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.captcha'].'</strong></td>
  <td><input type="checkbox" name="captcha" value="1"'._checkboxActivate(_captcha).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.captcha.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.bbcode'].'</strong></td>
  <td><input type="checkbox" name="bbcode" value="1"'._checkboxActivate(_bbcode).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.bbcode.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.smileys'].'</strong></td>
  <td><input type="checkbox" name="smileys" value="1"'._checkboxActivate(_smileys).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.smileys.help'].'</td>
  </tr>
  
    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.lightbox'].'</strong></td>
  <td><input type="checkbox" name="lightbox" value="1"'._checkboxActivate(_lightbox).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.lightbox.help'].'</td>
  </tr>

  <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.codemirror'].'</strong></td>
  <td><input type="checkbox" name="codemirror" value="1"'._checkboxActivate(_codemirror).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.codemirror.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.printart'].'</strong></td>
  <td><input type="checkbox" name="printart" value="1"'._checkboxActivate(_printart).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.printart.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.ratemode'].'</strong></td>
  <td>'.$ratemode_select.'</td>
  <td class="lpad">'.$_lang['admin.settings.mods.ratemode.help'].'</td>
  </tr>

      <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);"><td colspan="3">&nbsp;</td></tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.pagingmode'].'</strong></td>
  <td>'.$pagingmode_select.'</td>
  <td class="lpad">'.$_lang['admin.settings.mods.pagingmode.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.showpages'].'</strong></td>
  <td><input type="text" name="showpages" class="inputmini" value="'.(_showpages*2+1).'" /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.showpages.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.commentsperpage'].'</strong></td>
  <td><input type="text" name="commentsperpage" class="inputmini" value="'._commentsperpage.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.commentsperpage.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.messagesperpage'].'</strong></td>
  <td><input type="text" name="messagesperpage" class="inputmini" value="'._messagesperpage.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.messagesperpage.help'].'</td>
  </tr>
  
    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.extratopicslimit'].'</strong></td>
  <td><input type="text" name="extratopicslimit" class="inputmini" value="'._extratopicslimit.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.extratopicslimit.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.rsslimit'].'</strong></td>
  <td><input type="text" name="rsslimit" class="inputmini" value="'._rsslimit.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.rsslimit.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.sboxmemory'].'</strong></td>
  <td><input type="text" name="sboxmemory" class="inputmini" value="'._sboxmemory.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.sboxmemory.help'].'</td>
  </tr>

      <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);"><td colspan="3">&nbsp;</td></tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.atreplace'].'</strong></td>
  <td><input type="text" name="atreplace" class="inputmini" value="'._atreplace.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.atreplace.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.profileemail'].'</strong></td>
  <td><input type="checkbox" name="profileemail" value="1"'._checkboxActivate(_profileemail).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.profileemail.help'].'</td>
  </tr>
  
    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.uploadavatar'].'</strong></td>
  <td><input type="checkbox" name="uploadavatar" value="1"'._checkboxActivate(_uploadavatar).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.uploadavatar.help'].'</td>
  </tr>
  
    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.mailerusefrom'].'</strong></td>
  <td><input type="checkbox" name="mailerusefrom" value="1"'._checkboxActivate(_mailerusefrom).' /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.mailerusefrom.help'].'</td>
  </tr>
  
  </table>

  </fieldset>
  
  
  
  <!-- *************** IPLOG EXPIRE TIMES ETC. *************** -->
  <fieldset>
  <legend>'.$_lang['admin.settings.iplog'].'</legend>

  <table>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.mods.postadmintime'].'</strong></td>
  <td><input type="text" name="postadmintime" class="inputsmall" value="'.(_postadmintime/60/60).'" /></td>
  <td class="lpad">'.$_lang['admin.settings.mods.postadmintime.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.iplog.maxloginattempts'].'</strong></td>
  <td><input type="text" name="maxloginattempts" class="inputsmall" value="'._maxloginattempts.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.iplog.maxloginattempts.help'].'</td>
  </tr>
  
    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.iplog.maxloginexpire'].'</strong></td>
  <td><input type="text" name="maxloginexpire" class="inputsmall" value="'.(_maxloginexpire/60).'" /></td>
  <td class="lpad">'.$_lang['admin.settings.iplog.maxloginexpire.help'].'</td>
  </tr>
  
    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.iplog.artreadexpire'].'</strong></td>
  <td><input type="text" name="artreadexpire" class="inputsmall" value="'.(_artreadexpire/60).'" /></td>
  <td class="lpad">'.$_lang['admin.settings.iplog.artreadexpire.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.iplog.artrateexpire'].'</strong></td>
  <td><input type="text" name="artrateexpire" class="inputsmall" value="'.(_artrateexpire/60).'" /></td>
  <td class="lpad">'.$_lang['admin.settings.iplog.artrateexpire.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.iplog.pollvoteexpire'].'</strong></td>
  <td><input type="text" name="pollvoteexpire" class="inputsmall" value="'.(_pollvoteexpire/60).'" /></td>
  <td class="lpad">'.$_lang['admin.settings.iplog.pollvoteexpire.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.iplog.postsendexpire'].'</strong></td>
  <td><input type="text" name="postsendexpire" class="inputsmall" value="'._postsendexpire.'" /></td>
  <td class="lpad">'.$_lang['admin.settings.iplog.postsendexpire.help'].'</td>
  </tr>

  </table>

  </fieldset>
  


  <!-- *************** ADMIN *************** -->
  <fieldset>
  <legend>'.$_lang['admin.settings.admin'].'</legend>

  <table>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.admin.adminscheme'].'</strong></td>
  <td>'.$adminscheme_select.'</td>
  <td class="lpad">'.$_lang['admin.settings.admin.adminscheme.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.admin.wysiwyg'].'</strong></td>
  <td><input type="checkbox" name="wysiwyg" value="1"'._checkboxActivate(_wysiwyg)._inputDisable(@file_exists(_indexroot."admin/modules/tinymce.slam")).' /> '._tmp_docsIcon('wysiwyg').'</td>
  <td class="lpad">'.$_lang['admin.settings.admin.wysiwyg.help'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['admin.settings.admin.adminlinkprivate'].'</strong></td>
  <td><input type="checkbox" name="adminlinkprivate" value="1"'._checkboxActivate(_adminlinkprivate).' /></td>
  <td class="lpad">'.$_lang['admin.settings.admin.adminlinkprivate.help'].'</td>
  </tr>
  </table>
  </fieldset>

  <!-- *************** XXL *************** -->
  <fieldset>
  <legend>XXL</legend>
 <table>
 <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>Facebook tlačítko "Líbí se mi"</strong></td>
  <td><input type="checkbox" name="fcbbutton" value="1"'._checkboxActivate(_fcbbutton).' /></td>
  <td class="lpad">zapne tlačítko "Líbíse mi" u článků</td>
  </tr>

 <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['xxl.admin.act'].'</strong></td>
  <td><input type="checkbox" name="activation" value="1"'._checkboxActivate(_activation).' /></td>
  <td class="lpad">'.$_lang['xxl.admin.act.desc'].'</td>
  </tr>

  <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['xxl.admin.settings.avatars'].'</strong></td>
  <td><input type="checkbox" name="avatars" value="1"'._checkboxActivate(_avatars).' /></td>
  <td class="lpad">'.$_lang['xxl.admin.settings.avatars.desc'].'</td>
  </tr>
  
  <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['xxl.admin.fcborsystemcomments'].'</strong></td>
  <td>'.$fcborsystemcomments_select.'</td>
  <td class="lpad">'.$_lang['xxl.admin.fcborsystemcomments.desc'].'</td>
  </tr>
  
  <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['xxl.admin.postlink.p'].'</strong></td>
  <td><input type="checkbox" name="postlinks" value="1"'._checkboxActivate(_postlinks).' /></td>
  <td class="lpad">'.$_lang['xxl.admin.postlink.desc'].'</td>
  </tr>
  
  <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['xxl.admin.urlgenerate'].'</strong></td>
  <td><input type="checkbox" name="urlgenerate" value="1"'._checkboxActivate(_urlgenerate).' /></td>
  <td class="lpad">'.$_lang['xxl.admin.urlgenerate.desc'].'</td>
  </tr>
  
  <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['xxl.admin.linkimage'].'</strong></td>
  <td><input type="checkbox" name="linkimage" value="1"'._checkboxActivate(_linkimage).' /></td>
  <td class="lpad">'.$_lang['xxl.admin.linkimage.desc'].'</td>
  </tr>
  
  <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad"><strong>'.$_lang['xxl.admin.select-art'].'</strong></td>
  <td><input type="checkbox" name="selectart" value="1"'._checkboxActivate(_selectart).' /></td>
  <td class="lpad">'.$_lang['xxl.admin.select-art.desc'].'</td>
  </tr>  
  

  </table>
  </fieldset>


  <fieldset>
  <legend>'.$_lang['xxl.admin.socialsetting'].'</legend>
  <table>
  <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad">'.$_lang['xxl.admin.socialfcb'].'</td>
  <td><input type="checkbox" name="socialfcb" value="1"'._checkboxActivate(_socialfcb).' /></td>
  <td class="lpad">'.$_lang['xxl.admin.socialfcb.desc'].'</td>
  </tr>


    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad">'.$_lang['xxl.admin.socialtopclanky'].'</td>
  <td><input type="checkbox" name="socialtopclanky" value="1"'._checkboxActivate(_socialtopclanky).' /></td>
  <td class="lpad">'.$_lang['xxl.admin.socialtopclanky.desc'].'</td>
  </tr>

    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad">'.$_lang['xxl.admin.socialtwitter'].'</td>
  <td><input type="checkbox" name="socialtwitter" value="1"'._checkboxActivate(_socialtwitter).' /></td>
  <td class="lpad">'.$_lang['xxl.admin.socialtwitter.desc'].'</td>
  </tr>


    <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad">'.$_lang['xxl.admin.socialdelicio'].'</td>
  <td><input type="checkbox" name="socialdelicio" value="1"'._checkboxActivate(_socialdelicio).' /></td>
  <td class="lpad">'.$_lang['xxl.admin.socialdelicio.desc'].'</td>
  </tr>


      <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad">'.$_lang['xxl.admin.socialjagg'].'</td>
  <td><input type="checkbox" name="socialjagg" value="1"'._checkboxActivate(_socialjagg).' /></td>
  <td class="lpad">'.$_lang['xxl.admin.socialjagg.desc'].'</td>
  </tr>

      <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad">'.$_lang['xxl.admin.sociallinkuj'].'</td>
  <td><input type="checkbox" name="sociallinkuj" value="1"'._checkboxActivate(_sociallinkuj).' /></td>
  <td class="lpad">'.$_lang['xxl.admin.sociallinkuj.desc'].'</td>
  </tr>

  <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);">
  <td class="rpad">'.$_lang['xxl.admin.socialvybralisme'].'</td>
  <td><input type="checkbox" name="socialvybralisme" value="1"'._checkboxActivate(_socialvybralisme).' /></td>
  <td class="lpad">'.$_lang['xxl.admin.socialvybralisme.desc'].'</td>
  </tr>


  <tr onmouseover="highlight_row(this, 1);" onmouseout="highlight_row(this, 0);"><td colspan="3">&nbsp;</td></tr>
  <tr valign="top">
  <td class="rpad">'.$_lang['admin.settings.pocitadlo'].'</td>
  <td colspan="2">
  <p>'.$_lang['admin.settings.pocitadlo.help'].'</p>
  <textarea name="pocitadlo" rows="9" cols="33" class="areasmallwide codemirror xml">'._htmlStr(_pocitadlo).'</textarea>
  </td>
  </tr>
  
  
  </table>

  </fieldset>



<br />
<input type="submit" value="'.$_lang['global.save'].'" />
<input type="reset" value="'.$_lang['global.reset'].'" onclick="return _sysConfirm();" />

</form>


';

?>