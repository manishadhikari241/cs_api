<?php

namespace App\Utilities\Storage;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class S3
{
    /* boot a specific bucket */
    /* assets.collectionstock.com for protected desgins */
    /* public.collectionstock.com for public available uploads */
    public static function boot($bucket = null)
    {
        $client = new S3Client([
            'credentials' => [
                'key'    => getenv('S3_KEY'),
                'secret' => getenv('S3_SECRET'),
            ],
            'region'      => getenv('S3_REGION'),
            'version'     => 'latest',
        ]);
        $adapter    = new AwsS3Adapter($client, $bucket ?: getenv('S3_BUCKET'));
        $filesystem = new Filesystem($adapter);
        return $filesystem;
    }
}
