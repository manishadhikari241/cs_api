<?php

namespace App\Utilities\Storage;

use Illuminate\Http\Request;

class Bucket
{
    /* Correlate path without instance get path */
    public static function path($path)
    {
        if (app()->environment('testing')) {
            return 'fake_file';
        }
        if (app()->environment('local')) {
            return app()->path . "/../../cs/public/{$path}";
        }
        return $path;
    }

    /* Get the uploadable instance path */
    public static function getPath($instance, $subPath = '', $field = 'image')
    {
        if (app()->environment('testing')) {
        }
        if (app()->environment('local')) {
            return app()->path . "/../../cs/public/{$instance->getUploadPath($subPath)}{$instance->$field}";
        }
        return Bucket::getRealPath($instance, $subPath, $field);
    }

    /* Only Use it directly in Testing! */
    public static function getRealPath($instance, $subPath = '', $field = 'image')
    {
        return $instance->getUploadPath($subPath) . $instance->$field;
    }

    /* Get the Public upload File */
    public static function readFile($path)
    {
        if (app()->environment('testing')) {
            return 'fake_file';
        }
        if (app()->environment('local')) {
            return file_get_contents($path);
            // or redirect to live
        }
        return Bucket::readRealFile($path);
    }

    /* Only Use it directly in Testing! */
    public static function readRealFile($path)
    {
        return S3::boot(getenv('S3_PUBLIC'))->read($path);
    }

    /* Get the Protected Designs / Resources / ZIP */
    public static function readAssets($path)
    {
        if (app()->environment('testing')) {
            return 'fake_file';
        }
        if (app()->environment('local')) {
            // use to debug: if not found local, then fetch from live server
            try {
                return file_get_contents($path);
            } catch (\Exception $e) {
                return file_get_contents(getenv('LIVE_URL') . '/' . app('request')->path());
            }
        }
        if (app()->environment('staging')) {
            try {
                return Bucket::readRealAssets($path);
            } catch (\Exception $e) {
                return file_get_contents(getenv('LIVE_URL') . '/' . app('request')->path());
            }
        }
        return Bucket::readRealAssets($path);
    }

    /* Only Use it directly in Testing! */
    public static function readRealAssets($path)
    {
        try {
            $asset = S3::boot(getenv('S3_ASSETS'))->read($path);
        } catch (\Exception $e) {
            \Log::warning('not found @ path:' . $path . $e->getMessage());
            abort(404, 'ASSETS_NOT_FOUND');
        }
        return $asset;
    }
}
