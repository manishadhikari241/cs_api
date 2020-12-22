<?php

namespace App\Utilities\Filters;

use App\Marketplace\Libs\LibPlanUser;

class UserFilter extends QueryFilter
{
    public function isSuperAdmin()
    {
        return $this->builder->where('is_super_admin', 1);
    }

    public function isRepresentative()
    {
        return $this->builder->where('is_representative', 1);
    }

    public function isPremium()
    {
        return $this->builder->where('is_premium', 1);
    }

    public function hasSubscribed($val = false)
    {
        if ($val == 'true') {
            return $this->builder->whereNotNull('customer_id');
        }
        return $this->builder->whereNull('customer_id');
    }

    public function hasReferrals($val = true)
    {
        if ($val == 'false') {
            return $this->builder->whereDoesntHave('referrals');
        }
        return $this->builder->whereHas('referrals');
    }

    public function hasTrialDownloads($val = true)
    {
        if ($val == 'false') {
            return $this->builder->whereDoesntHave('trialDownloads');
        }
        return $this->builder->whereHas('trialDownloads');
    }

    public function hasTrialBefore()
    {
        return $this->builder->whereHas('libPlanUsers', function ($query) {
                $query->whereNull('subscription_id')->where('is_granted', false);
            });
    }

    public function status($status = [])
    {
        $this->builder->whereHas('profile', function ($query) use ($status) {
            return $query->whereIn('status', $status);
        });
    }

    public function designers()
    {
        $this->builder->whereHas('profile', function ($query) {
            return $query->whereIn('status', [2, 3]);
        });
    }

    public function isActive($value)
    {
        if ($value == 'true') {
            return $this->builder->where('is_active', true);
        } else {
            return $this->builder->where('is_active', false);
        }
    }

    public function subscribe($value)
    {
        if ($value == 'true') {
            $this->builder->whereHas('profile', function ($query) use ($value) {
                return $query->where('subscribe', true);
            });
        } else {
            $this->builder->whereHas('profile', function ($query) use ($value) {
                return $query->where('subscribe', false);
            });
        }
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'profile'              => 'profile.country.translations',
            'country'              => 'country',
            'addresses'            => 'addresses',
            'products'             => 'designs',
            'designs'              => 'designs',
            'telex'                => 'telex',
            'groups'               => 'profile.creatorGroup',
            'admin_groups'         => 'groups',
            'plan'                 => 'plan.translations',
            'credit'               => 'credit',
            'creditUsage'          => 'creditUsage',
            'studio'               => 'studio.translations',
            'studios'              => 'studios.translations',
            'referrals'            => 'referrals',
            'trialDownloads'       => 'trialDownloads',
            'representative'       => 'representative',
            'referRepGroupReps'    => 'referrer.group.representatives.user',
            'referRepresentative'  => 'referrer.user',

            'referRepRoot'         => 'referrer.parent.parent.user',
            'referRepParent'       => 'referrer.parent.user',

            'distributor'          => 'distributor',
            'referDistributor'     => 'referDistributor.user',
            'premiumRequest'       => 'premiumRequest',
            'public_images'        => 'publicImages',
            'inactivation'         => 'inactivation',
            'paymentTt'            => 'paymentTt',
            'libPlanUser'          => 'libPlanUser',
            'libPlanUser.libPlan'  => 'libPlanUser.libPlan',
            'libPlanUsers.libPlan' => 'libPlanUsers.libPlan',
            'customer'             => 'customer',
            'logs'                 => 'logs.handler',
        ];
        $relations = [];
        foreach ($scopes as $key => $value) {
            if (isset($relatable[$value])) {
                array_push($relations, $relatable[$value]);
            }
        }
        return $this->builder->with($relations);
    }

    public function groups($groups = [])
    {
        $this->builder->whereHas('groups', function ($query) use ($groups) {
            return $query->whereIn('group_id', $groups);
        });
    }

    public function representativeId($id)
    {
        return $this->builder->where('representative_id', $id);
    }

    public function representativeParent($id)
    {
        return $this->builder->whereHas('referrer', function ($query) use ($id) {
            $query->whereHas('parent', function ($q) use ($id) {
                $q->where('id', $id);
            })->orWhere('id', $id);
        });
    }

    public function representativeRoot($id)
    {
        return $this->builder->whereHas('referrer', function ($query) use ($id) {
            $query->whereHas('parent', function ($q2) use ($id) {
                $q2->whereHas('parent', function ($q3) use ($id) {
                    $q3->where('id', $id);
                })->orWhere('id', $id);;
            })->orWhere('id', $id);;
        });
    }

    public function isTelex()
    {
        return $this->builder->whereHas('telex');
    }

    public function permissions($permissions = [])
    {
        $this->builder->whereHas('permissions', function ($query) use ($permissions) {
            return $query->whereIn('permission_id', $permissions);
        });
    }

    public function code($code)
    {
        $this->builder->whereHas('profile', function ($query) use ($code) {
            return $query->where('code', $code);
        });
    }

    public function email($email)
    {
        return $this->builder->where('email', 'like', "%{$email}%");
    }

    public function dateFrom($date)
    {
        return $this->builder->whereDate('created_at', '>=', $date);
    }

    public function dateTo($date)
    {
        return $this->builder->whereDate('created_at', '<=', $date);
    }

    public function dateBetween($data)
    {
        return $this->builder->whereDate('created_at', '>=', $data['0'])->whereDate('created_at', '<=', $data['1']);
    }

    public function lastLoginFrom($date)
    {
        return $this->builder->whereDate('last_login', '>=', $date);
    }

    public function lastLoginTo($date)
    {
        return $this->builder->whereDate('last_login', '<=', $date);
    }

    public function lastLoginBetween($data)
    {
        return $this->builder->whereDate('last_login', '>=', $data['0'])->whereDate('last_login', '<=', $data['1']);
    }

    public function createdFrom($date)
    {
        return $this->builder->whereDate('created_at', '>=', $date);
    }

    public function createdTo($date)
    {
        return $this->builder->whereDate('created_at', '<=', $date);
    }

    public function createdBetween($data)
    {
        return $this->builder->whereDate('created_at', '>=', $data['0'])->whereDate('created_at', '<=', $data['1']);
    }

    public function updatedFrom($date)
    {
        return $this->builder->whereDate('updated_at', '>=', $date);
    }

    public function updatedTo($date)
    {
        return $this->builder->whereDate('updated_at', '<=', $date);
    }

    public function updatedBetween($data)
    {
        return $this->builder->whereDate('updated_at', '>=', $data['0'])->whereDate('updated_at', '<=', $data['1']);
    }

    public function salesType($type)
    {
        switch ($type) {
            case 'or':
                return $this->builder->whereNull('representative_id')->whereDoesntHave('libPlanUsers');
                break;
            case 'om':
                return $this->builder->whereNull('representative_id')->whereHas('libPlanUsers');
                break;
            case 'sr':
                return $this->builder->whereNotNull('representative_id')->whereDoesntHave('libPlanUsers');
                break;
            case 'sm':
                return $this->builder->whereNotNull('representative_id')->whereHas('libPlanUsers');
                break;
        }
    }
    
    public function plan($plan = null)
    {
        $latest = LibPlanUser::latest()->groupBy('user_id')->get()->pluck('id')->all();
        if (!$plan) {
            
        } else if ($plan == 'free') {
        // inverted query for whereHas
            return $this->builder->whereNotNull('customer_id')->whereHas('libPlanUsers', function ($query) use ($plan) {
                    $query->whereNotNull('subscription_id')->orWhere('is_granted', true);
                }, '=', 0);
        } else if ($plan == 'have') {
            return $this->builder->whereNotNull('customer_id')->whereHas('libPlanUsers', function ($query) use ($plan) {
                    $query->whereNotNull('subscription_id')->orWhere('is_granted', true);
                });
        } else if ($plan == 'starter' || $plan == 'pro') {
            return $this->builder->whereNotNull('customer_id')->whereHas('libPlanUsers', function ($query) use ($plan, $latest) {
                    $query->whereIn('id', $latest)->whereHas('libPlan', function ($q) use ($plan) {
                        $q->where('group', $plan);
                    })->whereNotNull('subscription_id')->orWhere('is_granted', true);
                });
        } else {
            return $this->builder->whereNotNull('customer_id')->whereHas('libPlanUsers', function ($query) use ($plan, $latest) {
                    $query->whereIn('id', $latest)->where('lib_plan_id', $plan)->where(function ($q2) {
                        $q2->whereNotNull('subscription_id')->orWhere('is_granted', true);
                    });
                });
        }
    }

    public function planStatus($status = null)
    {
        $latest = LibPlanUser::latest()->groupBy('user_id')->get()->pluck('id')->all();
        if (!$status) {
            
        } else if ($status == 'is_granted') {
            return $this->builder->whereNotNull('customer_id')->whereHas('libPlanUsers', function ($query) use ($latest) {
                    $query->whereIn('id', $latest)->where('is_granted', true);
                });
        } else {
            if (!is_array($status)) {
                $status = [$status];
            }
            return $this->builder->whereNotNull('customer_id')->whereHas('libPlanUsers', function ($query) use ($status, $latest) {
                    $query->whereIn('id', $latest)->whereIn('status', $status);
                });
        }
    }

    public function planStartFrom($planstartfrom)
    {
        return $this->builder->whereHas('libPlanUsers', function ($q) use ($planstartfrom) {
            if (!$planstartfrom) {

            } else if ($planstartfrom) {
                return $q->whereDate('started_at', '>=', $planstartfrom);
            }
        });
    }

    public function planStartTo($planstartto)
    {
        // inverted query for whereHas
        return $this->builder->whereHas('libPlanUsers', function ($q) use ($planstartto) {
            if (!$planstartto) {

            } else if ($planstartto) {
                return $q->whereDate('started_at', '>=', $planstartto);
            }
        }, '=', 0);
    }

    public function source($source)
    {
        if ($source) {
            if ($source == "none") {
                $this->builder->whereNull('source');
            } else {
                $this->builder->where('source', $source);
            }
        }
    }
}
