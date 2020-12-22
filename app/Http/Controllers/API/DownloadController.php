<?php

namespace App\Http\Controllers\API;

use App\Constants\ErrorCodes;
use App\Http\Controllers\API\Traits\CanDownloadDesign;
use App\Http\Controllers\API\Traits\CanManageTokens;
use App\Http\Controllers\Controller;
use App\Http\Requests\DownloadRequest;
use App\Marketplace\Designs\Design;
use App\Marketplace\Libs\LibUserDownload;
use App\Quota;
use App\User;
use App\Utilities\Filters\DesignFilter;
use App\Utilities\Filters\LibUserDownloadFilter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller {

    use CanManageTokens;
    use CanDownloadDesign;

    public function getUserDownloads(LibUserDownloadFilter $filter) {
        $downloads = LibUserDownload::where('user_id', Auth::guard('api')->id())->filter($filter)->with('design.tags.translations');
        return $downloads->paginate();
    }

    public function getNumberOfDownloads() {
        return ['count' => LibUserDownload::where('user_id', Auth::guard('api')->id())->count()];
    }

    public function downloadDetails(Request $request, $code, $pkg) {
        $user = Auth::guard('api')->user();
        $design = Design::where('code', $code)->first();
        if (!$design)
            return respondError(ErrorCodes::NOT_FOUND, Response::HTTP_NOT_FOUND);

        $consumes = $this->consumesQuota($user, $design, $pkg);
        $canDownload = $this->canDownload($user, $pkg);
        $token = $canDownload ? $this->generateToken()->token : '';
        return ['consumesQuota' => $consumes, 'canDownload' => $canDownload, 'token' => $token];
    }

    private function consumesQuota($user, $design, $pkg) {
        $previous = LibUserDownload::where('user_id', $user->id)->where('design_id', $design->id)->first();
        $consumeQuota = true;
        if ($design->isExclusive()) {
            if ($design->buyer_id == $user->id) {
                $consumeQuota = false;
            } else {
                return respondError(ErrorCodes::NOT_FOUND, Response::HTTP_NOT_FOUND, 'Design not found');
            }
        } else if ($previous && (!$previous->package || $previous->package === 'extended' || ($pkg === 'standard' && $previous->package === 'standard'))) {
            $consumeQuota = false;
        }
        return $consumeQuota;
    }

    private function canDownload($user, $pkg) {
        $quota = $user->quota;
        return $quota && $quota->{$pkg};
    }

    public function download(DownloadRequest $request, $code) {
        if (!$request->has('token'))
            return respondError(ErrorCodes::INVALID_CREDENTIALS, Response::HTTP_UNAUTHORIZED, 'missing token');
        $token = $this->authorizeToken($request->token);
        if (!$token)
            return respondError(ErrorCodes::INVALID_CREDENTIALS, Response::HTTP_UNAUTHORIZED, 'invalid token');

        $user = User::find($token->user_id);
        $design = Design::where('code', $code)->first();
        $pkg = $request->package;

        // if ($user->isAdmin())
        //     return $this->forceDownload($user, $design, $pkg);


        $previous = LibUserDownload::where('user_id', $user->id)->where('design_id', $design->id)->first();
        $consumeQuota = $this->consumesQuota($user, $design, $pkg);
        $canDownload = $this->canDownload($user, $pkg);

        if ($consumeQuota) {
            if (!$canDownload) {
                return respondError(ErrorCodes::NOT_ENOUGH_QUOTA, Response::HTTP_UNAUTHORIZED, 'You don\'t have enough quota');
            }

            $design->increment('downloads', 1);
            $design->increment('pseudo_downloads', 1);

            if (!$previous) {
                $libUserDownload = new LibUserDownload();
                $libUserDownload->user_id = $user->id;
                $libUserDownload->design_id = $design->id;
                $libUserDownload->package = $pkg;
                $libUserDownload->save();
            } else {
                if ($previous->package === 'standard' && $pkg === 'extended') {
                    $previous->package = 'extended';
                    $previous->save();
                }
            }

            $quota = $user->quota;
            $quota->{$pkg} = $quota->{$pkg} - 1;
            $quota->save();
        }

        return $this->forceDownload($user, $design, $pkg);
    }

    protected function forceDownload($user, $design, $package) {
        return $this->downloadDesign($user, $design, $package);
    }

}
