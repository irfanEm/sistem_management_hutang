<?php

namespace IRFANM\SIASHAF\Domain;

class Agen
{
    public int $id;
    public string $kode_agen;
    public string $nama_agen;
    public ?string $kontak = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
}
