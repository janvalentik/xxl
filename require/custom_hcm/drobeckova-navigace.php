<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

function jval_intersection($id){
    $return="";
    $parent=mysql_fetch_array(mysql_query("select intersection from `"._mysql_prefix."-root` where id=".$id));
    if($parent[0]!=-1 ){
      $intersection=mysql_fetch_array(mysql_query("select title from `"._mysql_prefix."-root` where id=".$parent[0]));
      $return.="&nbsp;»&nbsp;<a href='"._linkroot($parent[0])."'>".$intersection[0]."</a>";
    }
    return $return;
}

$output.="<a href='./'>".$GLOBALS['_lang']['xxl.home']."</a>";
if (isset($_GET["p"]) and mysql_num_rows(mysql_query("select id from `"._mysql_prefix."-root` where id=".$_GET["p"]))!=0) {
  $output.=jval_intersection($_GET["p"]);
	$current=mysql_fetch_array(mysql_query("select title from `"._mysql_prefix."-root` where id=".$_GET["p"]));
  if ($_GET["p"]!=1) {
	   $output.="&nbsp;»&nbsp;".$current[0]."";
  }
}
if (isset($_GET["a"]) and mysql_num_rows(mysql_query("select id from `"._mysql_prefix."-articles` where id=".$_GET["a"]))!=0){
    $cats=mysql_fetch_array(mysql_query("select * from `"._mysql_prefix."-articles` where id=".$_GET["a"]));
    for($i=1; $i<=3; $i++){
      if($cats['home'.$i]!=-1){
      $title=mysql_fetch_array(mysql_query("SELECT title FROM `"._mysql_prefix."-root` WHERE id=".$cats['home'.$i]));
      $categories.="".$title['title']."";
      }
      if($i!=3 and $cats['home'.($i+1)]!=-1){$categories.=", ";}
    }
  $output.="&nbsp;»&nbsp;".$categories;
}
if (isset($_GET["m"]) and $_GET["m"]=="topic"){
    $forum=mysql_fetch_array(mysql_query("select home from `"._mysql_prefix."-posts` where id=".$_GET["id"]));
    $root=mysql_fetch_array(mysql_query("select title,id from `"._mysql_prefix."-root` where id=".$forum[0]));
    $output.=jval_intersection($root["id"]);
    $output.="&nbsp;>&nbsp;<a href='"._linkroot($root["id"])."'>".$root["title"]."</a>";
}


?>