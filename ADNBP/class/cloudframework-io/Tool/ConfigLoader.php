<?php
    namespace CloudFramework\Tool;
    use CloudFramework\Patterns\Singleton;

    /**
     * Class ConfigLoader
     * @package CloudFramework\Core
     */
    class ConfigLoader extends Singleton
    {
        private $config = array();

        public function __construct($configFilename = '')
        {
            if (strlen($configFilename)) {
                $this->loadConfigFile($configFilename);
            }
        }

        /**
         * Search config key and returns this value
         *
         * @param string $key
         *
         * @return mixed
         */
        public function getConf($key)
        {
            $value = NULL;
            if (count($this->config) > 0) {
                if (array_key_exists($key, $this->config)) {
                    $value = $this->config[$key];
                }
            }
            return $value;
        }

        /**
         * Set a config parameter
         *
         * @param string $key
         * @param mixed $value
         *
         * @return \CloudFramework\Tool\ConfigLoader
         */
        public function setConf($key, $value = NULL)
        {
            $this->config[$key] = $value;
            return $this;
        }

        /**
         * Reload config file
         *
         * @param $configFilename
         *
         * @throws \Exception
         */
        public function loadConfigFile($configFilename = '')
        {
            if (0 > strlen($configFilename) && file_exists($configFilename)) {
                $configs = json_decode(file_get_contents($configFilename), true);
                $this->extractComposedConfigs($configs);
            }
        }

        /**
         * @param array|mixed $configs
         * @param string $composedKey
         * @param array $composedConfig
         *
         * @return \CloudFramework\Tool\ConfigLoader
         */
        protected function extractComposedConfigs($configs, $composedKey = '', &$composedConfig = array())
        {
            if (is_array($configs)) {
                foreach ($configs as $key => $config) {
                    $_key = strlen($composedKey) ? $composedKey . '.' . $key : $key;
                    if (is_array($config)) {
                        $this->extractComposedConfigs($config, $_key, $composedConfig);
                    } else {
                        $this->setConf($_key, $config);
                    }
                }
            } elseif (strlen($composedKey)) {
                $this->setConf($composedKey, $configs);
            }

            return $this;
        }

        public static function getConfParam($key) {
            return self::getInstance()->getConf($key);
        }
    }
