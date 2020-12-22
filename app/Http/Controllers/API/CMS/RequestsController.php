<?php

namespace App\Http\Controllers\API\CMS;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Marketplace\Designs\Design;
use App\Marketplace\Libs\Feed;
use App\Marketplace\Libs\LibRequest;
use App\Marketplace\Libs\LibRequestCollection;
use App\Marketplace\Libs\LibRequestDesign;
use App\Marketplace\Libs\LibUserDownload;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequestsController extends Controller {

    public function indexCollections(Request $request) {
        $libRequests = LibRequest::where('is_collection', 1)->orderBy('id', 'desc');
        if ($request->has('term'))
            $libRequests->where('status', $request->term);

        $count = $request->has('take') ? $request->input('take') : 20;
        return $libRequests->paginate($count);
    }

    public function showCollection(Request $request, $id) {
        return LibRequest::where('id', $id)->with('files')->with('group.user')->with('collections.moodboards')->first();
    }

    public function rejectCollection(Request $request, $id) {
        $libRequest = LibRequest::findOrFail($id);
        $libRequest->message = $request->message;
        $libRequest->status = LibRequest::IS_REJECTED;
        $libRequest->save();

        $libRequest->group->user->sendCollectionRequestRejectedNotification($libRequest);

        return respondOK();
    }

    public function approveCollection(Request $request, $id) {
        $feed = Feed::findOrFail($request->collectionID);

        if (!$feed->is_active)
            return respondError(ErrorCodes::VALIDATION_FAILED, Response::HTTP_UNPROCESSABLE_ENTITY, 'This collection is not active');

        $libRequest = LibRequest::findOrFail($id);
        $libRequest->status = LibRequest::IS_APPROVED;
        $libRequest->save();

        $libRequestCollection = new LibRequestCollection();
        $libRequestCollection->lib_request_id = $id;
        $libRequestCollection->collection_id = $feed->id;
        $libRequestCollection->save();

        $libRequest->group->user->sendCollectionRequestApprovedNotification();

        return respondOK();
    }


    public function indexExclusive(Request $request) {
        $libRequests = LibRequest::where('is_collection', 0)->orderBy('id', 'desc');
        if ($request->has('term'))
            $libRequests->where('status', $request->term);

        $count = $request->has('take') ? $request->input('take') : 20;
        return $libRequests->paginate($count);
    }

    public function showExclusive(Request $request, $id) {
        return LibRequest::where('id', $id)->with('files')->with('group.user')->with('designs')->first();
    }

    public function rejectExclusive(Request $request, $id) {
        $libRequest = LibRequest::findOrFail($id);
        $libRequest->message = $request->message;
        $libRequest->status = LibRequest::IS_REJECTED;
        $libRequest->save();

        $user = $libRequest->group->user;
        $quota = $user->quota;
        $quota->exclusive += 1;
        $quota->save();

        $libRequest->group->user->sendExclusiveRequestRejectedNotification($libRequest);

        return respondOK();
    }

    public function approveExclusive(Request $request, $id) {
        $design = Design::findOrFail($request->designID);
        if ($design->isSold())
            return respondError(ErrorCodes::VALIDATION_FAILED, Response::HTTP_UNPROCESSABLE_ENTITY, 'This design is already sold');
        if (!$design->isExclusive())
            return respondError(ErrorCodes::VALIDATION_FAILED, Response::HTTP_UNPROCESSABLE_ENTITY, 'This design is not exclusive');


        $libRequest = LibRequest::findOrFail($id);
        $libRequest->status = LibRequest::IS_APPROVED;
        $libRequest->save();

        $libRequestDesign = new LibRequestDesign();
        $libRequestDesign->lib_request_id = $id;
        $libRequestDesign->design_id = $design->id;
        $libRequestDesign->save();

        $user = $libRequest->group->user;
        $design->buyer_id = $user->id;
        $design->owner_id = $user->id;
        $design->save();

        $libUserDownload = new LibUserDownload();
        $libUserDownload->user_id = $user->id;
        $libUserDownload->design_id = $design->id;
        $libUserDownload->package = 'exclusive';
        $libUserDownload->save();

        $libRequest->group->user->sendExclusiveRequestApprovedNotification();

        return respondOK();
    }

}
