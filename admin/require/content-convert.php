<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- priprava, kontrola pristupovych prav ---*/
$type_array=_tmp_getTypeArray();
$message="";
if(!(_loginright_adminsection or _loginright_admincategory or _loginright_adminbook or _loginright_adminseparator or _loginright_admingallery or _loginright_adminintersection)){$continue=false; $output.=_formMessage(3, $_lang['global.accessdenied']);}
else{$continue=true;}

/*--- akce ---*/
if(isset($_POST['target'])){

  $target=intval($_POST['target']);
  $newtype=intval($_POST['newtype']);
  if(isset($type_array[$newtype]) and $newtype!=4) {
    if(constant('_loginright_admin'.$type_array[$newtype])) {
  
      //nalezeni a kontrola
      $query=mysql_query("SELECT * FROM `"._mysql_prefix."-root` WHERE id=".$target);
      if(mysql_num_rows($query)!=0){
        $query=mysql_fetch_array($query);
        if($newtype!=$query['type']){
          if(constant('_loginright_admin'.$type_array[$query['type']])){
  
          //odstraneni souvisejicich polozek
          switch($query['type']){
        
            //komentare v sekcich
            case 1:
            mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE type=1 AND home=".$query['id']);
            break;
            
            //clanky v kategoriich a jejich komentare
            case 2:
            $rquery=mysql_query("SELECT id,home1,home2,home3 FROM `"._mysql_prefix."-articles` WHERE home1=".$query['id']." OR home2=".$query['id']." OR home3=".$query['id']);
              while($item=mysql_fetch_array($rquery)){
                if($item['home1']==$query['id'] and $item['home2']==-1 and $item['home3']==-1){mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE type=2 AND home=".$item['id']); mysql_query("DELETE FROM `"._mysql_prefix."-articles` WHERE id=".$item['id']); continue;} //delete
                if($item['home1']==$query['id'] and $item['home2']!=-1 and $item['home3']==-1){mysql_query("UPDATE `"._mysql_prefix."-articles` SET home1=home2 WHERE id=".$item['id']); mysql_query("UPDATE `"._mysql_prefix."-articles` SET home2=-1 WHERE id=".$item['id']); continue;} //2->1
                if($item['home1']==$query['id'] and $item['home2']!=-1 and $item['home3']!=-1){mysql_query("UPDATE `"._mysql_prefix."-articles` SET home1=home2 WHERE id=".$item['id']); mysql_query("UPDATE `"._mysql_prefix."-articles` SET home2=home3 WHERE id=".$item['id']); mysql_query("UPDATE `"._mysql_prefix."-articles` SET home3=-1 WHERE id=".$item['id']); continue;} //2->1,3->2
                if($item['home1']==$query['id'] and $item['home2']==-1 and $item['home3']!=-1){mysql_query("UPDATE `"._mysql_prefix."-articles` SET home1=home3 WHERE id=".$item['id']); mysql_query("UPDATE `"._mysql_prefix."-articles` SET home3=-1 WHERE id=".$item['id']); continue;} //3->1
                if($item['home1']!=-1 and $item['home2']==$query['id']){mysql_query("UPDATE `"._mysql_prefix."-articles` SET home2=-1 WHERE id=".$item['id']); continue;} //2->x
                if($item['home1']!=-1 and $item['home3']==$query['id']){mysql_query("UPDATE `"._mysql_prefix."-articles` SET home3=-1 WHERE id=".$item['id']); continue;} //3->x
              }
            break;
            
            //prispevky v knihach
            case 3:
            mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE type=3 AND home=".$query['id']);
            break;
            
            //obrazky v galerii
            case 5:
            mysql_query("DELETE FROM `"._mysql_prefix."-images` WHERE home=".$query['id']);
            break;
            
            //polozky v rozcestniku
            case 7:
            $rquery=mysql_query("SELECT id,ord FROM `"._mysql_prefix."-root` WHERE intersection=".$query['id']);
            while($item=mysql_fetch_array($rquery)){
            mysql_query("UPDATE `"._mysql_prefix."-root` SET intersection=-1,ord=".($query['ord'].".".intval($item['ord']))." WHERE id=".$item['id']);
            }
            break;
          
            //prispevky ve forech
            case 8:
            mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE type=5 AND home=".$query['id']);
            break;
        
          }
        
          //zmena typu stranky
          switch($newtype){
          case 1: $var1=0; $var2=_template_autoheadings; $var3=0; break;
          case 2: $var1=1; $var2=15; $var3=1; break;
          case 3: $var1=1; $var2=15; $var3=0; break;
          case 5: $var1=-1; $var2=10; $var3=128; break;
          case 7: $var1=1; $var2=0; $var3=0; break;
          case 8: $var1=30; $var2=0; $var3=1; break;
          default: $var1=0; $var2=0; $var3=0; break;
          }
          mysql_query("UPDATE `"._mysql_prefix."-root` SET `type`=".$newtype.", `var1`=".$var1.", `var2`=".$var2.", `var3`=".$var3." WHERE id=".$target);
          $message=_formMessage(1, $_lang['global.done']);
  
          } else {
            $message=_formMessage(2, $_lang['global.accessdenied']);
          }
        } else {
          $message=_formMessage(2, $_lang['admin.content.convert.nochange']);
        }
      } else {
        $message=_formMessage(2, $_lang['global.error404.title']);
      }

    } else {
      $message=_formMessage(2, $_lang['global.accessdenied']);
    }
  } else {
    $message=_formMessage(3, $_lang['global.badinput']);
  }

}

/*--- vystup ---*/
if($continue){

//target select
$target_select="<select name='target'>"._nl."<option value='-1'></option>"._nl;
$query=mysql_query('SELECT `id`,`title`,`type` FROM `'._mysql_prefix.'-root` WHERE `intersection`=-1 AND `type`!=4 ORDER BY `ord`');
while($item=mysql_fetch_assoc($query)){
  if($item['type']==7){
    //polozky v rozcestniku
    $target_select.="<optgroup label='".$item['title']."'>"._nl."<option value='".$item['id']."' class='special'>".$_lang['admin.content.convert.intersec']."</option>";
    $query2=mysql_query('SELECT `id`,`title`,`type` FROM `'._mysql_prefix.'-root` WHERE `intersection`='.$item['id'].' AND `type`!=4 ORDER BY `ord`');
    while($item2=mysql_fetch_assoc($query2)){
      $target_select.="<option value='".$item2['id']."'".(!constant('_loginright_admin'.$type_array[$item2['type']])?' disabled=\'disabled\'':'').">".$item2['title']."</option>"._nl;
    }
    $target_select.="</optgroup>"._nl;
  } else {
    $target_select.="<option value='".$item['id']."'".(!constant('_loginright_admin'.$type_array[$item['type']])?' disabled=\'disabled\'':'').">".$item['title']."</option>"._nl;
  }
}
$target_select.="</select>";

//type select
$type_select="<select name='newtype'>";
foreach($type_array as $type=>$name){
  if($type==4){continue;}
  $type_select.="<option value='".$type."'".(!constant('_loginright_admin'.$name)?' disabled=\'disabled\'':'').">".$_lang['admin.content.'.$name]."</option>"._nl;
}

//form
$output.="<p class='bborder'>".$_lang['admin.content.convert.p']."</p>".$message."
<form action='index.php?p=content-convert' method='post'>
<table>

<tr>
<td class='rpad'><strong>".$_lang['admin.content.convert.target']."</strong></td>
<td>".$target_select."</td>
</tr>

<tr>
<td><strong>".$_lang['admin.content.convert.newtype']."</strong></td>
<td>".$type_select."</td>
</tr>

<tr><td></td><td><input type='submit' value='".$_lang['global.do']."' onclick='return _sysConfirm();' /></td></tr>
</table>
</form>
";

}



?>