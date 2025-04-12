<?php

namespace IRFANM\SIASHAF\Repository;

use DateInterval;
use IRFANM\SIASHAF\Config\Database;
use IRFANM\SIASHAF\Domain\Teacher;
use IRFANM\SIASHAF\Domain\User;
use IRFANM\SIASHAF\Factory\TeacherFactory;
use IRFANM\SIASHAF\Factory\UserFactory;
use PHPUnit\Framework\TestCase;

class TeacherRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private TeacherRepository $teacherRepository;

    public function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConn());
        $this->teacherRepository = new TeacherRepository(Database::getConn());

        $this->teacherRepository->deleteAllPermanently();
        $this->userRepository->deleteAllPermanently();
    }

    public function testGetAllExistTeachers()
    {
        $user1 = new User();
        $user1->user_id = "ustadz001";
        $user1->name = "Zaidun";
        $user1->username = "zaidun_97@ustadz.com";
        $user1->password = password_hash("zaidun_97", PASSWORD_BCRYPT);
        $user1->role = "Ustadz";
        $user1->created_at = date("Y-m-d H:i:s");        
        $user1->updated_at = date("Y-m-d H:i:s");
        $this->userRepository->save($user1);

        $user2 = new User();
        $user2->user_id = "ustadz002";
        $user2->name = "Umarun";
        $user2->username = "umarun_96@ustadz.com";
        $user2->password = password_hash("umarun_96", PASSWORD_BCRYPT);
        $user2->role = "Ustadz";
        $user2->created_at = date("Y-m-d H:i:s");        
        $user2->updated_at = date("Y-m-d H:i:s");
        $this->userRepository->save($user2);

        $user3 = new User();
        $user3->user_id = "ustadz003";
        $user3->name = "Bakrun";
        $user3->username = "bakrun_99@ustadz.com";
        $user3->password = password_hash("bakrun_99", PASSWORD_BCRYPT);
        $user3->role = "Ustadz";
        $user3->created_at = date("Y-m-d H:i:s");        
        $user3->updated_at = date("Y-m-d H:i:s");
        $this->userRepository->save($user3);

        $users = $this->userRepository->getAllActive();
        self::assertNotNull($users);

        $teacher1 = new Teacher();
        $teacher1->user_id = $user1->user_id;
        $teacher1->teacher_code = "ustadz001";
        $teacher1->first_name = "Zaidun";
        $teacher1->last_name = "bin Ahmad";
        $teacher1->email = $user1->username;
        $teacher1->phone = "088221915000";
        $teacher1->address = "Desa Damai, Kec. Tentrem, Kab. Bima";
        $teacher1->date_of_birth = date("1997-11-27");
        $teacher1->hire_date = date("2023-01-01");
        $teacher1->department = "Ustadz Hufadz";
        $teacher1->status = "active";
        $teacher1->created_at = date("Y-m-d H:i:s");
        $teacher1->updated_at = date("Y-m-d H:i:s");
        $this->teacherRepository->save($teacher1);

        $teacher2 = new Teacher();
        $teacher2->user_id = $user2->user_id;
        $teacher2->teacher_code = "ustadz002";
        $teacher2->first_name = "Umarun";
        $teacher2->last_name = "bin Hamdan";
        $teacher2->email = $user2->username;
        $teacher2->phone = "085726091996";
        $teacher2->address = "Desa Konoha, Kec. Kirigakure, Kab. Anbu";
        $teacher2->date_of_birth = date("1997-11-27");
        $teacher2->hire_date = date("2023-06-01");
        $teacher2->department = "Ustadz Hufadz";
        $teacher2->status = "active";
        $teacher2->created_at = date("Y-m-d H:i:s");
        $teacher2->updated_at = date("Y-m-d H:i:s");
        $this->teacherRepository->save($teacher2);

        $teacher3 = new Teacher();
        $teacher3->user_id = $user3->user_id;
        $teacher3->teacher_code = "ustadz003";
        $teacher3->first_name = "Bakrun";
        $teacher3->last_name = "bin Ubaid";
        $teacher3->email = $user3->username;
        $teacher3->phone = "085720072000";
        $teacher3->address = "Desa Northblue, Kec. Grandline, Kab. New World";
        $teacher3->date_of_birth = date("1997-11-27");
        $teacher3->hire_date = date("2023-06-01");
        $teacher3->department = "Ustadz Hufadz";
        $teacher3->status = "active";
        $teacher3->created_at = date("Y-m-d H:i:s");
        $teacher3->updated_at = date("Y-m-d H:i:s");
        $this->teacherRepository->save($teacher3);

        $teacherExists = $this->teacherRepository->getAllActive();
        self::assertNotNull($teacherExists);
        self::assertCount(3, $teacherExists);
    }

    public function testUpdateTeacher()
    {
        $user = UserFactory::createUser("user001", "Zaidun Qoimun", "zaid_88@ustadz.com", "zaid_88", "Ustadz");
        $this->userRepository->save($user);

        $teacher = TeacherFactory::createTeacher($user->user_id, "tchr001", "Zaidun", "Qoimun", $user->username, "088567896543", "Jl. Merdeka Antah Berantah", date('1995-08-17'), date('2011-06-01'), "Ustadz");
        $this->teacherRepository->save($teacher);

        $teacher->last_name = "Jalisun";
        $teacher->address = "Jl. Yang lurus, Kec. Iman, Kab. Taqwa";
        $teacher->date_of_birth = date("1997-11-27");
        $teacher->updated_at = date_format(date_add(date_create("now"), new DateInterval("PT3H")), "Y-m-d H:i:s");

        $updatedTeacher = $this->teacherRepository->update($teacher);
        self::assertNotNull($updatedTeacher);
        self::assertEquals($teacher->user_id, $updatedTeacher->user_id);
        self::assertEquals($teacher->teacher_code, $updatedTeacher->teacher_code);
        self::assertEquals($teacher->first_name, $updatedTeacher->first_name);
        self::assertEquals($teacher->last_name, $updatedTeacher->last_name);
        self::assertEquals($teacher->email, $updatedTeacher->email);
        self::assertEquals($teacher->phone, $updatedTeacher->phone);
        self::assertEquals($teacher->address, $updatedTeacher->address);
        self::assertEquals($teacher->date_of_birth, $updatedTeacher->date_of_birth);
        self::assertEquals($teacher->hire_date, $updatedTeacher->hire_date);
        self::assertEquals($teacher->department, $updatedTeacher->department);
        self::assertEquals("active", $updatedTeacher->status);
    }

    public function testDeleteTeacherPermanently()
    {
        $user = UserFactory::createUser("user001", "Zaidun Qoimun", "zaid_88@ustadz.com", "zaid_88", "Ustadz");
        $this->userRepository->save($user);

        $teacher = TeacherFactory::createTeacher($user->user_id, "tchr001", "Zaidun", "Qoimun", $user->username, "088567896543", "Jl. Merdeka Antah Berantah", date('1995-08-17'), date('2011-06-01'), "Ustadz");
        $this->teacherRepository->save($teacher);

        $result = $this->teacherRepository->deletePermanently($teacher->user_id);
        self::assertTrue($result);

        $teacherNF = $this->teacherRepository->findByUserId($teacher->user_id);
        self::assertNull($teacherNF);
    }

    public function testDeleteAllTeacherPermanently()
    {
        $user = UserFactory::createUser("user001", "Zaidun Qoimun", "zaid_88@ustadz.com", "zaid_88", "Ustadz");
        $this->userRepository->save($user);

        $user0 = UserFactory::createUser("user002", "Bakrun Qoimun", "zaid_87@ustadz.com", "zaid_87", "Admin");
        $this->userRepository->save($user0);

        $user1 = UserFactory::createUser("user003", "Umarun Qoimun", "zaid_86@ustadz.com", "zaid_86", "Santri");
        $this->userRepository->save($user1);

        $teacher = TeacherFactory::createTeacher($user->user_id, "tchr001", "Zaidun", "Qoimun", $user->username, "088567896543", "Jl. Merdeka Antah Berantah", date('1995-08-17'), date('2011-06-01'), "Ustadz");
        $this->teacherRepository->save($teacher);

        $teacher0 = TeacherFactory::createTeacher($user0->user_id, "tchr002", "Bakrun", "Qoimun", $user0->username, "088567896543", "Jl. Merdeka Antah Berantah", date('1995-08-17'), date('2011-06-01'), "Ustadz");
        $this->teacherRepository->save($teacher0);

        $teacher1 = TeacherFactory::createTeacher($user1->user_id, "tchr003", "Umarun", "Qoimun", $user1->username, "088567896543", "Jl. Merdeka Antah Berantah", date('1995-08-17'), date('2011-06-01'), "Ustadz");
        $this->teacherRepository->save($teacher1);

        $result = $this->teacherRepository->deleteAllPermanently();
        self::assertTrue($result);

        $teachers = $this->teacherRepository->getAll();
        self::assertCount(0,$teachers);
    }

    public function testDeleteTeacherSoftly()
    {
        $user = UserFactory::createUser("user001", "Zaidun Qoimun", "zaid_88@ustadz.com", "zaid_88", "Ustadz");
        $this->userRepository->save($user);

        $teacher = TeacherFactory::createTeacher($user->user_id, "tchr001", "Zaidun", "Qoimun", $user->username, "088567896543", "Jl. Merdeka Antah Berantah", date('1995-08-17'), date('2011-06-01'), "Ustadz");
        $this->teacherRepository->save($teacher);

        $result = $this->teacherRepository->deleteSoftly($teacher->user_id);
        self::assertTrue($result);

        $teacherNF = $this->teacherRepository->findSoftDeleted($teacher->user_id);
        self::assertNotNull($teacherNF->deleted_at);
    }

    public function testDeleteAllTeacherSoftly()
    {
        $user = UserFactory::createUser("user001", "Zaidun Qoimun", "zaid_88@ustadz.com", "zaid_88", "Ustadz");
        $this->userRepository->save($user);

        $user0 = UserFactory::createUser("user002", "Bakrun Qoimun", "zaid_87@ustadz.com", "zaid_87", "Admin");
        $this->userRepository->save($user0);

        $user1 = UserFactory::createUser("user003", "Umarun Qoimun", "zaid_86@ustadz.com", "zaid_86", "Santri");
        $this->userRepository->save($user1);

        $teacher = TeacherFactory::createTeacher($user->user_id, "tchr001", "Zaidun", "Qoimun", $user->username, "088567896543", "Jl. Merdeka Antah Berantah", date('1995-08-17'), date('2011-06-01'), "Ustadz");
        $this->teacherRepository->save($teacher);

        $teacher0 = TeacherFactory::createTeacher($user0->user_id, "tchr002", "Bakrun", "Qoimun", $user0->username, "088567896543", "Jl. Merdeka Antah Berantah", date('1995-08-17'), date('2011-06-01'), "Ustadz");
        $this->teacherRepository->save($teacher0);

        $teacher1 = TeacherFactory::createTeacher($user1->user_id, "tchr003", "Umarun", "Qoimun", $user1->username, "088567896543", "Jl. Merdeka Antah Berantah", date('1995-08-17'), date('2011-06-01'), "Ustadz");
        $this->teacherRepository->save($teacher1);

        $result = $this->teacherRepository->deleteAllSoftly();
        self::assertTrue($result);

        $teacherDeleted = $this->teacherRepository->getAll();
        self::assertCount(3,$teacherDeleted);

        $teacherExists = $this->teacherRepository->getAllActive();
        self::assertCount(0,$teacherExists);
    }

    public function testRestoreDeletedTeacher()
    {
        $user = UserFactory::createUser("user001", "Zaidun Qoimun", "zaid_88@ustadz.com", "zaid_88", "Ustadz");
        $this->userRepository->save($user);

        $teacher = TeacherFactory::createTeacher($user->user_id, "tchr001", "Zaidun", "Qoimun", $user->username, "088567896543", "Jl. Merdeka Antah Berantah", date('1995-08-17'), date('2011-06-01'), "Ustadz");
        $this->teacherRepository->save($teacher);

        $deleted = $this->teacherRepository->deleteSoftly($teacher->user_id);
        self::assertTrue($deleted);

        $restored = $this->teacherRepository->restoreSoftDeleted($teacher->user_id);
        self::assertTrue($restored);
    }

    public function testRestoreDeletedAllTeacherSoftly()
    {
        $user = UserFactory::createUser("user001", "Zaidun Qoimun", "zaid_88@ustadz.com", "zaid_88", "Ustadz");
        $this->userRepository->save($user);

        $user0 = UserFactory::createUser("user002", "Bakrun Qoimun", "zaid_87@ustadz.com", "zaid_87", "Admin");
        $this->userRepository->save($user0);

        $user1 = UserFactory::createUser("user003", "Umarun Qoimun", "zaid_86@ustadz.com", "zaid_86", "Santri");
        $this->userRepository->save($user1);

        $teacher = TeacherFactory::createTeacher($user->user_id, "tchr001", "Zaidun", "Qoimun", $user->username, "088567896543", "Jl. Merdeka Antah Berantah", date('1995-08-17'), date('2011-06-01'), "Ustadz");
        $this->teacherRepository->save($teacher);

        $teacher0 = TeacherFactory::createTeacher($user0->user_id, "tchr002", "Bakrun", "Qoimun", $user0->username, "088567896543", "Jl. Merdeka Antah Berantah", date('1995-08-17'), date('2011-06-01'), "Ustadz");
        $this->teacherRepository->save($teacher0);

        $teacher1 = TeacherFactory::createTeacher($user1->user_id, "tchr003", "Umarun", "Qoimun", $user1->username, "088567896543", "Jl. Merdeka Antah Berantah", date('1995-08-17'), date('2011-06-01'), "Ustadz");
        $this->teacherRepository->save($teacher1);

        $deleteAllSoftly = $this->teacherRepository->deleteAllSoftly();
        self::assertTrue($deleteAllSoftly);

        $teachersDeleted = $this->teacherRepository->getAll();
        self::assertCount(3,$teachersDeleted);

        foreach($teachersDeleted as $teacherResult){
            self::assertNotNull($teacherResult['deleted_at']);
        }

        $teachersRestored = $this->teacherRepository->restoreAllSoftDeleted();
        self::assertTrue($teachersRestored);

        $teacherExists = $this->teacherRepository->getAllActive();
        self::assertCount(3,$teacherExists);

        foreach($teacherExists as $teacherActive){
            self::assertNull($teacherActive['deleted_at']);
        }
    }
}
