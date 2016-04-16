<?php

require_once 'autoload.php';

$app = \CloudFramework\CloudFrameworkApp::getInstance('cloud-framework', dirname(__FILE__) . '/../composer.json');
$app->run();
