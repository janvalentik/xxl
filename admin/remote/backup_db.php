<?php

/*--- inicializace jadra ---*/
define('_indexroot', '../../');
define('_tmp_customheader', '');
require(_indexroot."core.php");

/*--- vystup ---*/
if(!_loginright_adminbackup){exit;}

//data zalohy
$compress=_checkboxLoad("compress");
if($compress==1 and !function_exists("gzdeflate")){$compress=0;}
$backup_version=_checkVersion("database_backup", null, true);
$header=$backup_version[0].";".$compress."\n\n";
$delimiter="";
$output="";
$tables = array();
$result = mysql_query('SHOW TABLES');
while($row = mysql_fetch_row($result)){
  $pref=explode('-',$row[0]);
  if ($pref[0]==_mysql_prefix){$tables[] = $row[0];}
}
foreach($tables as $table)
{
 $result = mysql_query('SELECT * FROM `'.$table.'`');
 $num_fields = mysql_num_fields($result);

 for ($i = 0; $i < $num_fields; $i++)
 {
   while($row = mysql_fetch_row($result))
   {
     $tbl=explode('-',$table);
     $output.= $tbl[1].'` VALUES(';
     for($j=0; $j<$num_fields; $j++)
     {
       $row[$j] = addslashes($row[$j]);
       if (isset($row[$j])) { $output.= '"'.$row[$j].'"' ; } else { $output.= '""'; }
       if ($j<($num_fields-1)) { $output.= ','; }
     }
     $output.= ")\n";
   }
 }
}

$output=trim($output);

//stazeni
header('Content-Description: File Transfer');
header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename="'._mysql_db.'_'.date("Y_m_d").'.sld'.'"');
if($compress==1){$output=gzdeflate($output);}
echo $header.$output;

?>