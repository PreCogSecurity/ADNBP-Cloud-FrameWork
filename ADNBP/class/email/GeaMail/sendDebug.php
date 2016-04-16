<?php
    if (!defined('CRLF')) {
            $this->setCrlf($mode == 'mail' ? "\n" : "\r\n");
    }

    if (!$this->is_built) {
            $this->buildMessage();
    }

    echo "<h1>Mail GeaClasses 5.3</h1><h2>Debug mode</h2>";
    echo "<ul>";
    echo "<li>From:<b>".htmlentities($this->headers['From'])."</b>";
    echo "<li>ReturnParh:<b>".htmlentities($this->return_path)."</b>";
    echo "<li>Subject:<b>".htmlentities($this->headers['Subject'])."</b>";
    echo "</ul>";
    

    echo "<h2>Contenido HTML/TXT</h2>";
    echo "<form><textarea width='100%' cols=60 rows=10>".htmlentities($this->output)."</textarea><br />Size:".strlen($this->output)." bytes</form>";

    echo "<h2>Elementos 'embeded'</h2>";
    echo "<ol>";
    for($i=0;$i<count($this->html_images);$i++) echo "<li>".$this->html_images[$i]['name']. " - ".$this->html_images[$i]['c_type']." - ".$this->html_images[$i]['cid'];
    echo "</ol>";
 
    echo "<h2>Envio de usuarios</h2>";
 if(is_array($to)) {
   while (list ($key, $val) = each ($to)) {
    if($sent[$key]) 
       echo "<li><font color='red'>".htmlentities("Previamente enviado: $val <$key>")."</font>";
    else
       echo "<li>".htmlentities("Se quiere enviar a: $val <$key>");
   }
 } else {
   echo "<li>No hay especificado ningún usuario";
 }
    echo "</ol>";

?>