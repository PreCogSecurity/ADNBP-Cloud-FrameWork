<?php
namespace CloudFramework;
use CloudFramework\Tool\ConfigLoader;
use CloudFramework\Tool\RequestParser;
use CloudFramework\Tool\Performance;
use CloudFramework\Patterns\Singleton;

/**
 * Class CloudFramework
 * @package CloudFramework\Core
 */
class CloudFrameworkApp extends Singleton
{

    /**
     * @var string
     */
    private $app_name;

    /**
     * @var \CloudFramework\Tool\ConfigLoader $config
     */
    protected $config;

    /**
     * @var \CloudFramework\Tool\Performance $performance
     */
    protected $perfomance;

    public function __construct($name = 'CloudFramework', $configFile = '')
    {
        $this->performance = new Performance();

        $this->app_name = $name;
        $this->config = ConfigLoader::getInstance($configFile);

    }

    public function run()
    {
        echo "Hello " . RequestParser::getQueryParam('name') . '<br>' . $this . "<pre>";
        $this->config->setConf('a','b');
        echo $this->config->getConf('a').' '.ConfigLoader::getConfParam('a');
    }
}
