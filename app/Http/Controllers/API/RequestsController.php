<?php

namespace App\Http\Controllers\API;

use App\Constants\ErrorCodes;
use App\General\UploadManyFiles;
use App\Http\Controllers\Controller;
use App\Http\Requests\DesignRequest;
use App\Http\Requests\FreeRequest;
use App\Marketplace\Libs\LibRequest;
use App\Marketplace\Libs\LibRequestGroup;
use App\Utilities\Filters\LibRequestFilter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RequestsController extends Controller {

    public function collection(LibRequestFilter $filter) {
        return LibRequest::filter($filter)->with('collections.moodboards')->where('is_collection', 1)->orderBy('id', 'asc')->paginate(50);
    }

    public function exclusive(LibRequestFilter $filter) {
        return LibRequest::filter($filter)->with('designs')->where('is_collection', 0)->orderBy('id', 'asc')->paginate(50);
    }

    public function hasPending(Request $request) {
        $user = Auth::guard('api')->user();

        $pendingRequests = LibRequest::where('is_collection', 1)->where('status', LibRequest::IS_PENDING)->whereHas('group', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        if ($pendingRequests->first())
            return respondError(ErrorCodes::TOO_MANY_REQUESTS, Response::HTTP_TOO_MANY_REQUESTS, 'You already have a pending request. You can make only one request per time');

        return true;
    }

    public function store(FreeRequest $request) {
        $data = $request->toArray();
        $user = Auth::guard('api')->user();

        $checkPending = $this->hasPending($request);
        if ($checkPending !== true)
            return $checkPending;

        try {
            $this->saveRequest($data, $user);
        } catch (\Exception $e) {
            throw new \PDOException('Error in saving Request' . $e->getMessage());
        }

        return $request;
    }




    public function get_exclusive(Request $request) {
        $user = Auth::guard('api')->user();

        $checkRequest = LibRequest::where('is_collection', 0)->whereHas('group', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();

        return \response()->json(['data' => $checkRequest], 200);
    }

    public function deleteExclusiveRejected(Request $request, $id) {
        $libRequest = LibRequest::findorfail($id);
        $libRequest->delete();
        return \response()->json(['message' => 'Your Design has been deleted'], 200);
    }

    public function deleteCollection(Request $request, $id) {
        $libRequest = LibRequest::findorfail($id);
        $libRequest->delete();
        return \response()->json(['message' => 'Your Collection has been deleted'], 200);
    }

    private function saveRequest($data, $user) {
        $group = LibRequestGroup::forceCreate([
            'user_id' => $user->id
        ]);

        $request = new LibRequest();
        $request->is_collection = $data['is_collection'];
        $request->name = $data['name'];
        $request->age = $data['age'];
        $request->briefing = $data['briefing'];
        $request->business = $data['business'];
        // $request->color_limit = $data['color_limit'];
        $request->country = $data['country'];
        $request->gender = $data['gender'];
        $request->number = 0;
        $request->other_style = $data['other'];
        $request->product = $data['product'];
        $request->style = $data['style'];
        $request->tpx = $data['tpx'];
        $request->website = $data['website'];
        $request->lib_request_group_id = $group->id;
        $request->save();

        if (isset($data['files']) && $data['files']) {
            (new UploadManyFiles($data['files']))->to($request)->save('name');
        }

        return $request;
    }

    public function storeExclusive(DesignRequest $request) {
        $data = $request->toArray();
        $user = Auth::guard('api')->user();

        $quota = $user->quota;

        if ($data['is_collection'] == 0) {
            if (!$quota || !$quota->exclusive)
                return respondError(ErrorCodes::NOT_ENOUGH_QUOTA, Response::HTTP_UNAUTHORIZED, 'You don\'t have enough quota');
        }

        try {
           $save= $this->saveRequest($data, $user);
        } catch (\Exception $e) {
            throw new \PDOException('Error in saving Request' . $e->getMessage());
        }

        $quota->exclusive -= 1;
        $quota->save();

        $user->sendExclusiveRequestReceivedNotification($save);

        return $request;
    }

}
