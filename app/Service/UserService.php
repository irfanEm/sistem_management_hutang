<?php

namespace IRFANM\SIASHAF\Service;

use Exception;
use IRFANM\SIASHAF\Config\Database;
use IRFANM\SIASHAF\Domain\User;
use IRFANM\SIASHAF\Domain\Vip;
use IRFANM\SIASHAF\Exception\ValidationException;
use IRFANM\SIASHAF\Model\UserLoginRequest;
use IRFANM\SIASHAF\Model\UserLoginResponse;
use IRFANM\SIASHAF\Model\UserRegistrationRequest;
use IRFANM\SIASHAF\Model\UserRegistrationResponse;
use IRFANM\SIASHAF\Model\UserUpdateRequest;
use IRFANM\SIASHAF\Model\UserUpdateResponse;
use IRFANM\SIASHAF\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;
    private SessionService $sessionService;
    private Vip $vip;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->vip = new Vip();
    }

    /**
     * Mengambil semua data pengguna
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->getAll();
    }

        /**
     * Mengambil semua data pengguna
     */
    public function getAllActiveUsers(): array
    {
        return $this->userRepository->getAllActive();
    }

    /**
     * Menyimpan data pengguna baru
     */
    public function createUser(UserRegistrationRequest $request): UserRegistrationResponse  
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();
            $user = new User();
            $user->user_id = $request->user_id ?? uniqid();
            $user->name = $request->name;
            $user->username = $request->username;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
            $user->role = $request->role;
            $user->created_at = date("Y-m-d H:i:s");
            $user->updated_at = date("Y-m-d H:i:s");
            $user->deleted_at = null;

            $this->userRepository->save($user);

            $response = new UserRegistrationResponse();
            $response->user = $user;

            Database::commitTransaction();
            return $response;
        }catch(Exception $err) {
            Database::rollbackTransaction();
            throw $err;
        }
    }

    private function validateUserRegistrationRequest(UserRegistrationRequest $request)
    {
        // Pengecekan apakah ada field yang kosong
        if (empty(trim($request->name)) || 
            empty(trim($request->username)) || 
            empty(trim($request->password)) || 
            empty(trim($request->password_konfirmation)) || 
            empty(trim($request->role))) {
            throw new ValidationException("Nama, username, password, dan role tidak boleh kosong!");
        }
    
        // Validasi kecocokan password dan konfirmasinya
        if ($request->password !== $request->password_konfirmation) {
            throw new ValidationException("Password dan konfirmasi password tidak cocok!");
        }
    
        // Validasi apakah username sudah digunakan
        if ($this->userRepository->findByUsername($request->username) !== null) {
            throw new ValidationException("User dengan username tersebut sudah ada!");
        }
    }

    /**
     * function login
     */
    public function login(UserLoginRequest $request): UserLoginResponse
    {
        try {
            $result = $this->validateLoginRequest($request);
    
            // Login sukses untuk user biasa
            // Lanjutkan proses login (misalnya buat session)
            $response = new UserLoginResponse();
            $response->user = $result;
    
            return $response;
    
        } catch (ValidationException $e) {
            // Tangani error validasi
            throw $e;
        }
    }

    /**
     * function validate login request
     */
    private function validateLoginRequest(UserLoginRequest $request)
    {
        // Trim dan validasi input tidak kosong
        $username = trim($request->username);
        $password = trim($request->password);
    
        if (empty($username) || empty($password)) {
            throw new ValidationException("Username dan password tidak boleh kosong!");
        }
    
        // Cek apakah user adalah superadmin
        $vipUsername = $this->vip->getVipUsername();
        $vipPassWord = $this->vip->getVipPassword();
        if ($username === $vipUsername && $password === $vipPassWord) {
            // Tandai user sebagai superadmin (opsional)
            $user = new User();
            $user->user_id = $this->vip->user_id;
            $user->username = $vipUsername;
            $user->password = $vipPassWord;
            $user->role = $this->vip->role;
            $user->created_at = $this->vip->created_at;
            $user->updated_at = $this->vip->updated_at;
            $user->deleted_at = $this->vip->deleted_at;

            return $user;
        }
    
        // Validasi apakah user sudah terdaftar
        $user = $this->userRepository->findByUsername($username);
        if ($user === null) {
            throw new ValidationException("User dengan username {$username} belum terdaftar, silakan registrasi.");
        }
    
        // Validasi password
        if (!password_verify($password, $user->password)) {
            throw new ValidationException("Username atau password yang dimasukkan salah!");
        }
    
        return $user; // Kembalikan user jika validasi berhasil
    }
    


    /**
     * Memperbarui data pengguna
     */
    public function updateUser(UserUpdateRequest $request): UserUpdateResponse
    {
        $user = $this->validateUserUpdateRequest($request);

        try{
            Database::beginTransaction();
            
            $user->user_id = $request->user_id;
            $user->name = $request->name;
            $user->username = $request->username;
            $user->role = $request->role;
            $this->userRepository->update($user);

            Database::commitTransaction();

            $userUpdateResponse = new UserUpdateResponse();
            $userUpdateResponse->user = $user;
            return $userUpdateResponse;
            
        }catch(\Exception $err){
            Database::rollbackTransaction();
            throw $err;
        }
    }

    private function validateUserUpdateRequest(UserUpdateRequest $request): User
    {
        $name = trim($request->name);
        $username = trim($request->username);
        $role = trim($request->role);
        if(empty($name) || empty($username) || empty($role)){
            throw new ValidationException("Nama, username dan role tidak boleh kosong!");
        }

        $user = $this->userRepository->findById($request->user_id);
        if ($user === null) {
            throw new ValidationException("User dengan username '$request->username' tidak ditemukan.");
        }

        return $user;
    }

    /**
     * Mencari pengguna berdasarkan ID
     */
    public function findUserById(string $user_id): ?User
    {
        return $this->userRepository->findById($user_id);
    }

    /**
     * Menghapus pengguna secara soft delete
     */
    public function deleteUserById(string $user_id): bool
    {
        $user = $this->userRepository->findById($user_id);

        if ($user === null) {
            return false;
        }

        $user->deleted_at = date('Y-m-d H:i:s');
        return $this->userRepository->deleteSoftly($user->user_id);
    }

    /**
     * Menghapus pengguna secara permanen
     */
    public function deleteUserPermanently(string $user_id): bool
    {
        return $this->userRepository->deletePermanently($user_id);
    }

    /**
     * Soft delete semua pengguna
     */
    public function deleteAllUsers(): bool
    {
        return $this->userRepository->deleteAllSoftly();
    }

    /**
     * Menghapus semua pengguna secara permanen
     */
    public function deleteAllUsersPermanently(): bool
    {
        return $this->userRepository->deleteAllPermanently();
    }
}
