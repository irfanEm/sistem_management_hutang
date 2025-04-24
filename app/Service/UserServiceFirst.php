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
            $user->username = strtolower($request->username);
            $user->nama = $request->nama;
            $user->email = $request->email;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
            $user->role = $request->role ?? 'user';
            $user->status = $request->status ?? 1;
            $user->created_at = date('Y-m-d H:i:s');
            $user->updated_at = date('Y-m-d H:i:s');

            $this->checkDuplicateUsername($user->username);
            $this->checkDuplicateEmail($user->email);

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

            // Update field yang diizinkan
            $existingUser->nama = $request->nama;
            $existingUser->email = $request->email;
            $existingUser->role = $request->role;
            $existingUser->status = $request->status;
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

    public function deleteUser(string $username): void
    {
        $user = $this->userRepository->findById($username);
        
        if (!$user) {
            throw new DataNotFoundException("User tidak ditemukan");
        }

        try {
            Database::beginTransaction();
            $this->userRepository->deletePermanently($username);
            Database::commitTransaction();
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function getUserByUsername(string $username): UserResponse
    {
        $user = $this->userRepository->findById($username);
        
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
        // Validasi pagination
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        // Build kondisi pencarian
        $condition = '';
        $params = [];
        
        if (!empty($search)) {
            $condition = "username LIKE ? OR nama LIKE ? OR email LIKE ?";
            $params = ["%{$search}%", "%{$search}%", "%{$search}%"];
        }

        $users = $this->userRepository->getAll($condition, $params, $perPage, $offset);
        $total = $this->userRepository->count($condition, $params);

        return new UserListResponse($users, $total, $page, $perPage);
    }

    public function resetPassword(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            throw new DataNotFoundException("Email tidak terdaftar");
        }

        try {
            Database::beginTransaction();

            // Generate reset token
            $user->reset_token = bin2hex(random_bytes(32));
            $user->reset_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $this->userRepository->update($user);

            // TODO: Kirim email reset password
            Database::commitTransaction();
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function confirmPasswordReset(string $token, string $newPassword): void
    {
        $user = $this->userRepository->findByResetToken($token);
        
        if (!$user || $user->reset_expiry < date('Y-m-d H:i:s')) {
            throw new ValidationException("Token reset tidak valid atau kadaluarsa");
        }

        try {
            Database::beginTransaction();

            $user->password = password_hash($newPassword, PASSWORD_BCRYPT);
            $user->reset_token = null;
            $user->reset_expiry = null;
            
            $this->userRepository->update($user);

            Database::commitTransaction();
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    private function validateCreateRequest(UserCreateRequest $request): void
    {
        $errors = [];

        // Validasi username
        if (empty(trim($request->username))) {
            $errors[] = "Username wajib diisi";
        } elseif (!preg_match('/^[a-zA-Z0-9_]{5,20}$/', $request->username)) {
            $errors[] = "Username hanya boleh mengandung huruf, angka, dan underscore (5-20 karakter)";
        }

        // Validasi nama
        if (empty(trim($request->nama))) {
            $errors[] = "Nama lengkap wajib diisi";
        } elseif (strlen($request->nama) > 100) {
            $errors[] = "Nama maksimal 100 karakter";
        }

        // Validasi email
        if (empty(trim($request->email)) || !filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid";
        }

        // Validasi password
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
        $user = $this->userRepository->findById($request->username);
        
        if (!$user) {
            throw new DataNotFoundException("User tidak ditemukan");
        }

        $errors = [];

        // Validasi email
        if (empty(trim($request->email)) || !filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid";
        }

        // Validasi role
        $allowedRoles = ['admin', 'user', 'manager'];
        if (!in_array($request->role, $allowedRoles)) {
            $errors[] = "Role tidak valid";
        }

        if (!empty($errors)) {
            throw new ValidationException(implode(", ", $errors));
        }

        return $user;
    }

    private function checkDuplicateUsername(string $username): void
    {
        $existing = $this->userRepository->findById($username);
        if ($existing) {
            throw new ValidationException("Username sudah terdaftar");
        }
    }

    private function checkDuplicateEmail(string $email): void
    {
        $existing = $this->userRepository->findByEmail($email);
        if ($existing) {
            throw new ValidationException("Email sudah terdaftar");
        }
    }
}