<?php

namespace App\General;

use App\User;
use App\Utilities\Emails\Email;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\RepresentativeException;
use App\General\Representative\Representative;
use App\General\Representative\RepresentativeGroup;
use App\General\Representative\RepresentativeGroupLog;

class ManageRepresentative
{
    public function group($representative, $id)
    {
        if ($representative->representative_group_id !== $id) {
            // log down changes
            RepresentativeGroupLog::create([
                'representative_id'    => $representative->id,
                'percentage' => RepresentativeGroup::find($id)->percentage,
            ]);
         //   (new Email('representative-group-changed'))->send($representative->user, ['representative_id' => $id]);
        }

        $representative->representative_group_id = $id;
        $representative->save();
        return $representative;
    }

    /** Assign the rep using subId to current rep*/
    public function subRep($representative, $subId)
    {
        if ($representative->level >= 2) {
            throw new RepresentativeException('MAX_LEVEL_REACHED');
        }
        $subRep = Representative::findOrFail($subId);
        if ($representative->representative_group_id !== $subRep->representative_group_id) {
            throw new RepresentativeException('DIFFERENT_REP_GROUP');
        }

        // dd($representative->level);
        if (($representative->level !== 0) && !self::isAdmin() && self::userRep()->level !== 0) {
            throw new RepresentativeException('ROOT_LEVEL_REQUIRED');
        }

        // must be root level (0) to do this?
        $subRep->parent_id = $representative->id;
        $subRep->level     = $representative->level + 1;
        $subRep->save();
        return $subRep;
    }

    /** Assign the rep using subId to current rep*/
    public function reassignUser($representative, $params)
    {
        $from = Representative::findOrFail($params['from_representative_id']);
        $to = Representative::findOrFail($params['to_representative_id']);

        if ($from->representative_group_id !== $to->representative_group_id) {
            throw new RepresentativeException('DIFFERENT_REP_GROUP');
        }

        if ($representative->representative_group_id !== $to->representative_group_id) {
            throw new RepresentativeException('DIFFERENT_REP_GROUP');
        }

        if (($representative->level >= $from->level && $representative->id !== $from->id) && $representative->level !== 0) { // root or lower level!
            throw new RepresentativeException('LOWER_LEVEL_REQUIRED');
        }

        $user = User::where('representative_id', $from->id)->where('id', $params['user_id'])->firstOrFail();
        $user->representative_id = $to->id;
        $user->save();

        return $user;
    }
    
    public static function isAdmin () {
        return Auth::user()->is_super_admin;
    }

    public static function userRep() {
        return Auth::user()->representative()->first();
    }

}
