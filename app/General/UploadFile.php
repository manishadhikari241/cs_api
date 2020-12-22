<?php

namespace App\General;

use App\Utilities\Storage\S3;
use Aws\S3\Exception\S3Exception;
use Illuminate\Http\UploadedFile;

// use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFile
{
    protected $instance;
    protected $file;

    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    public function to($instance)
    {
        $this->instance = $instance;
        return $this;
    }

    public function save($type = 'portfolio')
    {
        $path                  = $this->instance->getUploadPath($type);
        $name                  = $this->makeFileName();
        $name                  = "{$name}.{$this->file->getClientOriginalExtension()}";
        $fullPath              = $path . $name;
        $this->instance[$type] = $name;

        if ($type != 'content') {
            $this->instance->save();
        }

        $this->store($path, $fullPath);

        return $this->instance;
    }

    // copy to target destination but do not save instance
    public function copy($type = 'portfolio', $name = null)
    {
        $path                  = $this->instance->getUploadPath($type);
        if (!$name) {
            $name                  = $this->makeFileName();
            $name                  = "{$name}.{$this->file->getClientOriginalExtension()}";
        }
        $fullPath              = $path . $name;

        $this->store($path, $fullPath);

        return $this->instance;
    }

    public function store($path, $fullPath)
    {
        try {
            $bucket = getenv('S3_BUCKET');
            $cloud  = S3::boot();
            $cloud->put($fullPath, file_get_contents($this->file));
        } catch (S3Exception $e) {
            abort(400, 'Upload Error' . $e->getMessage());
        }
    }

    // protected function makePhoto()
    // {
    //     return new GoodsPhoto(['name' => $this->makeFileName()]);
    // }

    protected function makeFileName()
    {
        $name = sha1(
            time() .
            $this->file->getClientOriginalName()
        );

        return $name;
    }
}
