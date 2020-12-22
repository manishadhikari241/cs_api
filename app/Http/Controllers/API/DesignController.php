<?php

namespace App\Http\Controllers\API;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Marketplace\Designs\Design;
use App\Utilities\Filters\DesignFilter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DesignController extends Controller {

    public function index(DesignFilter $filter) {
        $designs = Design::filter($filter)->with('tags');
        $designs = $designs->where('licence_type', '!=', 'exclusive')->whereNull('buyer_id')->whereNull('owner_id');
        $designs = $designs->paginate($filter->input('take') ?: 20);
        return $designs;
    }

    public function show(DesignFilter $filter, $design) {
        $user = Auth::guard('api')->user();
        $design = Design::filter($filter)->where('code', $design)->firstOrFail();

        if ($design->isExclusive() && (!$user || $design->buyer_id != $user->id)) {
            return respondError(ErrorCodes::NOT_FOUND, Response::HTTP_NOT_FOUND, 'Design not found');
        }

        return $design;
    }

    public function tags(Request $request, $id) {
        $design = Design::findOrFail($id);
        $design->tags;
        foreach ($design->tags as $tag)
            $tag->translations;
        return $design->tags;
    }

}
