<?php

namespace IRFANM\SIMAHU\Tests\Service;

use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Domain\User;
use IRFANM\SIMAHU\Exception\ValidationException;
use IRFANM\SIMAHU\Exception\DataNotFoundException;
use IRFANM\SIMAHU\Model\UserCreateRequest;
use IRFANM\SIMAHU\Model\UserUpdateRequest;
use IRFANM\SIMAHU\Repository\UserRepository;
use IRFANM\SIMAHU\Service\UserService;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private UserService $userService;

    protected function setUp(): void
    {
        $conn = Database::getConn();
        $this->userRepository = new UserRepository($conn);
        $this->userService = new UserService($this->userRepository);
        
        $this->userRepository->deleteAllPermanently();
    }

    public function testCreateUserSuccess()
    {
        $request = new UserCreateRequest();
        $request->name = "John Doe";
        $request->username = "johndoe";
        $request->password = "password123";
        
        $response = $this->userService->createUser($request);
        
        self::assertEquals("johndoe", $response->user->username);
        self::assertTrue(password_verify("password123", $response->user->password));
    }

    public function testCreateUserValidationFailed()
    {
        $this->expectException(ValidationException::class);
        
        $request = new UserCreateRequest();
        $request->name = "";
        $request->username = "";
        $request->password = "short";
        
        $this->userService->createUser($request);
    }

    public function testCreateUserDuplicateUsername()
    {
        $this->createTestUser();
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Username sudah digunakan");
        
        $request = new UserCreateRequest();
        $request->name = "Jane Doe";
        $request->username = "testuser";
        $request->password = "password123";
        
        $this->userService->createUser($request);
    }

    public function testUpdateUserSuccess()
    {
        $user = $this->createTestUser();
        
        $request = new UserUpdateRequest();
        $request->user_id = $user->user_id;
        $request->name = "Updated Name";
        $request->password = "newpassword123";
        $request->role = "user";
        
        $response = $this->userService->updateUser($request);
        
        self::assertEquals("Updated Name", $response->user->name);
        self::assertTrue(password_verify("newpassword123", $response->user->password));
    }

    public function testDeleteUser()
    {
        $user = $this->createTestUser();
        
        $this->userService->deleteUser($user->user_id);
        
        $deletedUser = $this->userRepository->findById($user->user_id);
        self::assertNotNull($deletedUser->deleted_at);
    }

    public function testRestoreUser()
    {
        $user = $this->createTestUser();
        $this->userService->deleteUser($user->user_id);
        
        $this->userService->restoreUser($user->user_id);
        
        $restoredUser = $this->userRepository->findById($user->user_id);
        self::assertNull($restoredUser->deleted_at);
    }

    public function testForceDeleteUser()
    {
        $user = $this->createTestUser();
        
        $this->userService->forceDeleteUser($user->user_id);
        
        $result = $this->userRepository->findById($user->user_id);
        self::assertNull($result);
    }

    public function testGetUserNotFound()
    {
        $this->expectException(DataNotFoundException::class);
        $this->userService->getUserById("invalid_id");
    }

    public function testGetAllUsers()
    {
        $this->createTestUser("user1");
        $this->createTestUser("user2");
        
        $response = $this->userService->getAllUsers();
        
        self::assertCount(2, $response->users);
        self::assertEquals(2, $response->total);
    }

    private function createTestUser(string $username = "testuser"): User
    {
        $request = new UserCreateRequest();
        $request->name = "Test User";
        $request->username = $username;
        $request->password = "password";
        
        return $this->userService->createUser($request)->user;
    }
}