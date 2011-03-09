<?php

/*

  Autor: Tomáš Smetka (www.smetka.net)

  Spoluautor: Jan Valentík (www.janvalentik.cz)

*/

if (isset($_SESSION['form_message'])) {$output.=_formMessage(1,$_SESSION['form_message']);unset($_SESSION['form_message']);}

    // velikost

    $velikost_params=($_params[2]);



    //slozka kam se maji nahravat fotky (chmod 777)

    $slozka=($_params[1]);



    //povolene prilohy

    $allowed=array("png", "jpeg", "jpg", "gif");





$output.='<form name="newad" method="post" enctype="multipart/form-data" action="">

'.$_lang['xxl.imgupload.1'].' <input type="file" name="image" /><input name="Submit" type="submit" value="'.$_lang['xxl.imgupload.2'].'" /></form>';

define ("MAX_SIZE","".$velikost_params.""); //Velikost souboru vkB

if(isset($_FILES['image'])){

  $tmp_image=$_FILES['image']['tmp_name'];

  $tmp_image_info=pathinfo($_FILES['image']['name']);

  $jmeno_souboru=$tmp_image_info['basename'];

  $velikost=filesize($tmp_image);

  $nove_jmeno=$slozka.'/'.StrFTime("%d-%m-%Y-%H-%M-%S", Time()).".".$tmp_image_info['extension'];

  if (filesize($tmp_image) > MAX_SIZE*1024){

    $error_log[]="".$_lang['xxl.imgupload.3']."'.$velikost_params.' kB.";

  }

  if(!isset($tmp_image_info['extension'])){$tmp_image_info['extension']="";}

    if(in_array(mb_strtolower($tmp_image_info['extension']), $allowed)){

      list($width, $height) = image_shrink_size($tmp_image, 800, 800);

      image_resize($tmp_image, $nove_jmeno, $width, $height);

    }

    else{

      $error_log[]="".$_lang['xxl.imgupload.4']."";

    }



  if(isset($_POST['Submit']) && count($error_log)==0){

    $output='

    <div class="message1">'.$_lang['xxl.imgupload.5'].' <strong>'.$jmeno_souboru.'</strong>'.$_lang['xxl.imgupload.6'].'</div>

    <ul>

    <li>'.$_lang['xxl.imgupload.7'].' '.$velikost.' kB</li>

    <li>'.$_lang['xxl.imgupload.8'].' <a href="'.$nove_jmeno.'">'.$nove_jmeno.'</a></li>

    </ul>



    <h2 align="center"><a href="'._url.'/'._indexOutput_url.'">'.$_lang['xxl.imgupload.9'].'</a></h2>

    <table>

    <tr>

    <td>'.$_lang['xxl.imgupload.10'].'</td>

    <td><strong>&lt;img src=&quot;'._url.'/'.$nove_jmeno.'&quot; alt=&quot;Velikost - '.$velikost.' kB /&quot;&gt;</strong></tr><tr>

    </tr>

    <tr>

    <td>'.$_lang['xxl.imgupload.14'].'</td>

    <td><strong>&lt;img src=&quot;'._url.'/'.$nove_jmeno.'&quot; alt=&quot;Velikost - '.$velikost.' kB/ &quot;&gt;</strong></tr><tr>

    </tr>

    <tr>

    <td>'.$_lang['xxl.imgupload.11'].'</td>

    <td><strong>[hcm]ximg,'.$nove_jmeno.'[/hcm]</strong></tr><tr>

    </tr>

    </table>



     <h3>'.$_lang['xxl.imgupload.12'].'</h3>

     <div align="center"><small>'.$_lang['xxl.imgupload.13'].'</small><br />

     <a href="'.$nove_jmeno.'"" target="_blank">

     <img src="'.$nove_jmeno.'"  style="max-width:500px;" /></a>

     </div>';

  }

  else{

    $output.=_formMessage(2, _eventList($error_log, 'errors'));

  }

}



?>