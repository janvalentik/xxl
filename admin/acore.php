<?php

/*-- kontrola jadra --*/
if(!defined('_core')){exit;}

/*-- wysiwyg --*/
function _tmp_wysiwyg(){
if(_wysiwyg and _loginwysiwyg){return @file_get_contents(_indexroot."admin/modules/tinymce.slam");}
}

/*-- pole typu stranek --*/
function _tmp_getTypeArray(){
return array(
1=>"section",
2=>"category",
3=>"book",
4=>"separator",
5=>"gallery",
6=>"link",
7=>"intersection",
8=>"forum"
);
}
$type_array=_tmp_getTypeArray();

/*-- poznamka --*/
function _tmp_smallNote($str){
  return "<p class='note'><img src='images/icons/note.png' alt='note' class='icon' />".$str."</p>";
}

/*-- odkaz na napovedu v dokumentaci --*/
function _tmp_docsIcon($name, $page=false){
  global $_lang;
  if($page){$param="p";}else{$param="help";}
  return "<a href='http://sunlight.shira.cz/feedback/docs.php?".$param."=".$name."' target='_blank' title='".$_lang['admin.link.docs.defin']."'><img src='images/icons/help.gif' alt='help' class='icon' /></a>";
}

/*-- cast sql dotazu pro pristup k ankete - 'where' --*/
function _tmp_pollAccess($csep=true){
if($csep){$csep=" AND ";}else{$csep="";}
return ((!_loginright_adminallart)?$csep."author="._loginid:$csep."(author="._loginid." OR (SELECT level FROM `"._mysql_prefix."-groups` WHERE id=(SELECT `group` FROM `"._mysql_prefix."-users` WHERE id=`"._mysql_prefix."-polls`.author))<"._loginright_level.")");
}

/*-- cast sql dotazu pro pristup k clanku - 'where' --*/
function _tmp_artAccess(){
if(_loginright_adminallart){return " AND (author="._loginid." OR (SELECT level FROM `"._mysql_prefix."-groups` WHERE id=(SELECT `group` FROM `"._mysql_prefix."-users` WHERE id=`"._mysql_prefix."-articles`.author))<"._loginright_level.")";}
else{return " AND author="._loginid;}
}

/*-- odkaz na clanek ve vypisu --*/
function _tmp_articleEditLink($art, $ucnote=true){
global $_lang;
$output="";

 //trida
  $class="";
  if($art['visible']==0 and $art['public']==1){$class=" class='invisible'";}
  if($art['visible']==1 and $art['public']==0){$class=" class='notpublic'";}
  if($art['visible']==0 and $art['public']==0){$class=" class='invisible-notpublic'";}

  //odkaz
  $output.="<a href='"._indexroot._linkArticle($art['id'])."' target='_blank'".$class.">";
  if($art['time']<=time()){$output.="<strong>";}
  $output.=$art['title'];
  if($art['time']<=time()){$output.="</strong>";}
  $output.="</a>";

  //poznamka o neschvaleni
  if($art['confirmed']!=1 and $ucnote){$output.="&nbsp;&nbsp;<small>(".$_lang['global.unconfirmed'].")</small>";}

return $output;
}

/*-- vyber stranky --*/
function _tmp_rootSelect($name, $type, $selected, $allowempty, $emptycustomcaption=null){
global $_lang;
$return="<select name='".$name."' class='ae-artselect'>\n";
$items=mysql_query("SELECT id,title,type FROM `"._mysql_prefix."-root` WHERE (type=".$type." OR type=7) AND intersection=-1 ORDER BY ord");
if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-root` WHERE type=".$type), 0)!=0){
if($allowempty){if($emptycustomcaption==null){$emptycustomcaption=$_lang['admin.content.form.category.none'];} $return.="<option value='-1' class='special'>".$emptycustomcaption."</option>";}
  while($item=mysql_fetch_array($items)){
    if($item['type']==$type){
      if($item['id']==$selected){$sel=" selected='selected'";}else{$sel="";}
      $return.="<option value='".$item['id']."'".$sel.">"._cutStr($item['title'], 22)."</option>\n";
    }
    else{
    $iitems=mysql_query("SELECT id,title,type FROM `"._mysql_prefix."-root` WHERE type=".$type." AND intersection=".$item['id']." ORDER BY ord");
      if(mysql_num_rows($iitems)!=0){
      $return.="<optgroup label='".$item['title']."'>\n";
        while($iitem=mysql_fetch_array($iitems)){
        if($iitem['id']==$selected){$sel=" selected='selected'";}else{$sel="";}
        $return.="<option value='".$iitem['id']."'".$sel.">"._cutStr($iitem['title'], 22)."</option>\n";
        }
      $return.="</optgroup>\n";
      }
    }
  }
}
else{$return.="\n<option value='-1'>".$_lang['global.nokit']."</option>\n";}
$return.="\n</select>\n";
return $return;
}

/*-- vyber autora --*/
function _tmp_authorSelect($name, $selected, $gcond, $class=null, $extraoption=null, $groupmode=false, $multiple=null){
if($class!=null){$class=" class='".$class."'";}else{$class="";}
if($multiple!=null){$multiple=" multiple='multiple' size='".$multiple."'"; $name.="[]";}else{$multiple="";}
$return="<select name='".$name."'".$class.$multiple.">";
$query=mysql_query("SELECT id,title,level FROM `"._mysql_prefix."-groups` WHERE ".$gcond." AND id!=2 ORDER BY level DESC");
if($extraoption!=null){$return.="<option value='-1' class='special'>".$extraoption."</option>";}

if(!$groupmode){
  while($item=mysql_fetch_array($query)){
  $users=mysql_query("SELECT id,username FROM `"._mysql_prefix."-users` WHERE `group`=".$item['id']." AND (".$item['level']."<"._loginright_level." OR id="._loginid.") ORDER BY id");
    if(mysql_num_rows($users)!=0){
    $return.="<optgroup label='".$item['title']."'>";
      while($user=mysql_fetch_array($users)){
      if($selected==$user['id']){$sel=" selected='selected'";}else{$sel="";}
      $return.="<option value='".$user['id']."'".$sel.">".$user['username']."</option>\n";
      }
    $return.="</optgroup>";
    }
  }
}
else{
  while($item=mysql_fetch_array($query)){
  if($selected==$item['id']){$sel=" selected='selected'";}else{$sel="";}
  $return.="<option value='".$item['id']."'".$sel.">".$item['title']." (".mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users` WHERE `group`=".$item['id']), 0).")</option>\n";
  }
}

$return.="</select>";
return $return;
}

/*-- editace casu --*/
function _tmp_editTime($timestamp, $updatebox=false, $updateboxchecked=false){
global $_lang;
$timestamp=getdate($timestamp);
$return="<input type='text' size='1' maxlength='2' name='tday' value='".$timestamp['mday']."' />.<input type='text' size='1' maxlength='2' name='tmonth' value='".$timestamp['mon']."' />&nbsp;&nbsp;<input type='text' size='3' maxlength='4' name='tyear' value='".$timestamp['year']."' />&nbsp;&nbsp;<input type='text' size='1' maxlength='2' name='thour' value='".$timestamp['hours']."' />:<input type='text' size='1' maxlength='2' name='tminute' value='".$timestamp['minutes']."' />:<input type='text' size='1' maxlength='2' name='tsecond' value='".$timestamp['seconds']."' />&nbsp;&nbsp;<small>".$_lang['admin.content.form.timehelp']."</small>";
  if($updatebox){
  if($updateboxchecked){$updateboxchecked=" checked='checked'";}else{$updateboxchecked="";}
  $return.="&nbsp;&nbsp;<label><input type='checkbox' name='tupdate' value='1'".$updateboxchecked." /> ".$_lang['admin.content.form.timeupdate']."</label>";
  }
return $return;
}

function _tmp_loadTime($default){
if(!_checkboxLoad('tupdate')){
  $day=intval($_POST['tday']);
  $month=intval($_POST['tmonth']);
  $year=intval($_POST['tyear']);
  $hour=intval($_POST['thour']);
  $minute=intval($_POST['tminute']);
  $second=intval($_POST['tsecond']);
  if(checkdate($month, $day, $year) and $hour>=0 and $hour<24 and $minute>=0 and $minute<60 and $second>=0 and $second<60){return mktime($hour, $minute, $second, $month, $day, $year);}
  else{return $default;}
}
else{
return time();
}
}

?>