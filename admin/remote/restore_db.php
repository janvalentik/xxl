<?php

/*--- inicializace jadra ---*/
define('_indexroot', '../../');
require(_indexroot."core.php");

/*--- vystup ---*/
if(!_loginright_adminbackup){exit;}

//funkce pro vypsani hlasky
function _tmp_restoreMessage($string, $success=false){
global $_lang;

if($success){$link=_indexroot;}
else{$link=_indexroot."admin/index.php?p=other-backup";}

require(_indexroot."require/headstart.php");
?>
<link href="style.php?s=<?php echo _adminscheme; ?>" type="text/css" rel="stylesheet" />
<title><?php echo $_lang['admin.other.backup.restore']; ?></title>
</head>

<body>
<div id="external-container">
<h1><?php echo $_lang['admin.other.backup.restore']; ?></h1>
<p><?php echo $string; ?></p>
<p><a href="<?php echo $link; ?>">&lt; <?php echo $_lang['global.return']; ?></a></p>
</div>
</body>
</html><?php

exit;

}

//nacteni uploadovaneho souboru
if(is_uploaded_file($_FILES['backup']['tmp_name'])){
  $info=pathinfo($_FILES['backup']['name']);
    if(isset($info['extension']) and $info['extension']=="sld"){
      $data=@file_get_contents($_FILES['backup']['tmp_name']);
        if($data!=null){

          //nacteni hlavicky a obsahu zalohy
          $data=@explode("\n\n", $data, 2);
          if(count($data)!=2){_tmp_restoreMessage($_lang['admin.other.backup.restore.dataerror']);}
          $header=@explode(";", $data[0]);
          if(count($header)!=2){_tmp_restoreMessage($_lang['admin.other.backup.restore.dataerror']);}

          //nacteni obsahu
          $contents=$data[1];

            //dekomprese
            if($header[1]==1){
              if(!function_exists("gzinflate")){_tmp_restoreMessage($_lang['admin.other.backup.restore.cannotdecompress']);}
              $contents=gzinflate($contents);
              if($contents==false){_tmp_restoreMessage($_lang['admin.other.backup.restore.dataerror']);}
            }

          $contents=@explode("\n", $contents);
          if($contents==false or count($contents)==1){_tmp_restoreMessage($_lang['admin.other.backup.restore.dataerror']);}

            //kontrola verze, obnoveni databaze
            if(_checkVersion("database_backup", $header[0])){

              //promazani tabulek
              $tables = array();
              $result = mysql_query('SHOW TABLES');
              while($row = mysql_fetch_row($result)){
                $pref=explode('-',$row[0]);
                if ($pref[0]==_mysql_prefix){$tables[] = $row[0];}
              }

              foreach($tables as $table){
              mysql_query("TRUNCATE TABLE `".$table."`");
              }

              //vlozeni dat
              foreach($contents as $line){
              if($line==""){continue;}
              mysql_query("INSERT INTO `"._mysql_prefix."-".$line);
              if(mysql_error()!=false){_tmp_restoreMessage($_lang['admin.other.backup.restore.queryerror']);}
              }

              //odhlaseni, zprava
              _userLogout();
              _tmp_restoreMessage($_lang['admin.other.backup.restore.success'], true);

            }
            else{
            _tmp_restoreMessage($_lang['admin.other.backup.restore.badversion']);
            }
        }
        else{
        _tmp_restoreMessage($_lang['admin.other.backup.restore.dataerror']);
        }
    }
    else{
    _tmp_restoreMessage($_lang['admin.other.backup.restore.badextension']);
    }
}
else{
  _tmp_restoreMessage($_lang['admin.other.backup.restore.notuploaded']);
}

?>