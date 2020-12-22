<?php

namespace App\General;

use App\User;
use App\Utilities\Emails\Email;
use App\Marketplace\Studio\Studio;
use App\Marketplace\Designs\Design;
use App\Marketplace\Studio\ManageStudio;
use App\Marketplace\Designs\DesignRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ManageUserProfile
{
    public function handle($user, $data)
    {
        $profile = $user->profile;
        if (!$profile) {
            $data['user_id'] = $user->id;
            $profile         = $this->initNewProfile($data);
        }
        $profile = $this->update($profile, $data);
        return $profile;
    }

    public function approve($profile, $group_id)
    {
        $profile->status           = Profile::IS_APPROVED_NOTICED;
        $profile->code             = $this->generateUniqueCode();
        $profile->creator_group_id = $group_id;
        $profile->save();
        CreatorGroupLog::create([
            'user_id'    => $profile->user_id,
            'percentage' => $profile->creatorGroup->percentage,
        ]);

        (new Email('creator-approved'))->send($profile->user);
        return $profile;
    }

    public function approveStudio($profile, $group_id)
    {
        $profile               = $this->approve($profile, $group_id);

        $studio                  = new Studio;
        $studio->code            = $this->generateUniqueStudioCode(7);
        $studio->logo            = $profile->logo ?: null;
        $studio->banner          = $profile->banner ?: null;
        $studio->mobile_banner   = $profile->mobile_banner ?: null;
        $studio->user_id         = $profile->user_id;
        $studio->country_id      = $profile->country_id;
        $studio->website         = $profile->portfolio_website;
        $studio->invitation_code = str_random(20);
        $studio->is_active       = true;

        $studio->save();

        $studio->designer()->sync($profile->user_id);

        $studio->accesses()->forceCreate([
            'user_id'         => $profile->user_id,
            'studio_id'       => $studio->id,
            'is_active'       => 1
        ]);

        User::where(['id' => $profile->user_id])->update(['is_premium' => true]);

        if ($profile->name) {
            (new ManageStudio)->updateName($studio, ['en'=> $profile->name, 'zh-CN'=> $profile->name]);
        }
        if ($profile->description) {
            (new ManageStudio)->updateDescription($studio, ['en'=> $profile->description, 'zh-CN'=> $profile->description]);
        }

        return $profile;
    }

    public function decline($profile, $data)
    {
        $profile->status = Profile::IS_REJECTED;
        $profile->save();
        $this->removePublicImages($profile->user);
        (new Email('creator-rejected'))->send($profile->user, [
            'message' => $data['message'] ?? '',
            'reason'  => $data['reason'],
        ]);
        return $profile;
    }

    public function warning($profile, $data)
    {
        (new Email('creator-warned'))->send($profile->user, [
            'message' => $data['message'] ?? '',
        ]);
        return $profile;
    }

    public function suspend($profile, $data)
    {
        $profile->status = Profile::IS_SUSPENDED;
        $profile->save();
        $designs = Design::where('designer_id', $profile->user_id)->where('status', '!=', 3)->where('is_licensing', false)->pluck('id', 'request_id');
        foreach ($designs as $request=> $val) {
            //update design to declined state if creator suspended
            Design::where(['id' =>$val])->update(['status' =>Design::IS_REJECTED]);
            DesignRequest::where(['id'=>$request])->update(['status'=>Design::IS_REJECTED]);
        }
        (new Email('creator-suspended'))->send($profile->user, [
            'message' => $data['message'] ?? '',
        ]);
        return $profile;
    }

    public function group($profile, $id)
    {
        // send email
        if (in_array($profile->status, [Profile::IS_APPROVED_NOTICED, Profile::IS_APPROVED])) {
            if ($profile->creator_group_id !== $id) {
                // log down changes
                CreatorGroupLog::create([
                    'user_id'    => $profile->user_id,
                    'percentage' => CreatorGroup::find($id)->percentage,
                ]);
                (new Email('creator-group-changed'))->send($profile->user);
            }
        }
        $profile->creator_group_id = $id;
        $profile->save();
        return $profile;
    }

    protected function initNewProfile($data)
    {
        $data['code']             = '';
        $data['status']           = 0;
        $data['creator_group_id'] = 6;
        if (isset($data['public_images'])) {
            $this->createPublicImages($data['public_images']);
        }
        return Profile::forceCreate($data);
    }

    protected function update($profile, $data)
    {
        if (isset($data['subscribe'])) {
            if (($data['subscribe'] && !$profile->subscribe) || !$data['subscribe'] && $profile->subscribe) {
                $by_admin = Auth::user()->is_super_admin && !$profile->user->is_super_admin;
                ConcentHistory::forceCreate(['subscribe' => $profile->subscribe, 'user_id' => $profile->user_id, 'created_at' => $profile->concent_at, 'is_by_admin' => $by_admin]);
                $profile->concent_at = Carbon::now()->toDateTimeString();
                if ($by_admin) {
                    (new Email('user-preference-updated'))->send($profile->user);
                }
            }
        }

        $profile->update($data);
        if (isset($data['passport_image'])) {
            $profile = (new UploadFile($data['passport_image']))->to($profile)->save('passport_image');
        }
        if (isset($data['portfolio'])) {
            $profile = (new UploadFile($data['portfolio']))->to($profile)->save('portfolio');
        }

        // create public uploads each update
        if (isset($data['public_images'])) {
            $this->createPublicImages($data['public_images']);
        }

        // we also copy to /uploads/studio/{user_id}/logo/{name}, so that they can be extracted to use later
        $tempStudio             = new Studio();
        $tempStudio->user_id    = $profile->user_id;

        if (isset($data['logo'])) {
            $profile = (new UploadFile($data['logo']))->to($profile)->save('logo');
            (new UploadFile($data['logo']))->to($tempStudio)->copy('logo', $profile->logo);
        }
        if (isset($data['banner'])) {
            $profile = (new UploadFile($data['banner']))->to($profile)->save('banner');
            (new UploadFile($data['banner']))->to($tempStudio)->copy('banner', $profile->banner);
        }
        if (isset($data['mobile_banner'])) {
            $profile = (new UploadFile($data['mobile_banner']))->to($profile)->save('mobile_banner');
            (new UploadFile($data['mobile_banner']))->to($tempStudio)->copy('mobile_banner', $profile->mobile_banner);
        }
        if (isset($data['apply_creator'])) {
            $profile = $this->handleCreatorApplication($profile);
        }
        $profile->save();
        return $profile;
    }

    protected function createPublicImages($images)
    {
        foreach ($images as $image) {
            $publicImage = new PublicImage();
            $publicImage->user()->associate(Auth::user());
            (new UploadFile($image))->to($publicImage)->save('image');
        }
    }

    protected function removePublicImages($user)
    {
        $user->publicImages()->delete();
    }

    protected function handleCreatorApplication($profile)
    {
        if ($profile->portfolio && $profile->passport_type && $profile->passport_image) {
            $profile->status = Profile::IS_PENDING;
            (new Email('creator-applied'))->send($profile->user);
        }
        return $profile;
    }

    protected function generateUniqueCode()
    {
        $unique   = false;
        $attempts = 0;
        while (!$unique && $attempts < 100) {
            $attempts += 1;
            $code   = rand(0, 999999);
            $code   = sprintf('%06d', $code);
            $unique = !Profile::where('code', $code)->exists();
        }
        return $code;
    }

    public function generateUniqueStudioCode($length)
    {
        $unique   = false;
        $attempts = 0;
        while (!$unique && $attempts < 100) {
            $attempts += 1;
            $code   = rand(0, pow(10, $length) - 1);
            $code   = sprintf('%07d', $code);
            $unique = !Studio::where('code', $code)->exists();
        }
        return $code;
    }

    //  public function suspend($profile, $data)
    // {
    //     $profile->status = Profile::IS_REJECTED;
    //     $profile->save();
    //     (new Email('creator-suspended'))->send($profile->user, [
    //         'reason'  => $data,
    //     ]);
    //     // send email
    //     return $profile;
    // }
}
