<?php

namespace App\General\Premium;

use Illuminate\Database\Eloquent\Model;

class ProjectAccess extends Model
{
    protected $table = "project_access";

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public static function grant($user, $project)
    {
        if (!$user) {throw new \Exception("USER_NOT_FOUND", 1);}
        if ($project->accesses()->where(['user_id' => $user->id])->exists()) {
            throw new \Exception("ACCESS_ALREADY_GIVEN", 1);
        }
        $access = ProjectAccess::forceCreate([
            'user_id'    => $user->id,
            'project_id' => $project->id,
        ]);
        return $access;
    }
}
