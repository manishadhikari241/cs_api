<?php

namespace App\Http\Controllers\API;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateListRequest;
use App\Http\Requests\ShareListRequest;
use App\Mail\ShareList;
use App\Marketplace\Shopping\MemberList;
use App\Utilities\Filters\MemberListFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ListController extends Controller {

    public function viewByToken(MemberListFilter $filter, $token) {
        return MemberList::filter($filter)->where(['view_token' => $token])->firstOrFail();
    }

    public function create(CreateListRequest $request) {
        $list = MemberList::where('name', $request->name)->where('user_id', Auth::guard('api')->id())->first();
        if ($list) return $list;
        $list = MemberList::forceCreate([
            'name'       => $request->input('name'),
            'user_id'    => Auth::guard('api')->id(),
            'view_token' => Str::random(20),
        ]);
        $list->load('products.designer.profile');
        return $list;
    }

    public function addProduct(Request $request, $id) {
        $list = MemberList::where(['id' => $id, 'user_id' => Auth::guard('api')->id()])->firstOrFail();
        if (!$list->products()->where('product_id', $request->design_id)->exists()) {
            $list->products()->attach([$request->design_id => [
                'usage' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]]);
        }
        $list->load('products.designer.profile');
        return $list;
    }

    public function removeProduct(Request $request, $listId, $designId) {
        $list = MemberList::where(['id' => $listId, 'user_id' => Auth::guard('api')->id()])->first();
        $list->products()->detach($designId);
        $list->load('products.designer.profile');
        return $list;
    }

    public function delete(Request $request, $id) {
        $list = MemberList::where(['id' => $id, 'user_id' => Auth::guard('api')->id()])->firstOrFail();
        $list->delete();
        return respondOK();
    }

    public function share(ShareListRequest $request, $list) {
        $emails   = $request->input('emails');
        $name    = $request->input('name');
        $message = $request->input('message');

        $emails = array_unique($emails);
        foreach ($emails as $email) {
            Mail::to($email)->queue(new ShareList($list, Auth::guard('api')->user(), $email, $name, $message));
        }
        
        return respondOK();
    }

    public function update(Request $request, $id) {
        $list = MemberList::where(['id' => $id, 'user_id' => Auth::guard('api')->id()])->firstOrFail();
        if (!$request->has('name') || !strlen($request->name))
            return respondError(ErrorCodes::VALIDATION_FAILED, Response::HTTP_UNPROCESSABLE_ENTITY, 'Name is required');

        $list->name = $request->name;
        $list->save();

        return respondOK();
    }

}
