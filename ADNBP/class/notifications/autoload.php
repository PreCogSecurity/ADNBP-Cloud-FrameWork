<?php
/**
 * Cloud Service Autoloader for Notifications Service
 */

if(!defined("__NOTIFICATIONS_AUTOLOADER__")) {
    define("__NOTIFICATIONS_AUTOLOADER__", true);
    /**
     * Class NotificationsAutoloader
     * @author Fran López <fl@bloombees.com>
     * @version 1.0
     */
    class NotificationsAutoloader {

        /**
         * Autoloader class function
         * @param $class
         * @return bool
         */
        public static function loadClass($class) {
            // it only autoload class into the Rain scope
            if (strpos($class, 'CloudFramework') !== false && strpos($class, 'Service') !== false  && strpos($class, 'Notifications') !== false) {
                // Change order src
                $path = str_replace("\\", DIRECTORY_SEPARATOR, $class);
                // transform the namespace in path
                $path = str_replace('CloudFramework' . DIRECTORY_SEPARATOR . 'Service' . DIRECTORY_SEPARATOR . 'Notifications', '', $path);
                // filepath
                $abs_path = __DIR__ . DIRECTORY_SEPARATOR . 'src' . $path . ".php";
                // require the file
                if (file_exists($abs_path)) {
                    require_once $abs_path;
                }
            }
            return false;
        }
    }
}
spl_autoload_register(array('NotificationsAutoloader', 'loadClass'), true, true);
