<?php

namespace IRFANM\SIASHAF\Domain;

class MasterHafalan
{
    public string $memory_id;
    public string $title;
    public int $ayat;
    public ?string $description;
    public string $created_at;
    public string $updated_at;
    public ?string $deleted_at;
}
