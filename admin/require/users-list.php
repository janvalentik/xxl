<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- vystup ---*/

$output.="<p>".$_lang['admin.users.list.p']."</p>";

//filtr skupiny
$grouplimit="";
$grouplimit2="1";
if(isset($_GET['group'])){
$group=intval($_GET['group']);
if($group!=-1){$grouplimit=" AND `"._mysql_prefix."-groups`.id=".$group; $grouplimit2="`group`=".$group;}
}
else{
$group=-1;
}

//aktivace vyhledavani
if(isset($_GET['search']) and $_GET['search']!=""){
  $search=true;
  $searchword=_safeStr($_GET['search']);
}
else{
  $search=false;
}

  //filtry - vyber skupiny, vyhledavani
  $output.='
  <table class="widetable">
  <tr>
  
  <td>
  <form class="cform" action="index.php" method="get">
  <input type="hidden" name="p" value="users-list" />
  <input type="hidden" name="search"'._restoreGetValue('search', '').' />
  <strong>'.$_lang['admin.users.list.groupfilter'].':</strong> '._tmp_authorSelect("group", $group, "id!=2", null, $_lang['global.all'], true).'
  </select> <input type="submit" value="'.$_lang['global.apply'].'" />
  </form>
  </td>
  
  <td>
  <form class="cform" action="index.php" method="get">
  <input type="hidden" name="p" value="users-list" />
  <input type="hidden" name="group" value="'.$group.'" />
  <strong>'.$_lang['admin.users.list.search'].':</strong> <input type="text" name="search" class="inputsmall"'._restoreGetValue('search').' /> <input type="submit" value="'.$_lang['mod.search.submit'].'" />
  '.($search?'&nbsp;<a href="index.php?p=users-list&amp;group='.$group.'">'.$_lang['global.cancel'].'</a>':'').'
  </form>
  </td>
  
  </tr>
  </table>
  ';

//tabulka

  //priprava strankovani
  if(!$search){$paging=_resultPaging("index.php?p=users-list&amp;group=".$group, 50, "users", $grouplimit2); $output.=$paging[0];}
  
  //tabulka
  $output.="<br />\n<table class='widetable'>\n<tr><td><strong>".$_lang['login.username']."</strong></td><td><strong>".$_lang['mod.settings.publicname']."</strong></td><td><strong>".$_lang['global.email']."</strong></td><td colspan='2'><strong>".$_lang['global.group']."</strong></td></tr>\n";

  //dotaz na db
  if(!$search){$query=mysql_query("SELECT `"._mysql_prefix."-users`.email,`"._mysql_prefix."-users`.username, `"._mysql_prefix."-users`.publicname, `"._mysql_prefix."-users`.levelshift, `"._mysql_prefix."-groups`.title, `"._mysql_prefix."-groups`.icon, `"._mysql_prefix."-users`.id FROM `"._mysql_prefix."-users`, `"._mysql_prefix."-groups` WHERE `"._mysql_prefix."-users`.`group`=`"._mysql_prefix."-groups`.id".$grouplimit." ORDER BY `"._mysql_prefix."-groups`.level DESC,`"._mysql_prefix."-users`.id ".$paging[1]);}
  else{$query=mysql_query("SELECT `"._mysql_prefix."-users`.email,`"._mysql_prefix."-users`.username, `"._mysql_prefix."-users`.publicname, `"._mysql_prefix."-users`.levelshift, `"._mysql_prefix."-groups`.title, `"._mysql_prefix."-groups`.icon, `"._mysql_prefix."-users`.id FROM `"._mysql_prefix."-users`, `"._mysql_prefix."-groups` WHERE `"._mysql_prefix."-users`.`group`=`"._mysql_prefix."-groups`.id AND (`"._mysql_prefix."-users`.username LIKE '%".$searchword."%' OR `"._mysql_prefix."-users`.publicname LIKE '%".$searchword."%' OR `"._mysql_prefix."-users`.email LIKE '%".$searchword."%' OR `"._mysql_prefix."-users`.ip LIKE '%".$searchword."%')".$grouplimit." ORDER BY `"._mysql_prefix."-groups`.level DESC,`"._mysql_prefix."-users`.id ");}

  //vypis
  if(mysql_num_rows($query)!=0){
    while($item=mysql_fetch_array($query)){
    $output.="<tr><td>".(($item['icon']!="")?"<img src='"._indexroot."groupicons/".$item['icon']."' alt='icon' class='groupicon' /> ":'')."<a href='index.php?p=users-edit&amp;id=".$item['username']."'>".(($item['levelshift']==1)?"<strong>":'').$item['username'].(($item['levelshift']==1)?"</strong>":'')."</a></td><td>".(($item['publicname']!="")?$item['publicname']:"-")."</td><td>".$item['email']."</td><td>".$item['title']."</td><td><a href='index.php?p=users-delete&amp;id=".$item['username']."' onclick='return _sysConfirm();'><img src='images/icons/delete.gif' alt='del' class='icon' />".$_lang['global.delete']."</a></td></tr>\n";
    }
  }
  else{
    $output.="<tr><td colspan='4'>".$_lang['global.nokit']."</td></tr>\n";
  }

  $output.="</table>";

  //pocet uzivatelu
  $totalusers=mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users`"), 0);
  $output.="\n<br />".$_lang['admin.users.list.totalusers'].": ".$totalusers;

?>