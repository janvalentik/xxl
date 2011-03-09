<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- pripava promennych ---*/
$message="";
$continue=false;
if(isset($_GET['g'])){
$g=intval($_GET['g']);
$galdata=mysql_query("SELECT title,var2,var3 FROM `"._mysql_prefix."-root` WHERE id=".$g." AND type=5");
  if(mysql_num_rows($galdata)!=0){
  $galdata=mysql_fetch_array($galdata);
  $continue=true;
  }
}

/*--- akce ---*/
if(isset($_POST['xaction'])){

  switch($_POST['xaction']){

  /*- vlozeni obrazku -*/
  case 1:

    //nacteni zakladnich promennych
    $title=_safeStr(_htmlStr(trim($_POST['title'])));
      if(!_checkboxLoad("autoprev")){$prev=_safeStr(_htmlStr($_POST['prev']));}
      else{$prev="";}
    $full=_safeStr(_htmlStr($_POST['full']));

    //vlozeni na zacatek nebo nacteni poradoveho cisla
    if(_checkboxLoad("moveords")){
    $smallerord=mysql_query("SELECT ord FROM `"._mysql_prefix."-images` WHERE home=".$g." ORDER BY ord LIMIT 1");
      if(mysql_num_rows($smallerord)!=0){$smallerord=mysql_fetch_array($smallerord); $ord=$smallerord['ord'];}
      else{$ord=1;}
    mysql_query("UPDATE `"._mysql_prefix."-images` SET ord=ord+1 WHERE home=".$g);
    }
    else{
    $ord=floatval($_POST['ord']);
    }

    //kontrola a vlozeni
    if($full!=''){
    $newid=_getNewID("images");
    mysql_query("INSERT INTO `"._mysql_prefix."-images` (id,home,ord,title,prev,full) VALUES(".$newid.",".$g.",".$ord.",'".$title."','".$prev."','".$full."')");
    $message=_formMessage(1, $_lang['global.inserted']);
    }else{
    $message=_formMessage(2, $_lang['admin.content.manageimgs.insert.error']);
    }

  break;

  /*- posunuti poradovych cisel -*/
  case 2:

    //nacteni promennych
    $action=intval($_POST['moveaction']);
    $zonedir=intval($_POST['zonedir']);
    $zone=floatval($_POST['zone']);
    $offset=floatval($_POST['offset']);

    //aplikace
    if($action==1){$sign="+";}else{$sign="-";}
    if($zonedir==1){$zonedir=">";}else{$zonedir="<";}
    mysql_query("UPDATE `"._mysql_prefix."-images` SET ord=ord".$sign.$offset." WHERE ord".$zonedir."=".$zone." AND home=".$g);
    $message=_formMessage(1, $_lang['global.done']);

  break;

  /*- vycisteni poradovych cisel -*/
  case 3:
      $items=mysql_query("SELECT id FROM `"._mysql_prefix."-images` WHERE home=".$g." ORDER BY ord");
      $counter=1;
      while($item=mysql_fetch_array($items)){
          mysql_query("UPDATE `"._mysql_prefix."-images` SET ord=".$counter." WHERE id=".$item['id']." AND home=".$g);
          $counter++;
      }
      $message=_formMessage(1, $_lang['global.done']);
  break;

  /*- aktualizace obrazku -*/
  case 4:
  $lastid=-1;
  $sql="";
  foreach($_POST as $var=>$val){
  if($var=="xaction"){continue;}
  $var=@explode("_", $var);
    if(count($var)==2){
      $id=intval(mb_substr($var[0], 1)); $var=_safeStr($var[1]);
      if($lastid==-1){$lastid=$id;}
      $quotes="'";
      $skip=false;
        switch($var){
        case "title": case "full": $val=_safeStr(_htmlStr($val)); break;
        case "prevtrigger": $var="prev"; if(!_checkboxLoad('i'.$id.'_autoprev')){$val=_safeStr(_htmlStr($_POST['i'.$id.'_prev']));}else{$val="";}break;
        case "ord": $val=floatval($val); $quotes=''; break;
        default: $skip=true; break;
        }
        
        //ukladani a cachovani
        if(!$skip){
        
          //ulozeni
          if($lastid!=$id){
          $sql=trim($sql, ",");
          mysql_query("UPDATE `"._mysql_prefix."-images` SET ".$sql." WHERE id=".$lastid." AND home=".$g);
          $sql=""; $lastid=$id;
          }

        $sql.=$var."=".$quotes.$val.$quotes.",";
        }
        
    }
  }

    //ulozeni posledniho nebo jedineho obrazku
    if($sql!=""){
      $sql=trim($sql, ",");
      mysql_query("UPDATE `"._mysql_prefix."-images` SET ".$sql." WHERE id=".$id." AND home=".$g);
    }

  $message=_formMessage(1, $_lang['global.saved']);
  break;
  
  /*- presunuti obrazku -*/
  case 5:
  $newhome=intval($_POST['newhome']);
  if($newhome!=$g){
    if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-root` WHERE id=".$newhome." AND type=5"), 0)!=0){
      if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-images` WHERE home=".$g), 0)!=0){

        //posunuti poradovych cisel v cilove galerii
        $moveords=_checkboxLoad("moveords");
        if($moveords){

          //nacteni nejvetsiho poradoveho cisla v teto galerii
          $greatestord=mysql_query("SELECT ord FROM `"._mysql_prefix."-images` WHERE home=".$g." ORDER BY ord DESC LIMIT 1");
          $greatestord=mysql_fetch_array($greatestord);
          $greatestord=$greatestord['ord'];

        mysql_query("UPDATE `"._mysql_prefix."-images` SET ord=ord+".$greatestord." WHERE home=".$newhome);
        }
        
        //presun obrazku
        mysql_query("UPDATE `"._mysql_prefix."-images` SET home=".$newhome." WHERE home=".$g);
        
        //zprava
        $message=_formMessage(1, $_lang['global.done']);

      }
      else{
        $message=_formMessage(2, $_lang['admin.content.manageimgs.moveimgs.nokit']);
      }
    }
    else{
      $message=_formMessage(2, $_lang['global.badinput']);
    }
  }
  else{
    $message=_formMessage(2, $_lang['admin.content.manageimgs.moveimgs.samegal']);
  }
  break;
  
  /*- odstraneni obrazku -*/
  case 6:
  if(_checkboxLoad("confirm")){
    mysql_query("DELETE FROM `"._mysql_prefix."-images` WHERE home=".$g);
    $message=_formMessage(1, $_lang['global.done']);
  }
  break;

  }

}

/*--- odstraneni obrazku ---*/
if(isset($_GET['del'])){
$del=intval($_GET['del']);
  if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-images` WHERE id=".$del." AND home=".$g), 0)!=0){
  mysql_query("DELETE FROM `"._mysql_prefix."-images` WHERE id=".$del." AND home=".$g);
  $message=_formMessage(1, $_lang['global.done']);
  }
}

/*--- vystup ---*/
if($continue){
$output.="
<a href='index.php?p=content-editgallery&amp;id=".$g."' class='backlink'>&lt; návrat zpět</a>
<h1>".$_lang['admin.content.manageimgs.title']." "._tmp_docsIcon("galimgs", true)."</h1>
<p class='bborder'>".str_replace("*galtitle*", $galdata['title'], $_lang['admin.content.manageimgs.p'])."</p>

".$message."

<fieldset>
<legend>".$_lang['admin.content.manageimgs.insert']."</legend>
<form action='index.php?p=content-manageimgs&amp;g=".$g."' method='post' name='addform' onsubmit='_sysGalTransferPath(this);'>
<input type='hidden' name='xaction' value='1' />

<table>
<tr valign='top'>

<td>
    <table>
    <tr>
    <td class='rpad'><strong>".$_lang['admin.content.form.title']."</strong></td>
    <td><input type='text' name='title' class='inputmedium' /></td>
    </tr>
    
    <tr>
    <td class='rpad'><strong>".$_lang['admin.content.form.ord']."</strong></td>
    <td><input type='text' name='ord' class='inputsmall' disabled='disabled' />&nbsp;&nbsp;<label><input type='checkbox' name='moveords' value='1' checked='checked' onclick=\"_sysDisableField(this.checked, 'addform', 'ord');\" /> ".$_lang['admin.content.manageimgs.moveords']."</label></td>
    </tr>
    
    <tr>
    <td class='rpad'><strong>".$_lang['admin.content.manageimgs.prev']."</strong></td>
    <td><input type='text' name='prev' class='inputsmall' disabled='disabled' />&nbsp;&nbsp;<label><input type='checkbox' name='autoprev' value='1' checked='checked' onclick=\"_sysDisableField(this.checked, 'addform', 'prev');\" /> ".$_lang['admin.content.manageimgs.autoprev']."</label></td>
    </tr>
    
    <tr>
    <td class='rpad'><strong>".$_lang['admin.content.manageimgs.full']."</strong></td>
    <td><input type='text' name='full' class='inputmedium' /></td>
    </tr>
    
    <tr>
    <td></td>
    <td><input type='submit' value='".$_lang['global.insert']."' /></td>
    </tr>

    </table>
</td>

<td>
".(_loginright_adminfman?"<div id='gallery-browser'>
    ".(!isset($_GET['browserpath'])?
    "<a href='#' onclick=\"return _sysGalBrowse('"."..%2F..%2Fupload%2F".(_loginright_adminfmanlimit?_loginname.'%2F':'')."');\"><img src='images/icons/loupe.gif' alt='browse' class='icon' />".$_lang['admin.content.manageimgs.insert.browser.link']."</a>":
    "<script type='text/javascript'>_sysGalBrowse('"._htmlStr($_GET['browserpath'])."');</script>"
    )."
</div>":'')."
</td>

</tr>
</table>

</form>
</fieldset>

";

  //strankovani
  $paging=_resultPaging("index.php?p=content-manageimgs&amp;g=".$g, $galdata['var2'], "images", "home=".$g);
  $s=$paging[2];

$output.="
<fieldset>
<legend>".$_lang['admin.content.manageimgs.current']."</legend>
<form action='index.php?p=content-manageimgs&amp;g=".$g."&amp;page=".$s."' method='post' name='editform'>
<input type='hidden' name='xaction' value='4' />

<input type='submit' value='".$_lang['admin.content.manageimgs.savechanges']."' class='gallery-savebutton' />
".$paging[0]."
<div class='cleaner'></div>";

  //vypis obrazku
  $images=mysql_query("SELECT * FROM `"._mysql_prefix."-images` WHERE home=".$g." ORDER BY ord ".$paging[1]);
  $images_forms=array();
  if(mysql_num_rows($images)!=0){
    //sestaveni formularu
    while($image=mysql_fetch_array($images)){
        //kod nahledu
        $preview=_galleryImage($image, "1");
      
        //kod formulare
        $images_forms[].="
<table>

<tr>
<td class='rpad'><strong>".$_lang['admin.content.form.title']."</strong></td>
<td><input type='text' name='i".$image['id']."_title' class='inputmedium' value='".$image['title']."' /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['admin.content.form.ord']."</strong></td>
<td><input type='text' name='i".$image['id']."_ord' class='inputmedium' value='".$image['ord']."' /></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['admin.content.manageimgs.prev']."</strong></td>
<td><input type='hidden' name='i".$image['id']."_prevtrigger' value='1' /><input type='text' name='i".$image['id']."_prev' class='inputsmall' value='".$image['prev']."'"._inputDisable($image['prev']!="")." />&nbsp;&nbsp;<label><input type='checkbox' name='i".$image['id']."_autoprev' value='1' onclick=\"_sysDisableField(checked, 'editform', 'i".$image['id']."_prev');\""._checkboxActivate($image['prev']=="")." /> ".$_lang['admin.content.manageimgs.autoprev']."</label></td>
</tr>

<tr>
<td class='rpad'><strong>".$_lang['admin.content.manageimgs.full']."</strong></td>
<td><input type='text' name='i".$image['id']."_full' class='inputmedium' value='".$image['full']."' /></td>
</tr>

<tr valign='top'>
<td class='rpad'><strong>".$_lang['global.preview']."</strong></td>
<td>".$preview."<br /><br /><a href='index.php?p=content-manageimgs&amp;g=".$g."&amp;page=".$s."&amp;del=".$image['id']."' onclick='return _sysConfirm();'><img src='images/icons/delete.gif' alt='del' class='icon' />".$_lang['admin.content.manageimgs.delete']."</a></td>
</tr>

</table>
    ";
    }
    
    //sestaveni tabulky formularu po dvou
    $output.="\n<table id='gallery-edittable'>";
    $count=count($images_forms);
    for($i=0;$i<$count;$i+=2){
        if(isset($images_forms[$i])){
            $output.="<tr><td".(($i==0 and !isset($images_forms[$i+1]))?' colspan="2"':'')." class='gallery-edittable-td'>\n".$images_forms[$i]."\n</td>\n";
            if(isset($images_forms[$i+1])){
                $output.="<td class='gallery-edittable-td'>\n".$images_forms[$i+1]."\n</td></tr>\n";    
            }
        }
    }
    $output.='</table>';
    


  $output.="<input type='submit' value='".$_lang['admin.content.manageimgs.savechanges']."' class='gallery-savebutton' />\n".$paging[0];
  }
  else{
  $output.='<p>'.$_lang['global.nokit'].'</p>';
  }

$output.="
</form>
</fieldset>


<a name='func'></a>
<fieldset>
<legend>".$_lang['admin.content.manageimgs.moveallords']."</legend>

<form class='cform' action='index.php?p=content-manageimgs&amp;g=".$g."&amp;page=".$s."' method='post'>
<input type='hidden' name='xaction' value='2' />
<select name='moveaction'><option value='1'>".$_lang['admin.content.move.choice1']."</option><option value='2'>".$_lang['admin.content.move.choice2']."</option></select>&nbsp;
".$_lang['admin.content.move.text1']."&nbsp;
<select name='zonedir'><option value='1'>".$_lang['admin.content.move.choice3']."</option><option value='2'>".$_lang['admin.content.move.choice4']."</option></select>&nbsp;
".$_lang['admin.content.move.text2']."&nbsp;
<input type='text' name='zone' value='1' class='inputmini' maxlength='5' />&nbsp;,
".$_lang['admin.content.move.text3']."&nbsp;
<input type='text' name='offset' value='1' class='inputmini' maxlength='5' />.&nbsp;
<input type='submit' value='".$_lang['global.do']."' onclick='return _sysConfirm();' />
</form>

<div class='hr'><hr /></div>

<form class='cform' action='index.php?p=content-manageimgs&amp;g=".$g."&amp;page=".$s."' method='post'>
<input type='hidden' name='xaction' value='3' />
".$_lang['admin.content.manageimgs.moveallords.cleanup']." <input type='submit' value='".$_lang['global.do']."' onclick='return _sysConfirm();' />
</form>

</fieldset>

<table width='100%'>
<tr valign='top'>

<td width='50%'>
  <fieldset>
  <legend>".$_lang['admin.content.manageimgs.moveimgs']."</legend>

  <form class='cform' action='index.php?p=content-manageimgs&amp;g=".$g."&amp;page=".$s."' method='post'>
  <input type='hidden' name='xaction' value='5' />
  "._tmp_rootSelect("newhome", 5, -1, false)." <input type='submit' value='".$_lang['global.do']."' onclick='return _sysConfirm();' /><br /><br />
  <label><input type='checkbox' name='moveords' value='1' checked='checked' /> ".$_lang['admin.content.manageimgs.moveords']."</label>
  </form>

  </fieldset>
</td>

<td>
  <fieldset>
  <legend>".$_lang['admin.content.manageimgs.delimgs']."</legend>

  <form class='cform' action='index.php?p=content-manageimgs&amp;g=".$g."&amp;page=".$s."' method='post'>
  <input type='hidden' name='xaction' value='6' />
  <label><input type='checkbox' name='confirm' value='1' /> ".$_lang['admin.content.manageimgs.delimgs.confirm']."</label>&nbsp;&nbsp;<input type='submit' value='".$_lang['global.do']."' onclick='return _sysConfirm();' />
  </form>

  </fieldset>
</td>

</tr>
</table>

";
}
else{
$output.=_formMessage(3, $_lang['global.badinput']);
}

?>