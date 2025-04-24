<?php

namespace IRFANM\SIMAHU\Service;

use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Domain\User;
use IRFANM\SIMAHU\Exception\ValidationException;
use IRFANM\SIMAHU\Exception\DataNotFoundException;
use IRFANM\SIMAHU\Model\UserCreateRequest;
use IRFANM\SIMAHU\Model\UserUpdateRequest;
use IRFANM\SIMAHU\Model\UserResponse;
use IRFANM\SIMAHU\Model\UserListResponse;
use IRFANM\SIMAHU\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(UserCreateRequest $request): UserResponse
    {
        $this->validateCreateRequest($request);

        try {
            Database::beginTransaction();

            $user = new User();
            $user->user_id = uniqid();
            $user->name = $request->name;
            $user->username = strtolower($request->username);
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
            $user->role = $request->role ?? 'user';
            $user->created_at = date('Y-m-d H:i:s');
            $user->updated_at = date('Y-m-d H:i:s');

            $this->checkDuplicateUsername($user->username);

            $savedUser = $this->userRepository->save($user);

            Database::commitTransaction();
            
            return new UserResponse($savedUser);
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function updateUser(UserUpdateRequest $request): UserResponse
    {
        $existingUser = $this->validateUpdateRequest($request);

        try {
            Database::beginTransaction();

            $existingUser->name = $request->name;
            $existingUser->username = strtolower($request->username);
            $existingUser->role = $request->role;
            $existingUser->updated_at = date('Y-m-d H:i:s');

            if (!empty($request->password)) {
                $existingUser->password = password_hash($request->password, PASSWORD_BCRYPT);
            }

            $updatedUser = $this->userRepository->update($existingUser);

            Database::commitTransaction();
            
            return new UserResponse($updatedUser);
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function deleteUser(string $userId): void
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new DataNotFoundException("User tidak ditemukan");
        }

        try {
            Database::beginTransaction();
            
            // Soft delete dengan mengupdate deleted_at
            $user->deleted_at = date('Y-m-d H:i:s');
            $this->userRepository->update($user);
            
            Database::commitTransaction();
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function restoreUser(string $userId): void
    {
        $user = $this->userRepository->findById($userId);
        
        if ($user == null) {
            throw new DataNotFoundException("User tidak ditemukan");
        }

        try {
            Database::beginTransaction();
            
            $user->deleted_at = null;
            $this->userRepository->update($user);
            
            Database::commitTransaction();
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function forceDeleteUser(string $userId): void
    {
        try {
            Database::beginTransaction();
            $this->userRepository->deletePermanently($userId);
            Database::commitTransaction();
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function getUserById(string $userId): UserResponse
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new DataNotFoundException("User tidak ditemukan");
        }
        
        return new UserResponse($user);
    }

    public function getAllUsers(
        string $search = '',
        int $page = 1,
        int $perPage = 10
    ): UserListResponse {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $condition = "deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $condition .= " AND (name LIKE ? OR username LIKE ?)";
            $params = ["%$search%", "%$search%"];
        }

        $users = $this->userRepository->getAll("WHERE $condition LIMIT $perPage OFFSET $offset", $params);
        $total = count($this->userRepository->getAll("WHERE $condition", $params));

        return new UserListResponse($users, $total, $page, $perPage);
    }

    private function validateCreateRequest(UserCreateRequest $request): void
    {
        $errors = [];

        if (empty(trim($request->name))) {
            $errors[] = "Nama wajib diisi";
        }

        if (empty(trim($request->username))) {
            $errors[] = "Username wajib diisi";
        }

        if (empty($request->password)) {
            $errors[] = "Password wajib diisi";
        } elseif (strlen($request->password) < 8) {
            $errors[] = "Password minimal 8 karakter";
        }

        if (!empty($errors)) {
            throw new ValidationException(implode(", ", $errors));
        }
    }

    private function validateUpdateRequest(UserUpdateRequest $request): User
    {
        $user = $this->userRepository->findById($request->user_id);
        
        if (!$user) {
            throw new DataNotFoundException("User tidak ditemukan");
        }

        $errors = [];

        if (empty(trim($request->name))) {
            $errors[] = "Nama wajib diisi";
        }

        if (!empty($errors)) {
            throw new ValidationException(implode(", ", $errors));
        }

        return $user;
    }

    private function checkDuplicateUsername(string $username): void
    {
        $existing = $this->userRepository->findByUsername($username);
        if ($existing) {
            throw new ValidationException("Username sudah digunakan");
        }
    }
}