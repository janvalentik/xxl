<?php 

if (_loginindicator==0){

    $output.=_formMessage(2, "".$_lang['xxl.admin.download.manager.upload.hcm']."");} 



else{

  $id=intval($_params[1]); 

  $vysledek = mysql_query("SELECT * FROM `" . _mysql_prefix . "-download` WHERE id=".$id.""); // Výběr z mysql

  while ($zaznam = mysql_fetch_array($vysledek))

    {
        $size=filesize('upload/download/'.$zaznam['cesta']);
        $size=number_format($size/1024,0,',',' ');
        $output.="<a href='download.php?dl=".$zaznam['id']."'>".$zaznam['cesta']."</a> (".$zaznam['stazeno']."x) [".$size." kB]";

    };

}

    

?>