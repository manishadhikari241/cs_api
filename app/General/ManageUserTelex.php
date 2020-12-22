<?php

namespace App\General;

class ManageUserTelex
{

    public function handle($user, $data)
    {
        $telex = $user->telex;
        if (!$telex) {
            $data['user_id'] = $user->id;
            $telex = $this->initNewProfile($data);
        }
        $telex = $this->update($telex, $data);
        return $telex;
    }

    protected function initNewProfile($data)
    {
        return TelexTransferUser::forceCreate($data);
    }

    protected function update($telex, $data)
    {
        $telex->update($data);
        $telex->save();
        return $telex;
    }

}