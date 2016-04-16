<?php
    $params['content_type'] = $value['c_type'];
    $params['encoding']     = 'base64';
    $params['disposition']  = 'inline';
    $params['dfilename']    = $value['name'];
    $params['cid']          = $value['cid'];
    $obj->addSubpart($value['body'], $params);
?>