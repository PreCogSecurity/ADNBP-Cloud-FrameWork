<?php

namespace CloudFramework\Tool;

use CloudFramework\CloudFrameworkApp;
use CloudFramework\Patterns\Singleton;

class Performance extends Singleton
{

    var $data;

    function __construct()
    {
        // Performance Vars
        $this->data['initMicrotime'] = microtime(TRUE);
        $this->data['lastMicrotime'] = $this->data['initMicrotime'];
        $this->data['initMemory'] = memory_get_usage() / (1024 * 1024);
        $this->data['lastMemory'] = $this->data['initMemory'];
        $this->data['lastIndex'] = 1;
        $this->data['info'][] = 'File: ' . str_replace($_SERVER['DOCUMENT_ROOT'], '', __FILE__);
        $this->data['info'][] = 'Init Memory Usage: ' . number_format(round($this->data['initMemory'], 4), 4) . 'Mb';
    }

    function add($title, $file = '', $type = 'all')
    {
        // Hidding full path (security)
        $file = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);


        if ($type == 'note') $line = "[$type";
        else $line = $this->data['lastIndex'] . ' [';

        if (strlen($file)) $file = " ($file)";

        $_mem = memory_get_usage() / (1024 * 1024) - $this->data['lastMemory'];
        if ($type == 'all' || $type == 'endnote' || $type == 'memory' || $_GET['data'] == $this->data['lastIndex']) {
            $line .= number_format(round($_mem, 3), 3) . ' Mb';
            $this->data['lastMemory'] = memory_get_usage() / (1024 * 1024);
        }

        $_time = microtime(TRUE) - $this->data['lastMicrotime'];
        if ($type == 'all' || $type == 'endnote' || $type == 'time' || $_GET['data'] == $this->data['lastIndex']) {
            $line .= (($line == '[') ? '' : ', ') . (round($_time, 3)) . ' secs';
            $this->data['lastMicrotime'] = microtime(TRUE);
        }
        $line .= '] ' . $title;
        $line = (($type != 'note') ? '[' . number_format(round(memory_get_usage() / (1024 * 1024), 3), 3) . ' Mb, '
                . (round(microtime(TRUE) - $this->data['initMicrotime'], 3))
                . ' secs] / ' : '') . $line . $file;
        if ($type == 'endnote') $line = "[$type] " . $line;
        $this->data['info'][] = $line;

        if ($title) {
            $this->data['titles'][$title]['mem'] = $_mem;
            $this->data['titles'][$title]['time'] += $_time;
            $this->data['titles'][$title]['lastIndex'] = $this->data['lastIndex'];

        }

        if (isset($_GET['__p']) && $_GET['__p'] == $this->data['lastIndex']) {
            __sp();
            exit;
        }

        $this->data['lastIndex']++;

    }

    function init($spacename, $key)
    {
        $this->data['init'][$spacename][$key]['mem'] = memory_get_usage();
        $this->data['init'][$spacename][$key]['time'] = microtime(TRUE);
        $this->data['init'][$spacename][$key]['ok'] = TRUE;
    }

    function end($spacename, $key, $ok = TRUE, $msg = FALSE)
    {
        $this->data['init'][$spacename][$key]['mem'] = round((memory_get_usage() - $this->data['init'][$spacename][$key]['mem']) / (1024 * 1024), 3) . ' Mb';
        $this->data['init'][$spacename][$key]['time'] = round(microtime(TRUE) - $this->data['init'][$spacename][$key]['time'], 3) . ' secs';
        $this->data['init'][$spacename][$key]['ok'] = $ok;
        if ($msg !== FALSE) $this->data['init'][$spacename][$key]['notes'] = $msg;
    }

    function getLog()
    {
        $ret = '';
        $spaces = "";
        if (is_array($this->data['info']))
            foreach ($this->data['info'] as $key => $value) {
                if (is_string($value) && strpos($value, '[endnote]') !== FALSE) $spaces = substr($spaces, 0, -2);
                $ret .= $spaces;
                $ret .= ((is_string($value)) ? $value : print_r($value, TRUE)) . "\n";
                if (is_string($value) && strpos($value, '[note]') !== FALSE) $spaces .= "  ";
                if (is_string($value) && strpos($value, '[endnote]') !== FALSE) $ret .= "\n";

            }
        $ret .= "\n\nTOTALS:\n";
        if (is_array($this->data['titles']))
            foreach ($this->data['titles'] as $key => $value) {
                $ret .= "[$key] : " . round($value['mem'], 3) . ' Mb / ' . round($value['time'], 3) . " secs.\n";
            }

        return $ret;
    }
}

// Performance Functions
$__p = Performance::getInstance();

function __sp($title = '', $top = "<!--\n", $bottom = "\n-->")
{

    $__p = Performance::getInstance();
    $addhtml = '';

    if (isset($_GET['debug'])) {
        if (is_object($adnbp)) {
            $__p->data['info'][] = 'Object ADNBP';
            $__p->data['info'][] = $adnbp;
        }
        $__p->data['info'][] = '$_SERVER';
        $__p->data['info'][] = $_SERVER;
    }
    echo $top;
    echo $title;
    $spaces = "";
    foreach ($__p->data['info'] as $key => $value) {
        if (is_string($value) && strpos($value, '[endnote]') !== FALSE) $spaces = substr($spaces, 0, -2);
        echo $spaces;
        echo ((is_string($value)) ? $value : print_r($value, TRUE)) . "\n";
        if (is_string($value) && strpos($value, '[note]') !== FALSE) $spaces .= "  ";
        if (is_string($value) && strpos($value, '[endnote]') !== FALSE) echo "\n";

    }
    echo "\n\nTOTALS:\n";
    foreach ($__p->data['titles'] as $key => $value) {
        echo "[$key] : " . round($value['mem'], 3) . ' Mb / ' . round($value['time'], 3) . " secs.\n";
    }
    echo $addhtml;
    echo $bottom;
}

function __p($title = NULL, $file = NULL, $type = 'all')
{
    $__p = Performance::getInstance();
    if ($title === NULL && $file == NULL) return $__p->data;
    else $__p->add($title, $file, $type);
}

function __print($args)
{
    echo "<pre>";
    for ($i = 0, $tr = count($args); $i < $tr; $i++) {
        if ($args[$i] === "exit")
            exit;
        echo "\n<li>[$i]: ";
        if (is_array($args[$i]))
            echo print_r($args[$i], TRUE);
        else if (is_object($args[$i]))
            echo var_dump($args[$i]);
        else if (is_bool($args[$i]))
            echo ($args[$i]) ? 'true' : 'false';
        else if (is_null($args[$i]))
            echo 'NULL';
        else
            echo $args[$i];
    }
    echo "</pre>";
}

function _print()
{
    __print(func_get_args());
}

function _printe()
{
    __print(array_merge(func_get_args(), array('exit')));
}