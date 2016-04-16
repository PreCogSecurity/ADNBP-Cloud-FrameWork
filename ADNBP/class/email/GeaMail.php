<?php  
    ########################################################### 
    # Madrid 12 de nov de 2008
    # GeaNet S.L.  
    # http://www.geanet.es (info@geanet.es)
    #####  
    # Equipo de trabajo  
    #   Hector L�pez
    ########################################################### 
    # SOPORTADO POR: Hector L�pez
    # Last Update: 16 de oct 2002
    # Last Update: 23 de may 2002 
    # Last Update: 28 de nov 2003 
    # notes: add getText, getHtml functions
    # notes: corrected addText, addHtml functions
    # notes: added isValidEmail function
    # notes: bugFixed in remoteHtmlToLocal
    # Last Update: 13 de ene 2004 
    # notes: bugFixed in remoteHtmlToLocal for images with http://
    # Last Update: 23 de ene 2004 
    # notes: bugFixed in setHtml initializing this->html_images 
    ########################################################### 
/**
* Paquete de clases de utilidad general
* @version 5.3.7
* @author Hector L�pez <hlopez@geanet.es>
* @package com.geanet.geaclasses.utils
*/

if (!defined ("_GEAMAIL_CLASS_") ) {
    define ("_GEAMAIL_CLASS_", TRUE);

require_once(dirname(__FILE__) . '/mimePart.php');

    /**
    * @class GeaMail
    * Representa un mensaje de correo electr�nico
    *
    * @version 5.3.7
    * @author Hector L�pez <hlopez@geanet.es>
    * @copyright Copyright � 2008, GeaNet S.L.
    */
   class GeaMail { 
    
        /**
        * The html part of the message
        * @var string
        */
        var $html;

        /**
        * The text part of the message(only used in TEXT only messages)
        * @var string
        */
        var $text;

        /**
        * The main body of the message after building
        * @var string
        */
        var $output;

        /**
        * The alternative text to the HTML part (only used in HTML messages)
        * @var string
        */
        var $html_text;

        /**
        * An array of embedded images/objects
        * @var array
        */
        var $html_images;

        /**
        * An array of recognised image types for the findHtmlImages() method
        * @var array
        */
        var $image_types;

        /**
        * Parameters that affect the build process
        * @var array
        */
        var $build_params;
        /**
        * Array of attachments
        * @var array
        */
        var $attachments;

        /**
        * The main message headers
        * @var array
        */
        var $headers;

        /**
        * Whether the message has been built or not
        * @var boolean
        */
        var $is_built;

        /**
        * The return path address. If not set the From:
        * address is used instead
        * @var string
        */
        var $return_path;

        /**
        * String for debug
        * @var string
        */
        var $traza;

        /**
        * STMP server name or address
        * @access   private
        * @var      string  $_smtpserver
        */
        var $_smtpserver;

        /**
        * Error string
        * @access   private
        * @var      string  $_error;
        */
        var $_error;

        /**
        * Constructor de la clase
        * @param    string  $from       Direcci�n del remitente. Opcional, por defecto vac�a
        * @param    string  $subject    Asunto del mensaje a enviar. Opcional, por defecto vac�a
        * @param    string  $text       Texto del mensaje a enviar. Opcional, por defecto vac�a
        * @param    string  $smtpserver Nombre del servidor smtp. Opcional, por defecto vac�a
        */
        #--------------------------------------- 
    
        Function GeaMail ($from = '', $subject = '', $text = '', $smtpserver = '') {
               $this->build_params['html_encoding'] = 'quoted-printable';
               $this->build_params['text_encoding'] = '7bit';
               $this->build_params['html_charset']  = 'ISO-8859-1';
               $this->build_params['text_charset']  = 'ISO-8859-1';
               $this->build_params['head_charset']  = 'ISO-8859-1';
               $this->build_params['text_wrap']     = 998;
               $this->image_types = array('gif'   => 'image/gif',
                                          'jpg'   => 'image/jpeg',
                                          'jpeg'  => 'image/jpeg',
                                          'jpe'   => 'image/jpeg',
                                          'bmp'   => 'image/bmp',
                                          'png'   => 'image/png',
                                          'tif'   => 'image/tiff',
                                          'tiff'  => 'image/tiff',
                                          'css'  => 'text/css',
                                          'swf'   => 'application/x-shockwave-flash'
                                          );

                /**
                * Make sure the MIME version header is first.
                */
                $this->headers['MIME-Version'] = '1.0';

                if (strlen ($from) ) $this->setFrom ($from);
                if (strlen ($subject) ) $this->setSubject($subject);
                if (strlen ($text) ) $this->setText($text);
                if (strlen ($smtpserver) ) $this->setSmtpServer ($smtpserver);
        } 

        /**
        * Asigna un servidor SMTP a trav�s del que enviar los mails
        *
        * Por defecto la clase usa sendmail; si se asigna un servidor SMTP, usa el protocolo SMTP
        */
        function setSmtpServer ($server) {
            $this->_smtpserver = $server;
        }

        /**
        * Check if email sent has a right sintax
        */

        function isValidEmail($email) {
          return (preg_match ("/^[A-Z0-9._%-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,4}$/i", $email));
          //return (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email));
        }

        /**
        * This function will read a file in
        * from a supplied filename and return
        * it. This can then be given as the first
        * argument of the the functions
        * add_html_image() or add_attachment().
        */
        function getFile($filename) {
            include(dirname(__FILE__)."/GeaMail/getFile.php");
            return $return;
        }

        /**
        * Accessor to set the CRLF style
        */
        function setCrlf($crlf = "\n") {
                if (!defined('CRLF')) {
                        define('CRLF', $crlf, true);
                }

                if (!defined('MAIL_MIMEPART_CRLF')) {
                        define('MAIL_MIMEPART_CRLF', $crlf, true);
                }
        }

        /**
        * Builds the multipart message from the
        * list ($this->_parts). $params is an
        * array of parameters that shape the building
        * of the message. Currently supported are:
        *
        * $params['html_encoding'] - The type of encoding to use on html. Valid options are
        *                            "7bit", "quoted-printable" or "base64" (all without quotes).
        *                            7bit is EXPRESSLY NOT RECOMMENDED. Default is quoted-printable
        * $params['text_encoding'] - The type of encoding to use on plain text Valid options are
        *                            "7bit", "quoted-printable" or "base64" (all without quotes).
        *                            Default is 7bit
        * $params['text_wrap']     - The character count at which to wrap 7bit encoded data.
        *                            Default this is 998.
        * $params['html_charset']  - The character set to use for a html section.
        *                            Default is ISO-8859-1
        * $params['text_charset']  - The character set to use for a text section.
        *                          - Default is ISO-8859-1
        * $params['head_charset']  - The character set to use for header encoding should it be needed.
        *                          - Default is ISO-8859-1
        */
        function buildMessage($params = array())
        {
                if (!empty($params)) {
                        while (list($key, $value) = each($params)) {
                                $this->build_params[$key] = $value;
                        }
                }
                if (!empty($this->html_images)) {
                        foreach ($this->html_images as $value) {
                                $this->html = str_replace($value['name'], 'cid:'.$value['cid'], $this->html);
                        }
                }

                $null        = null;
                $attachments = !empty($this->attachments) ? true : false;
                $html_images = !empty($this->html_images) ? true : false;
                $html        = !empty($this->html)        ? true : false;
                $text        = isset($this->text)         ? true : false;

                switch (true) {
                        case $text AND !$attachments:
                                $message = &$this->_addTextPart($null, $this->text);
                                break;

                        case !$text AND $attachments AND !$html:
                                $message = &$this->_addMixedPart();

                                for ($i=0; $i<count($this->attachments); $i++) {
                                        $this->_addAttachmentPart($message, $this->attachments[$i]);
                                }
                                break;

                        case $text AND $attachments:
                                $message = &$this->_addMixedPart();
                                $this->_addTextPart($message, $this->text);

                                for ($i=0; $i<count($this->attachments); $i++) {
                                        $this->_addAttachmentPart($message, $this->attachments[$i]);
                                }
                                break;
                        case $html AND !$attachments AND !$html_images:
                                if (!is_null($this->html_text)) {
                                        $message = &$this->_addAlternativePart($null);
                                        $this->_addTextPart($message, $this->html_text);
                                        $this->_addHtmlPart($message);
                                } else {
                                        $message = &$this->_addHtmlPart($null);
                                }
                                break;

                        case $html AND !$attachments AND $html_images:
                                if (!is_null($this->html_text)) {
                                        $message = &$this->_addAlternativePart($null);
                                        $this->_addTextPart($message, $this->html_text);
                                        $related = &$this->_addRelatedPart($message);
                                } else {
                                        $message = &$this->_addRelatedPart($null);
                                        $related = &$message;
                                }
                                $this->_addHtmlPart($related);
                                for ($i=0; $i<count($this->html_images); $i++) {
                                        $this->_addHtmlImagePart($related, $this->html_images[$i]);
                                }
                                break;
                        case $html AND $attachments AND !$html_images:
                                $message = &$this->_addMixedPart();
                                if (!is_null($this->html_text)) {
                                        $alt = &$this->_addAlternativePart($message);
                                        $this->_addTextPart($alt, $this->html_text);
                                        $this->_addHtmlPart($alt);
                                } else {
                                        $this->_addHtmlPart($message);
                                }
                                for ($i=0; $i<count($this->attachments); $i++) {
                                        $this->_addAttachmentPart($message, $this->attachments[$i]);
                                }
                                break;

                        case $html AND $attachments AND $html_images:
                                $message = &$this->_addMixedPart();
                                if (!is_null($this->html_text)) {
                                        $alt = &$this->_addAlternativePart($message);
                                        $this->_addTextPart($alt, $this->html_text);
                                        $rel = &$this->_addRelatedPart($alt);
                                } else {
                                        $rel = &$this->_addRelatedPart($message);
                                }
                                $this->_addHtmlPart($rel);
                                for ($i=0; $i<count($this->html_images); $i++) {
                                        $this->_addHtmlImagePart($rel, $this->html_images[$i]);
                                }
                                for ($i=0; $i<count($this->attachments); $i++) {
                                        $this->_addAttachmentPart($message, $this->attachments[$i]);
                                }
                                break;

                }
                if (isset($message)) {
                        $output = $message->encode();
                        $this->output   = $output['body'];
                        $this->headers  = array_merge($this->headers, $output['headers']);

                        // Add message ID header
                        srand((double)microtime()*10000000);
                        $message_id = sprintf('<%s.%s@%s>', base_convert(time(), 10, 36), base_convert(rand(), 10, 36), !empty($GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST']) ? $GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST'] : $GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME']);
                        $this->headers['Message-ID'] = $message_id;

                        $this->is_built = true;
                        return true;
                } else {
                        return false;
                }
        }

        /**
        * Adds a html part to the mail.
        * Also replaces image names with
        * content-id's.
        * @param    string  $html   texto en formato html que se asignara al mail que se enviara 
        * @param    string  $text   texto en formato html alternativo
        * @param    string  $images_dir ruta de imagenes a buscar
        * @return   void
        */
        function setHtml($html, $text = null, $images_dir = null)
        {
                $this->html      = $html;
                $this->html_text = $text;

                if (isset($images_dir)) {
                        unset($this->html_images);
                        $this->_findHtmlImages($images_dir);
                }

                $this->is_built = false;
        }

        /**
        * Return html content
        * @return   string  retorna el contenido del mail
        */
        function getHtml() { return($this->html); }


        /**
        * Accessor function to set the text encoding
        * @param    string  $encoding   cadena indica tipo de codificacion
        * @return   void
        */
        function setTextEncoding($encoding = '7bit') { $this->build_params['text_encoding'] = $encoding; }

        /**
        * Accessor function to set the HTML encoding
        */
        function setHtmlEncoding($encoding = 'quoted-printable') { $this->build_params['html_encoding'] = $encoding; }


        /**
        * Accessor function to set the text charset
        */
        function setTextCharset($charset = 'ISO-8859-1') { $this->build_params['text_charset'] = $charset; }

        /**
        * Accessor function to set the HTML charset
        */
        function setHtmlCharset($charset = 'ISO-8859-1') { $this->build_params['html_charset'] = $charset; }

        /**
        * Accessor function to set the header encoding charset
        */
        function setHeadCharset($charset = 'ISO-8859-1') { $this->build_params['head_charset'] = $charset; }

        /**
        * Accessor function to set the text wrap count
        */
        function setTextWrap($count = 998) { $this->build_params['text_wrap'] = $count; }


        /**
        * Accessor to set a header
        */
        function setHeader($name, $value) { $this->headers[$name] = $value; }

        /**
        * Accessor to add a Subject: header
        * @param    string  $subject cadena indica texto de la cabecera   
        * @return   void
        */
        function setSubject($subject) { $this->headers['Subject'] = $subject; }

        /**
        * Accessor to add a From: header
        * @param string $from cadena indica direccion remitente
        * @return void
        */
        function setFrom($from) { $this->headers['From'] = $from; }

        /**
        * Accessor to set the return path
        * @param    string  $returnPath    cadena indica informacion a la cabecera
        * @return   void
        */
        function setReturnPath($returnPath) { $this->return_path = $returnPath; }

        /**
        * Accessor to set the Reply-to header (return address)
        * @param    string  $replyto        Return address
        * @return   void
        */
        function setReplyTo ($replyto) {
            $this->headers['Reply-to'] = $replyto;
        }

        /**
        * Accessor to add a Cc: header
        */
        function setCc($cc) { $this->headers['Cc'] = $cc; }

        /**
        * Accessor to add a Bcc: header
        */
        function setBcc($bcc) { $this->headers['Bcc'] = $bcc; }

        /**
        * Codifica una cabecera si es necesario
        * according to RFC2047
        * @access private
        */
        function _encodeHeader($input, $charset = 'ISO-8859-1')
        {
            $output = $input;
            $routput = false;
                preg_match_all('/(\w*([\x80-\xFF]+)\w*)/', $output, $matches);
                for ($i = 0, $ct = count ($matches[1]); $i < $ct; $i++) {
                    $value = $matches[1][$i];
                    $routput = true;// |= (!ereg("^[ ]+$", $matches[2][$i]));
                        $replacement = preg_replace('/([\x80-\xFF])/e', '"=" . strtoupper(dechex(ord("\1")))', $value);
                        $output = str_replace($value, '=?' . $charset . '?Q?' . $replacement . '?=', $output);
                }
                if ($routput) $output = str_replace (" ", '=?' . $charset . '?Q?=20?=', $output);
                if (strlen ($output) > 1000) {
                    $parts = split ("\=\?" . $charset . "\?Q\?", $output);
                    $wrappedoutput = "";
                    for ($i = 0, $ct = count ($parts); $i < $ct; $i++) {
                        if (strlen ($wrappedoutput . '=?' . $charset . '?Q?' . $parts[$i]) > 1000) {
                            break;
                        }
                        if (strlen ($parts[$i]) ) $wrappedoutput .=  '=?' . $charset . '?Q?' . $parts[$i];
                    }
                    $output = $wrappedoutput;
                    $routput = true;
                }
                //  die($output);

                // Only return encoded output if more than spaces have been changed, or if size exceeds limit
                return $routput? $output: $input;
        }

        /**
        * Adds an image to the list of embedded
        * images.
        * @access private
        */
        function addHtmlImage($file, $name = '', $c_type='application/octet-stream') {
            include(dirname(__FILE__)."/GeaMail/addHtmlImage.php");
        }

        /**
        * init a file to the list of attachments.
        * @access private
        * @param    string  $file   contenido binario del fichero
        * @param    string  $name   cadena indica nombre del fichero q aparecera en el mail
        * @param    string  $c_type cadena indica tipo del fichero q aparecera en el mail 
        * @param    string  $encoding   cadena indica el tipo de mail 
        * @return   void
        */
        function setAttachment($file, $name = '', $c_type='application/octet-stream', $encoding = 'base64')
        {
            $this->attachments = array();
            $this->addAttachment($file,$name,$c_type,$encoding);
        }        
        /**
        * Adds a file to the list of attachments.
        * @access private
        * @param    string  $file   contenido binario del fichero
        * @param    string  $name   cadena indica nombre del fichero q aparecera en el mail
        * @param    string  $c_type cadena indica tipo del fichero q aparecera en el mail 
        * @param    string  $encoding   cadena indica el tipo de mail 
        * @return   void
        */
        function addAttachment($file, $name = '', $c_type='application/octet-stream', $encoding = 'base64')
        {
            include(dirname(__FILE__)."/GeaMail/addAttachment.php");
        }

        /**
        * Adds a text subpart to a mime_part object
        */
        function &_addTextPart(&$obj, $text)
        {
                $params['content_type'] = 'text/plain';
                $params['encoding']     = $this->build_params['text_encoding'];
                $params['charset']      = $this->build_params['text_charset'];
                if (is_object($obj)) {
                        return $obj->addSubpart($text, $params);
                } else {
                        return new Mail_mimePart($text, $params);
                }
        }

/**
* Adds a html subpart to a mime_part object
*/
        function &_addHtmlPart(&$obj)
        {
                $params['content_type'] = 'text/html';
                $params['encoding']     = $this->build_params['html_encoding'];
                $params['charset']      = $this->build_params['html_charset'];
                if (is_object($obj)) {
                        return $obj->addSubpart($this->html, $params);
                } else {
                        return new Mail_mimePart($this->html, $params);
                }
        }

/**
* Starts a message with a mixed part
*/
        function &_addMixedPart()
        {
                $params['content_type'] = 'multipart/mixed';
                return new Mail_mimePart('', $params);
        }
/**
* Adds an alternative part to a mime_part object
*/
        function &_addAlternativePart(&$obj)
        {
                $params['content_type'] = 'multipart/alternative';
                if (is_object($obj)) {
                        return $obj->addSubpart('', $params);
                } else {
                        return new Mail_mimePart('', $params);
                }
        }

/**
* Adds a html subpart to a mime_part object
*/
        function &_addRelatedPart(&$obj)
        {
            include(dirname(__FILE__)."/GeaMail/_addRelatedPart.php");
        }
/**
* Adds an html image subpart to a mime_part object
*/
        function &_addHtmlImagePart(&$obj, $value)
        {
            include(dirname(__FILE__)."/GeaMail/_addHtmlImagePart.php");
        }

/**
* Adds an attachment subpart to a mime_part object
*/
        function &_addAttachmentPart(&$obj, $value)
        {
            include(dirname(__FILE__)."/GeaMail/_addAttachmentPart.php");
        }

        /**
        * Function for extracting images from
        * html source. This function will look
        * through the html code supplied by add_html()
        * and find any file that ends in one of the
        * extensions defined in $obj->image_types.
        * If the file exists it will read it in and
        * embed it, (not an attachment).
        * @access private
        * @author Dan Allen
        */
        function _findHtmlImages($images_dir) {
            include(dirname(__FILE__)."/GeaMail/_findHtmlImages.php");
        }

        /**
        * Adds plain text. Use this function
        * when NOT sending html email
        * @param    string  $text cadena indica el texto del mail
        * @return   void
        */
        function setText($text = '') { $this->text = $text; $this->is_built = false;
        }

        /**
        * Return text value
        */
        function getText() { return($this->text); }


        /**
        * Permite antes de realizar un env�o comprobar el estado de la configuraci�n
        */
        function sendDebug($to,$sent="") {
            include(dirname(__FILE__)."/GeaMail/sendDebug.php");
        }

        /**
        * Env�a el mensaje de correo
        * @param    mixed   $recipients     Direcci�n del destinatario o destinatarios. Si son varios puede ser
        *                                   una cadena separada por comas o un array
        * @param    string  $type           Tipo de env�o. Opcional, por defecto "mail". Por el momento s�lo
        *                                   se admite la opci�n "mail"
        * @return   boolean true si la operaci�n tiene �xito, false si hay alg�n error
        */
        Function send($recipients,$type="mail",$cc="") {
            if (is_array ($recipients) ) $recipients = implode(',', $recipients);

                if (!defined('CRLF')) {
                        $this->setCrlf($type == 'mail' ? "\n" : "\r\n");
                }

                if (!$this->is_built) {
                        $this->buildMessage();
                }
                switch ($type) {
                        case 'mail':
                                $subject = '';
                                if (!empty($this->headers['Subject'])) {
                                        $subject = $this->_encodeHeader($this->headers['Subject'], $this->build_params['head_charset']);
                                        unset($this->headers['Subject']);
                                }

                                // Get flat representation of headers
                                foreach ($this->headers as $name => $value) {
                                        $headers[] = $name . ': ' . $this->_encodeHeader($value, $this->build_params['head_charset']);
                                }

                                if (!empty($cc)) {
                                    $headers[] = 'Cc: ' . $this->_encodeHeader($cc, $this->build_params['head_charset']);
                                }

                                $to = $this->_encodeHeader($recipients, $this->build_params['head_charset']);

                                if (strlen ($this->_smtpserver) ) $result = $this->_sendSMTP ($to, $subject, $this->output, implode(CRLF, $headers), $this->return_path);
                                else {
                                    if (!empty($this->return_path)) {
                                            $result = @mail($to, $subject, $this->output, implode(CRLF, $headers), '-f' . $this->return_path);
                                    } else {
                                            $result = @mail($to, $subject, $this->output, implode(CRLF, $headers));
                                    }
                                    if (!$result) $this->_error = $php_errormsg;
                                }

                                // Reset the subject in case mail is resent
                                if ($subject !== '') {
                                        $this->headers['Subject'] = $subject;
                                }

                                // Return
                                return $result;
                                break;
                }
        }

        /**
        * Env�a un mensaje a trav�s del protocolo SMTP
        * @access   private
        * @param    string  $to         Direcci�n del destinatario o destinatarios
        * @param    string  $subject    Asunto del mensaje
        * @param    string  $text       Contenido del mensaje
        * @param    string  $headers    Cabeceras del mensaje
        * @param    string  $returnpath Direcci�n de retorno del mensaje
        * @return   boolean true si la operaci�n tiene �xito, false si hay alg�n error
        */
        function _sendSMTP ($to, $subject, $text, $headers, $returnpath) {
            include(dirname(__FILE__)."/GeaMail/_sendSMTP.php");

            return $ret;
        }

        /**
        * Env�a un comando a un servidor SMTP
        * @access   private
        * @param    resource    $socket     Identificador de conexi�n con el servidor
        * @param    string      $command    Comando a enviar
        * @return   void
        */
        function _serverSend ($socket, $command) {
            fputs ($socket, $command);
            $this->traza .= "SMTP send: $command\n";
        }

        /**
        * Recibe e interpreta respuesta de un servidor SMTP
        * @access   private
        * @param    resource    $socket     Identificador de conexi�n con el servidor
        * @param    string      $response   Respuesta esperada, si no coincide se informa de error
        * @return   boolean     TRUE si la operaci�n tiene �xito, FALSE si no
        */
        function _serverParse ($socket, $response) {
            include(dirname(__FILE__)."/GeaMail/_serverParse.php");
            return $ret;
        }

        /**
        * Devuelve el error producido en la conexi�n SMTP
        * @return   string  Mensaje de error, cadena vac�a si no se ha producido ninguno
        */
        function getLastError() {
            return $this->_error;
        }

        /**
        * Use this method to return the email
        * in message/rfc822 format. Useful for
        * adding an email to another email as
        * an attachment. there's a commented
        * out example in example.php.
        */
        function getRFC822($recipients)
        {
            include(dirname(__FILE__)."/GeaMail/getRFC822.php");
            return $ret;
        }

        /**
        * Con este m�todo podemos guardar en un disco local una p�gina que haga referencia
        * a im�genes y estilos en remoto guard�ndolo en disco local 
        * y prepararla para un env�o embeded
        * No admite im�genes de m�s de 8M
        * @param    string  $url_base   ruta de acceso donde se encuentran los archivos deseados
        * @param    string  $html   texto en formato html que se guardara en el archivo a crear
        * @param    string  $dir_dest   ruta destino donde queremos que se guarde los archivos que se crear�n
        * @param    int  $debug $debug=1: visualiza error en caso de no abrirse el fichero y retorna ""
        * @return   string  ruta completa del archivo creado
        */
        function remoteHtmlToLocal($url_base,$html,$dir_dest,$debug=0) {
            include(dirname(__FILE__)."/GeaMail/remoteHtmlToLocal.php");
           return $ret;
        }

        /**
        * Con este m�todo podemos guardar en un disco local una p�gina que haga referencia
        * a im�genes y estilos en remoto pero no guardando en disco local las im�genes
        * y prepararla para un env�o NO embeded
        * @param    string  $html   texto en formato html que se guardara en el archivo a crear
        * @param    string  $dir_dest   ruta destino donde queremos que se guarde los archivos que se crear�n
        * @param    int  $debug $debug=1: visualiza error en caso de no abrirse el fichero y retorna ""
        * @return   string  ruta completa del archivo creado
        */
        function HtmlToLocal($html,$dir_dest,$debug=0) {
            include(dirname(__FILE__)."/GeaMail/HtmlToLocal.php");
           return $ret;
        }

        /**
        * Con este m�todo podemos guardar en un disco local una p�gina que haga referencia
        * a un fichero de texto.
        * @param    string  $txt   texto que se guardara en el archivo a crear
        * @param    string  $dir_dest   ruta destino donde queremos que se guarde los archivos que se crear�n
        * @param    int  $debug $debug=1: visualiza error en caso de no abrirse el fichero y retorna ""
        * @return   string  ruta completa del archivo creado
        */
        function remoteTxtToLocal($txt,$dir_dest,$debug=0) {
            include(dirname(__FILE__)."/GeaMail/remoteTxtToLocal.php");
           return $ret;
        }

        /**
        * Con este m�todo podemos a�adir una url a todas las referencia de im�genes y css que no lo tengan
		* hacia un http dentro de un contenido html dado. Se utiliza para los env�os no embeded.
        * @param    string  $url_base   Url a �adir
        * @param    string  $html   texto en formato html que se guardara en el archivo a crear
        * @return   string  ruta archivo modificado completo.
        */
        function addUrlToHtml($url_base,$html) {
            include(dirname(__FILE__)."/GeaMail/addUrlToHtml.php");
           return $html;
        }
		
} // End of class.

}   // Multiple including protection
?>