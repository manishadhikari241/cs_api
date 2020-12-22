<?php

namespace App\General\Premium;

use App\General\UploadManyFiles;

class ManageProject
{
    public function updateName($project, $names = [])
    {
        if (!is_array($names)) {$names = [$names];}
        $project->load('translations');
        foreach ($names as $key => $value) {
            $translation = $project->translations()->where('id', $project->id)->where('lang', $key)->first();
            if (!$translation) {
                $project->translations()->save(new ProjectsTranslation(['id' => $project->id, 'name' => $value, 'lang' => $key]));
            } else {
                ProjectsTranslation::where(['id' => $project->id, 'lang' => $key])->update(['name' => $value]);
            }
        }
    }

    public function attach($project, $moodBoards)
    {
        (new UploadManyFiles($moodBoards))->to($project, 'moodBoards')->save("name");
        return $project;
    }

}
