<?php
  if(!function_exists('num_files')){function num_files($dir, $recursive=false, $counter=0) {
    static $counter;
    if(is_dir($dir)) {
      if($dh = opendir($dir)) {
        while(($file = readdir($dh)) !== false) {
          if($file != "." && $file != "..") {
              $counter = (is_dir($dir."/".$file) and $recursive==true) ? num_files($dir."/".$file, true, $counter) : $counter+1;
          }
        }
        closedir($dh);
      }
    }
    return $counter;
  }}

  $output.=($_params[2]==0 ? num_files($_params[1]) : num_files($_params[1],true));
?>