<?php

namespace IRFANM\SIASHAF\Repository;

use IRFANM\SIASHAF\Config\Database;
use IRFANM\SIASHAF\Factory\ClassFactory;
use IRFANM\SIASHAF\Factory\StudentFactory;
use IRFANM\SIASHAF\Factory\UserFactory;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

class StudentRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private ClassRepository $classRepository;
    private StudentRepository $studentRepository;

    public function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConn());
        $this->classRepository = new ClassRepository(Database::getConn());
        $this->studentRepository = new StudentRepository(Database::getConn());

        $this->studentRepository->deleteAllPermanently();
        $this->userRepository->deleteAllPermanently();
        $this->classRepository->deleteAllPermanently();
    }

    public function testGetAllStudent()
    {
        // 1
        $user0 = UserFactory::createUser("user001", "Zaidun", "zaid_00@santri.com", "zaid_00", "Santri");
        $this->userRepository->save($user0);

        $class0 = ClassFactory::createClass("class_001", "Class 7A");
        $this->classRepository->save($class0);

        $student0 = StudentFactory::createStudent($user0->user_id, "stdn001", "Zaidun", "Hamidun", $user0->username, "088299778833", "Jl. Kemakmuran, Desa Ketentraman, Kec. Kedamaian, Kab. Keabadian", "2010-01-02", $class0->class_id, "2019-06-01");
        $this->studentRepository->save($student0);

        // 2
        $user1 = UserFactory::createUser("user002", "Bakrun", "bakrun_00@ustadz.com", "bakrun_00", "Ustadz");
        $this->userRepository->save($user1);

        $class1 = ClassFactory::createClass("class_002", "Class 7B");
        $this->classRepository->save($class1);

        $student1 = StudentFactory::createStudent($user1->user_id, "stdn002", "Bakrun", "Hamidun", $user1->username, "088299778833", "Jl. Kemakmuran, Desa Ketentraman, Kec. Kedamaian, Kab. Keabadian", "2010-01-02", $class1->class_id, "2019-06-01");
        $this->studentRepository->save($student1);

        // 3
        $user2 = UserFactory::createUser("user003", "Umarun", "umarun_00@ustadz.com", "umarun_00", "Ustadz");
        $this->userRepository->save($user2);

        $class2 = ClassFactory::createClass("class_003", "Class 7C");
        $this->classRepository->save($class2);

        $student2 = StudentFactory::createStudent($user2->user_id, "stdn003", "Umarun", "Hamidun", $user2->username, "088299778833", "Jl. Kemakmuran, Desa Ketentraman, Kec. Kedamaian, Kab. Keabadian", "2010-01-02", $class2->class_id, "2019-06-01");
        $this->studentRepository->save($student2);

        $students = $this->studentRepository->getAll();
        self::assertNotNull($students);
        self::assertCount(3, $students);

        $existStudents = $this->studentRepository->getAllActive();
        self::assertNotNull($existStudents);
        self::assertCount(3, $existStudents);
        foreach($existStudents as $existStudent) {
            self::assertNull($existStudent['deleted_at']);
        }

        $deleteAllStudentsSoftly = $this->studentRepository->deleteAllSoftly();
        self::assertTrue($deleteAllStudentsSoftly);

        $deletedStudentsSoftly = $this->studentRepository->getAllDeleted();
        self::assertNotNull($deletedStudentsSoftly);
        self::assertCount(3, $deletedStudentsSoftly);
        foreach($deletedStudentsSoftly as $deletedStudentSoftly) {
            self::assertNotNull($deletedStudentSoftly['deleted_at']);
        }
    }

    public function testRestoreAllDeletedStudentsSoftly()
    {
        // 1
        $user0 = UserFactory::createUser("user001", "Zaidun", "zaid_00@santri.com", "zaid_00", "Santri");
        $this->userRepository->save($user0);

        $class0 = ClassFactory::createClass("class_001", "Class 7A");
        $this->classRepository->save($class0);

        $student0 = StudentFactory::createStudent($user0->user_id, "stdn001", "Zaidun", "Hamidun", $user0->username, "088299778833", "Jl. Kemakmuran, Desa Ketentraman, Kec. Kedamaian, Kab. Keabadian", "2010-01-02", $class0->class_id, "2019-06-01");
        $this->studentRepository->save($student0);

        // 2
        $user1 = UserFactory::createUser("user002", "Bakrun", "bakrun_00@ustadz.com", "bakrun_00", "Ustadz");
        $this->userRepository->save($user1);

        $class1 = ClassFactory::createClass("class_002", "Class 7B");
        $this->classRepository->save($class1);

        $student1 = StudentFactory::createStudent($user1->user_id, "stdn002", "Bakrun", "Hamidun", $user1->username, "088299778833", "Jl. Kemakmuran, Desa Ketentraman, Kec. Kedamaian, Kab. Keabadian", "2010-01-02", $class1->class_id, "2019-06-01");
        $this->studentRepository->save($student1);

        // 3
        $user2 = UserFactory::createUser("user003", "Umarun", "umarun_00@ustadz.com", "umarun_00", "Ustadz");
        $this->userRepository->save($user2);

        $class2 = ClassFactory::createClass("class_003", "Class 7C");
        $this->classRepository->save($class2);

        $student2 = StudentFactory::createStudent($user2->user_id, "stdn003", "Umarun", "Hamidun", $user2->username, "088299778833", "Jl. Kemakmuran, Desa Ketentraman, Kec. Kedamaian, Kab. Keabadian", "2010-01-02", $class2->class_id, "2019-06-01");
        $this->studentRepository->save($student2);

        $deleteAllStudentsSoftly = $this->studentRepository->deleteAllSoftly();
        self::assertTrue($deleteAllStudentsSoftly);

        $restoredDeletedStudentsSoftly = $this->studentRepository->restoreAllSoftDeleted();
        self::assertTrue($restoredDeletedStudentsSoftly);

        $existStudents = $this->studentRepository->getAllActive();
        self::assertNotNull($existStudents);
        self::assertCount(3, $existStudents);
        foreach($existStudents as $existStudent) {
            self::assertNull($existStudent['deleted_at']);
        }
    }

    public function testUpdateStudent()
    {
        // 1
        $user0 = UserFactory::createUser("user001", "Zaidun", "zaid_00@santri.com", "zaid_00", "Santri");
        $this->userRepository->save($user0);

        $class0 = ClassFactory::createClass("class_001", "Class 7A");
        $this->classRepository->save($class0);

        $student0 = StudentFactory::createStudent($user0->user_id, "stdn001", "Zaidun", "Hamidun", $user0->username, "088299778833", "Jl. Kemakmuran, Desa Ketentraman, Kec. Kedamaian, Kab. Keabadian", "2010-01-02", $class0->class_id, "2019-06-01");
        $this->studentRepository->save($student0);

        $student0->first_name = 'Ahmad';
        $student0->last_name = 'Buchori';
        $student0->phone = '088194428553';
        $student0->date_of_birth = '2009-12-12';
        $student0->enrollment_date = '2020-01-04';

        $this->studentRepository->update($student0);

        $updatedStudent = $this->studentRepository->findByUserId($student0->user_id);
        self::assertNotNull($updatedStudent);
        self::assertEquals("Ahmad", $updatedStudent->first_name);
        self::assertEquals("Buchori", $updatedStudent->last_name);
        self::assertEquals("088194428553", $updatedStudent->phone);
        self::assertEquals("2009-12-12", $updatedStudent->date_of_birth);
        self::assertEquals("2020-01-04", $updatedStudent->enrollment_date);
    }

    public function testFindSoftDeletedStudent()
    {
        // 1
        $user0 = UserFactory::createUser("user001", "Zaidun", "zaid_00@santri.com", "zaid_00", "Santri");
        $this->userRepository->save($user0);

        $class0 = ClassFactory::createClass("class_001", "Class 7A");
        $this->classRepository->save($class0);

        $student0 = StudentFactory::createStudent($user0->user_id, "stdn001", "Zaidun", "Hamidun", $user0->username, "088299778833", "Jl. Kemakmuran, Desa Ketentraman, Kec. Kedamaian, Kab. Keabadian", "2010-01-02", $class0->class_id, "2019-06-01");
        $this->studentRepository->save($student0);

        $statusDelete = $this->studentRepository->deleteSoftly($student0->user_id);
        self::assertTrue($statusDelete);

        $deletedStudent = $this->studentRepository->findSoftDeleted($student0->user_id);
        self::assertNotNull($deletedStudent);       
        self::assertNotNull($deletedStudent->updated_at);       
    }

    public function testRestoreDeletedStudent()
    {
        // 1
        $user0 = UserFactory::createUser("user001", "Zaidun", "zaid_00@santri.com", "zaid_00", "Santri");
        $this->userRepository->save($user0);

        $class0 = ClassFactory::createClass("class_001", "Class 7A");
        $this->classRepository->save($class0);

        $student0 = StudentFactory::createStudent($user0->user_id, "stdn001", "Zaidun", "Hamidun", $user0->username, "088299778833", "Jl. Kemakmuran, Desa Ketentraman, Kec. Kedamaian, Kab. Keabadian", "2010-01-02", $class0->class_id, "2019-06-01");
        $this->studentRepository->save($student0);

        $this->studentRepository->deleteSoftly($student0->user_id);

        $restored = $this->studentRepository->restoreSoftDeleted($student0->user_id);
        self::assertTrue($restored);

        $studentRestored = $this->studentRepository->findByUserId($student0->user_id);
        assertNotNull($studentRestored);
        assertNull($studentRestored->deleted_at);
    }

    public function testDeleteStudentPermanently()
    {
        $user0 = UserFactory::createUser("user001", "Zaidun", "zaid_00@santri.com", "zaid_00", "Santri");
        $this->userRepository->save($user0);

        $class0 = ClassFactory::createClass("class_001", "Class 7A");
        $this->classRepository->save($class0);

        $student0 = StudentFactory::createStudent($user0->user_id, "stdn001", "Zaidun", "Hamidun", $user0->username, "088299778833", "Jl. Kemakmuran, Desa Ketentraman, Kec. Kedamaian, Kab. Keabadian", "2010-01-02", $class0->class_id, "2019-06-01");
        $this->studentRepository->save($student0);

        $result = $this->studentRepository->deletePermanently($student0->user_id);
        self::assertTrue($result);

        $studentDeleted = $this->studentRepository->findByUserId($student0->user_id);
        assertNull($studentDeleted);
    }
}
