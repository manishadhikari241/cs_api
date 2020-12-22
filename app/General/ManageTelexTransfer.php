<?php

namespace App\General;

class ManageTelexTransfer
{

    public function handle($telex, $data)
    {
        $telex = $this->update($telex, $data);
        return $telex;
    }

    protected function update($telex, $data)
    {
        $telex->update($data);
        $telex->save();
        return $telex;
    }

    public function settle(){
        // change status
        // add transaction ID
    }

}