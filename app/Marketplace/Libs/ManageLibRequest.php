<?php

namespace App\Marketplace\Libs;

use App\User;
use Carbon\Carbon;
use App\Utilities\Emails\Email;
use App\General\UploadManyFiles;
use Illuminate\Http\JsonResponse;
use App\Marketplace\Studio\Studio;
use App\Marketplace\Libs\LibRequest;
use App\Marketplace\Libs\LibPlanUser;
use App\Exceptions\LibRequestException;

class ManageLibRequest
{
    public function create($data, $user)
    {
        // check plan
        // $plan = $user->libPlanUsers()->where('is_active', 1)->whereIn('lib_plan_id', [ 2, 4 ])->first();

        // if (!$plan) {
        //     throw new LibRequestException('PRO_YEARLY_PLAN_REQUIRED');
        // }

        // // set the [from, to] date checking existing group according to the plan billing date.
        // $billingFrom = Carbon::now()->day($plan->billing_day);
        // $billingTo = Carbon::now()->day($plan->billing_day)->addMonth(1);
        // $group = LibRequestGroup::whereBetween('created_at', [$billingFrom, $billingTo])->where('user_id', $user->id)->first();

        // if ($group) { // check quota
        //     if ($group->requests()->count() >= 3) {
        //         throw new LibRequestException('MAX_MONTHLY_REQUEST_QUOTA_REACHED');
        //     }
        // } else { // store new group
        //     $group = LibRequestGroup::forceCreate([
        //         'user_id' => $user->id
        //     ]);
        // }
        $reqQuota = (new self())->getQuota($user);
        // if (!$reqQuota['legitimate_plan']) {
        //     throw new LibRequestException('PRO_YEARLY_PLAN_REQUIRED');
        // }
        if (!$reqQuota['legitimate_plan']) {
            throw new LibRequestException('YEARLY_PLAN_REQUIRED');
        }
        if (!$reqQuota['quota']) {
            throw new LibRequestException('MAX_QUOTA_REACHED');
        }
        // if (LibRequestGroup::whereNull('notified_at')->where('user_id', $user->id)->exists()) {
        //     throw new LibRequestException('HAS_OUTSTANDING_REQUEST');
        // }
        $group = LibRequestGroup::forceCreate([
            'user_id' => $user->id
        ]);

        // store the request
        $request       = new LibRequest();
        // $request->message = $data['message'];
        $request->name               = $data['name'];
        $request->age                = $data['age'];
        $request->briefing           = $data['briefing'];
        $request->business           = $data['business'];
        $request->color_limit        = $data['color_limit'];
        $request->country            = $data['country'];
        $request->gender             = $data['gender'];
        $request->number             = $data['number'];
        $request->other_style        = $data['other'];
        $request->product            = $data['product'];
        $request->style              = $data['style'];
        $request->tpx                = $data['tpx'];
        $request->website            = $data['website'];

        $request->lib_request_group_id = $group->id;

        $request->save();

        if (isset($data['files']) && $data['files']) {
            (new UploadManyFiles($data['files']))->to($request)->save('name');
        }

        return $request;
    }

    public function getQuota ($user) {
        $legitPlan = LibPlanUser::where('user_id', $user->id)->whereHas('libPlan', function ($plan) {
            $plan->whereIn('key', ['pro_yearly', 'starter_yearly']);
        })->whereIn('status', [LibPlanUser::IS_ENDING, LibPlanUser::IS_STARTED])->first();

        // or free access
        if (!$legitPlan) {
            $legitPlan = LibPlanUser::where('user_id', $user->id)->whereIn('status', [LibPlanUser::IS_ENDING, LibPlanUser::IS_STARTED, LibPlanUser::IS_TRIAL])->first();
        }
        $max = 0;
        $usage = 0;
        if ($legitPlan) {
            $perPlanQuota = $legitPlan->libPlan->key === 'pro_yearly' ? 10 : 5;
            if ($legitPlan->status === LibPlanUser::IS_TRIAL) {
                $perPlanQuota = 9999;
            }
            $max = $perPlanQuota;
            // breaking case: Alipay user pay 2 years forward and it's counting latest year
            $date = Carbon::parse($legitPlan->next_billing_at ? : $legitPlan->payment_required_until);
            $end = $date->copy();
            $start = $date->subYear();
            while ($start->gt(Carbon::now())) { // make sure user pre-paid a few years, count more
                $start = $date->subYear();
                $max += $perPlanQuota;
            }
            $usage = LibRequest::whereHas('group', function ($group) use ($user) {
                $group->where('user_id', $user->id);
            })->whereIn('status', [ LibRequest::IS_PENDING, LibRequest::IS_APPROVED ])->whereBetween('created_at', [$start, $end])->count();
            // dd([$start, $end]);
        }
        return [
            'legitimate_plan' => $legitPlan,
            'max' => $max,
            'quota' => max(0, $max - $usage)
        ];
    }
}