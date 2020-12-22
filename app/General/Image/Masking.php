<?php

namespace App\General\Image;

use App\Utilities\Storage\Bucket;
use Intervention\Image\ImageManagerStatic as Image;

class Masking
{

    public static function insert($image, $good)
    {
        $image   = Image::make($image);
        $path    = Bucket::getPath($good);
        $file    = Bucket::readFile($path);
        $masking = Image::make($file);
        $masking->fit(600);
        $image->insert($masking, 'center');

        return $image->encode()->encoded;
    }
}
