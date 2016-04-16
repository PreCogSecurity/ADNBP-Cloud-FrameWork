<?php
    $params['content_type'] = $value['c_type'];
    $params['encoding']     = $value['encoding'];
    $params['disposition']  = 'attachment';
    $params['dfilename']    = $value['name'];
    $obj->addSubpart($value['body'], $params);
?>