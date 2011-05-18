<?php

/* --- kontrola jadra --- */
if (!defined('_core')) {
    exit;
}

/* --- pripava promennych --- */
$mysqlver = mysql_get_server_info();
if (mb_substr_count($mysqlver, "-") != 0) {
    $mysqlver = mb_substr($mysqlver, 0, strpos($mysqlver, "-"));
}
$software = getenv('SERVER_SOFTWARE');
if (mb_strlen($software) > 16) {
    $software = substr($software, 0, 13) . "...";
}

/* --- vystup --- */

//nacteni verze mysql
$mysqlver = @mysql_get_server_info();
if ($mysqlver != null and mb_substr_count($mysqlver, "-") != 0) {
    $mysqlver = mb_substr($mysqlver, 0, strpos($mysqlver, "-"));
}

if (isset($_POST['poznamky'])) {
    mysql_query("UPDATE `" . _mysql_prefix . "-settings` set val='" . _safeStr($_POST['poznamky']) . "' WHERE var='note'");
    if (!mysql_error()) {
        $output.=_formMessage(1, $_lang['global.saved']);
    }
}

$output.="
<table id='indextable'>
<tr valign='top'>
<td width='250'>
<p>" . $_lang['admin.index.p'] . "</p>
<ul class='net'>
<li><a href='http://labs.studioart.cz/' target='_blank'>" . $_lang['xxl.admin.link'] . "</a></li>
<li><a href='http://sunlight.shira.cz/' target='_blank'>" . $_lang['admin.link.web'] . "</a></li>
<li><a href='http://sunlight.shira.cz/feedback/docs.php' target='_blank'>" . $_lang['admin.link.docs'] . "</a></li>
<li><a href='http://sunlight.shira.cz/feedback/forum.php' target='_blank'>" . $_lang['admin.link.forum'] . "</a></li>
<li><a onclick=\"window.open('/dokumentace/','XXL Dokumentace','resizable=yes,scrollbars=yes,width=800,height=600,left=300,top=300');return false;\" href=\"#\">" . $_lang['admin.link.docs.xxl'] . "</a></li>

</ul>
</td>

<td width='600' style='height:200px; overflow:auto;'>
<h2>" . $_lang['admin.index.note'] . "</h2>";
$output.="<form action='' method='post' name='formular'>
<textarea name='poznamky' class='noe-area' rows='9' cols='33'>" . mysql_result(mysql_query("SELECT val FROM `" . _mysql_prefix . "-settings` WHERE var='note'"), 0) . "</textarea>
<br /><input type='submit' value='" . $_lang['global.save'] . "'></td>";

$output.="
<td width='500' style='height:150px;overflow:auto;'>
<h2>" . $_lang['admin.index.news'] . "</h2>
" . _parseHCM("[hcm]recentposts,5[/hcm]") . "
</td>
</tr>

<tr>

</tr>
</table>

<br />
<table id='indextable'>
<tr valign='top'>
<td width='200' style='height:250px;overflow:auto;'>
<h2>" . $_lang['admin.index.online'] . "</h2>
" . _parseHCM("[hcm]users,2,5[/hcm]") . "
</td>


<td width='150' style='height:250px;overflow:auto;'>
<h2>" . $_lang['admin.index.reg'] . "</h2>
" . _parseHCM("[hcm]users,1,5[/hcm]") . "
</td>


<td width='300' style='height:250px;overflow:auto;'>
<h2>" . $_lang['admin.index.waiting'] . "</h2>
" . _parseHCM("[hcm]custom,articleblocked[/hcm]") . "
</td>

<td width='250'>
<h2>" . $_lang['admin.index.stat'] . "</h2>
<div style='padding:5px;'>
" . $_lang['admin.index.stat-user'] . "<b>" . _parseHCM("[hcm]countusers[/hcm]") . "</b><br />
" . $_lang['admin.index.stat-article'] . "<b>" . _parseHCM("[hcm]countart[/hcm]") . "</b><br />
" . $_lang['admin.index.stat-down'] . "<b>" . _parseHCM("[hcm]custom,filecounter,../upload/download/[/hcm]") . "</b><br /><br />

" . _parseHCM("[hcm]custom,statshcm[/hcm]") . "<br />
<strong>" . $_lang['global.version'] . ":</strong> <span style=\"color:#008000;\">XXL 1.0.1</span><br />
<span id='hook'></span>
<strong>PHP:</strong> " . PHP_VERSION . "<br />
<strong>MySQL:</strong> " . $mysqlver . "<br />
</div>
</td>
</tr>
</table>
<br />";
$doc = new DOMDocument();
$doc->load('http://labs.studioart.cz/remote/rss.php?tp=4&id=29');
$arrFeeds = array();
foreach ($doc->getElementsByTagName('item') as $node) {
$itemRSS = array ( 
  'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
  'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
  'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
  'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
  );
array_push($arrFeeds, $itemRSS);
}
$output.="<h2>Aktuality</h2>";
$output.="<table id='indextable'><tr><td>";
$output.="<table id='news'>";
foreach ($arrFeeds as $key => $value) {
    if ($key==5) break;
    $output.="<tr><td>";
    $now=strtotime(str_replace('/', '', $value['date']));
    $output.=date('d.m.Y',$now)." - <strong><a target='_blank' title='Přejít na celý článek' href='".$value['link']."'>".$value['title']."</a></strong><br /><br />";
    $output.=eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_+.~#?&//=]+)', '<a href="\\1" target=_blank>\\1</a>', $value['desc']) . "<br />";
    $output.="</td></tr>";
}
$output.="</table>";
$output.="</td></tr></table>";
?>