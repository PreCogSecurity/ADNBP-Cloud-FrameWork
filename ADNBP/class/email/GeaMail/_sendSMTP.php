<?php
    // PENDIENTE: de momento no se tienen en cuenta cabeceras CC y BCC

    // Limpieza extra
    $text = preg_replace("#(?<!\r)\n#si", "\r\n", $text);
    $headers = chop($headers);
    $headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers);

    $ato = split (",", $to);

    // Conexión
    $ret = true;
    $this->traza .= "Connecting to " . $this->_smtpserver . "\n";
    if( !$socket = @fsockopen($this->_smtpserver, 25, $errno, $errstr, 20) ) {
        $this->_error = "SMTP Connect: " . $errno . ' - ' . $errstr;
        $this->traza .= $this->_error;
        $ret = false;
    }

    if ($ret && !$this->_serverParse($socket, "220") ) $ret = false;

    if ($ret) {
	  $this->_serverSend ($socket, "HELO " . $this->_smtpserver . "\r\n");
	  if (!$this->_serverParse($socket, "250") ) $ret = false;
    }

    if ($ret) {
	   $this->_serverSend($socket, "MAIL FROM: " . $this->headers['From'] . "\r\n");
	   if (!$this->_serverParse($socket, "250") ) $ret = false;
    }
	
    for ($i = 0, $ct = count ($ato); $ret && ($i < $ct); $i++) {
        $this->_serverSend($socket, "RCPT TO: " . $ato[$i] . "\r\n");
        if (!$this->_serverParse($socket, "250") ) $ret = false;
    }

    if ($ret) {
       $this->_serverSend($socket, "DATA\r\n");
       if (!$this->_serverParse($socket, "354") ) $ret = false;
    }

    if ($ret) {
        $this->_serverSend($socket, "Subject: $subject\r\n");
        $this->_serverSend($socket, "To: $to\r\n");
        $this->_serverSend($socket, "$headers\r\n\r\n");
        $this->_serverSend($socket, "$text\r\n");

    	$this->_serverSend($socket, ".\r\n");
        if (!$this->_serverParse($socket, "250") ) $ret = false;
    }

    if ($ret) {
        $this->_serverSend($socket, "QUIT\r\n");
        fclose($socket);
    }

    $ret = true;
?>