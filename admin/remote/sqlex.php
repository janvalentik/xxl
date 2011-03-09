<?php

/*--- inicializace jadra ---*/
define('_indexroot', '../../');
require(_indexroot."core.php");

/*--- vystup ---*/
if(_loginright_level!=10001){exit;}
require(_indexroot."require/headstart.php");

?>
<link href="style.php?s=<?php echo _adminscheme; ?>" type="text/css" rel="stylesheet" />
<title><?php echo $_lang['admin.other.sqlex.title']; ?></title>
<script type="text/javascript">
//<![CDATA[
function _tmp_tableName(){
tablename=prompt("<?php echo $_lang['admin.other.sqlex.tablename']; ?>:");
if(tablename!=null && tablename!=""){document.form.sql.value=document.form.sql.value+"`<?php echo _mysql_prefix ?>-"+tablename+"`";}
document.form.sql.focus();
return false;
}
//]]>
</script>
</head>

<body>
<div id="external-container">


<?php

//nacteni postdat
$process=false;
if(isset($_POST['sql'])){
$sql=_stripSlashes($_POST['sql']);
$process=true;
}

?>

<h1><?php echo $_lang['admin.other.sqlex.title']; ?></h1>

<form action="sqlex.php" method="post" name="form">
<input type="text" name="sql" class="inputbig"<?php if($process){echo " value=\""._htmlStr($sql)."\"";} ?> />
<input type="submit" value="<?php echo $_lang['admin.other.sqlex.run']; ?>" />&nbsp;&nbsp;<a href="#" onclick="return _tmp_tableName();"><?php echo $_lang['admin.other.sqlex.tablename.hint']; ?></a>
</form>

<?php
if($process){
  echo '<h2>'.$_lang['global.result'].'</h2><br />';
  $query=@mysql_query($sql);
    if(mysql_error()==null){

      $heading=false;
      $fields=array();
      $aff_rows=mysql_affected_rows();
      $num_rows=intval(@mysql_num_rows($query));

      if($num_rows!=0){
        echo '<p><strong>'.$_lang['admin.other.sqlex.rows'].':</strong> '.$num_rows.'</p><table border="1">'."\n";
        while($item=@mysql_fetch_array($query)){

          //nacteni sloupcu, vytvoreni hlavicky tabulky
          if(!$heading){

            //sloupce
            $load=false;
            foreach($item as $field=>$value){
            if($load){$fields[]=$field;}
            $load=!$load;
            }

            //hlavicka
            $heading=true;
            echo "<tr>";
            foreach($fields as $field){echo "<td><strong>".$field."</strong></td>";}
            echo "</tr>\n";

          }

          //radek vystupu
          echo "<tr valign='top'>";
            foreach($fields as $field){
              if(mb_substr_count($item[$field], "\n")==0){$content=_htmlStr($item[$field]);}
              else{$content="<textarea rows='8' cols='80' readonly='readonly'>"._htmlStr($item[$field])."</textarea>";}
              echo "<td>".$content."</td>";
            }
          echo "</tr>\n";

        }
        echo "</table>";
      }
      else{
        if($aff_rows==0){echo "\n<p>".$_lang['admin.other.sqlex.null']."</p>\n";}
        else{echo "\n<p><strong>".$_lang['admin.other.sqlex.affected'].":</strong> ".$aff_rows."</p>\n";}
      }

    }
    else{
      echo "<h3>".$_lang['global.error'].":</h3>\n<pre>"._htmlStr(mysql_error())."</pre>";
    }
}
?>

</div>
</body>
</html>