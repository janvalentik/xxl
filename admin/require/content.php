<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- priprava ---*/
$message="";

if(_loginright_adminsection or _loginright_admincategory or _loginright_adminbook or _loginright_adminseparator or _loginright_admingallery or _loginright_adminlink or _loginright_adminintersection or _loginright_adminforum){

    //akce
    if(isset($_POST['ac'])){
    $ac=intval($_POST['ac']);
    
      switch($ac){

      //ulozeni poradovych cisel
      case 1:
      foreach($_POST as $id=>$ord){
      if($id=="ac"){continue;}
      $id=intval($id); $ord=floatval($ord);
      mysql_query("UPDATE `"._mysql_prefix."-root` SET ord=".$ord." WHERE id=".$id);
      }
      break;
      
      //vytvoreni stranky
      case 2:
      $type=intval($_POST['type']);
      if(isset($type_array[$type])){
        if(constant('_loginright_admin'.$type_array[$type])){
          define('_tmp_redirect', 'index.php?p=content-edit'.$type_array[$type]);
        }
      }
      break;
      
      }
    
    }

    //horni panel
    
      //seznam typu stranek
      $create_list="";
      foreach($type_array as $type=>$name){
      if(constant('_loginright_admin'.$name)){
      $create_list.="<option value='".$type."'>".$_lang['admin.content.'.$name]."</option>";
      }
      }
    
    $rootitems='
    <td class="contenttable-box" style="'.((_loginright_adminart or _loginright_adminconfirm or _loginright_admincategory or _loginright_adminpoll or _loginright_adminsbox or _loginright_adminbox)?'width: 75%; ':'border-right: none;').'padding-bottom: 0px;">

    <form action="index.php?p=content" method="post" class="inline">
    <input type="hidden" name="ac" value="2" />
    <img src="images/icons/new.gif" alt="new" class="contenttable-icon" />
    <select name="type">
    '.$create_list.'
    </select>
    <input type="submit" value="'.$_lang['global.create'].'" />
    </form>

    <span style="color:#b2b2b2;">&nbsp;&nbsp;|&nbsp;&nbsp;</span>

    <a href="index.php?p=content-move"><img src="images/icons/action.gif" alt="act" class="contenttable-icon" />'.$_lang['admin.content.move'].'</a>&nbsp;&nbsp;
    <a href="index.php?p=content-titles"><img src="images/icons/action.gif" alt="act" class="contenttable-icon" />'.$_lang['admin.content.titles'].'</a>&nbsp;&nbsp;
    <a href="index.php?p=content-convert"><img src="images/icons/action.gif" alt="act" class="contenttable-icon" />'.$_lang['admin.content.convert'].'</a>

    <div class="hr"><hr /></div>

    <form action="index.php?p=content" method="post">
    <input type="hidden" name="ac" value="1" />
    <div class="pad">
    <table id="contenttable-list">
    ';
    
    //funkce pro vypis polozky
    $counter=0;
    function _tmp_rootItemOutput($item, $itr){
    global $_lang, $counter, $highlight;
    $type_array=_tmp_getTypeArray();

        //pristup k polozce
        if(!constant('_loginright_admin'.$type_array[$item['type']])){$denied=true;}else{$denied=false;}

        //trida pro neviditelnost anebo neverejnost
        $sclass="";
        if($item['visible']==0 xor $item['public']==0){
          if($item['visible']==0){$sclass=" class='invisible'";}
          if($item['public']==0){$sclass=" class='notpublic'";}
        }
        else{
          if($item['visible']==0 and $item['public']==0){$sclass=" class='invisible-notpublic'";}
          else{$sclass=" class='normal'";}
        }
        
        //pozadi oddelovace
        if($item['type']==4){$sepbg_start="<div class='sep'".(($counter==0)?" style='padding-top:0;'":'')."><div class='sepbg'>"; $sepbg_end="</div></div>"; $highlight=false;}
        else{$sepbg_start=""; $sepbg_end=""; $sepbg_start_sub=""; $sepbg_end_sub="";}

        //kod radku
        $dclass="";
        if($itr==true){
          if($highlight){$dclass=" class='intersecpad-hl'";}else{$dclass=" class='intersecpad'";}
        }
        else{
          if($highlight){$dclass=" class='hl'";}
        }

        return "
        

        <tr".$dclass.">
        <td class='name'>".$sepbg_start."<input type='text' name='".$item['id']."' value='".$item['ord']."' /><a".(($item['type']!=4)?" href='"._indexroot._linkRoot($item['id'])."' target='_blank'".$sclass:'').">".$item['title']."</a>".$sepbg_end."</td>
        <td class='type'".($denied?" colspan='2'":'').">".$sepbg_start."<div class='tpad'>".$_lang['admin.content.'.$type_array[$item['type']]]."</div>".$sepbg_end."</td>
        ".(!$denied?"<td class='actions'>".$sepbg_start."<div class='tpad'><a href='index.php?p=content-edit".$type_array[$item['type']]."&amp;id=".$item['id']."'><img src='images/icons/edit.gif' alt='edit' class='contenttable-icon' />".$_lang['global.edit']."</a>&nbsp;&nbsp;&nbsp;<a href='index.php?p=content-delete&amp;id=".$item['id']."'><img src='images/icons/delete.gif' alt='del' class='contenttable-icon' />".$_lang['global.delete']."</a></div>".$sepbg_end."</td>":'')."
        </tr>\n
        ";
        
    }
    
    //tabulka polozek
    $highlight=false;
    $query=mysql_query("SELECT id,title,type,visible,public,ord FROM `"._mysql_prefix."-root` WHERE intersection=-1 ORDER BY ord");
    if(mysql_num_rows($query)!=0){
      while($item=mysql_fetch_array($query)){
      $rootitems.=_tmp_rootItemOutput($item, false); $highlight=!$highlight;
      
        //polozky v rozcestniku
        if($item['type']==7){
        $iquery=mysql_query("SELECT id,title,type,visible,public,ord FROM `"._mysql_prefix."-root` WHERE intersection=".$item['id']." ORDER BY ord");
          while($iitem=mysql_fetch_array($iquery)){
            $rootitems.=_tmp_rootItemOutput($iitem, true); $highlight=!$highlight;
          }
        }

      $counter++;
      }
    }
    else{
    $rootitems.="<tr><td colspan='3'>".$_lang['global.nokit']."</td></tr>\n";
    }
    
    
    
$rootitems.='
</table></div>

<div class="hr"><hr /></div>
<input type="submit" value="'.$_lang['admin.content.saveord'].'" />

</form>

</td>
';
}
else{
$rootitems="";
}

/*--- vystup ---*/

  //zprava
  if(isset($_GET['done'])){$message=_formMessage(1, $_lang['global.done']);}

$output.='
<p>'.$_lang['admin.content.p'].'</p>
'.$message.'
<table id="contenttable">
<tr valign="top">

  '.$rootitems.'

  '.((_loginright_adminart or _loginright_adminconfirm or _loginright_admincategory or _loginright_adminpoll or _loginright_adminsbox or _loginright_adminbox)?


    '<td class="contenttable-box" style="border: none ;">

      '.((_loginright_adminart or _loginright_adminconfirm or _loginright_admincategory)?'<h2>'.$_lang['admin.content.articles'].'</h2><p>':'').'
      '.(_loginright_adminart?'<a href="index.php?p=content-articles-edit"><img src="images/icons/action.gif" alt="act" class="icon" />'.$_lang['admin.content.newart'].'</a><br /><a href="index.php?p=content-articles"><img src="images/icons/action.gif" alt="act" class="icon" />'.$_lang['admin.content.manage'].'</a><br />':'').'
      '.(_loginright_adminconfirm?'<a href="index.php?p=content-confirm"><img src="images/icons/action.gif" alt="act" class="icon" />'.$_lang['admin.content.confirm'].'</a><br />':'').'
      '.(_loginright_admincategory?'<a href="index.php?p=content-movearts"><img src="images/icons/action.gif" alt="act" class="icon" />'.$_lang['admin.content.movearts'].'</a><br />':'').'
      '.(_loginright_admincategory?'<a href="index.php?p=content-artfilter"><img src="images/icons/action.gif" alt="act" class="icon" />'.$_lang['admin.content.artfilter'].'</a><br />':'').'
      '.((_loginright_adminart or _loginright_adminconfirm or _loginright_admincategory)?'</p><br />':'').'

      '.((_loginright_adminpoll or _loginright_adminsbox or _loginright_adminbox)?'<h2>'.$_lang['admin.content.other'].'</h2><p>':'').'
      '.(_loginright_adminpoll?'<a href="index.php?p=content-polls"><img src="images/icons/action.gif" alt="act" class="icon" />'.$_lang['admin.content.polls'].'</a><br />':'').'
      '.(_loginright_adminsbox?'<a href="index.php?p=content-sboxes"><img src="images/icons/action.gif" alt="act" class="icon" />'.$_lang['admin.content.sboxes'].'</a><br />':'').'
      '.(_loginright_adminbox?'<a href="index.php?p=content-boxes"><img src="images/icons/action.gif" alt="act" class="icon" />'.$_lang['admin.content.boxes'].'</a><br />':'').'
      '.(_loginright_admindownload?'<a href="index.php?p=download-manager"><img src="images/icons/action.gif" alt="act" class="icon" />'.$_lang['xxl.admin.download.manager'].'</a><br />':'').'
      '.(_loginright_adminsitemap?'<a href="index.php?p=sitemap"><img src="images/icons/action.gif" alt="act" class="icon" />'.$_lang['xxl.admin.sitemap'].'</a><br />':'').'
      '.(_loginright_adminstatsword?'<a href="index.php?p=statsword"><img src="images/icons/action.gif" alt="act" class="icon" />'.$_lang['xxl.statsword'].'</a><br />':'').'
     
      '.((_loginright_adminpoll or _loginright_adminsbox or _loginright_adminbox)?'</p>':'').'


    </td>'

    
  :'').'


</tr>
</table>
';

?>