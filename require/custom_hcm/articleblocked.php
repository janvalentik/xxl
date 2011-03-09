<?php

$output.="<ul>";

$query=("SELECT * FROM `"._mysql_prefix."-articles` WHERE confirmed=0 LIMIT 10"); 

$vysledek = mysql_query($query); 

while ($zaznam = mysql_fetch_array($vysledek)){

$id=$zaznam["id"];

$title=$zaznam["title"];

$time=$zaznam["time"];

$author=$zaznam["author"];

$output.="<li><a href='"._url."/admin/index.php?p=content-articles-edit&id=".$id."&returnid=load&returnpage=1'>".$title."</a> <small>- $author / ("._formatTime($time).")</small>";

}

$output.="</ul>";

?>