<?php
    $return = '';
    if ($fp = fopen($filename, 'rb')) {
            while (!feof($fp)) {
                    $return .= fread($fp, 1024);
            }
            fclose($fp);
    } else {
            $return = false;
    }
?>