<?php
    /**
     * General autoloader
     */

    if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    }
    //CloudFramework
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cloudframework-io' . DIRECTORY_SEPARATOR . 'autoload.php';

    //Notifications autoloader
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'notifications' . DIRECTORY_SEPARATOR . 'autoload.php';

    //Notifications autoloader
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'datastore' . DIRECTORY_SEPARATOR . 'autoload.php';