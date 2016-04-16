<?php
namespace CloudFramework\Patterns;


/**
 * Class Singleton
 * @package CloudFramework\Patterns
 */
class Singleton
{

    private static $instance = array();

    /**
     * Singleton instance generator
     * @return $this
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!array_key_exists($class, self::$instance) || null === self::$instance[$class]) {
            $reflectorClass = new \ReflectionClass($class);
            try {
                self::$instance[$class] = $reflectorClass->newInstanceArgs(func_get_args());
            } catch(\Exception $e) {
                syslog(LOG_ERR, $e->getMessage());
                throw $e;
            }
        }
        return self::$instance[$class];
    }

    /**
     * Instance generator alias
     * @return $this
     */
    public static function create()
    {
        return self::getInstance(func_get_args());
    }

    /**
     * Magic setter
     * @param string $variable
     * @param mixed $value
     */
    public function __set($variable, $value)
    {
        if (property_exists($this, $variable)) {
            $this->$variable = $value;
        }
    }

    /**
     * Magic getter
     * @param string $variable
     * @return mixed
     */
    public function __get($variable)
    {
        if (property_exists($this, $variable)) {
            return $this->$variable;
        } else {
            return null;
        }
    }

    /**
     * Prevent the instance from being cloned
     * @return void
     */
    private function __clone() {}

    public function __toString()
    {
        $size = round(strlen(print_r($this, true)) / 1024, 4);
        return get_class($this) . " " . $size . " Kbytes";
    }
}
