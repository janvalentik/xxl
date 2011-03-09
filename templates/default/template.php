<?php if(!defined('_core')){exit;}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php _templateHead(); ?>
</head>

<body id="<?php echo _pagetype;?>">

<!-- outer -->
<div id="outer">

  <!-- page -->
  <div id="page">

    <!-- head -->
    <div id="head">
    <a href="./" title="<?php echo _title; ?> - <?php echo _description; ?>"><span><?php echo _title; ?></span></a>
    </div>
    
    <!-- menu -->
    <div id="menu">
    <?php echo _templateMenu(0, 10); ?>
    </div>
    <hr class="hidden" />

    <!-- column -->
    <div id="column">
    <div id="column-pad">
<?php _templateBoxes(1); ?>
    </div>
    </div>

    <!-- content -->
    <div id="content">
    <div id="content-pad">
<?php _templateContent(); ?>
    </div>
    </div>

    <div class="cleaner"></div>
    

  </div>



</div>

<!-- footer -->
<hr class="hidden" />
<div id="footer">
<?php _templateLinks(); ?>
<?php echo _footer; ?>
</div>
</body>
</html>