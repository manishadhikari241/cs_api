<?php

namespace App\General\Image;

use Intervention\Image\ImageManagerStatic as Image;

class Watermark
{

    public static function insert($image, $watermark = 'watermark.png')
    {
        $image = Image::make($image);
        $path = app()->path . '/General/Image/' . $watermark;
        $watermark = Image::make($path);
        $image->insert($watermark, 'center');

        return $image->encode()->encoded;
    }

    public static function noWaterMark($image)
    {
        $image = Image::make($image);
        return $image->encode()->encoded;
    }
}
