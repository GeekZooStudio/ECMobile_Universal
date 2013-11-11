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

Class Imagick
{
    private $im = null;

    public function readImage($file)
    {
        $this->im = new Gd($file);

        return true;
    }
    
    public function cropThumbnailImage($w, $h)
    {
        $this->im->scale_fill($w, $h);
    }

    public function thumbnailImage($w, $h, $fit = false)
    {
        if ($fit) {
            $this->im->scale($w, $h);
        } else {
            if ($h) {
                $this->im->resize($w, $h);
            } else {
                $this->im->scale($w, $h);
            }
        }
    }

    public function getImageFormat()
    {
        return $this->im->type;
    }

    public function __toString()
    {
        ob_start();
        $this->im->output($this->im->type);

        return ob_get_clean();
    }
}

?>
