<?php

/*--- inicializace jadra ---*/
define('_indexroot', '../');
require(_indexroot."core.php");
if(!_template_smileys_list){exit;}
require(_indexroot."require/headstart.php");

?>
<link href="<?php echo _indexroot; ?>templates/<?php echo _template; ?>/style/system.css" type="text/css" rel="stylesheet" />
<title><?php echo $_lang['misc.smileys.list']; ?></title>
</head>

<body>

<h1><?php echo $_lang['misc.smileys.list']; ?></h1>
<div class="hr"><hr /></div><br />

<table border="1">
<tr><td><strong><?php echo $_lang['misc.smileys.smiley']; ?></strong></td><td><strong><?php echo $_lang['misc.smileys.tag']; ?></strong></td></tr>
<?php

//vyhledani smajlu
$handle=@opendir(_indexroot."templates/"._template."/images/smileys/");
$smileys=array();
while($item=@readdir($handle)){
  $iteminfo=pathinfo($item);
  if(isset($iteminfo['extension']) and $iteminfo['extension']=="gif"){
    $fname=mb_substr($item, 0, mb_strrpos($item, "."));
    if(strval(intval($fname))==$fname and intval($fname)<1000){
      $smileys[$fname]=$item;
    }
  }
}
@closedir($handle);

natsort($smileys);

//vypis smajlu
foreach($smileys as $id=>$smiley){
echo "<tr><td><img src='"._indexroot."templates/"._template."/images/smileys/".$smiley."' alt='".$id."' /></td><td>*".$id."*</td></tr>\n";
}

?>
</table>

</body>
</html>