<?php
    // Make up the date header as according to RFC822
    $this->setHeader('Date', date('D, d M y H:i:s O'));

    if (!defined('CRLF')) {
            $this->setCrlf($type == 'mail' ? "\n" : "\r\n");
    }

    if (!$this->is_built) {
            $this->buildMessage();
    }

    // Return path ?
    if (isset($this->return_path)) {
            $headers[] = 'Return-Path: ' . $this->return_path;
    }

    // Get flat representation of headers
    foreach ($this->headers as $name => $value) {
            $headers[] = $name . ': ' . $value;
    }
    $headers[] = 'To: ' . implode(', ', $recipients);

    $ret = implode(CRLF, $headers) . CRLF . CRLF . $this->output;
?>