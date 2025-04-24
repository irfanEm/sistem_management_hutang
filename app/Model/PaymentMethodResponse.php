<?php

namespace IRFANM\SIMAHU\Model;

use IRFANM\SIMAHU\Domain\PaymentMethod;

class PaymentMethodResponse
{
    public PaymentMethod $paymentMethod;

    public function __construct(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }
}
