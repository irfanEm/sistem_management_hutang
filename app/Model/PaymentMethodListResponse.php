<?php

namespace IRFANM\SIMAHU\Model;

class PaymentMethodListResponse
{
    public array $paymentMethods;
    public int $total;
    public int $page;
    public int $perPage;

    public function __construct(array $paymentMethods, int $total, int $page, int $perPage)
    {
        $this->paymentMethods = $paymentMethods;
        $this->total = $total;
        $this->page = $page;
        $this->perPage = $perPage;
    }
}
