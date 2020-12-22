<?php

namespace App\General\Image;

use Intervention\Image\ImageManagerStatic as Image;

class Thumbnail
{

    public static function make($image, $size = 600)
    {
        $thumbnail = Image::make($image)
                     ->fit($size)
                     ->encode('jpg', 85);

        return $thumbnail;
    }
}
