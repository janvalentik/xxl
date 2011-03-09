<?php

/*--- inicializace jadra ---*/
define('_indexroot', '../');
define('_tmp_customheader', 'Content-type: application/javascript; charset=utf-8');
session_cache_limiter('public');
require(_indexroot."core.php");

?>
			$(document).ready(function(){
				//Examples of how to assign the ColorBox event to elements
				$("a[rel*=\'lightbox\']").colorbox({
          slideshowStart:"<?php echo $_lang['xxl.colorbox.slideshowstart']; ?>",
          slideshowStop:"<?php echo $_lang['xxl.colorbox.slideshowstop']; ?>",
          current:"'.$_lang['xxl.colorbox.current']; ?>",
          previous:"<?php echo $_lang['xxl.colorbox.previous']; ?>",
          next:"<?php echo $_lang['xxl.colorbox.next']; ?>",
          close:"<?php echo $_lang['xxl.colorbox.close']; ?>",
          scalePhotos:false,
          scrolling:false;
          });
			});