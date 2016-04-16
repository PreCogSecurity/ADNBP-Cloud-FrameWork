<?php
    // Build the list of image extensions
    reset($this->image_types);
    while (list($key,) = each($this->image_types)) {
            $extensions[] = $key;
    }

    preg_match_all('/(?:"|\')([^"\']+\.('.implode('|', $extensions).'))(?:"|\')/Ui', $this->html, $images);


    for ($i=0; $i<count($images[1]); $i++) {
            if(!ereg("/$",$images_dir) && !ereg("^/",$images[1][$i])) $add= "/";
            else $add="";
            if (file_exists($images_dir . $add. $images[1][$i])) {
                    $html_images[] = $images[1][$i];
                    $this->html = str_replace($images[1][$i], basename($images[1][$i]), $this->html);
            }
    }

    if (!empty($html_images)) {

            // If duplicate images are embedded, they may show up as attachments, so remove them.
            $html_images = array_unique($html_images);
            sort($html_images);

            for ($i=0; $i<count($html_images); $i++) {
                    if(!ereg("/$",$images_dir) && !ereg("^/",$html_images[$i])) $add= "/";
                    else $add="";
                    if ($image = $this->getFile($images_dir.$add.$html_images[$i])) {
                            $ext = substr($html_images[$i], strrpos($html_images[$i], '.') + 1);
                            $content_type = $this->image_types[strtolower($ext)];
                            $this->addHtmlImage($image, basename($html_images[$i]), $content_type);
                    }
            }
       
    }
?>