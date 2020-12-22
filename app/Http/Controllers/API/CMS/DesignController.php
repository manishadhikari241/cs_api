<?php

namespace App\Http\Controllers\API\CMS;

use App\Constants\ErrorCodes;
use App\General\UploadAssets;
use App\Http\Controllers\Controller;
use App\Marketplace\Designs\Design;
use App\Utilities\Filters\DesignFilter;
use App\Utilities\Storage\S3;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateDesignRequest;

class DesignController extends Controller {

    public function index(DesignFilter $filter) {
        $designs = Design::filter($filter)->with('tags');

        if ($filter->has('type') && strlen($filter->input('type')))
            $designs = $designs->where('licence_type', $filter->input('type'));

        $designs = $designs->paginate($filter->input('take') ?: 20);
        return $designs;
    }

    public function show(DesignFilter $filter, $id) {
        $design = Design::filter($filter)->where('id', $id)->with('colors.translations')->firstOrFail();
        return $design;
    }

    public function create(CreateDesignRequest $request) {
        $design = new Design();
        $design->design_name = $request->design_name;
        $design->designer_id = Auth::guard('api')->id();
        $design->code = $this->generateUniqueCode();
        $design->has_eps = $request->has_eps;
        $design->has_pdf = $request->has_pdf;
        $design->has_ai = $request->has_ai;
        $design->has_jpg = $request->has_jpg;
        $design->has_psd = $request->has_psd;
        $design->licence_type = $request->licence_type;
        $design->save();

        if ($request->tags)
            $design->tags()->sync(explode(',', $request->tags));
        if ($request->colors)
            $design->colors()->sync(explode(',', $request->colors));

        (new UploadAssets($request->file('image')))->to($design)->save('image');
        (new UploadAssets($request->file('file')))->to($design)->save('file');

        return response()->json($design, 201);
    }

    protected function generateUniqueCode() {
        $unique   = false;
        $attempts = 0;
        while (!$unique && $attempts < 100) {
            $attempts += 1;
            $code   = rand(0, 99999999);
            $code   = sprintf('%08d', $code);
            $unique = !Design::where('code', $code)->exists();
        }
        return $code;
    }

    public function update(Request $request, $id) {
        $design = Design::findOrFail($id);
        $design->design_name = $request->design_name;
        $design->has_eps = $request->has_eps;
        $design->has_pdf = $request->has_pdf;
        $design->has_ai = $request->has_ai;
        $design->has_jpg = $request->has_jpg;
        $design->has_psd = $request->has_psd;
        $design->save();

        if ($request->tags)
            $design->tags()->sync($request->tags);
        if ($request->colors)
            $design->colors()->sync($request->colors);

        return $design;
    }

    public function updateImage(Request $request, $id) {
        $design = Design::findOrFail($id);

        $fullPath = $design->getUploadPath().$design->image;
        $bucket = getenv('S3_ASSETS');

        try {
            S3::boot($bucket)->delete($fullPath);
        } catch (Exception $e) {

        }

        (new UploadAssets($request->file('image')))->to($design)->save('image');

        return respondOK();
    }

    public function updateFile(Request $request, $id) {
        $design = Design::findOrFail($id);

        $fullPath = $design->getUploadPath().$design->file;
        $bucket = getenv('S3_ASSETS');

        try {
            S3::boot($bucket)->delete($fullPath);
        } catch (Exception $e) {

        }

        (new UploadAssets($request->file('file')))->to($design)->save('file');

        return respondOK();
    }

}
