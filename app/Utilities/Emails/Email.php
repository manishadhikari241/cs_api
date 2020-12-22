<?php

namespace App\Utilities\Emails;

use App\User;
use GuzzleHttp\Client;

class Email
{
    /* Usage */
    //
    // (new Email('creator-applied'))->send($user, $params)
    // (new Email('gift-card'))->send($user, [ 'voucher_id' => 1 ])
    // if you want it really fired during testing
    // (new Email('gift-card'))->sendTesting($user, [ 'voucher_id' => 1 ])
    //

    /* if mocking is false, the testing env will actually fire the email. */
    protected $mocking = true;

    public function __construct($emailType = 'creator-applied')
    {
        $apis = [
            'local'      => getenv('APP_URL') . '/email',
            'testing'    => getenv('APP_URL') . '/email',
            'staging'    => getenv('APP_URL') . '/email',
            'production' => getenv('APP_URL') . '/email',
        ];
        $this->client = new Client();
        $this->api    = $apis[getenv('APP_ENV')] . '/' . $emailType;
        $this->token  = User::admin()->whereNotNull('api_token')->first()->api_token;
    }

    public function send($userOrEmail, $params = [])
    {
        if ($this->mocking && app()->environment('testing')) {
            return (object) ['message' => 'success', 'note' => 'Email is skipped in this test.'];
        }
        $params['api_token'] = $this->token;
        $idOrEmail           = $userOrEmail->id ?? $userOrEmail;
        $response            = $this->client->request('POST', "{$this->api}/{$idOrEmail}", [
            'json' => $params,
        ]);
        //dd($response);
        return json_decode($response->getBody());
    }

    /* You will call send as Testing if you really want your email fired during testing */
    public function sendTesting($user, $params = [])
    {
        $this->mocking = false;
        return $this->send($user, $params);
    }
}
