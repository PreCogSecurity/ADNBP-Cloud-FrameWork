<?php  
 /* * File: SimpleImage.php * Author: Simon Jarvis * Copyright: 2006 Simon Jarvis 
  * * Date: 08/11/06 
  * * Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php 
  * * * This program is free software; you can redistribute it and/or 
  * * modify it under the terms of the GNU General Public License 
  * * as published by the Free Software Foundation; either version 2 
  * * of the License, or (at your option) any later version. 
  * * * This program is distributed in the hope that it will be useful, 
  * * but WITHOUT ANY WARRANTY; without even the implied warranty of 
  * * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
  * * GNU General Public License for more details: 
  * * http://www.gnu.org/licenses/gpl.html 
  * */
  
class ImageManager {
	var $image;
	var $image_type;
	var $error = false;
	function load($filename) {
		$image_info = @getimagesize($filename);
		if($image_info===fase) $this->error=true;
		{
			$this -> image_type = $image_info[2];
			if ($this -> image_type == IMAGETYPE_JPEG) {   $this -> image = imagecreatefromjpeg($filename);
			} elseif ($this -> image_type == IMAGETYPE_GIF) {   $this -> image = imagecreatefromgif($filename);
			} elseif ($this -> image_type == IMAGETYPE_PNG) {   $this -> image = imagecreatefrompng($filename);
			}
		}
	}
	function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null) {
		if ($image_type == IMAGETYPE_JPEG) { imagejpeg($this -> image, $filename, $compression);
		} elseif ($image_type == IMAGETYPE_GIF) {   imagegif($this -> image, $filename);
		} elseif ($image_type == IMAGETYPE_PNG) {   imagepng($this -> image, $filename);
		}
		if ($permissions != null) {   chmod($filename, $permissions);
		}
	}
	function saveBucket($filename) {
		ob_start();
		imagepng($this -> image);
		$options = array('gs' => array('acl'=>'public-read','Content-Type' => 'image/png'));
		$ctx = stream_context_create($options);	
		file_put_contents($filename, ob_get_contents(),0,$ctx);
		ob_end_clean();
	}
	function output($image_type = IMAGETYPE_PNG) {
		if ($image_type == IMAGETYPE_JPEG) { imagejpeg($this -> image);
		} elseif ($image_type == IMAGETYPE_GIF) {   imagegif($this -> image);
		} elseif ($image_type == IMAGETYPE_PNG) {   imagepng($this -> image);
		}
	}
	function finishOutput($image_type = IMAGETYPE_PNG) {
			header('Content-type: image/png');
		    $this->output($image_type);
			imagedestroy($this -> image);
			exit;
	}
	function destroy() {
			imagedestroy($this -> image);
	}
	
	function getWidth() {
		return imagesx($this -> image);
	}
	function getHeight() {
		return imagesy($this -> image);
	}
	function resizeToHeight($height) {   $ratio = $height / $this -> getHeight();
		$width = $this -> getWidth() * $ratio;
		$this -> resize($width, $height);
	}
	function resizeToWidth($width) { $ratio = $width / $this -> getWidth();
		$height = $this -> getheight() * $ratio;
		$this -> resize($width, $height);
	}
	function scale($scale) { $width = $this -> getWidth() * $scale / 100;
		$height = $this -> getheight() * $scale / 100;
		$this -> resize($width, $height);
	}
	function resize($width, $height) {
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this -> image, 0, 0, 0, 0, $width, $height, $this -> getWidth(), $this -> getHeight());
		$this -> image = $new_image;
	}
	function flip ( $mode )
	{
	
	    $width                        =    $this->getWidth();
	    $height                       =    $this->getHeight();
	
	    $src_x                        =    0;
	    $src_y                        =    0;
	    $src_width                    =    $width;
	    $src_height                   =    $height;
	
	    switch ( $mode )
	    {
	
	        case '1': //vertical
	            $src_y                =    $height -1;
	            $src_height           =    -$height;
	        break;
	
	        case '2': //horizontal
	            $src_x                =    $width -1;
	            $src_width            =    -$width;
	        break;
	
	        case '3': //both
	            $src_x                =    $width -1;
	            $src_y                =    $height -1;
	            $src_width            =    -$width;
	            $src_height           =    -$height;
	        break;
	
	    }
	
	    $imgdest = imagecreatetruecolor ( $width, $height );
		imagealphablending($imgdest, false);
		imagesavealpha($imgdest, true);
	
	    if ( imagecopyresampled ( $imgdest, $this->image, 0, 0, $src_x, $src_y , $width, $height, $src_width, $src_height ) )
	    {
	        $this->image = $imgdest;
	    }
	
	}	

	function setOpacity( $opacity ) //params: image resource id, opacity in percentage (eg. 80)
        {
            if( !isset( $opacity ) )
                { return false; }
            $opacity /= 100;
            
            //get image width and height
            $w = $this->getWidth();
            $h = $this->getHeight();
			$img = $this->image;
            
            //turn alpha blending off
            imagealphablending( $img, false );
            
            //find the most opaque pixel in the image (the one with the smallest alpha value)
            $minalpha = 127;
            for( $x = 0; $x < $w; $x++ )
                for( $y = 0; $y < $h; $y++ )
                    {
                        $alpha = ( imagecolorat( $img, $x, $y ) >> 24 ) & 0xFF;
                        if( $alpha < $minalpha )
                            { $minalpha = $alpha; }
                    }
            
            //loop through image pixels and modify alpha for each
            for( $x = 0; $x < $w; $x++ )
                {
                    for( $y = 0; $y < $h; $y++ )
                        {
                            //get current alpha value (represents the TANSPARENCY!)
                            $colorxy = imagecolorat( $img, $x, $y );
                            $alpha = ( $colorxy >> 24 ) & 0xFF;
                            //calculate new alpha
                            if( $minalpha !== 127 )
                                { $alpha = 127 + 127 * $opacity * ( $alpha - 127 ) / ( 127 - $minalpha ); }
                            else
                                { $alpha += 127 * $opacity; }
                            //get the color index with new alpha
                            $alphacolorxy = imagecolorallocatealpha( $img, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha );
                            //set pixel with the new color + opacity
                            if( !imagesetpixel( $img, $x, $y, $alphacolorxy ) )
                                { return false; }
                        }
                }
            $this->image = $img;
        }

		function imageFromText($text,$font,$size,$x,$y) {
			// Crear la imagen
			$im = imagecreatetruecolor($x, $y);
			
			// Crear algunos colores
			$blanco = imagecolorallocate($im, 255, 255, 255);
			$green = imagecolorallocate($im, 88, 197, 197);
			imagefilledrectangle($im, 0, 0, $x, $y, $green);
			
			// Añadir algo de sombra al texto
			//imagettftext($im, $size, 0, 11, 21, $gris, $font, $text);
			
			// Añadir el texto
			imagettftext($im, $size, 0, 0, 10, $blanco, $font, $text);
			
			// Usar imagepng() resultará en un texto más claro comparado con imagejpeg()
			$this->image = $im;
			//imagepng($im);
		}

		function stampImage($stamp,$xInit,$yInit,$xMargin,$yMargin,$opacity=0) {
		
			// Load the stamp and the photo to apply the watermark to
			if($opacity)  {
				$tmp = $this->image;
				$this->image = $stamp;
				$this->setOpacity($opacity);
				$stamp = $this->image;
				$this->image = $tmp;
				unset($tmp);
			}
		
			// Cal stamp postion
			if($xInit=='left') {
				$xStamp = $xMargin;
			} else {
				$xStamp = imagesx($this->image)-imagesx($stamp)-$xMargin;
			}
			
			if($yInit=='top') {
				$yStamp = $yMargin;
			} else {
				$yStamp = imagesy($this->image)-imagesy($stamp)-$xMargin;
			}
			
			imagecopy($this->image, $stamp, $xStamp, $yStamp, 0, 0, imagesx($stamp), imagesy($stamp));
			
		
			
		}

}
?>