<?php

namespace IRFANM\SIASHAF\Factory;

use IRFANM\SIASHAF\Domain\MasterHafalan;

class HafalanMasterFactory
{
    public static function createHafalanMaster(string $memory_id, string $title, int $ayat, ?string $description = null):MasterHafalan
    {
        $hafalanMaster = new MasterHafalan();
        $hafalanMaster->memory_id = $memory_id;
        $hafalanMaster->title = $title;
        $hafalanMaster->ayat = $ayat;
        $hafalanMaster->description = $description;
        $hafalanMaster->created_at = date("Y-m-d H:i:s");
        $hafalanMaster->updated_at = date("Y-m-d H:i:s");
        $hafalanMaster->deleted_at = null;

        return $hafalanMaster;
    }
}
