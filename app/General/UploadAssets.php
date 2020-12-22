<?php

namespace App\General;

use App\Utilities\Storage\S3;
use Aws\S3\Exception\S3Exception;

class UploadAssets extends UploadFile
{
    /* Store This Asset (Design, Request, Zip) to a protected S3 Bucket */
    public function save($type = 'file')
    {

        $path = $this->instance->getUploadPath($type);
        $name = $this->makeFileName();
        $name = "{$name}.{$this->file->getClientOriginalExtension()}";

        $fullPath = $path . $name;

        $this->instance[$type] = $name;

        $this->instance->save();
        if (app()->environment('local', 'testing')) {
            $this->file->move(
                app()->basePath() . '/../cs/public/' . $path,
                $fullPath
            );
        } else {
            try {
                $bucket = getenv('S3_ASSETS');
                S3::boot($bucket)->put($fullPath, file_get_contents($this->file));
            } catch (S3Exception $e) {
                abort(400, 'Upload Error' . $e->getMessage());
            }
        }

        return $this->instance;

    }
}
