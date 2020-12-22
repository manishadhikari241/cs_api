<?php

namespace App\Http\Controllers\API;

use App\General\Image\Watermark;
use App\Http\Controllers\Controller;
use App\General\Image\Thumbnail;
use App\Marketplace\Designs\Design;
use App\Utilities\Storage\Bucket;

class ImageController extends Controller {

    private $proxyThumbnail = 'https://dev.collectionstock.com/api/v1/image/thumbnail/design';
    private $proxyDetail = 'https://dev.collectionstock.com/api/v1/image/detail/design';

    public function thumbnail($code) {
        return $this->makeThumbnail($code, 600);
    }

    public function tinyThumbnail($code) {
        return $this->makeThumbnail($code, 330);
    }

    public function largeThumbnail($code) {
        return $this->makeThumbnail($code, 900);
    }

    protected function makeThumbnail($code, $size) {
        $design = Design::where('code', $code)->first();
        if (!$design) { abort(404, 'DESIGN_NOT_FOUND'); }
        if ($design->image) {
            $path = Bucket::getPath($design);
            try {
                $file = Bucket::readAssets($path);
            } catch (\Exception $e) {
                $file = file_get_contents($this->proxyThumbnail.'/'.$code);
            }
            $image = Thumbnail::make($file, $size + 30)->crop($size, $size);
            $watermark = Watermark::insert($image, 'watermark_600.png');
            return response()->make($watermark)->header('Content-Type', 'image/jpg');
        }
        return $this->request($design->request->id, $size);
    }

    public function detail($code)
    {
        $design    = Design::where('code', $code)->first();
        $instance  = $design->image ? $design : $design->request;
        $path      = Bucket::getPath($instance);
        try {
            $file = Bucket::readAssets($path);
        } catch (\Exception $e) {
            $file = file_get_contents($this->proxyDetail.'/'.$code);
        }
        $watermark = Watermark::insert($file, 'watermark_2000.png');
        return response()->make($watermark)->header('Content-Type', 'image/jpg');
    }

}
