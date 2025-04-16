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
        $user->username = uniqid();
        $user->nama = 'User Test 1';
        $user->email = 'user_save@test.com';
        $user->password = 'password';
        $user->role = 'user';
        $user->status = 1;
        $user->reset_token = null;
        $user->reset_expiry = null;
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");

        $savedUser = $this->userRepository->save($user);

        self::assertEquals($user->username, $savedUser->username);
        self::assertEquals($user->nama, $savedUser->nama);
        self::assertEquals($user->email, $savedUser->email);
        self::assertEquals($user->password, $savedUser->password);
        self::assertEquals($user->role, $savedUser->role);
        self::assertEquals($user->status, $savedUser->status);
    }

    public function testGetAll()
    {
        $user1 = new User();
        $user1->username = uniqid();
        $user1->nama = 'User 1';
        $user1->email = 'user_satu@test.com';
        $user1->password = 'password_satu';
        $user1->role = 'user';
        $user1->status = 1;
        $user1->reset_token = null;
        $user1->reset_expiry = null;
        $user1->created_at = date("Y-m-d H:i:s");
        $user1->updated_at = date("Y-m-d H:i:s");

        $this->userRepository->save($user1);

        $user2 = new User();
        $user2->username = uniqid();
        $user2->nama = 'User 2';
        $user2->email = 'user_dua@test.com';
        $user2->password = 'password_dua';
        $user2->role = 'user';
        $user2->status = 1;
        $user2->reset_token = null;
        $user2->reset_expiry = null;
        $user2->created_at = date("Y-m-d H:i:s");
        $user2->updated_at = date("Y-m-d H:i:s");

        $this->userRepository->save($user2);

        $user3 = new User();
        $user3->username = uniqid();
        $user3->nama = 'User 3';
        $user3->email = 'user_tiga@test.com';
        $user3->password = 'password_tiga';
        $user3->role = 'admin';
        $user3->status = 1;
        $user3->reset_token = null;
        $user3->reset_expiry = null;
        $user3->created_at = date("Y-m-d H:i:s");
        $user3->updated_at = date("Y-m-d H:i:s");

        $this->userRepository->save($user3);

        $users = $this->userRepository->getAll();

        self::assertNotNull($users);
        self::assertCount(3, $users);
    }

    public function testFindUserById()
    {
        $user = new User();
        $user->username = uniqid();
        $user->nama = 'User Test Find By Username';
        $user->email = 'user_save@test.com';
        $user->password = 'password';
        $user->role = 'user';
        $user->status = 1;
        $user->reset_token = null;
        $user->reset_expiry = null;
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");

        $this->userRepository->save($user);
        $savedUser = $this->userRepository->findById($user->username);

        self::assertEquals($user->username, $savedUser->username);
        self::assertEquals($user->nama, $savedUser->nama);
        self::assertEquals($user->email, $savedUser->email);
        self::assertEquals($user->password, $savedUser->password);
        self::assertEquals($user->role, $savedUser->role);
        self::assertEquals($user->status, $savedUser->status);
    }

    public function testUpdateUser()
    {
        $user = new User();
        $user->username = uniqid();
        $user->nama = 'User Test Default';
        $user->email = 'user_default@test.com';
        $user->password = 'password_default';
        $user->role = 'user';
        $user->status = 0;
        $user->reset_token = null;
        $user->reset_expiry = null;
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");

        $this->userRepository->save($user);

        $user->nama = "User Test Update";
        $user->email = "user_updated@test.com";
        $user->password = "password_update";
        $user->role = "admin";
        $user->status = 1;

        $this->userRepository->update($user);

        $updatedUser = $this->userRepository->findById($user->username);

        self::assertNotNull($updatedUser);
        self::assertEquals($user->username, $updatedUser->username);
        self::assertEquals($user->nama, $updatedUser->nama);
        self::assertEquals($user->email, $updatedUser->email);
        self::assertEquals($user->password, $updatedUser->password);
        self::assertEquals($user->role, $updatedUser->role);
        self::assertEquals($user->status, $updatedUser->status);
    }

    public function testDeletePermanentlyById()
    {
        $user = new User();
        $user->username = uniqid();
        $user->nama = 'User Test Default';
        $user->email = 'user_default@test.com';
        $user->password = 'password_default';
        $user->role = 'user';
        $user->status = 0;
        $user->reset_token = null;
        $user->reset_expiry = null;
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");

        $this->userRepository->save($user);
        $this->userRepository->deletePermanently($user->username);

        $result = $this->userRepository->findById($user->username);
        self::assertNull($result);
    }

    // public function testGetAllDeletedUsers()
    // {
    //     for($i = 0; $i < 3; $i++) {
    //         $user = new User();
    //         $user->user_id = uniqid();
    //         $user->username = 'user_deleted'.$i.'@test.com';
    //         $user->password = 'password_'.$i;
    //         $user->role = 'santri_'.$i;
    //         $user->created_at = date("Y-m-d H:i:s");
    //         $user->updated_at = date("Y-m-d H:i:s");
    //         $user->deleted_at = null;
    
    //         $this->userRepository->save($user);
    //         $this->userRepository->deleteSoftly($user->user_id);
    //     }

    //     $deletedUsers = $this->userRepository->getAllDeleted();

    //     self::assertCount(3, $deletedUsers);
    //     for($i = 0; $i < 3; $i++){
    //         self::assertNotNull($deletedUsers[$i]['deleted_at']);
    //     }
    // }

    // public function testRestoreDeletedUserById()
    // {
    //     $user = new User();
    //     $user->user_id = uniqid();
    //     $user->username = 'user_restore@test.com';
    //     $user->password = 'password';
    //     $user->role = 'santri';
    //     $user->created_at = date("Y-m-d H:i:s");
    //     $user->updated_at = date("Y-m-d H:i:s");
    //     $user->deleted_at = null;

    //     $this->userRepository->save($user);
    //     $this->userRepository->deleteSoftly($user->user_id);

    //     $deletedUser = $this->userRepository->findSoftDeleted($user->user_id);
    //     self::assertNotNull($deletedUser);

    //     $restoredUser = $this->userRepository->restoreSoftDeleted($user->user_id);

    //     self::assertTrue($restoredUser);

    //     $result = $this->userRepository->findById($user->user_id);
    //     self::assertNotNull($result);
    // }

    // public function testDeleteAll()
    // {
    //     $user1 = new User();
    //     $user1->user_id = uniqid();
    //     $user1->username = 'user1@test.com';
    //     $user1->password = 'password';
    //     $user1->role = 'santri';
    //     $user1->created_at = date("Y-m-d H:i:s");
    //     $user1->updated_at = date("Y-m-d H:i:s");
    //     $user1->deleted_at = null;        

    //     $user2 = new User();
    //     $user2->user_id = uniqid();
    //     $user2->username = 'user2@test.com';
    //     $user2->password = 'password';
    //     $user2->role = 'santri';
    //     $user2->created_at = date("Y-m-d H:i:s");
    //     $user2->updated_at = date("Y-m-d H:i:s");
    //     $user2->deleted_at = null;        

    //     $this->userRepository->save($user1);
    //     $this->userRepository->save($user2);

    //     $this->userRepository->deleteAllSoftly();

    //     $deletedUsers = $this->userRepository->getAllDeleted();
    //     self::assertCount(2, $deletedUsers);
    // }

    // public function testDeleteAllPermanently()
    // {
    //     $user1 = new User();
    //     $user1->user_id = uniqid();
    //     $user1->username = 'user_perm1@test.com';
    //     $user1->password = 'password';
    //     $user1->role = 'santri';
    //     $user1->created_at = date("Y-m-d H:i:s");
    //     $user1->updated_at = date("Y-m-d H:i:s");
    //     $user1->deleted_at = null; 

    //     $this->userRepository->save($user1);
    //     $this->userRepository->deleteAllPermanently();

    //     $result = $this->userRepository->getAllActive();
    //     self::assertCount(0, $result);
    // }

    // public function testRestoreAll()
    // {
    //     $user1 = new User();
    //     $user1->user_id = uniqid();
    //     $user1->username = 'user1@test.com';
    //     $user1->password = 'password';
    //     $user1->role = 'santri';
    //     $user1->created_at = date("Y-m-d H:i:s");
    //     $user1->updated_at = date("Y-m-d H:i:s");
    //     $user1->deleted_at = date("Y-m-d H:i:s");

    //     $user2 = new User();
    //     $user2->user_id = uniqid();
    //     $user2->username = 'user2@test.com';
    //     $user2->password = 'password';
    //     $user2->role = 'santri';
    //     $user2->created_at = date("Y-m-d H:i:s");
    //     $user2->updated_at = date("Y-m-d H:i:s");
    //     $user2->deleted_at = date("Y-m-d H:i:s");

    //     $this->userRepository->save($user1);
    //     $this->userRepository->save($user2);
    //     $this->userRepository->restoreAllSoftDeleted();

    //     $result = $this->userRepository->getAllActive();
    //     self::assertCount(2, $result);
    // }
}
