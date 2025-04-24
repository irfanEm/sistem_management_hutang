<?php

namespace IRFANM\SIMAHU\Model;

class HutangCreateRequest
{
    public ?string $debt_id = null;
    public ?int $user_id = null;
    public ?int $agent_id = null;
    public ?int $payment_method_id = null;
    public ?string $tanggal_hutang = null;
    public ?string $tanggal_jatuh_tempo = null;
    public ?int $total_hutang = null;
}