<?php
   $ret = "";
   $traza = "";

   @mkdir($dir_dest,0755);
   if(!$fp = @fopen($dir_dest."/index.txt","w")) {
           $traza .= "<li>No se puede generar fichero embeded: $dir_dest/index.txt. Contacte con el administrador\n";
   } else {
            fwrite($fp,$txt);
            @fclose($fp);
   }
   $ret = $dir_dest."/index.txt";
   if($debug) { echo $traza; $ret = ""; }
?>