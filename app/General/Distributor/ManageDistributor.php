<?php

namespace App\General\Distributor;

use App\General\Distributor\DistributorGroup;
use App\General\Distributor\DistributorGroupLog;
use App\User;
use App\Utilities\Emails\Email;

class ManageDistributor
{
    public function group($distributor, $id)
    {
        if ($distributor->Distributor_group_id !== $id) {
            // log down changes
            DistributorGroupLog::create([
                'distributor_id' => $distributor->id,
                'percentage' => DistributorGroup::find($id)->percentage,
            ]);
         //   (new Email('distributor-group-changed'))->send($distributor->user, ['distributor_id' => $id]);
        }

        $distributor->distributor_group_id = $id;
        $distributor->save();
        return $distributor;
    }

}
