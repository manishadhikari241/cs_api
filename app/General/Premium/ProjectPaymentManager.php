<?php

namespace App\General\Premium;

use Auth;
use App\General\Address;
use App\Utilities\Emails\Email;
use App\Marketplace\Payments\Gateways\Gateway as PaymentGateway;

class ProjectPaymentManager
{
    /**
     * @depreciated
     */
    public function checkout($info = [])
    {
        $package = ProjectPackage::find($info['project_package_id']);

        $gatewayPayment = PaymentGateway::via($info['payment_method'])->settle($package, $info);

        $projectPayment = $package->createPayment(Auth::user(), $gatewayPayment);

        if (isset($info['address_id'])) {
            $address = Address::find($info['address_id']);
            $projectPayment->updateAddress($address);
        }
        return $this->handleCheckoutSuccess($projectPayment);
    }

    public function refund(ProjectPayment $payment)
    {
        PaymentGateway::via($payment->payment_method)->refund($payment);
        $payment->status = ProjectPayment::IS_REFUNDED;
        $payment->save();
        $request = $payment->request;
        if ($request) {
            $request->status = ProjectRequest::IS_REFUNDED;
            $request->save();
        }
        // @todo send email here
        return $payment;
    }

    public function handleCheckoutSuccess($projectPayment)
    {
        // send invoice email
        // (new Email('project-payment-invoice'))->send(Auth::user(), ['project_payment_id' => $projectPayment->id]);

        return $projectPayment;
    }
}
