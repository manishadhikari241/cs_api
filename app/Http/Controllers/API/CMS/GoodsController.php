<?php

namespace App\Http\Controllers\API\CMS;

use App\General\UploadFile;
use App\Http\Controllers\Controller;
use App\Marketplace\Goods\Good;
use App\Marketplace\Goods\GoodRequest;
use App\Marketplace\Goods\GoodsTranslation;
use App\Utilities\Storage\S3;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GoodsController extends Controller {

    public function indexGoods(Request $request) {
        $goods = Good::with(['translations', 'photos'])->whereNull('user_id')->orderBy('sort_order', 'asc');
        if ($request->has('all'))
            return $goods->get();
        if ($request->term)
            $goods = $goods->whereHas('translations', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->term.'%');
            });
        $count = $request->has('take') ? $request->input('take') : 20;
        return $goods->paginate($count);
    }

    public function showGood(Request $request, $id) {
        return Good::with(['translations', 'photos'])->whereNull('user_id')->find($id);
    }

    public function storeGood(Request $request) {
        $good = new Good();
        $good->sort_order = $request->sort_order;
        $good = (new UploadFile($request->file('image')))->to($good)->save('image');
        $goodTranslationEN = new GoodsTranslation();
        $goodTranslationEN->id = $good->id;
        $goodTranslationEN->lang = 'en';
        $goodTranslationEN->name = $request->nameEN;
        $goodTranslationEN->save();
        $goodTranslationCH = new GoodsTranslation();
        $goodTranslationCH->id = $good->id;
        $goodTranslationCH->lang = 'zh-CN';
        $goodTranslationCH->name = $request->nameCH;
        $goodTranslationCH->save();
        $good->save();

        return response()->json($good, 201);
    }

    public function updateGoodImage(Request $request, $id) {
        $good = Good::findOrFail($id);

        $fullPath = $good->getUploadPath().$good->image;
        $cloud  = S3::boot();
        try {
            $cloud->delete($fullPath);
        } catch (\Exception $e) {}

        $good = (new UploadFile($request->file('image')))->to($good)->save('image');
        $good->save();

        return respondOK();
    }

    public function updateGood(Request $request, $id) {
        $good = Good::findOrFail($id);
        $good->sort_order = $request->sort_order;
        GoodsTranslation::where('id', $id)->where('lang', 'en')->update(['name' => $request->nameEN]);
        GoodsTranslation::where('id', $id)->where('lang', 'zh-CN')->update(['name' => $request->nameCH]);
        $good->save();

        return respondOK();
    }

    public function deleteGood(Request $request, $id) {
        $good = Good::findOrFail($id);

        $fullPath = $good->getUploadPath().$good->image;
        $cloud  = S3::boot();
        try {
            $cloud->delete($fullPath);
        } catch (\Exception $e) {}

        GoodsTranslation::where('id', $id)->delete();

        $good->delete();

        return respondOK();
    }

    public function inexRequests(Request $request) {
        $goodRequests = GoodRequest::orderBy('id', 'desc')->with('good');
        if ($request->has('term')) {
            if ($request->term == '2')
                $goodRequests = $goodRequests->whereNotNull('good_id');
            else if ($request->term == '0')
                $goodRequests = $goodRequests->whereNull('good_id')->where('message', '');
            else
                $goodRequests->whereNull('good_id')->where('message', '!=', '');
        }
        return $goodRequests->paginate(20);
    }

    public function showRequest(Request $request, $id) {
        $goodRequest = GoodRequest::findOrFail($id);
        $goodRequest->user;
        $goodRequest->good;
        return $goodRequest;
    }

    public function approveRequest(Request $request, $id) {
        $goodRequest = GoodRequest::findOrFail($id);

        $good = new Good();
        $good->sort_order = 0;
        $good = (new UploadFile($request->file('image')))->to($good)->save('image');
        $goodTranslationEN = new GoodsTranslation();
        $goodTranslationEN->id = $good->id;
        $goodTranslationEN->lang = 'en';
        $goodTranslationEN->name = $goodRequest->name;
        $goodTranslationEN->save();
        $goodTranslationCH = new GoodsTranslation();
        $goodTranslationCH->id = $good->id;
        $goodTranslationCH->lang = 'zh-CN';
        $goodTranslationCH->name = $goodRequest->name;
        $goodTranslationCH->save();
        $good->save();

        $goodRequest->good_id = $good->id;
        $goodRequest->approved_at = Carbon::now();
        $goodRequest->save();

        $goodRequest->user->sendSimulatorRequestApprovedNotification();

        return respondOK();
    }

    public function rejectRequest(Request $request, $id) {
        $goodRequest = GoodRequest::findOrFail($id);
        $goodRequest->message = $request->message;
        $goodRequest->save();

        $quota = $goodRequest->user->quota;
        $quota->simulator += 1;
        $quota->save();

        $goodRequest->user->sendSimulatorRequestRejectedNotification($goodRequest);

        return respondOK();
    }

}
