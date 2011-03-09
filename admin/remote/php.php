<?php

/*--- inicializace jadra ---*/
define('_indexroot', '../../');
require(_indexroot."core.php");

/*--- vystup ---*/
if(_loginright_level!=10001){exit;}
require(_indexroot."require/headstart.php");

?>
<link href="style.php?s=<?php echo _adminscheme; ?>" type="text/css" rel="stylesheet" />
<title><?php echo $_lang['admin.other.php.title']; ?></title>
</head>

<body>
<div id="external-container">


<?php

//nacteni postdat
$process=false;
if(isset($_POST['code'])){
$code=_stripSlashes($_POST['code']);
$process=true;
}

?>

<h1><?php echo $_lang['admin.other.php.title']; ?></h1>

<form action="php.php" method="post">
<textarea name="code" rows="25" cols="94" class="areabig"><?php if($process){echo _htmlStr($code);} ?></textarea><br />
<input type="submit" value="<?php echo $_lang['global.do']; ?>" />
</form>

<?php
if($process){
  echo '<h2>'.$_lang['global.result'].'</h2><br />';
  eval($code);
}
?>

</div>
</body>
</html>