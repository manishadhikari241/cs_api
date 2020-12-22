<?php

namespace App\Http\Controllers\API\CMS;

use App\Constants\ErrorCodes;
use App\General\UploadFile;
use App\Http\Controllers\Controller;
use App\Marketplace\Designs\Design;
use App\Marketplace\Libs\Feed;
use App\Marketplace\Libs\FeedsMoodboard;
use App\Marketplace\Libs\FeedsTranslation;
use App\Utilities\Filters\FeedFilter;
use App\Utilities\Storage\S3;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CollectionsController extends Controller {

    public function index(FeedFilter $filter) {
        $feeds = Feed::filter($filter)->with('translations');
        $count = $filter->has('take') ? $filter->input('take') : 20;
        return $feeds->paginate($count);
    }

    public function show(FeedFilter $filter, $id) {
        $feeds = Feed::filter($filter)->with('translations')->with('designs.tags.translations');
        return $feeds->findOrFail($id);
    }

    public function create(Request $request) {
        $feed = new Feed();
        $feed->designer_id = Auth::guard('api')->id();
        $feed->is_active = 1;
        $feed->season_id = $request->season;
        $feed->save();

        $feedTranslationEN = new FeedsTranslation();
        $feedTranslationEN->id = $feed->id;
        $feedTranslationEN->lang = 'en';
        $feedTranslationEN->title = $request->titleEN;
        $feedTranslationEN->description = $request->descriptionEN;
        $feedTranslationEN->save();

        $feedTranslationCH = new FeedsTranslation();
        $feedTranslationCH->id = $feed->id;
        $feedTranslationCH->lang = 'zh-CN';
        $feedTranslationCH->title = $request->titleCH;
        $feedTranslationCH->description = $request->descriptionCH;
        $feedTranslationCH->save();

        $feed->designs()->sync(explode(',', $request->designs));
        $feed->categories()->sync(explode(',', $request->categories));
        $feed->goods()->sync(explode(',', $request->goods));

        if ($request->file('moodboardEN')) {
            $mood = FeedsMoodboard::forceCreate([
                'feed_id' => $feed->id,
                'lang' => 'en'
            ]);
            (new UploadFile($request->file('moodboardEN')))->to($mood)->save('moodboard');
        }

        if ($request->file('moodboardCH')) {
            $mood = FeedsMoodboard::forceCreate([
                'feed_id' => $feed->id,
                'lang' => 'zh-CN'
            ]);
            (new UploadFile($request->file('moodboardCH')))->to($mood)->save('moodboard');
        }

        return response()->json($feed, 201);
    }

    public function toggle(Request $request, $id) {
        $feed = Feed::findOrFail($id);
        $feed->is_active = $feed->is_active ? 0 : 1;
        $feed->save();

        return respondOK();
    }

    public function update(Request $request, $id) {
        $feed = Feed::findOrFail($id);

        $feed->season_id = $request->season;
        $feed->save();

        FeedsTranslation::where('id', $id)->where('lang', 'en')->update(['title' => $request->titleEN]);
        FeedsTranslation::where('id', $id)->where('lang', 'zh-CN')->update(['title' => $request->titleCH]);
        FeedsTranslation::where('id', $id)->where('lang', 'en')->update(['description' => $request->descriptionEN]);
        FeedsTranslation::where('id', $id)->where('lang', 'zh-CN')->update(['description' => $request->descriptionCH]);

        $feed->categories()->sync($request->categories);
        $feed->goods()->sync($request->goods);

        return respondOK();
    }

    public function addDesign(Request $request, $id) {
        $design = Design::findOrFail($request->designId);
        if ($design->isExclusive() || $design->isSold())
            return respondError(ErrorCodes::VALIDATION_FAILED, Response::HTTP_UNPROCESSABLE_ENTITY, 'Exclusive designs can not be linked to a collection');

        $feed = Feed::findOrFail($id);
        $designs = $feed->designs;
        $ids = [];

        foreach ($designs as $design) {
            array_push($ids, $design->id);
        }
        array_push($ids, $request->designId);

        $feed->designs()->sync($ids);

        return respondOK();
    }

    public function removeDesign(Request $request, $id, $designId) {
        $feed = Feed::findOrFail($id);
        $designs = $feed->designs;
        $ids = [];

        foreach ($designs as $design) {
            if ($designId != $design->id)
                array_push($ids, $design->id);
        }

        $feed->designs()->sync($ids);

        return respondOK();
    }

    public function addMoodboard(Request $request, $id) {
        $moodboard = FeedsMoodboard::where('feed_id', $id)->where('lang', $request->lang)->first();
        if ($moodboard)
            $this->deleteMoodboard($request, $id, $moodboard->id);

        $mood = FeedsMoodboard::forceCreate([
            'feed_id' => $id,
            'lang' => $request->lang
        ]);
        (new UploadFile($request->file('moodboard')))->to($mood)->save('moodboard');

        return respondOK();
    }

    public function deleteMoodboard(Request $request, $id, $moodboardId) {
        $moodboard = FeedsMoodboard::findOrFail($moodboardId);

        $fullPath = $moodboard->getUploadPath().$moodboard->moodboard;

        try {
            $bucket = getenv('S3_BUCKET');
            $cloud  = S3::boot();
            $cloud->delete($fullPath);
        } catch (Exception $e) {}

        $moodboard->delete();

        return respondOK();
    }

}
