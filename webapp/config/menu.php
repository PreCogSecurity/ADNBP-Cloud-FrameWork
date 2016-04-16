<?php
    $this->pushMenu(array("level" => 0, "path" => "/", "template" => "CloudFrameWorkIntro.php", "notopbottom" => 1));
    $this->pushMenu(array("level" => 0, "path" => "/file-upload", "logic" => "file_upload.php", "template" => "file_upload.php"));
    $this->pushMenu(array("level" => 0, "path" => "/socialnetworks", "logic" => "socialnetworks.php", "template" => "socialnetworks.php"));
    $this->pushMenu(array("level" => 0, "path" => "/pushMessages", "logic" => "pushMessages.php", "template" => "pushMessages.php"));
    $this->pushMenu(array("level" => 0, "path" => "/datastore", "logic" => "datastore.php", "template" => "datastore.php"));
