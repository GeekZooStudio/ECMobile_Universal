<?php

/*
 *                                                                          
 *       _/_/_/                      _/        _/_/_/_/_/                     
 *    _/          _/_/      _/_/    _/  _/          _/      _/_/      _/_/    
 *   _/  _/_/  _/_/_/_/  _/_/_/_/  _/_/          _/      _/    _/  _/    _/   
 *  _/    _/  _/        _/        _/  _/      _/        _/    _/  _/    _/    
 *   _/_/_/    _/_/_/    _/_/_/  _/    _/  _/_/_/_/_/    _/_/      _/_/       
 *                                                                          
 *
 *  Copyright 2013-2014, Geek Zoo Studio
 *  http://www.ecmobile.cn/license.html
 *
 *  HQ China:
 *    2319 Est.Tower Van Palace 
 *    No.2 Guandongdian South Street 
 *    Beijing , China
 *
 *  U.S. Office:
 *    One Park Place, Elmira College, NY, 14901, USA
 *
 *  QQ Group:   329673575
 *  BBS:        bbs.ecmobile.cn
 *  Fax:        +86-10-6561-5510
 *  Mail:       info@geek-zoo.com
 */

/* esq Gd by xuanyan <xunayan1983@gmail.com> */

class Gd
{
    public $im     = null;
    public $width  = null;
    public $height = null;
    public $type = null;

    function __construct($data = null)
    {
        if (empty($data))
        {
            return true;
        }

        if (file_exists($data) && is_readable($data))
        {
            return $this->loadFile($data);
        }
        elseif (is_resource($data) && get_resource_type($data) == "gd")
        {
            return $this->loadResource($data);
        }
        else
        {
            return $this->loadData($data);
        }
    }

    public static function creat($width, $height, $color = '#FFFFFF')
    {
        $thumb = imagecreatetruecolor($width, $height);
        $color = ltrim($color, '#');
        if (strlen($color) == 3)
        {
            $color .= $color;
        }
        sscanf($color, "%2x%2x%2x", $red, $green, $blue);
        imagefilledrectangle($thumb, 0, 0, $width, $height, imagecolorallocate($thumb, $red, $green, $blue));

        return new self($thumb);
    }

    public function loadResource($im)
    {
        if (!is_resource($im) || !get_resource_type($im) == "gd")
        {
            return false;
        }

        $this->im     = $im;
        $this->width  = imagesx($im);
        $this->height = imagesy($im);

        return true;
    }

    public function loadData($filedata)
    {
        $im = imagecreatefromstring($filedata);

        return $this->loadResource($im);
    }

    public function loadFile($filename)
    {
        if (!file_exists($filename) || !is_readable($filename))
        {
            return false;
        }

        $info = getimagesize($filename);
        $type = image_type_to_extension($info[2], false);
        $this->type = $type;
        if ($type == "jpeg" && (imagetypes() & IMG_JPG))
        {
            $im = imagecreatefromjpeg($filename);
        }
        elseif ($type == "png" && (imagetypes() & IMG_PNG))
        {
            $im = imagecreatefrompng($filename);
            //imageSaveAlpha($im, true);
            //imagealphablending($im, false);
        }
        elseif ($type == "gif" && (imagetypes() & IMG_GIF))
        {
            $im = imagecreatefromgif($filename);
        }
        else
        {
            return false;
        }

        return $this->loadResource($im);
    }

	//重定图片大小
    public function resize($new_width, $new_height)
    {

		
		$dest = imagecreatetruecolor( $new_width, $new_height ); 
		
		//如果是支持透明颜色的GIF, PNG图片, 就在此做特殊处理
		if ( ($this->type == "png") || ($this->type == "gif") ) 
		{ 
			//将打开的原图像$this->im中的透明色设置为color
			$trnprt_indx = imagecolortransparent($this->im);
			
			//根据$trnprt_indx的值判断是否有透明色, 如果有
			if ($trnprt_indx >= 0) 
			{
				//返回透明图片的RED, GREEN, BLUE 和 alpha 值
				$trnprt_color = imagecolorsforindex($this->im, $trnprt_indx);
				
				//为新图像分配与上面同样的颜色资源, RGB颜色
				$trnprt_indx = imagecolorallocate($dest, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				
				//透明颜色填充新图像
				imagefill($dest, 0, 0, $trnprt_indx);
				
				//设置新图像背景色为原透明色
				imagecolortransparent($dest, $trnprt_indx);		
			} elseif ($this->type == "png") {
				//关闭透明色混合模式(临时的)
				imagealphablending($dest, false);
				
				//(为新图像创建一个新的完全透明颜色), 最后一个值范围 0 - 127, 代表:不透明 - 完全透明
				$color = imagecolorallocatealpha($dest, 0, 0, 0, 127); 
				
				//用刚刚创建的新透明颜色作为背景
				imagefill($dest, 0, 0, $color); 
				
				//恢复混合的透明色	 
				imagesavealpha($dest, true); 
			}
		}
		
        if (imagecopyresampled($dest, $this->im, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height))
        {

            return $this->loadResource($dest);
        }

        return false;
    }

	//裁剪图片
    public function crop($x, $y, $w, $h)
    {
        $dest = imagecreatetruecolor($w, $h);
		
		if ( ($this->type == "png") || ($this->type == "gif") ) 
		{
			//将打开的原图像$this->im中的透明色设置为color
			$trnprt_indx = imagecolortransparent($this->im);
			
			//根据$trnprt_indx的值判断是否有透明色, 如果有
			if ($trnprt_indx >= 0) 
			{
				//返回透明图片的RED, GREEN, BLUE 和 alpha 值
				$trnprt_color = imagecolorsforindex($this->im, $trnprt_indx);
				
				//为新图像分配与上面同样的颜色资源, RGB颜色
				$trnprt_indx = imagecolorallocate($dest, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				
				//透明颜色填充新图像
				imagefill($dest, 0, 0, $trnprt_indx);
				
				//设置新图像背景色为原透明色
				imagecolortransparent($dest, $trnprt_indx);	
				
			} elseif ($this->type == "png") {
				//关闭透明色混合模式(临时的)
				imagealphablending($dest, false);
				
				//(为新图像创建一个新的完全透明颜色), 最后一个值范围 0 - 127, 代表:不透明 - 完全透明
				$color = imagecolorallocatealpha($dest, 0, 0, 0, 127); 
				
				//用刚刚创建的新透明颜色作为背景
				imagefill($dest, 0, 0, $color); 
				
				//恢复混合的透明色	 
				imagesavealpha($dest, true); 
			}
		}
		
        if (imagecopyresampled($dest, $this->im, 0, 0, $x, $y, $w, $h, $w, $h))
        {
            return $this->loadResource($dest);
        }

        return false;
    }

    public function merge(Gd $thumb, $left, $top)
    {
        imagealphablending($this->im, true);
        imageCopy($this->im, $thumb->im, $left, $top, 0, 0, $thumb->width, $thumb->height);

        return true;
    }

    public function merge_auto(Gd $thumb, $pct = 100)
    {
        $left = round(($this->width - $thumb->width) / 2);
        $top = round(($this->height - $thumb->height) / 2);
        imagealphablending($thumb->im, true);
        imagecopymerge($this->im, $thumb->im, $left, $top, 0, 0, $thumb->width, $thumb->height, $pct);
    }

    public function output($type = "jpeg", $quality = 80)
    {
        if ($type == "jpeg" && (imagetypes() & IMG_JPG))
        {            
            header("Content-Type: image/jpeg");
            imagejpeg($this->im, '', $quality);
            return true;
        }
        elseif ($type == "png" && (imagetypes() & IMG_PNG))
        {
            //header("Content-Type: image/png");
            imagepng($this->im);
            return true;
        }
        elseif ($type == "gif" && (imagetypes() & IMG_GIF))
        {
            //header("Content-Type: image/gif");
            imagegif($this->im);
            return true;
        }
        else
        {
            return false;
        }
    }

    public function saveAs($filename, $type = "jpg", $quality = 90)
    {
        $dir = dirname($filename);
        if (!file_exists($dir))
        {
            @mkdir($dir, 0777, true);
        }
        if ($type == "jpg" && (imagetypes() & IMG_JPG))
        {
            return imagejpeg($this->im, $filename, $quality);
        }
        elseif ($type == "png" && (imagetypes() & IMG_PNG))
        {
            return imagepng($this->im, $filename, $quality);
        }
        elseif ($type == "gif" && (imagetypes() & IMG_GIF))
        {
            return imagegif($this->im, $filename);
        }
        else
        {
            return false;
        }
    }

    public function scale($new_width = null, $new_height = null)
    {
        if (!is_null($new_width) && is_null($new_height))
        {
            $new_height = round($new_width * $this->height / $this->width);
        }
        elseif (is_null($new_width) && !is_null($new_height))
        {
            $new_width = $this->width / $this->height * $new_height;
        }
        elseif(!is_null($new_width) && !is_null($new_height))
        {
            $width = round($new_height * $this->width / $this->height);
            if ($width > $new_width)
            {
                $new_height = round($new_width * $this->height / $this->width);
            }
            else
            {
                $new_width = $width;
            }
        }
        else
        {
            return false;
        }

        return $this->resize($new_width, $new_height);
    }

    public function scale_fill($new_width = null, $new_height = null)
    {
        if (!is_null($new_width) && is_null($new_height))
        {
            $new_height = $new_width;
        }
        elseif (is_null($new_width) && !is_null($new_height))
        {
            $new_width = $new_height;
        }

        $sw = $new_width;
        $sh = $new_height;

        $width = round($new_height * $this->width / $this->height);
        if ($width < $new_width)
        {
            $new_height = round($new_width * $this->height / $this->width);
        }
        else
        {
            $new_width = $width;
        }

        $this->resize($new_width, $new_height);
        $x = ($this->width - $sw) / 2;
        $y = ($this->height - $sh) / 2;

        $this->crop($x, $y, $sw, $sh);

        return true;
    }
}
?>