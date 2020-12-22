<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Marketplace\Libs\Feed;
use App\Utilities\Filters\FeedFilter;

class CollectionsController extends Controller {

    public function index(FeedFilter $filter) {
        $feeds = Feed::filter($filter)->with('translations')->where('is_active', 1);
        $count = $filter->has('take') ? $filter->input('take') : 20;
        return $feeds->paginate($count);
    }

    public function show(FeedFilter $filter, $id) {
        $feeds = Feed::filter($filter)->with('translations')->with('designs.tags.translations')->where('is_active', 1);
        return $feeds->findOrFail($id);
    }

}
