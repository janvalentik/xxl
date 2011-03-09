<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_jv_template(){

  $return='<form action="" method="post" class="jv_template">';
  //vyber motivu
  $return.='<select name="template">';
    $handle=@opendir(_indexroot."templates");
    while($item=@readdir($handle)){
    if($item=="." or $item==".."){continue;}
    if($item==$_SESSION['template'] or $item==$_POST['template']){$selected=' selected="selected"';}else{$selected="";}
    $return.='<option value="'.$item.'"'.$selected.'>'.$item.'</option>';
    }
    closedir($handle);
  $return.='</select>';
  $return.='<input type="submit" value="Změnit" />';
  $return.='</form>';

  return $return;

}

?>