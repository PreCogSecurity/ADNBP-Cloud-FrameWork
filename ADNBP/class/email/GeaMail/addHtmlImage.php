<?php
    $this->html_images[] = array(
                                                                    'body'   => $file,
                                                                    'name'   => $name,
                                                                    'c_type' => $c_type,
                                                                    'cid'    => md5(uniqid(time()))
                                                            );
?>