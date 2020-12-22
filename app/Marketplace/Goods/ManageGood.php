<?php

namespace App\Marketplace\Goods;

use App\General\UploadFile;

class ManageGood
{
    public function create($data)
    {
        $good = new Good();

        if (isset($data['image']) && $data['image'] && is_file($data['image'])) {
            $good = (new UploadFile($data['image']))->to($good)->save('image');
        }
        $good->is_active      = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $good->is_purchasable = filter_var($data['is_purchasable'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if (isset($data['user_id']) && $data['user_id']) {
            $good->user_id = $data['user_id'];
        }
        $good->save();

        if (isset($data['name']) && !is_array($data['name'])) {
            $good->translations()->save(new GoodsTranslation([
                'id'          => $good->id,
                'name'        => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'lang'        => 'en']));
        } else {
            $this->updateName($good, $data['name'] ?? []);
            $this->updateDescription($good, $data['description'] ?? []);
        }

        return $good;
    }

    public function update($good, $data)
    {
        if (isset($data['name'])) {
            $this->updateName($good, $data['name']);
        }
        if (isset($data['description'])) {
            $this->updateDescription($good, $data['description']);
        }
        if (isset($data['image']) && $data['image'] && is_file($data['image'])) {
            $good = (new UploadFile($data['image']))->to($good)->save('image');
        }
        $good->is_active      = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $good->is_purchasable = filter_var($data['is_purchasable'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $good->region         = $data['region'] ?? 1;

        $good->save();

        return $good;
    }

    public function updateName($good, $names = [])
    {
        // dd($names);
        if (!is_array($names)) {
            $names = [$names];
        }
        $good->load('translations');
        foreach ($names as $key => $value) {
            $translation = $good->translations->where('id', $good->id)->where('lang', $key)->first();
            if (!$translation) {
                $good->translations()->save(new GoodsTranslation(['id' => $good->id, 'name' => $value, 'lang' => $key]));
            } else {
                GoodsTranslation::where(['id' => $good->id, 'lang' => $key])->update(['name' => $value]);
            }
        }
    }

    public function updateDescription($good, $descriptions = [])
    {
        if (!is_array($descriptions)) {
            $descriptions = [$descriptions];
        }
        $good->load('translations');
        foreach ($descriptions as $key => $value) {
            $translation = $good->translations->where('id', $good->id)->where('lang', $key)->first();
            if (!$translation) {
                $good->translations()->save(new GoodsTranslation(['id' => $good->id, 'description' => $value, 'lang' => $key]));
            } else {
                GoodsTranslation::where(['id' => $good->id, 'lang' => $key])->update(['description' => $value]);
            }
        }
    }
}
