<?php

namespace App\Http\Controllers\API\Media;

use App\General\Image\Watermark;
use App\Http\Controllers\Controller;
use App\Marketplace\Designs\Design;
use App\Utilities\Storage\Bucket;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class MediaController extends Controller {

    public function preview(Request $request, $code) {
        $design = Design::where('code', $code)->first();

        $designName = trim($design->design_name);
        if (strtoupper(substr($designName, 0, 2)) != 'CS')
            $designName = 'CS'.$designName;

        $url = 'https://dev.collectionstock.com/api/v1/image/detail/design/'.$code;

        try {
            file_get_contents($url);
        } catch(\Exception $e) {
            $url = Bucket::readAssets(Bucket::getPath($design));
            $url = Watermark::insert($url, 'watermark_2000.png');
        }

        $canvas = Image::canvas(1000, 1080, '#ffffff');
        $image = Image::make($url)->fit(1000)->encode('png');
        $logo = Image::make(public_path('logo.png'))->fit(200, 70);

        $canvas->insert($image);
        $canvas->insert($logo, 'bottom-left', 15, 5);
        $canvas->text('collectionstock.com/'.$designName, 550, 1050, function($font) {
            $font->file(public_path('fonts/Open_Sans/OpenSans-Bold.ttf'));
            $font->size(25);
        });

        $name = 'Preview_'.$designName.'.jpg';

        $headers = [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename='. $name,
        ];
        $canvas = $canvas->encode('jpg');
        return response()->stream(function() use ($canvas) {
            echo $canvas;
        }, 200, $headers);
    }

}
