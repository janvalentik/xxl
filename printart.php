<?php

/*---- inicializace jadra ----*/

define('_indexroot', './');
require(_indexroot."core.php");
if(!_printart){exit;}


/*---- vystup ----*/

if(_publicAccess(!_notpublicsite) and isset($_GET['id'])){

$id=intval($_GET['id']);

  //nacteni dat clanku
  $query=mysql_query("SELECT * FROM `"._mysql_prefix."-articles` WHERE id=".$id);
  if(mysql_num_rows($query)!=0){

    //rozebrani dat, test pristupu
    $query=mysql_fetch_array($query);
    $access=_articleAccess($query);
    $artlink=_linkArticle($id);
    $url=_url."/".$artlink;
    define('_indexOutput_url', $artlink);

    //vypsani obsahu
    if($access==1){

//vlozeni zacatku hlavicky
require(_indexroot."require/headstart.php");

?>
<link href="<?php echo _indexroot; ?>templates/<?php echo _template; ?>/style/print.css" type="text/css" rel="stylesheet" />
<link href="<?php echo _indexroot; ?>templates/<?php echo _template; ?>/style/system.css" type="text/css" rel="stylesheet" />
<title><?php echo $query['title']." "._titleseparator." "._title; ?></title>
</head>

<body onload="setTimeout('this.print();', 500);">

<p id="informations"><?php echo "<strong>".$_lang['global.source'].":</strong> <a href='".$url."'>".$url."</a>"._template_listinfoseparator."<strong>".$_lang['article.posted'].":</strong> "._formatTime($query['time'])._template_listinfoseparator."<strong>".$_lang['article.author'].":</strong> "._linkUser($query['author'], null, true, true); ?></p>

<h1><?php echo $query['title']; ?></h1>
<?php
// volba pripony
$path = _indexroot.'upload/img/perex/'.$id.'.';
if(file_exists($path.'png'))
{$path .= 'png';}
elseif(file_exists($path.'jpg'))
{$path .= 'jpg';}
elseif(file_exists($path.'gif'))
{$path .= 'gif';}
else {$path = null;}
?>
<p><?php
echo (!is_null($path)?'<img src="'.$path.'" class="article-image" title="'.$title.'" alt="'.$title.'" />':'');
echo $query['perex']; ?></p>
<?php echo _parseHCM($query['content']); ?>

</body>
</html><?php
    }

  }

}

?>