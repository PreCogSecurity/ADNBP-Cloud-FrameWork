<?php
       $ret = "";
       $this->traza = "";
    
       $url_rel = $url_base;
       $url_abs = $url_base;
    
       // Descompongo el $url_base
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
       while (list($key,) = each($this->image_types)) { $extensions[] = $key; }
       preg_match_all('/(?:"|\')([^"\']+\.('.implode('|', $extensions).'))(?:"|\')/Ui', $html, $images);
    
    
       // Convert into an associative array to delete files duplicated and assign a new id
       for ($i=0; $i<count($images[1]); $i++) $files[$images[1][$i]] = "GeaClassesPrefijo".basename($images[1][$i]);
    
       if(is_array($files))
       while (list($key,$value) = each($files)) { 
             $html = str_replace($key, basename($value), $html);
       }
    
       // Let's write to local files
       if(!is_dir($dir_dest))
           @mkdir($dir_dest,0755);
    
       if(!$fp = @fopen($dir_dest."/index.htm","w")) {
               $this->traza .= "<li>No se puede generar fichero embeded: $dir_dest/index.htm. Contacte con el administrador\n";
       } else {
                fwrite($fp,$html);
                @fclose($fp);
                if(is_array($files))
                reset($files);
    
                if(is_array($files))
                while (list($key,$value) = each($files)) { 
                     if(!$fp = @fopen($dir_dest."/$value","w")) {
                        $this->traza .= "<li>$dir_dest/$value can no be generated.\n";
                     } else {
                         if(ereg("^http",$key)) {
                             $url_base = dirname($key)."/";
                             $key = basename($key);
                         } else {
                             if(ereg("^/",$key)) $url_base = $url_abs;
                             else $url_base = $url_rel;
                             // $key = ereg_replace($url_base,"",$key);
                             // $key = ereg_replace("//","",$key);
                         }
    
                         if(!$f = @fopen( $url_base.$key, "rb" )) {
                            $this->traza .= "<li>".$url_base.$key." can not be access\n";
                            $location = "";
                         } else {
                            // $stream = fread( $f, filesize( $url_base.$key ) );
                            // Como no funciona para ficheros remotos utilizamos 8000000 como máximo
                            while( $stream = fread( $f, 800000 )) {
                                fwrite($fp,$stream);
                            }
                            fclose($f); 
                            $this->traza .= "<li>".$url_base.$key." written in ".$dir_dest."/$value"."\n";
                         }
                         @fclose($fp);
                     }
                }
       }
       if($debug) { echo $this->traza; }
       $ret = $dir_dest."/index.htm";
?>