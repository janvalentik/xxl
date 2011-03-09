<?php



/*--- kontrola jadra ---*/

if(!defined('_core')){exit;}



/*--- ulozeni ---*/

if(isset($_POST['title']) and $continue){



  //pole vstupu (nazev, checkbox?, zpracovani: 0-nic 1-htmlstr 2-intval 3-floatval, textova hodnota?)

  $save_array=array(

  array("title",            false, 1, true),

  array("intersection",     false, 2, false),

  array("ord",              false, 3, false),

  array("visible",          true,  0, false),

  array("public",           true,  0, false),

  array("intersectionperex",false, 0, true),

  array("content",          false, (($type!=6)?0:1), true),

  array("keywords",         false, 0, true),

  array("seotitle",         false, 0, true),

  array("description",         false, 0, true),

  );

  $save_array=array_merge($save_array, $custom_array);



  //ulozeni

  $sql="";

  $new_column_list="";

  foreach($save_array as $item){



    //nacteni a zpracovani hodnoty

    if($item[1]==false){

      if(!isset($_POST[$item[0]])){$_POST[$item[0]]=null;}

        switch($item[2]){

        case 0: $val=$_POST[$item[0]]; break;

        case 1: $val=_htmlStr($_POST[$item[0]]); break;

        case 2: $val=intval($_POST[$item[0]]); break;

        case 3: $val=floatval($_POST[$item[0]]); break;

        }

    }

    else{

    $val=_checkboxLoad($item[0]);

    }



    //individualni akce

    $skip=false;

    $stripslashes=true;

    switch($item[0]){

    

      //content

      case "content":

      $val=_filtrateHCM(_stripSlashes($val));

      $stripslashes=false;

      break;



      //intersection

      case "intersection":

      if(mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-root` WHERE id=".$val." AND type=7"), 0)==0 or $type==7){$val=-1;}

      break;



      //title

      case "title":

      $val=trim($val); if($val==""){$val=$_lang['global.novalue'];}

      break;



      //var1

      case "var1":

      switch($type){

        //zpusob razeni v kategoriich

        case 2: if($val<1 or $val>4){$val=1;} break;

        //obrazku na radek v galerii

        case 5: if($val<=0 and $val!=-1){$val=1;} break;

        //temat na stranu ve forech

        case 8: if($val<=0){$val=1;} break;

      }

      break;



      //var2

      case "var2":

      switch($type){

        //clanku na stranu v kategoriich, prispevku na stranu v knihach, obrazku na stranu v galeriich

        case 2: case 3: case 5: if($val<=0){$val=1;} break;

      }

      break;

      

      //var3

      case "var3":

      switch($type){

        //vyska nahledu v galeriich

        case 5: if($val<=0){$val=1;} if($val>1024){$val=1024;} break;

      }

      break;



        //smazani komentaru v sekcich

        case "delcomments":

        if($type==1 and $val==1){mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE home=".$id." AND type=1");}

        if($type==5 and $val==1){mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE home=".$id." AND type=7");}

        $skip=true;

        break;



        //smazani prispevku v knihach

        case "delposts":

        if($val==1){

          $ptype=null;

            switch($type){

              case 3: $ptype=3; break;

              case 8: $ptype=5; break;

            }

          if($ptype!=null){mysql_query("DELETE FROM `"._mysql_prefix."-posts` WHERE home=".$id." AND type=".$ptype);}

        }

        $skip=true;

        break;

    }



    if($skip!=true){



      //uvozovky pro text

      if($item[3]==true){$quotes="'";}

      else{$quotes="";}



      //ulozeni casti sql dotazu

      if(!$new){$sql.=$item[0]."=".$quotes._safeStr($val, $stripslashes).$quotes.",";}

      else{$new_column_list.=$item[0].","; $sql.=$quotes._safeStr($val, $stripslashes).$quotes.",";}



    }



    }

    

    //vlozeni / ulozeni

    $sql=trim($sql, ",");

    if(!$new){

      //ulozeni

      mysql_query("UPDATE `"._mysql_prefix."-root` SET ".$sql."  WHERE id=".$id);

    }

    else{

      //vytvoreni

      $id=_getNewID("root");

      $new_column_list=trim($new_column_list, ",");

      mysql_query("INSERT INTO `"._mysql_prefix."-root` (id,type,".$new_column_list.") VALUES (".$id.",".$type.",".$sql.")");

    }



define('_tmp_redirect', 'index.php?p=content-edit'.$type_array[$type].'&id='.$id.'&saved');

}



/*--- vystup ---*/

if($continue!=true){

$output.=_formMessage(3, $_lang['global.badinput']);

}

else{



  //vyber rozcestniku

  if($type!=7){

  $intersection_select="<select name='intersection' class='selectmedium'><option value='-1' class='special'>".$_lang['admin.content.form.intersection.none']."</option>";

  $isquery=mysql_query("SELECT id,title FROM `"._mysql_prefix."-root` WHERE type=7 ORDER BY ord");

    while($item=mysql_fetch_array($isquery)){

    if($item['id']==$query['intersection']){$selected=" selected='selected'";}else{$selected="";}

    $intersection_select.="<option value='".$item['id']."'".$selected.">"._cutStr($item['title'], 22)."</option>";

    }

  $intersection_select.="</select>";

  }

  else{

  $intersection_select="";

  }



//wysiwyg editor

$output.=_tmp_wysiwyg();



//stylove oddeleni individualniho nastaveni

if($custom_settings!=""){$custom_settings="<span class='customsettings'>".$custom_settings."</span>";}



//formular

$output.="<div class='hr'><hr /></div><br />"

.(isset($_GET['saved'])?_formMessage(1, $_lang['global.saved']."&nbsp;&nbsp;<small>("._formatTime(time()).")</small>"):'')."

<form".(($type!=4)?" class='cform'":'')." action='index.php?p=content-edit".$type_array[$type].(!$new?"&amp;id=".$id:'')."' method='post'>

<table>



<tr>

<td class='rpad'><strong>".$_lang['admin.content.form.title']."</strong></td>

<td><input type='text' name='title' value='".$query['title']."' class='inputmedium' /></td>

</tr>



".(($type!=7)?"<tr><td class='rpad'><strong>".$_lang['admin.content.form.intersection']."</strong></td><td>".$intersection_select."</td></tr>":'')."



<tr>

<td class='rpad'><strong>".$_lang['admin.content.form.ord']."</strong></td>

<td><input type='text' name='ord' value='".$query['ord']."' class='inputmedium' /></td>

</tr>



".(($type!=4 and $type!=6)?"

<tr>

<td class='rpad'><strong>".$_lang['admin.content.form.keywords']."</strong></td>

<td><input type='text' name='keywords' value='".$query['keywords']."' class='inputmedium' /></td>

</tr>

<tr>

<td class='rpad'><strong>".$_lang['admin.content.form.seotitle']."</strong></td>

<td><input type='text' name='seotitle' value='".$query['seotitle']."' class='inputmedium' /></td>

</tr>

<tr valign='top'>

<td class='rpad'><strong>".$_lang['admin.content.form.description']."</strong></td>

<td><textarea name='description' rows='2' cols='94' class='arealine'>"._htmlStr($query['description'])."</textarea></td>

</tr>

":'')."



".(($type!=4)?"

<tr valign='top'>

<td class='rpad'><strong>".$_lang['admin.content.form.intersectionperex']."</strong></td>

<td><textarea name='intersectionperex' rows='2' cols='94' class='arealine codemirror xml'>"._htmlStr($query['intersectionperex'])."</textarea></td>

</tr>



<tr valign='top'>

<td class='rpad'><strong>".$_lang['admin.content.form.'.(($type!=6)?'content':'url')]."</strong>".(!$new?" <a href='"._indexroot._linkRoot($query['id'])."' target='_blank'><img src='images/icons/loupe.gif' alt='prev' /></a>":'')."</td>

<td>

".(($type!=6)?

"<textarea name='content' rows='25' cols='94' class='areabig".((!_wysiwyg || !_loginwysiwyg) ? ' codemirror xml' : '')."' id='wysiwygtarget'>"._htmlStr($query['content'])."</textarea>":

"<input type='text' name='content' value='".$query['content']."' class='inputbig' />"

)."

</td>

</tr>



<tr>

<td class='rpad'><strong>".$_lang['admin.content.form.settings']."</strong></td>

<td>

<label><input type='checkbox' name='visible' value='1'"._checkboxActivate($query['visible'])." /> ".$_lang['admin.content.form.visible']."</label>&nbsp;&nbsp;

<label><input type='checkbox' name='public' value='1'"._checkboxActivate($query['public'])." /> ".$_lang['admin.content.form.public']."</label>&nbsp;&nbsp;

".$custom_settings."

</td>

</tr>

":'')."





<tr><td></td><td><br />

<input type='submit' value='".($new?$_lang['global.create']:$_lang['global.savechanges'])."' />".(!$new?"&nbsp;&nbsp;<small>".$_lang['admin.content.form.thisid']." ".$query['id']."</small>":'')."

</td></tr>



</table>

</form>

";



}



?>