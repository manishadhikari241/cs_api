<?php

namespace App\Marketplace\Designs;

use App\User;
use App\Setting;
use Carbon\Carbon;
use App\General\UploadAssets;
use App\Marketplace\Common\Tag;
use App\Utilities\Emails\Email;
use App\General\Premium\Project;
use App\Marketplace\Libs\LibMonth;
use App\Marketplace\Studio\Studio;
use App\General\Premium\ProjectItem;
use App\Marketplace\Libs\LibCategory;
use App\Marketplace\Libs\LibMonthDesign;
use App\Exceptions\DesignRequestException;
use App\Marketplace\Common\TagsTranslation;

class ManageDesignRequest
{
    public function submit($user, $data)
    {
        $isProjectRevise = isset($data['project_item_id']) && $data['project_item_id'];
        if ($isProjectRevise) {
            $designRequest = $this->usePreviousRequestValues($data);
        } else {
            $designRequest = new DesignRequest($data);
        }
        $designRequest->code         = str_random(10);
        $designRequest->licence_type = DesignRequest::licence_type[$data['licence_type'] ?? 0];
        $designRequest->user()->associate($user);

        $designRequest = (new UploadAssets($data['image']))->to($designRequest)->save('image');

        $designRequest = (new UploadAssets($data['file']))->to($designRequest)->save('file');

        $designRequest->save();

        $hasProjectCode = isset($data['project_code']) && $data['project_code'];
        if ($isProjectRevise || $hasProjectCode) {
            // @todo instant queue + approve
            $this->instantAssignToProjectItem($designRequest);
        }

        return $designRequest;
    }

    protected function usePreviousRequestValues($data)
    {
        $project           = Project::where('code', $data['project_code'])->first();
        if (!$project) {
            throw new DesignRequestException('WRONG_PROJECT_CODE');
        }
        $item              = $project->items()->find($data['project_item_id']);
        if (!$item) {
            throw new DesignRequestException('WRONG_PROJECT_ITEM');
        }
        $previousRequest            = $item->designs()->first()->request;
        $values                     = $previousRequest->toArray();
        $values['status']           = DesignRequest::IS_PENDING;
        $values['project_code']     = $project->code;
        $values['project_item_id']  = $data['project_item_id'];
        unset($values['id'], $values['created_at'], $values['updated_at']);

        return new DesignRequest($values);
    }

    public function queue($request)
    {
        if (!$request->queueable()) {
            abort(422, 'REQUEST_NOT_QUEUEABLE');
        }
        $request->status = DesignRequest::IS_WAITING_APPROVAL;
        $request->save();

        $user                               = User::find($request->user_id);
        $studio                             = Studio::where('user_id', $user->id)->latest()->first() ?: $user->studios()->latest()->first();

        $design                             = $request->design ?: (new Design()); // or find existing
        $design->design_name                = $request->design_name;
        $design->designer_id                = $request->user_id;
        $design->request_id                 = $request->id;
        $design->studio_id                  = $studio->id ?? 1;
        $design->code                       = $this->generateUniqueCode();
        $design->price                      = $request->price;
        $design->custom_id                  = $request->custom_id;
        $design->licence_type               = $request->licence_type;
        $design->licence_price              = $request->licence_price ?: $request->price * (float) Setting::key('licence_price');
        $design->status                     = 1;
        // you cannot pre set
        $design->raw_image                  = $request->image;
        // $design->file                       = $request->file;
        $design->custom_file                = $request->custom_file;
        $design->has_eps                    = $request->has_eps ?: 0;
        $design->has_pdf                    = $request->has_pdf ?: 0;
        $design->has_ai                     = $request->has_ai ?: 0;
        $design->has_jpg                    = $request->has_jpg ?: 0;
        $design->has_psd                    = $request->has_psd ?: 0;
        $design->is_exclusive_view          = false;
        // $design->ai                = $request->source;
        $design->save();

        if ($request->colors) {
            $design->colors()->sync(explode(',', $request->colors) ?: []);
        }

        if ($request->tags) {
            $request->load('design');
            $request = $this->extractAndCreateTags($request, explode(',', $request->tags) ?: []);
        }

        // move to S3?

        return $request;
    }

    public function reject($request, $reason = 0, $message = null)
    {
        if (!$request->rejectable()) {
            abort(422, 'REQUEST_NOT_REJECTABLE');
        }
        $request->status  = DesignRequest::IS_REJECTED;
        $request->reason  = $reason;
        $request->message = $message;
        $request->save();
        $creator = User::find($request->user_id);
        if ($request->design) {
            LibMonthDesign::where([ 'design_id' => $request->design->id ])->delete();
        }
        (new Email('upload-declined'))->send($creator, ['request_id' => $request->id, 'reason' => $reason, 'message' => $message]);
        // send email
        return $request;
    }

    // public function group($request)
    // {
    //   $request->status = DesignRequest::IS_REJECTED;
    //   $request->save();
    //   // send email
    //   return $request;
    // }

    public function approve($request, $data)
    {
        if (!$request->approvable()) {
            abort(422, 'REQUEST_NOT_APPROVABLE');
        }
        $request->status = DesignRequest::IS_APPROVED;
        $request->save();
        $request->load('design');
        $request                       = $this->handleUploads($request, $data);
        $request                       = $this->extractAndCreateTags($request, $data['tags']);
        if (is_null($request->project_code) && is_null($request->project_item_id)) {
            $request                       = $this->syncColors($request, $data['colors']);
        }
        $request->design->status       = Design::IS_PREMIUM_ONLY;
        $request->design->published_at = Carbon::now()->addDays(Setting::key('premium_days'))->toDateString();
        $request->design->save();
        if ($request->lib_month_id && $request->lib_category_id) {
            $this->assignLibrary($request);
        }
        return $request->design;
    }

    public function assignLibrary($request, $lib_month_id = null, $lib_category_id = null)
    {
        $cat   = LibCategory::find($lib_category_id ?? $request->lib_category_id);
        $month = LibMonth::find($lib_month_id ?? $request->lib_month_id);
        if ($cat && $month) {
            LibMonthDesign::forceCreate([
                'pro'             => 1,
                'basic'           => 1,
                'is_trial'        => 1,
                'design_id'       => $request->design->id,
                'lib_category_id' => $cat->id,
                'lib_month_id'    => $month->id,
            ]);
            $design = $request->design()->first();
            // $notExclusive = $request->licence_type !== 'exlusive';
            // $published = Carbon::parse($design->published_at)->lt(Carbon::now());
            // if ($notExclusive || $published) {
            // $design->update(['status' => Design::IS_LIBRARY_ONLY]);
            $design->status = Design::IS_LIBRARY_ONLY;
            $design->save();
            // }
        } else {
            \Log::error('Want to assign library but seems model not found', $request->toArray());
        }
    }

    public function assignProject($request)
    {
        $project = Project::where('code', $request->project_code)->first();
        $design  = $request->design;
        if (!$project) {
            throw new DesignRequestException('INVALID_PROJECT_CODE');
        }

        if ($request->project_item_id) {
            $projectItem  = $project->items()->find($request->project_item_id);
            // note: studio add designs to item and add count, this maybe NA in near future
            $projectItem->revise_count = ++$projectItem->revise_count;
        } else {
            $projectItem  = new ProjectItem(['project_id' => $project->id]);
        }

        $projectItem->is_revised     = true;
        $projectItem->last_design_id = $design->id;
        $projectItem->save();

        $project->designs()->syncWithoutDetaching([$design->id => ['project_item_id' => $projectItem->id]]);
        $design->status = Design::IS_PROJECT;
        $design->save();
        $design->request->status = DesignRequest::IS_APPROVED;
        $design->request->save();
        $user = User::find($design->designer_id);

        if ($request->project_item_id) {
            (new Email('project-item-revised'))->send($project->user, ['project_item_id' => $projectItem->id]);
        }
        // (new Email('design-approved'))->send($user, ['design_id' => $design->id]);
        return $design;
    }

    protected function instantAssignToProjectItem($request)
    {
        $request        = $this->queue($request->fresh());
        $data           = [];
        // @todo using previous design's value is better here
        $data['colors'] = explode(',', $request->colors) ?: [];
        $data['tags']   = explode(',', $request->tags) ?: [];
        $this->approve($request, $data);
        $this->assignProject($request);
        $design               = $request->design;
        $design->status       = Design::IS_PROJECT;
        $design->approved_at  = Carbon::now()->toDateTimeString();
        $design->published_at = Carbon::now()->toDateTimeString();
        $design->save();
        $request->status = DesignRequest::IS_APPROVED;
        $request->save();
        return $request;
    }

    public function delete($request)
    {
        if (!$request->destroyable()) {
            abort(422, 'REQUEST_NOT_DESTROYABLE');
        }
        $request->status = DesignRequest::IS_DELETED;
        $request->save();
        return $request;
    }

    protected function generateUniqueCode()
    {
        $unique   = false;
        $attempts = 0;
        while (!$unique && $attempts < 100) {
            $attempts += 1;
            $code   = rand(0, 99999999);
            $code   = sprintf('%08d', $code);
            $unique = !Design::where('code', $code)->exists();
        }
        return $code;
    }

    protected function handleUploads($request, $data)
    {
        if (isset($data['image'])) {
            $request->design = (new UploadAssets($data['image']))->to($request->design)->save('image');
        }
        if (isset($data['file'])) {
            $request->design = (new UploadAssets($data['file']))->to($request->design)->save('file');
        }
        return $request;
    }

    protected function extractAndCreateTags($request, $tags)
    {
        $tagIds = [];
        foreach ($tags as $tag) {
            $tag = TagsTranslation::tagify($tag);
            if ($tag) {
                $tagIds[count($tagIds)] = $this->findOrCreate($tag, $request);
            }
        }
        $request->design->tags()->sync($tagIds);
        return $request;
    }

    protected function syncColors($request, $colors)
    {
        $request->design->colors()->sync($colors);
        return $request;
    }

    protected function findOrCreate($name, $request)
    {
        $tag = TagsTranslation::where('name', $name)->first();

        if (is_null($tag)) {
            $tag = Tag::forceCreate([
                'is_active'       => true,
                // 'is_exclusive'    => $request->design->is_exclusive_view,
            ]);
            TagsTranslation::forceCreate([
                'id'   => $tag->id,
                'name' => $name,
                'lang' => $request->lang ?: 'en',
            ]);
        }

        return $tag->id;
    }
}
