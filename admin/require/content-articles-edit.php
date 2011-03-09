<?php
/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

function _tmp_issafefile($filename){
  if(!_loginright_adminfmanplus){
    $filename=pathinfo($filename);
      if(isset($filename['extension']) and in_array(trim($filename['extension']), array("php", "php3", "php4", "php5", "phtml", "shtml", "asp", "py", "cgi", "htaccess"))){return false;}
      else{return true;}
  }
  else{
  return true;
  }
}

/*--- nacteni promennych ---*/
$message="";
$continue=false;
$path=_indexroot."upload/img/perex/";
$att_dir=_indexroot."upload/article_";
if(isset($_GET['id']) and isset($_GET['returnid']) and isset($_GET['returnpage'])){
$id=intval($_GET['id']);
$returnid=$_GET['returnid']; if($returnid!="load"){$returnid=intval($returnid);}
$returnpage=intval($_GET['returnpage']);
$query=mysql_query("SELECT * FROM `"._mysql_prefix."-articles` WHERE id=".$id._tmp_artAccess());
  if(mysql_num_rows($query)!=0){
  $query=mysql_fetch_array($query);
  $readed_counter=$query['readed'];
  if($returnid=="load"){$returnid=$query['home1'];} $backlink="index.php?p=content-articles-list&amp;cat=".$returnid."&amp;page=".$returnpage;
  $actionplus="&amp;id=".$id."&amp;returnid=".$returnid."&amp;returnpage=".$returnpage;
  $submittext="global.savechanges";
  $artlink=" <a href='"._indexroot._linkArticle($query['id'])."' target='_blank'><img src='images/icons/loupe.gif' alt='prev' /></a>";
  $new=false;
  $continue=true;
  }
  //pereximg
  if (file_exists($path.$id.".jpg")) {
    $pereximg="<a href='".$path.$id.".jpg' rel='lightbox'><img style='vertical-align:middle;' width='94' src='".$path.$id.".jpg' /></a>";
  }
  elseif (file_exists($path.$id.".gif")) {
  	$pereximg="<a href='".$path.$id.".gif' rel='lightbox'><img style='vertical-align:middle;' width='94' src='".$path.$id.".gif' /></a>";
  }
  elseif (file_exists($path.$id.".png")) {
  	$pereximg="<a href='".$path.$id.".png' rel='lightbox'><img style='vertical-align:middle;' width='94' src='".$path.$id.".png' /></a>";
  }
}
else{
$backlink="index.php?p=content-articles";
$actionplus="";
$submittext="global.create";
$artlink="";
$new=true;
$id=-1;
$readed_counter=0;
$query=array(
"id"=>-1,
"title"=>"",
"perex"=>"",
"content"=>"",
"infobox"=>"",
"author"=>_loginid,
"home1"=>-2,
"home2"=>-1,
"home3"=>-1,
"time"=>time(),
"visible"=>1,
"public"=>1,
"comments"=>1,
"commentslocked"=>0,
"showinfo"=>1,
"confirmed"=>!_loginright_adminneedconfirm,
"rateon"=>1,
"readed"=>0,
"description"=>"",
"keywords"=>"",
"seotitle"=>""
);
$continue=true;
}
/*--- ulozeni ---*/
if(isset($_POST['title'])){
  //nacteni promennych
  $newdata['id']=$id;
  $newdata['title']=_safeStr(_htmlStr($_POST['title']));
  $newdata['home1']=intval($_POST['home1']);
  $newdata['home2']=intval($_POST['home2']);
  $newdata['home3']=intval($_POST['home3']);
  if(_loginright_adminchangeartauthor){$newdata['author']=intval($_POST['author']);}else{$newdata['author']=$query['author'];}
  $newdata['perex']=_safeStr(_stripSlashes($_POST['perex']), false);
  $newdata['content']=_safeStr(_filtrateHCM(_stripSlashes($_POST['content'])), false);
  $newdata['infobox']=_safeStr(_filtrateHCM(_stripSlashes($_POST['infobox'])), false);
  $newdata['public']=_checkboxLoad('public');
  $newdata['visible']=_checkboxLoad('visible');
  if(_loginright_adminconfirm){$newdata['confirmed']=_checkboxLoad('confirmed');}else{$newdata['confirmed']=$query['confirmed'];}
  $newdata['comments']=_checkboxLoad('comments');
  $newdata['commentslocked']=_checkboxLoad('commentslocked');
  $newdata['rateon']=_checkboxLoad('rateon');
  $newdata['showinfo']=_checkboxLoad('showinfo');
  $newdata['resetrate']=_checkboxLoad('resetrate');
  $newdata['delcomments']=_checkboxLoad('delcomments');
  $newdata['resetread']=_checkboxLoad('resetread');
  $newdata['time']=_tmp_loadTime($query['time']);
  $newdata['description']=_safeStr(_htmlStr($_POST['description']));
  $newdata['keywords']=_safeStr(_htmlStr($_POST['keywords']));
  $newdata['seotitle']=_safeStr(_htmlStr($_POST['seotitle']));
  //kontrola promennych
  $error_log=array();
    //titulek
    if($newdata['title']==""){$error_log[]=$_lang['admin.content.articles.edit.error1'];}
    //kategorie
    $homechecks=array("home1", "home2", "home2");
    foreach($homechecks as $homecheck){
      if($newdata[$homecheck]!=-1 or $homecheck=="home1"){
        if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-root` WHERE type=2 AND id=".$newdata[$homecheck]), 0)==0){
        $error_log[]=$_lang['admin.content.articles.edit.error2'];
        }
      }
    }
      //zruseni duplikatu
      if($newdata['home1']==$newdata['home2']){$newdata['home2']=-1;}
      if($newdata['home2']==$newdata['home3'] or $newdata['home1']==$newdata['home3']){$newdata['home3']=-1;}
    //autor
    if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users` WHERE id=".$newdata['author']." AND (id="._loginid." OR (SELECT level FROM `"._mysql_prefix."-groups` WHERE id=`"._mysql_prefix."-users`.`group`)<"._loginright_level.")"), 0)==0){
    $error_log[]=$_lang['admin.content.articles.edit.error3'];
    }
  //ulozeni
  if(count($error_log)==0){
    if(!$new){
      //data
      mysql_query("UPDATE `"._mysql_prefix."-articles` SET title='".$newdata['title']."',home1=".$newdata['home1'].",home2=".$newdata['home2'].",home3=".$newdata['home3'].",author=".$newdata['author'].",perex='".$newdata['perex']."',content='".$newdata['content']."',infobox='".$newdata['infobox']."',public=".$newdata['public'].",visible=".$newdata['visible'].",confirmed=".$newdata['confirmed'].",comments=".$newdata['comments'].",commentslocked=".$newdata['commentslocked'].",rateon=".$newdata['rateon'].",showinfo=".$newdata['showinfo'].",time=".$newdata['time'].",description='".$newdata['description']."',keywords='".$newdata['keywords']."',seotitle='".$newdata['seotitle']."' WHERE id=".$newdata['id']) or die (mysql_error());
      //smazani komentaru
      if($newdata['delcomments']==1){mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE type=2 AND home=".$id);}
      //vynulovani poctu precteni
      if($newdata['resetread']==1){mysql_query("UPDATE `"._mysql_prefix."-articles` SET readed=0 WHERE id=".$id);}
      //vynulovani hodnoceni
      if($newdata['resetrate']==1){
      mysql_query("UPDATE `"._mysql_prefix."-articles` SET ratenum=0,ratesum=0 WHERE id=".$id);
      mysql_query("DELETE FROM `"._mysql_prefix."-iplog` WHERE type=3 AND var=".$id);
      }
      if ($_FILES['pereximg']['name']!='' or _checkboxLoad('pereximgdel')) {
	      if(file_exists($path.$newdata['id'].".jpg")){unlink($path.$newdata['id'].".jpg");}
        if(file_exists($path.$newdata['id'].".gif")){unlink($path.$newdata['id'].".gif");}
        if(file_exists($path.$newdata['id'].".png")){unlink($path.$newdata['id'].".png");}
      }
      if (isset($_FILES['pereximg'])) {
        $tmp_image=$_FILES['pereximg']['tmp_name'];
        $tmp_image_info=pathinfo($_FILES['pereximg']['name']);
        if(!isset($tmp_image_info['extension'])){$tmp_image_info['extension']="";}
          move_uploaded_file($tmp_image, $path.$newdata['id'].".".$tmp_image_info['extension']);
      }
      $att_dir=$att_dir.$id."/";
      foreach($_FILES as $key=>$item){
        if ($key=="pereximg")continue;
        $tmp_name=$item['tmp_name'];
        $name=_anchorStr(basename($item['name']));
        $exist=false;
        $x=1;
        while (!$exist) {
          if(file_exists($att_dir.$name)){
            $name=substr($name, 0,strrpos($name,'.'))."(".$x.").".substr($name, strrpos($name, '.') + 1);
          }
          else{
            $exist=true;
          }
          $x++;
        }
        if(_tmp_issafefile($name)){
          move_uploaded_file($tmp_name, $att_dir.$name);
        }
        $x=1;
      }
      if(isset($_POST['delatt'])){
        foreach ($_POST['delatt'] as $key=>$value) {
        	@unlink($att_dir.$value);
        }
      }
      //presmerovani
      define('_tmp_redirect', 'index.php?p=content-articles-edit&id='.$newdata['id'].'&saved&returnid='.$returnid.'&returnpage='.$returnpage);
    }
    else{
      //vlozeni
      $newdata['id']=_getNewID("articles");
      $att_dir=$att_dir.$newdata['id']."/";
      recursive_remove_directory(_indexroot.$att_dir.$id);
      @mkdir($att_dir);
      chmod($att_dir, 0777);
      mysql_query("INSERT INTO `"._mysql_prefix."-articles` (id,title,perex,content,infobox,author,home1,home2,home3,time,visible,public,comments,commentslocked,confirmed,showinfo,readed,rateon,ratenum,ratesum,description,keywords,seotitle) VALUES (".$newdata['id'].",'".$newdata['title']."','".$newdata['perex']."','".$newdata['content']."','".$newdata['infobox']."',".$newdata['author'].",".$newdata['home1'].",".$newdata['home2'].",".$newdata['home3'].",".$newdata['time'].",".$newdata['visible'].",".$newdata['public'].",".$newdata['comments'].",".$newdata['commentslocked'].",".intval(!_loginright_adminneedconfirm).",".$newdata['showinfo'].",0,".$newdata['rateon'].",0,0,'".$newdata['description']."','".$newdata['keywords']."','".$newdata['seotitle']."')");
      if (isset($_FILES['pereximg'])) {
	      if(file_exists($path.$newdata['id'].".jpg")){unlink($path.$newdata['id'].".jpg");}
        if(file_exists($path.$newdata['id'].".gif")){unlink($path.$newdata['id'].".gif");}
        if(file_exists($path.$newdata['id'].".png")){unlink($path.$newdata['id'].".png");}
        $tmp_image=$_FILES['pereximg']['tmp_name'];
        $tmp_image_info=pathinfo($_FILES['pereximg']['name']);
        if(!isset($tmp_image_info['extension'])){$tmp_image_info['extension']="";}
          move_uploaded_file($tmp_image, $path.$newdata['id'].".".$tmp_image_info['extension']);
      }
      foreach($_FILES as $key=>$item){
        if ($key=="pereximg")continue;
        $tmp_name=$item['tmp_name'];
        $name=_anchorStr(basename($item['name']));
        if(_tmp_issafefile($name)){
          move_uploaded_file($tmp_name, $att_dir.$name);
        }
      }

      //presmerovani
      define('_tmp_redirect', 'index.php?p=content-articles-edit&id='.$newdata['id'].'&created&returnid='.$newdata['home1'].'&returnpage=1');
    }
  }
  else{
  $message=_formMessage(2, _eventList($error_log, 'errors'));
  }
}
/*--- vystup ---*/
if($continue){
  //vyber autora
  if(_loginright_adminchangeartauthor){
  $author_select="
  <tr>
  <td class='rpad'><strong>".$_lang['article.author']."</strong></td>
  <td>"._tmp_authorSelect("author", $query['author'], "adminart=1", "selectbig")."</td></tr>
  ";
  }
  else{
  $author_select="";
  }
  //zprava
  if(isset($_GET['saved'])){$message=_formMessage(1, $_lang['global.saved']."&nbsp;&nbsp;<small>("._formatTime(time()).")</small>");}
  if(isset($_GET['created'])){$message=_formMessage(1, $_lang['global.created']);}
//wysiwyg editor
$output.=_tmp_wysiwyg();
//vypocet hodnoceni
if(!$new){
  if($query['ratenum']!=0){$rate=mysql_result(mysql_query("SELECT ROUND(ratesum/ratenum) FROM `"._mysql_prefix."-articles` WHERE id=".$query['id']), 0)."%, ".$query['ratenum']."x";}
  else{$rate=$_lang['article.rate.nodata'];}
}
else{
  $rate="";
}
//formular
$output.="
<a href='".$backlink."' class='backlink'>&lt; ".$_lang['global.return']."</a>
<h1>".$_lang['admin.content.articles.edit.title']." "._tmp_docsIcon("editart", true)."</h1>
<p class='bborder'>".$_lang['admin.content.articles.edit.p']."</p>".$message."
".(($new==true and _loginright_adminneedconfirm)?_tmp_smallNote($_lang['admin.content.articles.edit.newconfnote']):'')."
".(($new==false and $query['confirmed']!=1)?_tmp_smallNote($_lang['admin.content.articles.edit.confnote']):'')."
<form class='cform' action='index.php?p=content-articles-edit".$actionplus."' method='post' enctype='multipart/form-data' name='artform'"._jsCheckForm("artform", array("title")).">
<table>
<tr>
<td class='rpad'><strong>".$_lang['admin.content.form.title']."</strong></td>
<td><input type='text' name='title' value='".$query['title']."' class='inputbig' /></td>
</tr>
<tr>
<td class='rpad'><strong>".$_lang['article.category']."</strong></td>
<td>"._tmp_rootSelect("home1", 2, $query['home1'], false)." "._tmp_rootSelect("home2", 2, $query['home2'], true)." "._tmp_rootSelect("home3", 2, $query['home3'], true)."</td>
</tr>
".$author_select."
<tr>
<td class='rpad'><strong>".$_lang['article.keywords']."</strong></td>
<td><input type='text' name='keywords' value='".$query['keywords']."' class='inputbig' /></td>
</tr>
<tr>
<td class='rpad'><strong>".$_lang['article.seotitle']."</strong></td>
<td><input type='text' name='seotitle' value='".$query['seotitle']."' class='inputbig' /></td>
</tr>
<tr>
<td class='rpad'><strong>".$_lang['article.description']."</strong></td>
<td><input type='text' name='description' value='".$query['description']."' class='inputbig' /></td>
</tr>
<tr valign='top'>
<td class='rpad'><strong>".$_lang['admin.content.form.perex']."</strong></td>
<td><textarea name='perex' rows='9' cols='94' class='areabigperex codemirror xml'>"._htmlStr($query['perex'])."</textarea></td>
</tr>
<tr>
<td class='rpad'><strong>".$_lang['admin.content.form.perex.img']."</strong></td>
<td><input type='file' name='pereximg' />&nbsp;&nbsp;".(isset($pereximg) ? $pereximg." <input type='checkbox' name='pereximgdel' value='1' /> Odstranit":"")."</td>
</tr>
<tr valign='top'>
<td class='rpad'><strong>".$_lang['admin.content.form.content']."</strong>".$artlink."</td>
<td>
  <table id='ae-table'>
  <tr valign='top'>
    <td id='content-cell'>
      <textarea name='content' rows='25' cols='68' class='wysiwyg_editor".((!_wysiwyg || !_loginwysiwyg) ? ' codemirror xml' : '')."'>"._htmlStr($query['content'])."</textarea>
    </td>
    <td id='is-cell'>
      <div>
      <h2>".$_lang['admin.content.form.infobox']."</h2>
      <textarea name='infobox' rows='10' cols='20' class='codemirror xml'>"._htmlStr($query['infobox'])."</textarea><br /><br />
      <h2>".$_lang['admin.content.form.settings']."</h2>
      <p>
      <label><input type='checkbox' name='public' value='1'"._checkboxActivate($query['public'])." /> ".$_lang['admin.content.form.public']."</label>
      <label><input type='checkbox' name='visible' value='1'"._checkboxActivate($query['visible'])." /> ".$_lang['admin.content.form.visible']."</label>
      ".((_loginright_adminconfirm and !$new)?"<label><input type='checkbox' name='confirmed' value='1'"._checkboxActivate($query['confirmed'])." /> ".$_lang['admin.content.form.confirmed']."</label>":'')."
      <label><input type='checkbox' name='comments' value='1'"._checkboxActivate($query['comments'])." /> ".$_lang['admin.content.form.comments']."</label>
      <label><input type='checkbox' name='commentslocked' value='1'"._checkboxActivate($query['commentslocked'])." /> ".$_lang['admin.content.form.commentslocked']."</label>
      <label><input type='checkbox' name='rateon' value='1'"._checkboxActivate($query['rateon'])." /> ".$_lang['admin.content.form.artrate']."</label>
      <label><input type='checkbox' name='showinfo' value='1'"._checkboxActivate($query['showinfo'])." /> ".$_lang['admin.content.form.showinfo']."</label>
      ".(!$new?"<label><input type='checkbox' name='resetrate' value='1' /> ".$_lang['admin.content.form.resetartrate']." <small>(".$rate.")</small></label>":'')."
      ".(!$new?"<label><input type='checkbox' name='delcomments' value='1' /> ".$_lang['admin.content.form.delcomments']." <small>(".mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-posts` WHERE home=".$query['id']." AND type=2"), 0).")</small></label>":'')."
      ".(!$new?"<label><input type='checkbox' name='resetread' value='1' /> ".$_lang['admin.content.form.resetartread']." <small>(".$readed_counter.")</small></label>":'')."
      </p>
      </div>
    </td>
  </tr>
  </table>
</td>
</tr>";
$handler = @opendir($att_dir.$id);
while ($file = @readdir($handler)) {
  if ($file != "." && $file != "..") {
    $files[] = $file;
  }
}
@closedir($handler);
$output.="<tr>
<td class='rpad' valign='top'><strong>Přílohy k článku (max. ".get_cfg_var('upload_max_filesize').")</strong></td>
<td>
  <table style='width:50%;'>
  <tr valign='top'>
    <td id='fmanFiles'>
      <input type='file' name='uf0' />&nbsp;&nbsp;<a href='#' onclick='return _sysFmanAddFile();'>".$_lang['admin.fman.upload.addfile']."</a>
    </td>
    <td>
      ";
      if (is_array($files)){
        $output.="<table>";
        foreach ($files as $key=>$value) {
        	$output.="<tr".(is_int($y/2) ? " class='hl'":"")."><td>".$value."</td><td><input type='checkbox' name='delatt[]' value='".$value."' /> odstranit při uložení</td></tr>";
          $y++;
        }
        $output.="</table>";
      }
    $output.="</td>
  </tr>
  </table>
</td>
</tr>
<tr id='time-cell'>
<td class='rpad'><strong>".$_lang['article.posted']."</strong></td>
<td>"._tmp_editTime($query['time'], true, $new)."</td>
</tr>
<tr>
<td></td>
<td><br /><input type='submit' value='".$_lang[$submittext]."' />
".(!$new?"
&nbsp;&nbsp;
<span class='customsettings'><a href='index.php?p=content-articles-delete&amp;id=".$query['id']."&amp;returnid=".$query['home1']."&amp;returnpage=1'><span><img src='images/icons/delete.gif' alt='del' class='icon' />".$_lang['global.delete']."</span></a></span>&nbsp;&nbsp;
<span class='customsettings'><small>".$_lang['admin.content.form.thisid']." ".$query['id']."</small></span>
":'')."
</td>
</tr>
</table>
</form>";
}
else{
$output.="<a href='index.php?p=content-articles' class='backlink'>&lt; ".$_lang['global.return']."</a>\n<h1>".$_lang['admin.content.articles.edit.title']."</h1>\n"._formMessage(3, $_lang['global.badinput']);
}
?>