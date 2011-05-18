<?php

//priprava
header("Content-Type: text/css; charset=utf-8");
header("Expires: ".gmdate("D, d M Y H:i:s", time()+86400)." GMT");
if(isset($_GET['s'])){$s=intval($_GET['s']);}
else{$s=0;}

//funkce pro barevny model HSB
function _hsb2rgb($hue /*0-359*/, $saturation /*0-100*/, $brightness /*0-100*/, $return_array=false){

  //normalizace vstupnich hodnot
  if($saturation<0){$saturation=0;}
  if($saturation>100){$saturation=100;}
  if($hue<0){$hue=0;}
  if($hue>359){$hue=359;}
  if($brightness<0){$brightness=0;}
  //if($brightness>100){$brightness=100;}

  //zjisteni cisla barvy, pozice a koeficientu sytosti
  $color=$hue/60;
  $color_number=floor($color);
  $color_offset=$color-floor($color);
  $saturation_coef=100-$saturation;
  $saturation_coef2=(100-$saturation)/100;
  $saturation_coef3=255*$saturation_coef2;

  //vypocet rgb slozek podle tonu a sytosti
  switch($color_number){

    case 0:
    /*static*/ $r=255;
    /*color*/  $g=$color_offset*255; $g+=(255-$g)*$saturation_coef2;
    /*static2*/$b=$saturation_coef3;
    break;

    case 1:
    /*color*/  $r=255-($color_offset*255); $r+=(255-$r)*$saturation_coef2;
    /*static*/ $g=255;
    /*static2*/$b=$saturation_coef3;
    break;

    case 2:
    /*static2*/$r=$saturation_coef3;
    /*static*/ $g=255;
    /*color*/  $b=$color_offset*255; $b+=(255-$b)*$saturation_coef2;
    break;

    case 3:
    /*static2*/$r=$saturation_coef3;
    /*color*/  $g=255-($color_offset*255); $g+=(255-$g)*$saturation_coef2;
    /*static*/ $b=255;
    break;

    case 4:
    /*color*/  $r=$color_offset*255; $r+=(255-$r)*$saturation_coef2;
    /*static2*/$g=$saturation_coef3;
    /*static*/ $b=255;
    break;

    case 5:
    /*static*/ $r=255;
    /*static2*/$g=$saturation_coef3;
    /*color*/  $b=255-($color_offset*255); $b+=(255-$b)*$saturation_coef2;
    break; 

  }

  //zpracovani hodnoty barvy, aplikace svetlosti
  foreach(array("r", "g", "b") as $color){
    $$color=round($$color*($brightness/100));
    if($$color>255){$$color=255;} if($$color<0){$$color=0;}
    if(!$return_array){$$color=str_pad(dechex($$color), 2, "0", STR_PAD_LEFT);}
  }

  //vystup
  if(!$return_array){return $r.$g.$b;}
  else{return array($r, $g, $b);}

}

//nastaveni barev - zakladni konfigurace
$text="000000";
$link=null;
$scheme_brightness=100;
$scheme_contrast="ffffff";
$scheme_contrast2=$text;
$smoke_text="808080";
$smoke_line="dcdcdc";
$smoke_bg="DD6538";
//podle schemat
switch($s){

  //modry
  case 1:
  $scheme=212;
  break;
  
  //zeleny
  case 2:
  $scheme=111;
  $scheme_brightness=60;
  break;
  
  //cerveny
  case 3:
  $scheme=8;
  break;
  
  //zluty
  case 4:
  $scheme=59;
  $scheme_contrast="000000";
  $link="8e8b00";
  break;
  
  //purpurovy
  case 5:
  $scheme=322;
  break;
  
  //azurovy
  case 6:
  $scheme=186;
  $scheme_contrast="000000";
  $link="0099aa";
  break;
  
  //fialovy
  case 7:
  $scheme=284;
  break;
  
  //hnedy
  case 8:
  $scheme=28;
  $scheme_brightness=50;
  break;
  
  //tmave modry
  case 9:
  $scheme=240;
  $scheme_brightness=60;
  break;
  
  

  //oranzovy
  default:
  $scheme=20;
  break;

}

//automaticke dopocitani barev
$scheme_dark=_hsb2rgb($scheme, 100, $scheme_brightness-10);
$scheme_lighter=_hsb2rgb($scheme, 40, $scheme_brightness+15);
$smoke=_hsb2rgb($scheme, 8, 100);
$scheme=_hsb2rgb($scheme, 100, $scheme_brightness);
if($link==null){$link=$scheme;}

?>
/* tagy */
* {margin: 0; padding: 0;}
body {font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #<?php echo $text; ?>; background-color: #ffffff; margin: 0;}
a {font-size: 12px; color: #<?php echo $link; ?>; text-decoration: none;}
a:hover {color: #<?php echo $text; ?>; text-decoration: none;}
h1 {font-size: 18px;}
h2 {font-size: 14px;}
h3 {font-size: 12px;}
p {padding: 0; margin: 2px 0 10px 0; line-height: 160%;}
ul, ol {padding: 2px 0 12px 40px;}


form {margin: 0 0 8px 0;}
fieldset {margin-bottom: 10px; padding: 8px; background-color: #<?php echo $smoke; ?>;}
fieldset fieldset {background-color: #ffffff;}
legend {font-weight: bold; color: #<?php echo $text; ?>;}
input, textarea {padding: 1px 0;}
label input {margin: 3px 4px 3px 1px; padding: 0;}
optgroup option {padding-left: 16px;}

img {border: 0;}
small {color: #<?php echo $smoke_text; ?>;}
td {font-size: 12px; padding: 0px;vertical-align:top;}

/* hlavicka */
#header {font-family: Georgia, "Times New Roman", Times, serif; font-size: 24px; color: #<?php echo $scheme_contrast; ?>; background-color: #<?php echo $scheme; ?>; padding: 10px; border-bottom: 3px solid #<?php echo $scheme_dark; ?>;}
#header span {float: right; position: relative; top: 6px;}
#header span, #header span * {font-size: 14px; font-weight: bold; text-decoration: none; color: #<?php echo $scheme_contrast; ?>;}

/* menu */
#menu {background: #<?php echo $scheme_lighter; ?>; color: #<?php echo $scheme_contrast; ?>; border-bottom: 1px solid #<?php echo $smoke_line; ?>;}
#menu-padding {padding: 6px 0 6px 6px;}
#menu a {color: #<?php echo $text; ?>; font-weight: bold; text-decoration: none; padding: 0px 4px;}
#menu a:hover, #menu a.act {text-decoration: underline;}

/* obsah */
#content {padding: 12px 16px 16px 16px; border-bottom: 1px solid #<?php echo $smoke_line; ?>; min-width: 800px;}

/* copyright */
#copyright {text-align: right; padding: 5px 8px; background-color: #<?php echo $scheme; ?>; border-bottom: 1px solid #<?php echo $smoke_line; ?>;}
#copyright, #copyright * {color: #<?php echo $scheme_contrast; ?>; font-size: 10px; text-decoration: none; font-weight: bold;}
#copyright a:hover {text-decoration: underline;}
#copyright div {float: left;}

/* ruzne */
#slhook {width: 55px; height: 13px; padding: 0; margin: -3px 0 0 0;}
#external-container {padding: 10px;}
#external-container h1 {border-bottom: 3px solid #<?php echo $scheme; ?>; padding-bottom: 3px; margin-bottom: 6px;}

  /* uvodni strana */
  #indextable {width: 100%; margin: 0; padding: 0; border-collapse: collapse;}
  #indextable td {padding: 10px; border: 1px solid #<?php echo $smoke_line; ?>; background-color: #<?php echo $smoke; ?>;}
  #indextable h2 {margin-bottom: 6px; border-bottom: 1px solid #<?php echo $smoke_line; ?>; padding-bottom: 6px;}
  #indextable li {padding: 3px;}
  #news-box h2 {font-size: 18px; border-bottom: 1px solid #<?php echo $smoke_line; ?>; margin-bottom: 12px; padding-bottom: 6px;}
  #news h3 {font-size: 16px; color: #<?php echo $link; ?>;}
  #news p {margin-bottom: 3px;}
  #news div {margin-bottom: 15px; color: #<?php echo $smoke_text; ?>;}

  /* sprava obsahu */
  #contenttable {width: 100%; border: 1px solid #<?php echo $smoke_line; ?>; line-height: 140%;}
  #contenttable a {text-decoration: none;}
  #contenttable h2 {margin: 0 0 8px 0; padding: 4px 0 7px 0; border-bottom: 1px solid #<?php echo $smoke_line; ?>;}
  #contenttable div.pad {padding: 20px 0;}
  .contenttable-box {padding: 8px; margin: 0; border-right: 1px solid #<?php echo $smoke_line; ?>;}
  .contenttable-icon {margin-right: 5px; position: relative; top: 3px;}
  #contenttable-list {width: 100%; margin-left: 4px;}
  #contenttable-list tr {vertical-align: top;}
  #contenttable-list td {padding: 0;}
  #contenttable-list .name {width: 60%;}
  #contenttable-list .name a {font-weight: bold;}
  #contenttable-list .name a:hover {color: #<?php echo $link; ?>;}
  #contenttable-list .name input {width: 30px; margin-right: 10px; position: relative; top: 2px; left: 2px;}
  #contenttable-list .type {width: 15%;}
  #contenttable-list .actions {width: 25%; white-space: nowrap;}
  #contenttable-list .actions .tpad, #contenttable-list .type .tpad {padding: 4px;}
  #contenttable-list .intersecpad .name, #contenttable-list .intersecpad-hl .name {padding-left: 30px;}
  #contenttable-list .sep {padding-top: 32px;}
  #contenttable-list .sep .sepbg {background-color: #<?php echo $scheme_lighter; ?>; height: 26px;}
  #contenttable-list .sep a {color: #<?php echo $text; ?>;}

  /* uprava clanku */
  #ae-table {width: 99.1%; border-collapse: collapse;}
  #ae-table, #ae-table td {margin: 0; padding: 0;}
  #content-cell {width: 75%;}
  #content-cell textarea {width: 99%; height: 416px;}
  #is-cell {width: 25%;}
  #is-cell div {padding-left: 5px;}
  #is-cell textarea {width: 99%; height: 179px;}
  #is-cell p {float: left; width: 223px; margin-bottom: -32px; z-index: 2; position: relative;}
  #is-cell label {display: block;}
  #is-cell label input {margin: 0;}
  #time-cell {z-index: 1; position: relative;}
  .ae-artselect {width: 249px;}
  .ae-artselect-disoption {color: #<?php echo $smoke_text; ?>;}

  /* uprava boxu */
  #boxesedit {width: 100%;}
  #boxesedit td.cell {padding: 10px 20px 25px 10px;}
  #boxesedit td.cell div {border: 1px solid #<?php echo $smoke_line; ?>; padding: 20px 15px;}
  
  /* souborovy manazer */
  #fman-action {border-bottom: 1px solid #<?php echo $smoke_line; ?>; margin-bottom: 10px;}
  #fman-action h2 {margin-bottom: 6px;}
  #fman-list {width: 100%; margin-bottom: 6px;}
  #fman-list a {color: #<?php echo $text; ?>;}
  #fman-list a:hover {color: #<?php echo $link; ?>;}
  #fman-list .actions, #fman-list .actions a {font-size: 10px;}
  #fman-list td {padding: 2px 4px;}
  #fman-list input {margin-right: 2px;}
  .fman-menu {border-width: 1px 0 1px 0; border-style: solid; border-color: #<?php echo $smoke_line; ?>;}
  .fman-menu, .fman-menu2 {margin-top: 5px; padding: 5px;}
  .fman-menu a, .fman-menu span, .fman-menu2 a, .fman-menu2 span {border-right: 1px solid #<?php echo $smoke_line; ?>; padding-right: 8px; margin-right: 8px;}
  
  /* galerie */
  .gallery-savebutton {float: left; margin: 4px 14px 0 0; display: block;}
  #gallery-browser {background-color: #FFFFFF; border: 1px solid #<?php echo $smoke_text; ?>; height: 150px; width: 460px; padding: 10px; margin-left: 10px; overflow: auto;}
  #gallery-browser #gallery-browser-actdir {padding-bottom: 3px; margin-bottom: 2px; border-bottom: 1px solid #<?php echo $smoke_line; ?>;}
  #gallery-browser #fman-list {width: 443px;}
  #gallery-browser a {color: #<?php echo $text; ?>;}
  #gallery-browser td.noimage a {color: #<?php echo $smoke_text; ?>;}
  #gallery-browser-dialog {width: 350px; height: 48px; background-color: #<?php echo $smoke; ?>; border: 2px solid #<?php echo $scheme; ?>; position: absolute;}
  #gallery-browser-dialog div {width: 338px; height: 33px; border: 2px solid #<?php echo $smoke; ?>; padding: 11px 0 0 8px; font-size: 16px;}
  #gallery-browser-dialog div span {font-weight: bold; padding-right: 4px;}
  #gallery-browser-dialog div a {font-size: 16px; padding: 2px 5px; margin: 0 4px; border: 2px outset #<?php echo $link; ?>; background-color: #<?php echo $scheme_lighter; ?>; color: #<?php echo $text; ?>;}
  #gallery-browser-dialog div a:active {border-style: inset;}
  #gallery-edittable {border-collapse: collapse; margin: 14px 0; background-color: #<?php echo $scheme_lighter; ?>;}
  .gallery-edittable-td {border: 1px solid #<?php echo $scheme_contrast; ?>; vertical-align: top;}
  .gallery-edittable-td {padding: 20px;}
  #gallery-edittable a[rel=lightbox\[1\]] img {border: 1px solid #<?php echo $smoke_text; ?>;}
  #gallery-insertform-cell {}

  #news{border-collapse:collapse;}
  #news td{border:none; border-bottom: 1px solid #<?php echo $smoke_line; ?>;}
  
/* tridy */
a.normal {color: #<?php echo $text; ?>;}
a.invisible {color: #<?php echo $smoke_text; ?>;}
a.notpublic {font-style: italic; color: #<?php echo $text; ?>;}
a.invisible-notpublic {color: #<?php echo $smoke_text; ?>; font-style: italic;}
.intersecpad-hl, .hl {background-color: #<?php echo $smoke; ?>;}

.message1, .message2, .message3 {margin: 5px 0 20px 0; padding: 13px 5px 13px 48px; border: 1px solid #<?php echo $smoke_line; ?>; font-weight: bold; background-color: #<?php echo $smoke; ?>; background-position: 5px 5px; background-repeat: no-repeat;}
.message1 ul, .message2 ul, .message3 ul {margin: 0; padding: 5px 0 0 15px;}
.message1 {background-image: url("../images/icons/info.png");}
.message2 {background-image: url("../images/icons/warning.png");}
.message3 {background-image: url("../images/icons/error.png");}

.cform table {width: 100%;}
.arealine {width: 99%; height: 50px;}
.areasmall {width: 290px; height: 150px;}
.areasmall_100pwidth {width: 100%; height: 200px;}
.areasmallwide {width: 620px; height: 150px;}
.areamedium {width: 600px; height: 350px;}
.areabig {width: 99%; height: 400px;}
.areabigperex {width: 99%; height: 150px;}
.inputbig {width: 750px;}
.inputsmall {width: 145px;}
.inputmedium {width: 290px;}
.inputmini {width: 32px;}
.inputmicro {width: 18px;}
.selectmedium {width: 294px;}
.selectbig {width: 753px;}

.hr {height: 10px; background-image: url("../images/hr.gif"); background-position: left center; background-repeat: repeat-x;}
.hr hr {display: none;}

.paging {padding: 6px 0 3px 1px;}
.paging span a {padding: 0 2px;}
.paging a.act {text-decoration: underline;}

.widetable {width: 100%; border: 1px solid #<?php echo $smoke_line; ?>;}
.widetable td {padding: 5px 15px;}
.widetable td.lpad {padding: 5px 15px 5px 32px;}
.widetable td.rbor {border-right: 1px solid #<?php echo $smoke; ?>;}
.widetable h2 {margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px solid #<?php echo $smoke_line; ?>;}
.list td {padding: 5px 32px 5px 0;}

.ex-list li {list-style-image: url("../images/icons/action.gif"); padding: 5px 10px;}
.ex-list a {font-weight: bold; font-size: 13px;}
.users {padding-left:0px;}
.users li {list-style:none;}

.bborder {padding-bottom: 8px; margin-bottom: 12px; border-bottom: 1px solid #<?php echo $smoke_line; ?>;}
fieldset .bborder {border-color: #<?php echo $smoke_text; ?>;}
.customsettings {border-left: 1px solid #<?php echo $smoke_line; ?>; padding-left: 8px;}
.customsettings strong, .customsettings span {border-left: 1px solid #ffffff;}
.backlink {display: block; font-weight: bold; padding-bottom: 10px;}
.icon {margin: -1px 5px 0 0; vertical-align: middle;}
.home-icon {float:left; margin: -1px 5px 0 0; vertical-align: middle;}
.net {list-style-image: url("../images/icons/action.gif"); margin:0px;}
.net li {padding:0px; margin:0px;}
.groupicon {vertical-align: middle; margin-top: -1px;}
.rpad {padding-right: 10px;}
.lpad {padding-left: 10px;}
.inline {display: inline;}
.hidden {display: none;}
.cleaner {clear: both;}
.micon {height: 15px; margin: 0 1px;}
.intersecpad {padding-left: 20px;}
.litem {font-weight: bold;}
.special {color: #<?php echo $link; ?>;}
.small {font-size: 10px;}
.block {display: block;}
.note {color: #<?php echo $smoke_text; ?>;}
.aktivni td {text-decoration: underline;}
.noe-area {width: 100%; height: 300px;}

