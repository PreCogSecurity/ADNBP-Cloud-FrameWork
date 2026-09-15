<?php
/**
 * Test bootstrap for ADNBP Cloud FrameWork.
 *
 * Provides minimal stubs for Core and Performance so that
 * cloudframework_appengine_php/src/class/DataStore.php can be
 * loaded and tested without the full Google App Engine SDK
 * or the web framework running.
 */

// Required by DataStore.php when constructing via Store -> ProtoBuf
$_SERVER['APPLICATION_ID'] = 'test-app';
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/..';

/**
 * Minimal stub for the Performance class used by DataStore::$core->__p
 */
class Performance
{
    public $data = [];
    public function add($title = '', $detail = '', $type = 'all')
    {
        // no-op in tests
    }
}

/**
 * Minimal stub for Core, replacing the full framework Core class.
 * Must match the type-hint in DataStore::__construct(Core &$core, $params)
 */
class Core
{
    public $__p;
    private $_conf = [];

    public function __construct()
    {
        $this->__p = new Performance();
    }

    /**
     * Minimal setConf used by some code paths
     */
    public function setConf($var, $value = null)
    {
        $this->_conf[$var] = $value;
    }

    /**
     * Minimal getConf used by some code paths
     */
    public function getConf($var, $default = null)
    {
        return isset($this->_conf[$var]) ? $this->_conf[$var] : $default;
    }
}

// Load DataStore.php which defines DataStore, Store, Schema, Entity,
// Gateway, ProtoBuf and related classes.
require_once __DIR__ . '/../cloudframework_appengine_php/src/class/DataStore.php';
