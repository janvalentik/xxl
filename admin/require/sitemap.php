<?php



/*--- kontrola jadra ---*/

if(!defined('_core')){exit;}





function _formatLastMod($timestamp){

  return date("Y-m-d\TH:i:sP", $timestamp);

}



//existující soubor smazat


  //nastavení priorit

  $prioKatSek="0.8";

  $prioArt="0.5";



  //nastavení chnagefreq

  $freqKatSek="weekly";

  $freqArt="monthly";



  //příprava souboru

  $file = FOpen(_indexroot."sitemap.xml", "w");



  //hlavička a doména



// <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

  $data='<?xml version="1.0" encoding="UTF-8"?'.'>

<?xml-stylesheet type="text/xsl" href="'._url.'/gss.xsl" ?'.'>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

  <url>

    <loc>'._url.'</loc>

    <lastmod>'._formatLastMod(time()).'</lastmod>

    <priority>1.0</priority>

    <changefreq>always</changefreq>

  </url>';

  FPutS($file, $data);





  //vypis kategorií a sekcí

  $cats=mysql_query("SELECT id,type,title,var1,var2 FROM `"._mysql_prefix."-root` WHERE visible=1 AND public=1 AND type!=4");

  while($item=mysql_fetch_array($cats)){

     $data='

  <url>

	  <loc>'._url.'/'._linkRoot($item['id']).'</loc>

	  <lastmod>'._formatLastMod(time()).'</lastmod>

	  <priority>'.$prioKatSek.'</priority>

    <changefreq>'.$freqKatSek.'</changefreq>

  </url>';

  FPutS($file, $data);

  }





  //vypis članků

  $arts=mysql_query("SELECT id,title,time FROM `"._mysql_prefix."-articles` WHERE visible=1 AND public=1 ORDER BY time DESC");

  while($art=mysql_fetch_array($arts)){

     $data='

  <url>

	  <loc>'._url.'/'._linkArticle($art['id']).'</loc>

    <lastmod>'._formatLastMod($art['time']).'</lastmod>

	  <priority>'.$prioArt.'</priority>

    <changefreq>'.$freqArt.'</changefreq>

  </url>';

  FPutS($file, $data);

  }



  //konec souboru

  $data='

</urlset>';

  FPutS($file, $data);

  FClose($file);



  //Sestavení zprávy

  $sizefile=round(@filesize(_indexroot."sitemap.xml")/1024);

  $output.="

  <div class='hr'></div>

  <p>".$_lang['xxl.admin.generator_ok_2']."</p>



<fieldset>

<legend>".$_lang['xxl.admin.generator_result']."</legend>

  <table><tr><td valign='top'><img src='/admin/images/icons/accept.png' alt='icon'/></td><td valign='top'><ul>

  <li>".$_lang['xxl.admin.generator_ok']."</li>

  <li>".$_lang['xxl.admin.generator_ok_1']."".$sizefile." (kB).</li>";



  /*

  $url="http://www.google.com/webmasters/sitemaps/ping?sitemap="._url."/sitemap.xml";

  $fp = @fopen($url, "r");

  if (!$fp){

    $mresult.="<li>Chyba spojení. Ping <em>".$url."</em> se nepodařil.</li>";

  }

  else{

    fwrite($fp, $out);

      while (!feof($fp)) {

          $result=fgets($fp, 512);

          $mresult="<li>Odeslání pingu se podařilo. Oznámení: <em>".substr($result,0,74)."</em></li>";

      }

      fclose($fp);

  }

*/

  $output.=$mresult."\n</ul></td></tr></table></fieldset>";





?>

