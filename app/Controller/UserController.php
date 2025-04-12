<?php

namespace IRFANM\SIASHAF\Controller;

use Exception;
use IRFANM\SIASHAF\App\View;
use IRFANM\SIASHAF\Config\Database;
use IRFANM\SIASHAF\Exception\ValidationException;
use IRFANM\SIASHAF\Model\UserLoginRequest;
use IRFANM\SIASHAF\Model\UserRegistrationRequest;
use IRFANM\SIASHAF\Model\UserUpdateRequest;
use IRFANM\SIASHAF\Repository\SessionRepository;
use IRFANM\SIASHAF\Repository\UserRepository;
use IRFANM\SIASHAF\Service\SessionService;
use IRFANM\SIASHAF\Service\UserService;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConn();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }
    /**
     * Menampilkan semua pengguna
     */
    public function home(): void
    {
        $users = $this->userService->getAllUsers();
        $currentUser = $this->sessionService->current();

        View::render("/Beranda/index", [
            "title" => "Beranda",
            "users" => $users,
            "curentUser" => $currentUser,
        ]);
    }

    /**
     * Menampilkan semua pengguna
     */
    public function index(): void
    {
        $users = $this->userService->getAllActiveUsers();
        $currentUser = $this->sessionService->current();

        View::render("/User/index", [
            "title" => "Data users",
            "users" => $users,
            "curentUser" => $currentUser,
        ]);
    }

    /**
     * Mengakses halaman register
     *
     */
    public function register()
    {
        View::render("User/tambah", [
            "title" => "Tambah user",
        ]);
    }

    /**
     * Menambahkan pengguna baru
     */
    public function postRegister(): void
    {
        $request = new UserRegistrationRequest();
        $request->name = $_POST['name'];
        $request->username = $_POST['username'];
        $request->password = $_POST['password'];
        $request->password_konfirmation = $_POST['password_konfirmation'];
        $request->role = $_POST['role'];

        try {
            $this->userService->createUser($request);
            $users = $this->userService->getAllUsers();
            View::render("User/index", [
                "title" => "Data Users",
                "users" => $users,
                "error" => "Berhasil menambahkan user baru",
                "status" => "success"
            ]);
        }catch(ValidationException $err){
            View::render("User/tambah", [
                "title" => "Tambah user baru.",
                "error" => $err->getMessage(),
                "status" => "danger"
            ]);
        }
    }

    /**
     * login user
     */
    public function login()
    {
        View::render("User/login", [
            "title" => "Login user",
        ]);
    }

    /**
     * handle login post
     */
    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->username = $_POST['username'];
        $request->password = $_POST['password'];

        try {
            $response = $this->userService->login($request);
            $this->sessionService->create($response->user);
            View::redirect("/admin/beranda");
        }catch(ValidationException $err) {
            View::render("User/login", [
                "title" => "Login user",
                "error" => $err->getMessage(),
            ]);

        }
    }

    /**
     * function logout
     */
    public function logout()
    {
        $this->sessionService->destroy();
        View::redirect("/users/login");
    }

    /**
     * Menampilkan form update
     */
    public function update(string $user_id)
    {
        $user = $this->userService->findUserById($user_id);
        View::render("User/edit", [
            'title' => 'Update user',
            'user_id' => $user->user_id,
            'nama' => $user->name,
            'username' => $user->username,
            'role' => $user->role,
            'created_at' => $user->created_at

        ]);
    }

    /**
     * Memperbarui data pengguna
     */
    public function postUpdate(): void
    {
        $request = new UserUpdateRequest();
        $request->user_id = $_POST['user_id'];
        $request->name = $_POST['name'];
        $request->username = $_POST['username'];
        $request->role = $_POST['role'];

        try {
            $this->userService->updateUser($request);
            $users = $this->userService->getAllUsers();
            View::render("User/index", [
                "title" => "Data Users",
                "users" => $users,
                "error" => "Berhasil mengubah data user.",
                "status" => "success"
            ]);
        }catch(\Exception $e) {
            View::render("User/edit", [
                "title" => "Update user", 
                "error" => $e->getMessage(),
                'user_id' => $request->user_id,
                'nama' => $request->name,
                'username' => $request->username,
                'role' => $request->role,
            ]);
        }
    }

    /**
     * Menampilkan detail pengguna tertentu
     */
    public function show(string $user_id): void
    {
        $user = $this->userService->findUserById($user_id);

        View::render('User/detail', [
            'title' => 'Detail User',
            'user_id' => $user->user_id,
            'nama' => $user->name,
            'username' => $user->username,
            'role' => $user->role,
            'created_at' => $user->created_at
        ]);
    }

    /**
     * Soft delete pengguna tertentu
     */
    public function hapus(string $user_id): void
    {
        if ($this->userService->deleteUserById($user_id)) {
            echo json_encode(["message" => "User berhasil dihapus (soft delete)."]);
        } else {
            echo json_encode(["error" => "User tidak ditemukan."]);
        }
    }

    /**
     * Hapus pengguna tertentu secara permanen
     */
    public function destroyPermanently(string $user_id): void
    {
        if ($this->userService->deleteUserPermanently($user_id)) {
            echo json_encode(["message" => "User berhasil dihapus secara permanen."]);
        } else {
            echo json_encode(["error" => "User tidak ditemukan."]);
        }
    }

    /**
     * Soft delete semua pengguna
     */
    public function destroyAll(): void
    {
        $this->userService->deleteAllUsers();
        echo json_encode(["message" => "Semua pengguna berhasil dihapus (soft delete)."]);
    }

    /**
     * Hapus semua pengguna secara permanen
     */
    public function destroyAllPermanently(): void
    {
        $this->userService->deleteAllUsersPermanently();
        echo json_encode(["message" => "Semua pengguna berhasil dihapus secara permanen."]);
    }
}
