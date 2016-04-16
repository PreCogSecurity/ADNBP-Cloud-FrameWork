<?php
   if(ereg("^http:",$url_base)) {
       $url_abs = ereg_replace("http://","",$url_base);
       $url_abs = "http://".ereg_replace("/.*","",$url_abs);
       $url_rel = dirname($url_base)."/";
   } else if(ereg("^https",$url_base)) {
       $url_abs = ereg_replace("https://","",$url_base);
       $url_abs = "https://".ereg_replace("/.*","",$url_abs);
       $url_rel = dirname($url_base)."/";
   }

   // Build the list of image extensions
   $extensions = array();
   while (list($key,) = each($this->image_types)) { $extensions[] = $key; }
   preg_match_all('/("|\')([^"\']+\.('.implode('|', $extensions).'))("|\')/Ui', $html, $images);


   // Convert into an associative array to delete files duplicated and assign a new id
   for ($i=0; $i<count($images[2]); $i++) if(!ereg("^http",$images[2][$i])) {
      if(ereg("^/",$images[2][$i])) {
          $files[$images[0][$i]] = $images[1][$i].$url_abs.$images[2][$i].$images[4][$i];
      } else {
          $files[$images[0][$i]] = $images[1][$i].$url_rel.$images[2][$i].$images[4][$i];
      }
   }

   if(is_array($files))
   while (list($key,$value) = each($files)) { 
     $html = str_replace($key, $value, $html);
   }
?>