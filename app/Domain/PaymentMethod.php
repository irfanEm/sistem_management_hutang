<?php

namespace IRFANM\SIMAHU\Domain;

class PaymentMethod
{
    public int $id = 0;
    public string $kode_metode;
    public string $nama_metode;
    public string $created_at;
    public string $updated_at;

    public function __construct()
    {
        $now = date('Y-m-d H:i:s');
        $this->created_at = $now;
        $this->updated_at = $now;
    }
}