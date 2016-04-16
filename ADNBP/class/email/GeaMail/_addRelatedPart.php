<?php
    $params['content_type'] = 'multipart/related';
    if (is_object($obj)) {
            return $obj->addSubpart('', $params);
    } else {
            return new Mail_mimePart('', $params);
    }
?>