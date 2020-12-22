<?php

namespace App\Http\Controllers\API;

use App\Constants\ErrorCodes;
use App\General\UploadFile;
use App\Http\Controllers\Controller;
use App\Marketplace\Goods\Good;
use App\Marketplace\Goods\GoodRequest;
use App\Utilities\Storage\S3;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class GoodsController extends Controller {

    public function index(Request $request) {
        $goods = Good::with(['translations', 'photos'])->whereNull('user_id')->orderBy('sort_order', 'ASC')->get();
        $goodRequests = Auth::guard('api')->check() ? GoodRequest::with(['good'])->where('user_id', Auth::guard('api')->id())->orderBy('id', 'asc')->get() : [];
        return ['goods' => $goods, 'requests' => $goodRequests];
    }

    public function store(Request $request) {
        $user = Auth::guard('api')->user();
        if (!$user->quota || !$user->quota->simulator)
            return respondError(ErrorCodes::NOT_ENOUGH_QUOTA, Response::HTTP_UNAUTHORIZED, 'You don\'t have enough quota');

        $this->validate($request, [
            'name'    => 'max:256',
            'image'   => 'required|mimes:jpeg,jpg,png',
            'remarks' => 'max:99999'
        ]);

        $goodReq = new GoodRequest($request->only('name', 'remarks'));
        $goodReq->message = '';
        $goodReq->user_id = Auth::guard('api')->id();
        $goodReq = (new UploadFile($request->file('image')))->to($goodReq)->save('image');
        $goodReq->save();

        $user->quota->simulator = $user->quota->simulator-1;
        $user->quota->save();

        $goodReq->good;

        $user->sendSimulatorRequestReceivedNotification();

        return $goodReq;
    }

    public function delete(Request $request, $id) {
        $user = Auth::guard('api')->user();
        $goodReq = GoodRequest::findOrFail($id);
        if ($goodReq->user_id != $user->id)
            return respondError(ErrorCodes::UNAUTHORIZED, Response::HTTP_UNAUTHORIZED);
        if (!$goodReq->message)
            return respondError(ErrorCodes::UNKNOWN_ERROR, Response::HTTP_EXPECTATION_FAILED, 'Request is not rejected');
        
        $fullPath = $goodReq->getUploadPath().$goodReq->image;
        try {
            $bucket = getenv('S3_BUCKET');
            $cloud  = S3::boot();
            $cloud->delete($fullPath);
        } catch (Exception $e) {}
        
        $goodReq->delete();
        return respondOK();
    }

}
