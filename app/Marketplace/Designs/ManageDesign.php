<?php

namespace App\Marketplace\Designs;

use App\User;
use App\Setting;
use Carbon\Carbon;
use App\General\UploadAssets;
use App\Marketplace\Common\Tag;
use App\Utilities\Emails\Email;
use Illuminate\Http\JsonResponse;
use App\Marketplace\Shopping\MemberCart;
use App\Marketplace\Shopping\MemberList;
use App\Marketplace\Common\TagsTranslation;

class ManageDesign
{
    public function update($design, $data)
    {
        // $design->update(['is_licensing' => $data['is_licensing']]);
        if (isset($data['is_exclusive_view'])) {
            $design->update(['is_exclusive_view' => $data['is_exclusive_view']]);
        }
        if (isset($data['design_name'])) {
            $design->update(['design_name' => $data['design_name']]);
        }
        if (isset($data['price'])) {
            $design->update(['price' => $data['price']]);
        }
        if (isset($data['licence_price'])) {
            $design->update(['licence_price' => $data['licence_price']]);
        }
        if (isset($data['is_onshow'])) {
            $design->update(['is_onshow' => $data['is_onshow']]);
        }
        if (isset($data['has_eps'])) {
            $design->update(['has_eps' => $data['has_eps']]);
        }
        if (isset($data['has_pdf'])) {
            $design->update(['has_pdf' => $data['has_pdf']]);
        }
        if (isset($data['has_ai'])) {
            $design->update(['has_ai' => $data['has_ai']]);
        }
        if (isset($data['has_jpg'])) {
            $design->update(['has_jpg' => $data['has_jpg']]);
        }
        if (isset($data['has_psd'])) {
            $design->update(['has_psd' => $data['has_psd']]);
        }
        if (isset($data['custom_file'])) {
            $design->custom_file = $data['custom_file'] !== 'null' ? $data['custom_file'] : null;
            $design->save();
        }
        $design->colors()->sync($data['colors'] ?? []);
        $this->findAndSyncTags($design, $data['tags'] ?? []);
        if (isset($data['image']) && $data['image']) {
            $design = (new UploadAssets($data['image']))->to($design)->save('image');
        }
        if (isset($data['file']) && $data['file']) {
            $design = (new UploadAssets($data['file']))->to($design)->save('file');
        }
        if (isset($data['licence_type']) && $data['licence_type'] && $data['licence_type'] !== $design->licence_type) {
            $this->changeLicenceType($design, $data['licence_type']);
        }
        if (isset($data['design_name']) && $data['design_name'] && $data['design_name'] !== $design->design_name) {
            $design->design_name = $data['design_name'];
            $design->save();
        }
        return $design;
    }

    public function publish($design, array $data = [])
    {
        $design              = $this->update($design, $data);
        // $design->status      = ($design->licence_type === 'non-exclusive' || (int) Setting::key('premium_days') === 0) ? Design::IS_APPROVED : Design::IS_PREMIUM_ONLY;
        $design->status       = Design::IS_PREMIUM_ONLY;
        $design->approved_at  = Carbon::now()->toDateTimeString();
        $design->published_at = $design->licence_type == 'non-exclusive'
                                ? Carbon::now()->toDateTimeString()
                                : Carbon::now()->addDays(Setting::key('premium_days'))->toDateTimeString();
        $design->save();
        $design->request->status = DesignRequest::IS_APPROVED;
        $design->request->save();
        $user = User::find($design->designer_id);
        (new Email('design-approved'))->send($user, ['design_id' => $design->id]);
        if ($design->request->project_code) {
            if ($design->studio->projects()->where('code', $design->request->project_code)->exists()) {
                (new ManageDesignRequest)->assignProject($design->request);
            }
        }
        if ($design->request->lib_month_id && $design->request->lib_category_id) {
            (new ManageDesignRequest)->assignLibrary($design->request, $data['lib_month_id'] ?? null, $data['lib_category_id'] ?? null);
        }
        return $design;
    }

    public function decline($design, $data = [])
    {
        $design->status = Design::IS_REJECTED;
        $design->save();
        $design->request->status  = DesignRequest::IS_REJECTED;
        $design->request->reason  = $data['reason']  ?? null;
        $design->request->message = $data['message'] ?? null;
        $design->request->save();
        //auto remove cart and list
        MemberCart::Where(['item' => $design->id, 'type' => 'product'])->orWhere('type', 'product-licence')->delete();
        $list = MemberList::with(['products' => function ($query) use ($design) {
            $query->where('product_id', $design->id);
        }])->get();
        foreach ($list as $list) {
            $list->products()->detach($design->id);
        }

        $user = User::find($design->designer_id);
        (new Email('design-declined'))->send($user, ['design_id' => $design->id, 'reason' => $data['reason'], 'message' => $data['message']]);
        return $design;
    }

    public function changeLicenceType($design, $type)
    {
        $this->checkSold($design);
        $this->checkLicensing($design);
        $design->licence_type = $type;
        $design->save();
        return $design;
    }

    protected function findAndSyncTags($design, $tags)
    {
        $tagIds = [];
        if (!is_array($tags) || !count($tags)) {
            return $design;
        }
        foreach ($tags as $tag) {
            $tag = TagsTranslation::tagify($tag);
            if ($tag) {
                $tagIds[count($tagIds)] = $this->findOrCreate($tag, $design);
            }
        }
        $design->tags()->sync($tagIds);
        return $design;
    }

    protected function findOrCreate($name, $design)
    {
        $tag = TagsTranslation::where('name', $name)->first();

        if (is_null($tag)) {
            $tag = Tag::forceCreate([
                'is_active'    => true,
                'is_exclusive' => $design->is_exclusive_view,
            ]);
            TagsTranslation::forceCreate([
                'id'   => $tag->id,
                'name' => $name,
                'lang' => $design->lang ?: 'en',
            ]);
        }

        return $tag->id;
    }

    public function pushPublic($design)
    {
        if ($design->status == Design::IS_PREMIUM_ONLY && $design->trends->count() == 0) {
            $design->status       = Design::IS_APPROVED;
            $design->published_at = Carbon::now()->toDateTimeString();
            $design->save();
            return $design;
        } else {
            return new JsonResponse(['message' => 'Design not found or cannot be approved'], 422);
        }
    }

    public function pushToPremium($design)
    {
        if ($design->status == Design::IS_APPROVED && $design->licence_type != 'non-exclusive') {
            $design->status       = Design::IS_PREMIUM_ONLY;
            $design->published_at = Carbon::now()->addDays(Setting::key('premium_days'))->toDateTimeString();
            $design->save();
            return $design;
        } else {
            return new JsonResponse(['message' => 'Design not found or cannot be pushed to premium'], 422);
        }
    }

    protected function checkLicensing(Design $design)
    {
        if ($design->is_licensing) {
            abort(442, 'DESIGN_IS_LICENSING');
        }
    }

    protected function checkSold(Design $design)
    {
        if ($design->status === Design::IS_SOLD || $design->buyer_id) {
            abort(442, 'DESIGN_IS_SOLD');
        }
    }
}
