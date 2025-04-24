<?php

namespace IRFANM\SIMAHU\Model;

class HutangUpdateRequest
{
    public ?string $debt_id = null;
    public ?int $payment_method_id = null;
    public ?string $tanggal_jatuh_tempo = null;
    public ?int $sisa_hutang = null;
}