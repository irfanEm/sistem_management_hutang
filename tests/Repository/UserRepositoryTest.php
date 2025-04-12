<?php

namespace IRFANM\SIASHAF\Repository;

use IRFANM\SIASHAF\Config\Database;
use IRFANM\SIASHAF\Domain\User;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;

    public function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConn());

        $this->userRepository->deleteAllPermanently();
    }

    public function testSave()
    {
        $user = new User();
        $user->user_id = uniqid();
        $user->username = 'user_save@test.com';
        $user->password = 'password';
        $user->role = 'santri';
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");

        $savedUser = $this->userRepository->save($user);

        self::assertEquals($user->user_id, $savedUser->user_id);
        self::assertEquals($user->username, $savedUser->username);
    }

    public function testUpdateUser()
    {
        $user = new User();
        $user->user_id = uniqid();
        $user->username = 'user_save@test.com';
        $user->password = 'password';
        $user->role = 'santri';
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");

        $savedUser = $this->userRepository->save($user);

        $savedUser->username = "user_update@test.com";
        $savedUser->role = "guru";

        $updatedUser = $this->userRepository->update($savedUser);

        self::assertNotNull($updatedUser);
        self::assertEquals($savedUser->username, $updatedUser->username);
        self::assertEquals($savedUser->role, $updatedUser->role);
    }

    public function testGetAllDeletedUsers()
    {
        for($i = 0; $i < 3; $i++) {
            $user = new User();
            $user->user_id = uniqid();
            $user->username = 'user_deleted'.$i.'@test.com';
            $user->password = 'password_'.$i;
            $user->role = 'santri_'.$i;
            $user->created_at = date("Y-m-d H:i:s");
            $user->updated_at = date("Y-m-d H:i:s");
            $user->deleted_at = null;
    
            $this->userRepository->save($user);
            $this->userRepository->deleteSoftly($user->user_id);
        }

        $deletedUsers = $this->userRepository->getAllDeleted();

        self::assertCount(3, $deletedUsers);
        for($i = 0; $i < 3; $i++){
            self::assertNotNull($deletedUsers[$i]['deleted_at']);
        }
    }

    public function testRestoreDeletedUserById()
    {
        $user = new User();
        $user->user_id = uniqid();
        $user->username = 'user_restore@test.com';
        $user->password = 'password';
        $user->role = 'santri';
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");
        $user->deleted_at = null;

        $this->userRepository->save($user);
        $this->userRepository->deleteSoftly($user->user_id);

        $deletedUser = $this->userRepository->findSoftDeleted($user->user_id);
        self::assertNotNull($deletedUser);

        $restoredUser = $this->userRepository->restoreSoftDeleted($user->user_id);

        self::assertTrue($restoredUser);

        $result = $this->userRepository->findById($user->user_id);
        self::assertNotNull($result);
    }

    public function testDeleteAll()
    {
        $user1 = new User();
        $user1->user_id = uniqid();
        $user1->username = 'user1@test.com';
        $user1->password = 'password';
        $user1->role = 'santri';
        $user1->created_at = date("Y-m-d H:i:s");
        $user1->updated_at = date("Y-m-d H:i:s");
        $user1->deleted_at = null;        

        $user2 = new User();
        $user2->user_id = uniqid();
        $user2->username = 'user2@test.com';
        $user2->password = 'password';
        $user2->role = 'santri';
        $user2->created_at = date("Y-m-d H:i:s");
        $user2->updated_at = date("Y-m-d H:i:s");
        $user2->deleted_at = null;        

        $this->userRepository->save($user1);
        $this->userRepository->save($user2);

        $this->userRepository->deleteAllSoftly();

        $deletedUsers = $this->userRepository->getAllDeleted();
        self::assertCount(2, $deletedUsers);
    }

    public function testDeleteAllPermanently()
    {
        $user1 = new User();
        $user1->user_id = uniqid();
        $user1->username = 'user_perm1@test.com';
        $user1->password = 'password';
        $user1->role = 'santri';
        $user1->created_at = date("Y-m-d H:i:s");
        $user1->updated_at = date("Y-m-d H:i:s");
        $user1->deleted_at = null; 

        $this->userRepository->save($user1);
        $this->userRepository->deleteAllPermanently();

        $result = $this->userRepository->getAllActive();
        self::assertCount(0, $result);
    }

    public function testDeletePermanentlyById()
    {
        $user = new User();
        $user->user_id = uniqid();
        $user->username = 'user_perm@test.com';
        $user->password = 'password';
        $user->role = 'santri';
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");
        $user->deleted_at = null; 

        $this->userRepository->save($user);
        $this->userRepository->deletePermanently($user->user_id);

        $result = $this->userRepository->findById($user->user_id);
        self::assertNull($result);
    }

    public function testRestoreAll()
    {
        $user1 = new User();
        $user1->user_id = uniqid();
        $user1->username = 'user1@test.com';
        $user1->password = 'password';
        $user1->role = 'santri';
        $user1->created_at = date("Y-m-d H:i:s");
        $user1->updated_at = date("Y-m-d H:i:s");
        $user1->deleted_at = date("Y-m-d H:i:s");

        $user2 = new User();
        $user2->user_id = uniqid();
        $user2->username = 'user2@test.com';
        $user2->password = 'password';
        $user2->role = 'santri';
        $user2->created_at = date("Y-m-d H:i:s");
        $user2->updated_at = date("Y-m-d H:i:s");
        $user2->deleted_at = date("Y-m-d H:i:s");

        $this->userRepository->save($user1);
        $this->userRepository->save($user2);
        $this->userRepository->restoreAllSoftDeleted();

        $result = $this->userRepository->getAllActive();
        self::assertCount(2, $result);
    }
}
