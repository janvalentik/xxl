<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- modul ---*/
if(_template_autoheadings==1){$module="<h1>".$_lang['admin.users.list']."</h1>";}else{$module="";}
$module.="<p class='bborder'>".$_lang['mod.ulist.p']."</p>";

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

  //vyber skupiny
  $module.='
  <form action="index.php" method="get">
  <input type="hidden" name="m" value="ulist" />
  <strong>'.$_lang['admin.users.list.groupfilter'].':</strong> <select name="group">
  <option value="-1">'.$_lang['global.all'].'</option>
  ';
    $query=mysql_query("SELECT id,title FROM `"._mysql_prefix."-groups` WHERE id!=2 ORDER BY level DESC");
    while($item=mysql_fetch_array($query)){
    if($item['id']==$group){$selected=' selected="selected"';}else{$selected="";}
    $module.='<option value="'.$item['id'].'"'.$selected.'>'.$item['title'].'</option>';
    }
  $module.='</select> <input type="submit" value="'.$_lang['global.apply'].'" /></form>';

//tabulka
$paging=_resultPaging("index.php?m=ulist&amp;group=".$group, 50, "users", $grouplimit2);
if(_pagingmode==1 or _pagingmode==2){$module.=$paging[0];}
$module.="<br /><table class='widetable'>\n<tr><td><strong>".$_lang['login.username']."</strong></td><td><strong>".$_lang['global.group']."</strong></td></tr>\n";
$query=mysql_query("SELECT `"._mysql_prefix."-users`.username, `"._mysql_prefix."-groups`.title, `"._mysql_prefix."-groups`.icon, `"._mysql_prefix."-users`.id FROM `"._mysql_prefix."-users`, `"._mysql_prefix."-groups` WHERE `"._mysql_prefix."-users`.`group`=`"._mysql_prefix."-groups`.id".$grouplimit." ORDER BY `"._mysql_prefix."-groups`.level DESC,`"._mysql_prefix."-users`.id ".$paging[1]);
while($item=mysql_fetch_array($query)){
$module.="<tr><td>".(($item['icon']!="")?"<img src='"._indexroot."groupicons/".$item['icon']."' alt='icon' class='icon' /> ":'')."<a href='index.php?m=profile&amp;id=".$item['username']."'>".$item['username']."</a></td><td>".$item['title']."</td></tr>";
}
$module.="</table>";

if(_pagingmode==2 or _pagingmode==3){$module.=$paging[0];}

  //celkovy pocet uzivatelu
  $totalusers=mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-users`"), 0);
  $module.="<p>".$_lang['admin.users.list.totalusers'].": ".$totalusers."</p>";

?>