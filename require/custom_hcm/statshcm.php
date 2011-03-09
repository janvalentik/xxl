<?php
$query=mysql_query("SELECT * FROM `"._mysql_prefix."-settings` WHERE var='pocitadlo'");
while ($item=mysql_fetch_array($query)) {
$output.="".$item['val']."";}
?>
