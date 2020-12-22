<?php

namespace App\General;

use App\Utilities\Storage\S3;
// use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Http\UploadedFile;

class UploadManyFiles
{
    protected $instance;
    protected $files;

    /* input a Array of uploaded File here */
    public function __construct(Array $files)
    {
        $this->files = $files;
    }

    public function to($instance, $relation = 'files')
    {
        $this->instance = $instance;
        $this->relation = $relation;
        return $this;
    }

    public function save($type = 'name')
    {
        foreach ($this->files as $key => $file) {
            $path = $this->instance->getUploadPath($type);
            $name = $this->makeFileName($file);
            $name = "{$name}.{$file->getClientOriginalExtension()}";

            $fullPath = $path . $name;

            $fileRelation = call_user_func([$this->instance, $this->relation]);
            $fileRelation->create([
                $type => $name
            ]);

            if (app()->environment('local', 'testing')) {
                // $contents = file_get_contents($file);
                $file->move(
                    app()->basePath() . '/../cs/public/' . $path,
                    $fullPath
                );
            } else {
                try {
                    $cloud = S3::boot();
                    $cloud->put($fullPath, file_get_contents($file));
                } catch(S3Exception $e){
                    abort(400, 'Upload Error' . $e->getMessage());
                }
            }
        }
        return $this->instance;
    }


    // protected function makePhoto()
    // {
    //     return new GoodsPhoto(['name' => $this->makeFileName()]);
    // }

    protected function makeFileName($file)
    {
        $name = sha1(
            time().
            $file->getClientOriginalName()
        );

        return $name;
    }
}
