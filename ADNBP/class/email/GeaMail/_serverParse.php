<?php
      $ret = true;
    while (substr($server_response, 3, 1) != ' ') {
      if (!($server_response = fgets($socket, 256) ) ) {
          $this->_error = "SMTP receive";
          $this->traza .= $this->_error;
          fclose ($socket);
          $ret = false;
      }

    } 

    if ($ret) {
        $this->traza .= "SMTP Received $server_response\n";

        $ret = (substr($server_response, 0, 3) == $response);
    }
    if (!$ret) {
        $this->_error = "SMTP receive: expected $response, got $server_response";
        $this->traza .= $this->_error;
        fclose ($socket);
    }
?>