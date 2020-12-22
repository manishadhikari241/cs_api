<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Marketplace\Common\TagsManager;
use App\Marketplace\Libs\FeedsManager;
use Illuminate\Http\Request;

class SearchController extends Controller {

    public function tagSuggestions(Request $request) {
        return (new TagsManager($request))->suggest($request->input('term'));
    }

    public function feedSuggestions(Request $request) {
        return (new FeedsManager($request))->suggest($request->input('term'));
    }

}
