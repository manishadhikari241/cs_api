<?php

namespace App\Marketplace\Payments\Gateways;

use Auth;
use App\User;
use Exception;

class Freecheckout
{

  // only order, not payable instance!
  public function settle($order, $input)
  {
    if ($order->total !== (float) 0) {
      abort(422, 'ORDER_NOT_FREECHECKOUT');
    }
    return (object) [
      'transaction_id' => null,
      'card_tail'      => null,
      'card_brand'     => null
    ];
  }

}
