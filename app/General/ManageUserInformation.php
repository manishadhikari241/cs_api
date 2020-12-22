<?php

namespace App\General;

use App\User;
use Carbon\Carbon;
use App\Utilities\Emails\Email;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ManageUserInformation
{
    public function handle($user, $data)
    {
        if (isset($data['email']) && $user->email !== $data['email']) {
            if (User::where('email', $data['email'])->exists()) {
                abort(422, 'EMAIL_TAKEN');
            }
            $user->email       = $data['email'];
            $user->email_token = str_random(60);
            $user->is_active   = false;
            $user->save();
            (new Email('email-verification'))->send($user);
        }

        if (isset($data['avatar'])) {
            (new UploadFile($data['avatar']))->to($user)->save('avatar');
        }

        if (isset($data['password'])) {
            if (strlen($data['password']) < 6) {
                abort(422, 'WRONG_PASSWORD');
            }
            $this->ensurePasswordCorrect($user, $data);
        }

        $user->update($data);
        $this->updateUserName($user, $data);

        if (isset($data['is_active']) && Auth::user()->is_super_admin) {
            $inactivation = $user->inactivation;
            if ($inactivation && $user->is_active) {
                $user->profile->subscribe = $inactivation->subscribe;
                $user->profile->save();
                $user->is_banned = 0;
                $user->save();
                $inactivation->delete();
            }
            if (!$inactivation && !$data['is_active']) {
                $user->profile->subscribe = 0;
                $user->profile->save();
                $user->is_banned = 1;
                $user->save();
                $user->inactivation()->forceCreate([
                    'user_id'    => $user->id,
                    'handler_id' => Auth::user()->id,
                    'subscribe'  => $user->profile->subscribe,
                    'erased_at'  => Carbon::now()->addMonth()->toDateTimeString()
                ]);
                (new Email('user-inactivated'))->send($user);
                // send email
            }
        }

        if (isset($data['subscribe'])) {
            $profile = (new ManageUserProfile)->handle($user, $data);
        }
        return $user;
    }

    public function activate($user)
    {
        $user->is_active = true;
        $user->save();
        (new Email('user-activated'))->send($user);
        $user->inactivation()->delete();
        return $user;
    }

    protected function ensureEmailNotExists($email)
    {
        if (User::where('email', $email)->exists()) {
            abort(422, 'EMAIL_ALREADY_TAKEN');
        }
    }

    protected function ensurePasswordCorrect($user, $data)
    {
        if ($user->algorithm === 'sha1' && !$user->oldAuth($data['old_password'])) {
            abort(422, 'WRONG_PASSWORD');
        } elseif (!Hash::check($data['old_password'], $user->password)) {
            abort(422, 'WRONG_PASSWORD');
        }
        $user->updateNewPassword($data['password']);
    }

    protected function updateUserName($user, $data)
    {
        if (!isset($data['first_name']) && !isset($data['last_name'])) {
            return;
        }
        $name   = isset($data['first_name']) ? $data['first_name'] : $user->first_name;
        $family = isset($data['last_name']) ? $data['last_name'] : $user->last_name;
        if (in_array($user->lang_pref, ['zh-CN', 'zh-HK', 'zh-TW'])) {
            $user->username = "$family$name";
        } else {
            $user->username = "$name $family";
        }
        $user->save();
    }
}
