<?php
    $ret = "";
   $traza = "";

   @mkdir($dir_dest,0755);
   if(!$fp = @fopen($dir_dest."/index.htm","w")) {
           $traza .= "<li>No se puede generar fichero embeded: $dir_dest/index.htm. Contacte con el administrador\n";
   } else {
            fwrite($fp,$html);
            @fclose($fp);
   }
   $ret = $dir_dest."/index.htm";
   if($debug) { echo $traza; $ret = ""; }
?>