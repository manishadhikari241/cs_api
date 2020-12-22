<?php

namespace App\Http\Controllers\API\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller {

    public function localImage(Request $request) {
        if ($request->has('url'))
            Storage::delete('public/'.$request->url);

        $image = $request->file('image');
        $fileName = time() . '.' . $image->getClientOriginalExtension();

        $image->storePubliclyAs('public/'.$request->get('folder', ''), $fileName);

        $finalURL = $request->has('folder') ? $request->folder.'/'.$fileName : $fileName;

        return ['url' => $finalURL];
    }

}
