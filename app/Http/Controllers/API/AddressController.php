<?php

namespace App\Http\Controllers\API;

use App\Constants\ErrorCodes;
use App\General\Address;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller {

    public function index(Request $request) {
        return Address::where('user_id', Auth::guard('api')->id())->get();
    }

    public function store(Request $request) {
        $userId = Auth::guard('api')->id();

        $address = new Address();
        $address->user_id = $userId;
        $address->first_name = $request->first_name ?? '---';
        $address->last_name = $request->last_name ?? '---';
        $address->company = $request->company ?? '---';
        $address->address1 = $request->address1;
        $address->address2 = $request->address2 ?? '---';
        $address->city = $request->city;
        $address->country = $request->country;
        $address->post_code = $request->post_code;
        $address->is_default = $request->is_default ?? false;
        $address->vat_number = $request->vat_number ?? '';
        $address->save();

        if ($request->is_default) {
            Address::where('user_id', $userId)->where('id', '!=', $address->id)->update(['is_default' => 0]);
        } else {
            if (!Address::where('user_id', $userId)->where('id', '!=', $address->id)->exists()) {
                $address->is_default = 1;
                $address->save();
            }
        }

        return $this->index($request);
    }

    public function update(Request $request, $id) {
        $address = Address::findOrFail($id);
        $userId = Auth::guard('api')->id();

        if ($address->user_id != $userId)
            return respondError(ErrorCodes::UNAUTHORIZED, Response::HTTP_UNAUTHORIZED);

        if ($address->is_default && !$request->is_default)
            return respondError(ErrorCodes::VALIDATION_FAILED, Response::HTTP_UNPROCESSABLE_ENTITY, 'You should have at least one default address');

        $address->first_name = $request->first_name;
        $address->last_name = $request->last_name;
        $address->company = $request->company;
        $address->address1 = $request->address1;
        $address->address2 = $request->address2;
        $address->city = $request->city;
        $address->country = $request->country;
        $address->post_code = $request->post_code;
        $address->is_default = $request->is_default;
        $address->vat_number = $request->vat_number;
        $address->save();

        if ($request->is_default) {
            Address::where('user_id', $userId)->where('id', '!=', $address->id)->update(['is_default' => '0']);
        }

        return $this->index($request);
    }

    public function delete(Request $request, $id) {
        $address = Address::findOrFail($id);

        if ($address->user_id != Auth::guard('api')->id())
            return respondError(ErrorCodes::UNAUTHORIZED, Response::HTTP_UNAUTHORIZED);

        $address->delete();

        return $this->index($request);
    }

}
