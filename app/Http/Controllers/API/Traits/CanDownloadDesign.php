<?php

namespace App\Http\Controllers\API\Traits;

use App\Constants\ErrorCodes;
use App\Utilities\Storage\Bucket;
use App\Utilities\Storage\ZipAsset;
use Illuminate\Http\Response;

trait CanDownloadDesign
{
    protected function downloadDesign($user, $design, $package)
    {
        $path = Bucket::getPath($design, '', ($package == 'standard' ? 'image' : 'file'));
        try {
            $file = Bucket::readAssets($path);
        } catch (\Exception $e) {
            return respondError(ErrorCodes::NOT_FOUND, Response::HTTP_NOT_FOUND, 'File not on Dev environment');
        }

        if ($package == 'standard') {
            $licensedZip = new ZipAsset($file, $design->design_name);
        } else {
            $licensedZip = new ZipAsset($file);
        }

        if ($package == 'standard') {
            $licensedZip->addTrialLicence($user);
        } else if ($package == 'exclusive') {
            $licensedZip->addExclusiveOwnership($user);
        } else {
            $licensedZip->addLicence($user);
        }
        return response()
            ->make($licensedZip->dump())
            ->header('Content-Type', 'application/zip')
            ->header('Content-Disposition', 'attachment; filename="'. $design->design_name .'.zip"');
    }
}
