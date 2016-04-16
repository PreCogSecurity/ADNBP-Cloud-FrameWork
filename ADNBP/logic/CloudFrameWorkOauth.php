<?php
    /**
     * Opauth example
     *
     * This is an example on how to instantiate Opauth
     * For this example, Opauth config is loaded from a separate file: opauth.conf.php
     *
     */

    /**
     * Define paths
     */
    if (isset($_GET['ret'])) $this->setSessionVar("redirectOnAuth", $_GET['ret']);
    $this->setConf("pageCode", "oauth");


    define('CONF_FILE', $this->_rootpath . '/ADNBP/class/auth/opauth/' . 'opauth.conf.php');
    define('OPAUTH_LIB_DIR', $this->_rootpath . '/ADNBP/class/auth/opauth/lib/Opauth/');

    /**
     * Load config
     */
    if (!file_exists(CONF_FILE)) {
        trigger_error('Config file missing at ' . CONF_FILE, E_USER_ERROR);
        exit();
    }
    require CONF_FILE;

    /**
     * Instantiate Opauth with the loaded config
     */

    list($foo, $this->_basename, $param) = explode('/', $this->_url, 3);
    if (strlen($param)) {
        include OPAUTH_LIB_DIR . 'Opauth.php';
        $Opauth = new Opauth($config);
        exit;
    } else {
        if (strlen($_GET[auth] == 'finished')) {
            if (strlen($this->getSessionVar("redirectOnAuth"))) {
                $ret = $this->getSessionVar("redirectOnAuth");
                $this->setSessionVar("redirectOnAuth", '');
                $_SESSION['opauth']['_redirect_'] = $ret;
                $this->urlRedirect($this->_url, $ret);
            } else {
                $this->urlRedirect($this->_url, "/");
            }
        }
    }

?>