<?php

namespace App\Utilities\Filters;

use App\User;
use Carbon\Carbon;
use App\Utilities\Stats;
use App\Marketplace\Common\Category;
use Illuminate\Support\Facades\Auth;
use App\Marketplace\Libs\LibPlanUser;
use App\Marketplace\Common\TagsTranslation;
use App\Marketplace\Designs\ColorsTranslation;
use App\Marketplace\Libs\Feed;
use App\Marketplace\Designs\Design;
use Illuminate\Support\Facades\DB;

class DesignFilter extends QueryFilter
{
    protected $cachedTagIds = null;

    public function min_price($minPrice = 1)
    {
        return $this->builder->where('price', '>=', $minPrice);
    }

    public function latest()
    {
        return $this->builder->orderBy('created_at', 'desc');
    }

    public function max_price($maxPrice = 999999)
    {
        return $this->builder->where('price', '<=', $maxPrice);
    }

    public function keywords($keyword = '')
    {
        return $this->keyword($keyword);
    }

    public function keyword($keyword = '')
    {
        if (!$keyword) {
            return null;
        }

        mb_internal_encoding('UTF-8');
        $keyword  = urldecode($keyword);
        $keyword  = mb_strtolower($keyword);
        $keyword  = ltrim($keyword);
        $keyword  = rtrim($keyword);
        $funcName = $this->guessKeywordType($keyword);
        if (method_exists($this, $funcName)) {
            call_user_func_array([$this, $funcName], [$keyword]);
        }
    }

    protected function guessKeywordType($keyword)
    {
        if (strpos($keyword, '@') !== false) {
            if (Auth::guard('api')->user()->role_id > 0) {
                return 'creatorEmail';
            }
        }
        $this->cachedTagIds = TagsTranslation::where('name', $keyword)->get()->pluck('id');
        if (!$this->cachedTagIds->count()) {
            return 'name';
        }
        return 'tag';
    }

    public function color($color = '')
    {
        // $color = ColorsTranslation::where('name', $keyword)->first()->color;
        $colorId = ColorsTranslation::where('name', strtolower($color))->firstOrFail()->id;
        $this->builder->whereHas('colors', function ($query) use ($colorId) {
            return $query->where('color_id', $colorId);
        });
        // return $this->builder->inRandomOrder();
    }

    public function colors($colors = [])
    {
        $this->builder->whereHas('colors', function ($query) use ($colors) {
            return $query->where('color_id', $colors);
        });
    }

    public function tag($tag)
    {
        $tagIds = $this->cachedTagIds ?: TagsTranslation::where('name', $tag)->get()->pluck('id');
        $this->builder
            ->whereHas('tags', function ($query) use ($tagIds) {
                return $query->whereIn('tag_id', $tagIds);
            });
    }

    public function name($name)
    {
        $this->builder->where('design_name', 'LIKE', "%{$name}%")->orWhere('code', 'LIKE', "%{$name}%");
    }

    public function creatorEmail($email)
    {
        if ($email) {
            return $this->builder->whereHas('designer', function ($query) use ($email) {
                return $query->where('email', 'LIKE', "%{$email}%");
            });
        }
    }

    public function customId($id)
    {
        return $this->builder->where('custom_id', 'LIKE', '%' . $id . '%');
    }

    public function designName($name)
    {
        return $this->builder->where('design_name', 'LIKE', '%' . $name . '%');
    }

    public function tags($tags = [])
    {
        if (!is_array($tags)) {
            $tags = [$tags];
        }
        $tagIds = TagsTranslation::whereIn('name', $tags)->pluck('id');
        $this->builder->whereHas('tags', function ($query) use ($tagIds) {
            return $query->whereIn('tag_id', $tagIds);
        });
    }

    public function designer($code)
    {
        $designer = User::whereHas('profile', function ($query) use ($code) {
            return $query->where('code', $code);
        })->first();
        return $this->builder->where('designer_id', $designer->id ?? 0);
    }

    public function inCategory()
    {
        $tagIds = Category::pluck('tag_id');
        return $this->builder->whereHas('tags', function ($query) use ($tagIds) {
            return $query->whereIn('tag_id', $tagIds);
        });
    }

    public function isLicensing($state)
    {
        return $this->builder->where('is_licensing', filter_var($state, FILTER_VALIDATE_BOOLEAN));
    }

    public function licenceType($type = [])
    {
        if (!is_array($type)) {
            $type = [$type];
        }
        return $this->builder->whereIn('licence_type', $type);
    }

    public function studioId($id)
    {
        return $this->builder->where('studio_id', $id);
    }

    public function isOnShow($state)
    {
        return $this->builder->where('is_onshow', filter_var($state, FILTER_VALIDATE_BOOLEAN));
    }

    public function notInTrend()
    {
        return $this->builder->whereDoesntHave('trends', null);
    }

    public function code($code)
    {
        if (!$code) {
            return null;
        }
        $code = strtolower($code);
        if (substr($code, 0, 2) === 'cs') {
            $code = substr($code, 2);
        }
        return $this->builder->where('code', $code);
    }

    public function codes($codes)
    {
        if (!$codes) {
            return null;
        }
        return $this->builder->whereIn('code', $codes);
    }

    public function price($price)
    {
        if ($price) {
            return $this->builder->where('price', $price);
        }
    }

    public function licencePrice($price)
    {
        if ($price) {
            return $this->builder->where('licence_price', $price);
        }
    }

    public function dateFrom($date)
    {
        if ($date) {
            return $this->builder->whereDate('approved_at', '>=', $date);
        }
    }

    public function dateTo($date)
    {
        if ($date) {
            return $this->builder->whereDate('approved_at', '<=', $date);
        }
    }

    public function dateBetween($data)
    {
        return $this->builder->whereDate('approved_at', '>=', $data['0'])->whereDate('approved_at', '<=', $data['1']);
    }

    public function purchasedFrom($date)
    {
        if ($date) {
            return $this->builder->whereDate('purchased_at', '>=', $date);
        }
    }

    public function purchasedTo($date)
    {
        if ($date) {
            return $this->builder->whereDate('purchased_at', '<=', $date);
        }
    }

    public function purchasedBetween($data)
    {
        return $this->builder->whereDate('purchased_at', '>=', $data['0'])->whereDate('purchased_at', '<=', $data['1']);
    }

    public function approvedFrom($date)
    {
        if ($date) {
            return $this->builder->whereDate('approved_at', '>=', $date);
        }
    }

    public function approvedTo($date)
    {
        if ($date) {
            return $this->builder->whereDate('approved_at', '<=', $date);
        }
    }

    public function approvedBetween($data)
    {
        return $this->builder->whereDate('approved_at', '>=', $data['0'])->whereDate('approved_at', '<=', $data['1']);
    }

    public function recentlyDownloaded()
    {
        $user = Auth::guard('api')->user();
        $downloadedList = $user->libDownloads()->where('is_active', 1)->pluck('design_id');
        return $this->builder->whereIn('id', $downloadedList);

//        $now        = Carbon::now();
//        $billingDay = LibPlanUser::currentPlan()->billing_day;
//        $start      = Carbon::now()->day($billingDay);
//        $end        = Carbon::now()->day($billingDay);
//        if ($now->day < $billingDay) {
//            $start = $start->subMonth();
//        } else {
//            $end = $end->addMonth();
//        }
//        // dd($start->toDateString(), $end->toDateString());
//        $user = Auth::user();
//        if ($user->is_trial) {
//            $downloadedList = $user->trialDownloads()->whereBetween('created_at', [
//                $start->toDateString(), $end->toDateString(),
//            ])->pluck('design_id');
//        } else {
//            $downloadedList = $user->libDownloads()->where('is_active', 1)->whereBetween('created_at', [
//                $start->toDateString(), $end->toDateString(),
//            ])->pluck('design_id');
//        }
//        return $this->builder->whereIn('id', $downloadedList);
    }

    public function downloaded()
    {
        return $this->builder->whereIn('id', Auth::user()->libDownloads()->pluck('design_id'));
    }

    public function libMonthId(int $id)
    {
        return $this->builder->whereHas('libMonth', function ($q) use ($id) {
            $q->where('lib_month_id', $id);
        });
    }

    public function inLibRequests(bool $yes)
    {
        if ($yes) {
            return $this->builder->whereHas('libRequests');
        }
    }

    public function libCategoryId(int $id)
    {
        return $this->builder->whereHas('libCategory', function ($q) use ($id) {
            $q->where('lib_category_id', $id);
        });
    }

    // exclude ids from non-published feeds
    public function onlyPublished()
    {
        $nonPublishedFeeds = Feed::where('is_active', 0)->get();
        $designIds = DB::table('feed_design')->whereIn('feed_id', $nonPublishedFeeds->pluck('id'))->get()->pluck('design_id');
        return $this->builder->whereNotIn('id', $designIds);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'keywords'                           => 'tags.translations',
            'tags'                               => 'tags.translations',
            'buyer'                              => 'buyer',
            'owner'                              => 'owner',
            'colors'                             => 'colors.translations',
            'freeDesign'                         => 'freeDesign',
            'designer'                           => 'designer',
            'designer.profile'                   => 'designer.profile',
            'request'                            => 'request',
            'request.libMonth'                   => 'request.libMonth',
            'request.libCategory'                => 'request.libCategory.translations',
            'projects'                           => 'projects.translations',
            'projects.items'                     => 'projects.items.designs',
            'projectItems'                       => 'projectItems',
            'trends'                             => 'trends',
            'libMonth'                           => 'libMonth',
            'libMonth.libMonth'                  => 'libMonth.libMonth',
            'libMonth.libCategory'               => 'libMonth.libCategory.translations',
            'studio'                             => 'studio.translations',
        ];
        $relations = [];
        foreach ($scopes as $key => $value) {
            if (isset($relatable[$value])) {
                array_push($relations, $relatable[$value]);
            }
        }
        return $this->builder->with($relations);
    }
}
