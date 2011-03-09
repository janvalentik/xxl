<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*---- nacteni promennych ----*/
$continue=false;
$custom_array=array();
$custom_settings="";

if(isset($_GET['id'])){
$id=intval($_GET['id']);
$query=mysql_query("SELECT * FROM `"._mysql_prefix."-root` WHERE id=".$id." AND type=".$type);
  if(mysql_num_rows($query)!=0){
  $query=mysql_fetch_array($query);
  $continue=true;
  $new=false;
  }
}
else{
$id=null;
$new=true;
$continue=true;

  /*--- simulace dat pro novou polozku ---*/
  switch($type){
  case 1: $var1=0; $var2=_template_autoheadings; $var3=0; $var4=0; $var5=0; break;
  case 2: $var1=1; $var2=15; $var3=1; $var4=0; $var5=0; break;
  case 3: $var1=1; $var2=15; $var3=0; $var4=0; $var5=0; break;
  case 5: $var1=-1; $var2=10; $var3=128; $var4=0; $var5=0; break;
  case 7: $var1=1; $var2=0; $var3=0; $var4=0; $var5=0; break;
  case 8: $var1=30; $var2=0; $var3=1; $var4=0; $var5=0; break;
  default: $var1=0; $var2=0; $var3=0; $var4=0; $var5=0; break;
  }
  
  $query=array(
  'id'=>-1,
  'title'=>$_lang['admin.content.'.$type_array[$type]],
  'type'=>$type,
  'intersection'=>-1,
  'intersectionperex'=>'',
  'ord'=>1,
  'content'=>'',
  'visible'=>1,
  'public'=>1,
  'var1'=>$var1,
  'var2'=>$var2,
  'var3'=>$var3,
  'var4'=>$var4,
  'var5'=>$var5
  );

}

?>