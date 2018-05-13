<?php

class ImageException extends Exception {
    
}


class ImageHandle {
    
    /**
     * 图片资源
     * @var null
     */
    private $resource = null;
    
    /**
     * 图片二进制流
     * @var null
     */
    private $stream = null;
    
    
    public function __construct($str) {
        gc_enable();
        $this->getImgResource($str);
    }
    
    /**
     * 获取图片资源
     * @param $urlOrFilePath
     * @return resource
     */
    private function getImgResource($urlOrFilePath) {
        try {
            $stream         = file_get_contents($urlOrFilePath);
            $this->resource = imagecreatefromstring($stream);
            unset($stream);
        } catch (ImageException $e) {
            echo $e;
        }
    }
    
    /**
     * @param $to_width
     * @param $to_height
     * @return $this
     */
    public function scale($to_width, $to_height) {
        $resource = $this->resource;
        $s_width  = imagesx($resource);
        $s_height = imagesy($resource);
        if (!$to_width || !$to_height) {
            $to_width  = $s_width;
            $to_height = $s_height;
        }
        $to_height = $s_height * $to_width / $s_width;
        $bg        = imagecreatetruecolor($to_width, $to_height);
        imageantialias($bg, true);
        $color = imagecolorallocate($bg, 222, 222, 222);
        imagefill($bg, 0, 0, $color);
        imagecolortransparent($bg, $color);
        imagecopyresampled($bg, $resource, 0, 0, 0, 0, $to_width, $to_height, $s_width, $s_height);
        $this->resource = $bg;
        unset($bg);
        return $this;
    }
    
    
    /**
     * @param int $radius
     * @return $this
     */
    public function radius($radius = 15) {
        $resource = $this->resource;
        $w        = imagesx($resource);
        $h        = imagesy($resource);
        $img      = imagecreatetruecolor($w, $h);
        imagesavealpha($img, true);
        //拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $bg);
        imageantialias($img, true);
        $r = $radius; //圆 角半径
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($resource, $x, $y);
                if (($x >= $radius && $x <= ($w - $radius)) || ($y >= $radius && $y <= ($h - $radius))) {
                    //不在四角的范围内,直接画
                    imagesetpixel($img, $x, $y, $rgbColor);
                } else {
                    //在四角的范围内选择画
                    //上左
                    $y_x = $r; //圆心X坐标
                    $y_y = $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //上右
                    $y_x = $w - $r; //圆心X坐标
                    $y_y = $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //下左
                    $y_x = $r; //圆心X坐标
                    $y_y = $h - $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //下右
                    $y_x = $w - $r; //圆心X坐标
                    $y_y = $h - $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                }
            }
        }
        $this->resource = $img;
        unset($img);
        return $this;
    }
    
    /**
     * 裁剪
     * @param $x
     * @param $y
     * @param $width
     * @param $height
     * @return $this
     */
    public function cut($x, $y, $width, $height) {
        $resource    = $this->resource;
        $finalWidth  = $width;
        $finalHeight = $height;
        $newImage    = imagecreatetruecolor($finalWidth, $finalHeight);
        $fillColor   = imagecolorallocatealpha($newImage, 250, 250, 250, 127);
        imagefill($newImage, 0, 0, $fillColor);
        imageantialias($newImage, true);
        imagecolortransparent($newImage, $fillColor);
        imagecopyresampled($newImage, $resource, 0, 0, $x, $y, $finalWidth, $finalHeight, $width, $height);
        $this->resource = $newImage;
        unset($newImage);
        return $this;
    }
    
    
    /**
     * 旋转图片
     * @param $degree
     * @param null $r
     * @param null $g
     * @param null $b
     * @return $this
     */
    public function rotate($degree, $r = null, $g = null, $b = null) {
        $resource      = $this->resource;
        $isTransparent = false;
        if (is_null($r) || is_null($g) || is_null($b)) {
            $r             = $g = $b = 222;
            $isTransparent = true;
        }
        $color  = imagecolorallocate($resource, $r, $g, $b);
        $rotate = imagerotate($resource, $degree, $color);
        if ($isTransparent) {
            imagecolortransparent($rotate, $color);
        }
        $this->resource = $rotate;
        unset($rotate);
        return $this;
    }
    
    /**
     * 输出文件流
     * @return null
     */
    public function outPutByFileStream() {
        return $this->stream;
    }
    
    /**
     * 图片输出
     * @param null $r
     * @param null $g
     * @param null $b
     * @return $this
     */
    private function formatOutPut($r = null, $g = null, $b = null) {
        $resource      = $this->resource;
        $isTransparent = false;
        if (is_null($r) || is_null($g) || is_null($b)) {
            $r             = $g = $b = 222;
            $isTransparent = true;
        }
        $s_width  = imagesx($resource);
        $s_height = imagesy($resource);
        $dstImage = imagecreatetruecolor($s_width, $s_height);
        
        $color = imagecolorallocate($dstImage, $r, $g, $b);
        imagefill($dstImage, 0, 0, $color);
        if ($isTransparent) {
            imagecolortransparent($dstImage, $color);
        }
        imagecopyresampled($dstImage, $resource, 0, 0, 0, 0, $s_width, $s_height, $s_width, $s_height);
        ob_start();
        imagepng($dstImage);
        $this->stream = ob_get_contents();
        ob_clean();
        return $this;
    }
    
    /**
     * @param $filePath
     */
    public function outPut($filePath) {
        try {
            $this->formatOutPut();
            file_put_contents($filePath, $this->stream);
        } catch (ImageException $e) {
            echo $e;
        }
    }
}

$testImg = __DIR__ . '/kobe.jpeg';
$obj     = new ImageHandle($testImg);
$obj->cut(10, 10, 320, 250)->outPut("./kobe1.png");
$obj->radius(20)->outPut("./kobe2.png");
$obj->rotate(90)->outPut("./kobe3.png");
$obj->scale(200, 400)->outPut("./kobe4.png");
