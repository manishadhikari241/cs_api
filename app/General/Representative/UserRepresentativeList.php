<?php

namespace App\General\Representative;

use App\User;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class UserRepresentativeList extends Model
{
    protected $table = 'user_representative_list';

    protected $fillable = ['user_id', 'representative_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public static function randomAssign(array $option)
    {
        $take  = $option['take'];

        $reps = [];
        if (isset($option['representative_group_id'])) {
            $reps = Representative::where('representative_group_id', $option['representative_group_id'])->where('is_active', 1)->get();
        } else {
            $reps = collect([Representative::find($option['representative_id'])]);
        }

        $repCount  = count($reps);
        $takeTotal = $take * $repCount;

        if (!$repCount) {
            abort('This group has no rep, do nothing');
        }
        $users = User::whereDoesntHave('libPlanUser')->whereDoesntHave('representativeList')->whereNull('representative_id')->take($takeTotal)->get();
        if ($users->count() < $takeTotal) {
            abort('There aint that much users unassigned. Lower your "take" param to <= (int)' . $users->count() / $repCount);
        }
        $users = $users->shuffle();

        $given  = 0;
        $next   = 0;
        foreach ($users as $user) {
            // if (self::where('user_id')->exists()) {
            //     continue;
            // }
            if ($given >= $take) {
                ++$next;
                $given = 0;
            }
            self::forceCreate([
                'user_id'           => $user->id,
                'representative_id' => $reps[$next]->id,
            ]);
            ++$given;
        }
    }

}
