<?php

namespace App\General\Image;

use Intervention\Image\ImageManagerStatic as Image;

class EmailHeader
{
    public static function make($file)
    {
        $cvs = Image::canvas(600, 180);
        $design = Image::make($file)->resize(300, 300);
        $cvs->insert($design);
        $cvs->insert($design, 'top-right');
        $path = app()->path . '/General/Image/email_logo.png';
        $logo = Image::make($path);
        $cvs->insert($logo, 'center');
        $cvs->encode('jpg', 85);

        return $cvs;
    }
}
