<?php

/*---- inicializace jadra ----*/
define('_indexroot', './');
require(_indexroot."core.php");


/*---- vystup ----*/
  $path="upload/download/";
  $location=""._url."";
  if (isset($_GET['dl'])) {
    $dl=AddSlashes(Chop($_GET['dl']));
    $result = mysql_query("select cesta,public from `" . _mysql_prefix . "-download` where id='".$_GET['dl']."'");
    $data = mysql_fetch_row($result);
    if (!empty($data[0])) {
      if ($data[1]==0 and _loginindicator==0){
        _systemMessage("Chyba", $_lang['notpublic.title']);
      }
      else{
        mysql_query("update `" . _mysql_prefix . "-download` set stazeno=stazeno+1 where id='".$_GET['dl']."'");
        $location=""._url."/".$path.$data[0];
      }
    }
  }
  Header("Location: ".$location);
  exit;

?>