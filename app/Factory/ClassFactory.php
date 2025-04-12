<?php

namespace IRFANM\SIASHAF\Factory;

use IRFANM\SIASHAF\Domain\ClassDomain;
use IRFANM\SIASHAF\Repository\ClassRepository;

class ClassFactory
{
    private ClassRepository $classRepository;

    public static function createClass(string $class_id, string $name, ?string $description = null): ClassDomain
    {
        $class = new ClassDomain();

        $class->class_id = $class_id;
        $class->name = $name;
        $class->description = $description;
        $class->created_at = date("Y-m-d H:i:s");
        $class->updated_at = date("Y-m-d H:i:s");
        $class->deleted_at = null;

        return $class;
    }

}
