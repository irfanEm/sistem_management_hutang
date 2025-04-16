<?php

namespace IRFANM\SIASHAF\Domain;

class Hutang
{
    public int $id;
    public string $debt_id;
    public int $user_id;
    public int $agent_id;
    public int $payment_method_id;
    public string $tanggal_hutang;
    public string $tanggal_jatuh_tempo;
    public int $sisa_hutang;
    public ?string $created_at = null;
    public ?string $updated_at = null;
}
