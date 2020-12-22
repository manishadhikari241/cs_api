<?php

namespace App\Utilities\Filters;

use App\Marketplace\Common\TagsTranslation;
use App\Marketplace\Goods\GoodsTranslation;
// use App\Marketplace\Libs\ApplyonsTranslation;
use App\Marketplace\Libs\LibCategoryTranslation;
use App\Marketplace\Designs\Design;

class FeedFilter extends QueryFilter
{
    protected $cachedApplyonIds;
    protected $cachedCategoryIds;
    protected $cachedGoodIds;
    protected $cachedTagIds;

    public function title($title)
    {
        if (!$title) {
            return null;
        }
        $this->builder->whereHas('translations', function ($query) use ($title) {
            return $query->where('title', 'like', "%{$title}%");
        });
    }

    public function isActive($value)
    {
        return $this->builder->where('is_active', $value);
    }

    public function seasonId($value)
    {
        if ($value == -1) {
            return $this->builder->where('season_id', null);
        }
        return $this->builder->where('season_id', $value);
    }

    public function createdFrom($date)
    {
        return $this->builder->whereDate('created_at', '>=', $date);
    }

    public function createdTo($date)
    {
        return $this->builder->whereDate('created_at', '<=', $date);
    }

    public function publishedFrom($date)
    {
        return $this->builder->whereDate('published_at', '>=', $date);
    }

    public function publishedTo($date)
    {
        return $this->builder->whereDate('published_at', '<=', $date);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'user'          => 'user',
            'moodboards'    => 'moodboards',
            'applyons'      => 'applyons.translations',
            'designs'       => 'designs',
            'translation'   => 'feedTranslations',
            'categories'    => 'categories.translations',
            'tags'          => 'tags.translations',
            'goods'         => 'goods.translations',
            'season'        => 'season.translations',
        ];
        $relations = [];
        foreach ($scopes as $key => $value) {
            if (isset($relatable[$value])) {
                array_push($relations, $relatable[$value]);
            }
        }
        return $this->builder->with($relations);
    }

    public function keyword($keyword)
    {
        $catIds = $this->cachedCategoryIds ?: LibCategoryTranslation::where('name', $keyword)->get()->pluck('id');
        if ($catIds->count()) {
            return $this->builder->whereHas('categories', function ($query) use ($catIds) {
                return $query->whereIn('category_id', $catIds);
            });
        }

        // $applyonIds = $this->cachedApplyonIds ?: ApplyonsTranslation::where('name', $keyword)->get()->pluck('id');
        // if ($applyonIds->count()) {
        //     return $this->builder->whereHas('applyons', function ($query) use ($applyonIds) {
        //         return $query->whereIn('applyon_id', $applyonIds);
        //     });
        // }
        // otherwise search tag.
        $goodIds = $this->cachedGoodIds ?: GoodsTranslation::where('name', $keyword)->get()->pluck('id');
        if ($goodIds->count()) {
            return $this->builder->whereHas('goods', function ($query) use ($goodIds) {
                return $query->whereIn('good_id', $goodIds);
            });
        }


        $tagIds = $this->cachedTagIds ?: TagsTranslation::where('name', $keyword)->get()->pluck('id');

        $designIds = Design::whereHas('tags', function ($tags) use ($tagIds) {
            return $tags->whereIn('tag_id', $tagIds);
        })->pluck('id');

        return $this->builder->whereHas('designs', function ($designs) use ($designIds) {
            return $designs->whereIn('design_id', $designIds);
        });

        // else search for content
    }
}
