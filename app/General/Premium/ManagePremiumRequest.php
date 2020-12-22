<?php

namespace App\General\Premium;

use App\Marketplace\Studio\StudioAccess;
use App\Marketplace\Studio\Studio;

class ManagePremiumRequest
{
    public function create($data)
    {
        $request       = new PremiumRequest();
        $request->name = $data['name'];
        // $request->message = $data['message'];
        $studio                            = Studio::find($data['studio_id'] ?? 1);
        $request->company_name             = $data['company_name'];
        $request->design_per_year          = $data['design_per_year'];
        $request->business                 = $data['business'];
        $request->specify                  = $data['specify'];
        $request->website                  = $data['website'];
        $request->employees                = $data['employees'];
        $request->internal_designers       = $data['internal_designers'];
        // $request->exclusive_design_detail  = $data['exclusive_design_detail'];
        // $request->consider_you             = $data['consider_you'];
        $request->motivate_short           = $data['motivate_short'];
        $request->country_id               = $data['country_id'];
        $request->user_id                  = \Auth::id();
        $request->studio_id                = $studio->id;
        $request->save();

        $hasAccess = StudioAccess::where([
            'user_id'   => \Auth::id(),
            'studio_id' => $data['studio_id']
        ])->exists();

        $user = \Auth::user();
        if (!$user->first_name && !$user->last_name) {
            $user->username = $data['name'];
            $user->save();
        }

        if (!$hasAccess) {
            $access = StudioAccess::request(\Auth::user(), $studio);
        }
        return $request;
    }
}
