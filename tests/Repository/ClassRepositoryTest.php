<?php

namespace IRFANM\SIASHAF\Repository;

use IRFANM\SIASHAF\Config\Database;
use IRFANM\SIASHAF\Factory\ClassFactory;
use PHPUnit\Framework\TestCase;

class ClassRepositoryTest extends TestCase
{
    private ClassRepository $classRepository;

    public function setUp(): void
    {
        $this->classRepository = new ClassRepository(Database::getConn());

        $this->classRepository->deleteAllPermanently();
    }

    public function testSaveClass()
    {
        $class7A = ClassFactory::createClass("class_7a", "Class 7A", "Class 7A");
        $this->classRepository->save($class7A);

        $class7B = ClassFactory::createClass("class_7b", "Class 7B", "Class 7B");
        $this->classRepository->save($class7B);

        $class7C = ClassFactory::createClass("class_7c", "Class 7C", "Class 7C");
        $this->classRepository->save($class7C);

        $classesExistOrNot = $this->classRepository->getAll();
        self::assertNotNull($classesExistOrNot);
        self::assertCount(3, $classesExistOrNot);

        $classExists = $this->classRepository->getAllActive();
        self::assertNotNull($classExists);
        self::assertCount(3, $classExists);
        foreach($classExists as $ce) {
            self::assertNull($ce['deleted_at']);
        }

        $deletedClasses = $this->classRepository->getAllDeleted();
        self::assertCount(0, $deletedClasses);
    }

    public function testUpdateClasses()
    {
        $class7A = ClassFactory::createClass("class_7a", "Class 7A", "Class 7A");
        $this->classRepository->save($class7A);

        $class7A->name = "Class Juz 'Amma awal";
        $class7A->description = "Class Permulaan";

        $this->classRepository->update($class7A);

        $updatedClass = $this->classRepository->findByClassId($class7A->class_id);
        self::assertNotNull($updatedClass);
        self::assertEquals("class_7a", $updatedClass->class_id);
        self::assertEquals("Class Juz 'Amma awal", $updatedClass->name);
        self::assertEquals("Class Permulaan", $updatedClass->description);
    }

    public function testFindClassByClassId()
    {
        $class7A = ClassFactory::createClass("class_7a", "Class 7A", "Class 7A");
        $this->classRepository->save($class7A);

        $class7B = ClassFactory::createClass("class_7b", "Class 7B", "Class 7B");
        $this->classRepository->save($class7B);

        $class7C = ClassFactory::createClass("class_7c", "Class 7C", "Class 7C");
        $this->classRepository->save($class7C);

        $findClass = $this->classRepository->findByClassId('class_7a');
        self::assertNotNull($findClass);
        self::assertEquals($class7A->class_id, $findClass->class_id);
        self::assertEquals($class7A->name, $findClass->name);
        self::assertEquals($class7A->description, $findClass->description);
        self::assertNull($findClass->deleted_at);

        $findClass2 = $this->classRepository->findByClassId('class_7b');
        self::assertNotNull($findClass2);
        self::assertEquals($class7B->class_id, $findClass2->class_id);
        self::assertEquals($class7B->name, $findClass2->name);
        self::assertEquals($class7B->description, $findClass2->description);
        self::assertNull($findClass2->deleted_at);

        $nullClass = $this->classRepository->findByClassId('notfound');
        self::assertNull($nullClass);
    }

    public function testDeleteClassPermanently()
    {
        $class7A = ClassFactory::createClass("class_7a", "Class 7A", "Class 7A");
        $this->classRepository->save($class7A);

        $deleteClass = $this->classRepository->deletePermanently($class7A->class_id);
        self::assertTrue($deleteClass);
        
        $findClass = $this->classRepository->findByClassId('class_7a');
        self::assertNull($findClass);    
    }

    public function testDeleteAllClassPermanently()
    {
        $class7A = ClassFactory::createClass("class_7a", "Class 7A", "Class 7A");
        $this->classRepository->save($class7A);

        $class7B = ClassFactory::createClass("class_7b", "Class 7B", "Class 7B");
        $this->classRepository->save($class7B);

        $class7C = ClassFactory::createClass("class_7c", "Class 7C", "Class 7C");
        $this->classRepository->save($class7C);

        $deleteAllClass = $this->classRepository->deleteAllPermanently();
        self::assertTrue($deleteAllClass);

        $classes = $this->classRepository->getAll();
        self::assertCount(0, $classes);
        self::assertIsArray($classes);
    }

    public function testDeleteClassSoftly()
    {
        $class7A = ClassFactory::createClass("class_7a", "Class 7A", "Class 7A");
        $this->classRepository->save($class7A);

        $class7B = ClassFactory::createClass("class_7b", "Class 7B", "Class 7B");
        $this->classRepository->save($class7B);

        $class7C = ClassFactory::createClass("class_7c", "Class 7C", "Class 7C");
        $this->classRepository->save($class7C);

        $deleteSoftly = $this->classRepository->deleteSoftly($class7A->class_id);
        self::assertTrue($deleteSoftly);

        $deletedClassSoftly = $this->classRepository->findSoftDeleted($class7A->class_id);
        self::assertNotNull($deletedClassSoftly);
        self::assertEquals($class7A->class_id, $deletedClassSoftly->class_id);
        self::assertEquals($class7A->name, $deletedClassSoftly->name);
        self::assertEquals($class7A->description, $deletedClassSoftly->description);
    }

    public function testRestoreDeletedClassSoftly()
    {
        $class7A = ClassFactory::createClass("class_7a", "Class 7A", "Class 7A");
        $this->classRepository->save($class7A);

        $class7B = ClassFactory::createClass("class_7b", "Class 7B", "Class 7B");
        $this->classRepository->save($class7B);

        $class7C = ClassFactory::createClass("class_7c", "Class 7C", "Class 7C");
        $this->classRepository->save($class7C);

        $deleteAllSoftly = $this->classRepository->deleteAllSoftly();
        self::assertTrue($deleteAllSoftly);

        $restoreClass = $this->classRepository->restoreSoftDeleted($class7A->class_id);
        self::assertTrue($restoreClass);

        $restoredClass = $this->classRepository->findByClassId($class7A->class_id);
        self::assertNotNull($restoredClass);
        self::assertEquals($class7A->class_id, $restoredClass->class_id);
        self::assertEquals($class7A->name, $restoredClass->name);
        self::assertEquals($class7A->description, $restoredClass->description);
        self::assertNull($restoredClass->deleted_at);
    }

    public function testRestoreAllDeletedClassSoftly()
    {
        $class7A = ClassFactory::createClass("class_7a", "Class 7A", "Class 7A");
        $this->classRepository->save($class7A);

        $class7B = ClassFactory::createClass("class_7b", "Class 7B", "Class 7B");
        $this->classRepository->save($class7B);

        $class7C = ClassFactory::createClass("class_7c", "Class 7C", "Class 7C");
        $this->classRepository->save($class7C);

        $deleteAllSoftly = $this->classRepository->deleteAllSoftly();
        self::assertTrue($deleteAllSoftly);

        $restoreALlDeletedSoftly = $this->classRepository->restoreAllSoftDeleted();
        self::assertTrue($restoreALlDeletedSoftly);

        $restoredDeletedClasses = $this->classRepository->getAllActive();
        self::assertNotNull($restoredDeletedClasses);
        self::assertCount(3, $restoredDeletedClasses);
        foreach($restoredDeletedClasses as $class){
            self::assertNull($class['deleted_at']);
        }
    }
}
