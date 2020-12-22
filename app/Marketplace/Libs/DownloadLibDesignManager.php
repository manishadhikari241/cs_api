<?php

namespace App\Marketplace\Libs;

use App\User;
use Carbon\Carbon;
use App\Marketplace\Libs\TrialDownload;
use App\Exceptions\LibDownloadException;

class DownloadLibDesignManager
{
    protected $design;

    public function __construct($design)
    {
        $this->design = $design;
    }

    public function authorize($user)
    {
        // find design lib month
        $libMonthDesign = $this->findLibMonthDesign();

        // find user lib month, only the active ones.
        // $libMonthUser = $this->findLibMonthUser($user, $libMonthDesign);

        // starter, pro, business design get any designs

        // $plan = $libMonthUser->libPlan;
        // // make sure user lib month group matches design group
        // if (!$libMonthDesign->basic && $plan->group !== 'pro') {
        //     throw new LibDownloadException('INVALID_SUBSCRIPTION');
        // }

        // $download = $this->addDownloadRecord($libMonthUser, $user);
        $download = $this->addDownloadRecord($user);
        return !!$download;
    }

    public function enableTrialDownload($user)
    {
        // find previous download
        $params =[
            'user_id'          => $user->id,
            'design_id'        => $this->design->id
        ];
        $download = TrialDownload::where($params)->first();
        $this->design->increment('downloads', 1);
        
        // if ($download) {
        //     return true;
        // }

        if (self::getTrialDownloadQuota($user) < 1) {
            throw new LibDownloadException('EXCEED_TRIAL_DOWNLOAD_QUOTA');
        }

        // check quota
        // if () {
        //     throw new LibDownloadException('EXCEED_PLAN_DOWNLOAD_QUOTA');
        // }

        $this->design->increment('pseudo_downloads', 200);

        $download = TrialDownload::forceCreate($params);

        return !!$download;
    }

    public static function getDownloadQuotaLastMonth($user, LibPlanUser $libPlanUser)
    {
        $quota   = $libPlanUser->status === $libPlanUser::IS_TRIAL ? 0 : $libPlanUser->libPlan->quota;
        $start   = Carbon::now()->day($libPlanUser->billing_day)->subMonth()->toDateString();
        $end     = Carbon::now()->day($libPlanUser->billing_day)->toDateString();
        // dd($start, $end);
        $usage   = LibUserDownload::where(['user_id' => $user->id])
                ->whereBetween('created_at', [$start, $end])
                ->where('is_active', 1)
                ->count();
        return $quota - $usage;
    }

    public static function getDownloadQuota($user, LibPlanUser $libPlanUser)
    {
        $quota   = in_array($libPlanUser->status, [$libPlanUser::IS_TRIAL, $libPlanUser::IS_GRACE_PERIOD]) ? 0 : $libPlanUser->libPlan->quota;
        $start   = Carbon::now()->day($libPlanUser->billing_day);
        $end     = Carbon::now()->day($libPlanUser->billing_day);
        if (Carbon::now()->day < $start->day) {
            $start = $start->subMonth();
        } else {
            $end = $end->addMonth();
        }
        $start   = $start->toDateString();
        $end     = $end->toDateString();
        // dd($start, $end);
        $usage   = LibUserDownload::where(['user_id' => $user->id])
                ->whereBetween('created_at', [$start, $end])
                ->where('is_active', 1)
                ->count();
        // dd($usage, $quota);
        return $quota - $usage;
    }

    public function findPreviousDownload($user, $trashed = false)
    {
        $download = LibUserDownload::where(['user_id' => $user->id, 'design_id' => $this->design->id]);
        if ($trashed) { // when add record, show all, find it then reactivate it
            $download->withTrashed();
        } else { // normal, dont show inactive
            $download->where('is_active', 1);
        }
        return $download->first();
    }

    /**
     * @return Boolean
     * OK: Has that record
     * ERROR: Total monthly count >= plan quota
     * OK: otherwise
     */
    public function addDownloadRecord($user)
    {
        $download = $this->findPreviousDownload($user);

        $this->design->increment('downloads', 1);

        if ($download) {
            return $download;
        }
        $libPlanUser = LibPlanUser::where('user_id', $user->id)->where('is_active', 1)->latest()->firstOrFail();
        if ($this::getDownloadQuota($user, $libPlanUser) < 1) {
            throw new LibDownloadException('EXCEED_PLAN_DOWNLOAD_QUOTA');
        }

        $this->design->increment('pseudo_downloads', 200);
        $trashed = $this->findPreviousDownload($user, $trashed = true);
        if ($trashed) {
            $trashed->deleted_at = null;
            $trashed->is_active  = 1;
            $trashed->save();
            return $trashed;
        }
        $download = LibUserDownload::forceCreate([
            'user_id'          => $user->id,
            'design_id'        => $this->design->id
        ]);
        return $download;
    }

    protected function findLibMonthDesign()
    {
        return LibMonthDesign::where('design_id', $this->design->id)->firstOrFail();
    }

    protected function findLibMonthUser($user, $libMonthDesign)
    {
        return LibMonthUser::where('user_id', $user->id)
        ->where('lib_month_id', $libMonthDesign->lib_month_id)
        ->where('is_active', 1)
        ->firstOrFail();
    }

    // free user gets trial downloads
    public static function getTrialDownloadQuota($user)
    {
        $base    = 10;
        $rewards = User::where('referral_id', $user->id)->where('is_active', 1)->count() * 10; // share to friends to get 20 reward
        $usage   = TrialDownload::where(['user_id' => $user->id])->count();
        return $base + $rewards - $usage;
    }
}
