<?php

namespace IRFANM\SIASHAF\Service;

use Exception;
use IRFANM\SIASHAF\Config\Database;
use IRFANM\SIASHAF\Domain\Guru;
use IRFANM\SIASHAF\Exception\ValidationException;
use IRFANM\SIASHAF\Model\GuruRegisterRequest;
use IRFANM\SIASHAF\Model\GuruRegisterResponse;
use IRFANM\SIASHAF\Model\GuruUpdateRequest;
use IRFANM\SIASHAF\Model\GuruUpdateResponse;
use IRFANM\SIASHAF\Model\UserRegistrationRequest;
use IRFANM\SIASHAF\Repository\GuruRepository;

class GuruService
{
    private GuruRepository $guruRepository;
    private UserService $userService;

    public function __construct(GuruRepository $guruRepository, UserService $userService)
    {
        $this->guruRepository = $guruRepository;
        $this->userService = $userService;
    }

    public function getAllGuru(): array
    {
        return $this->guruRepository->getAll();
    }

    public function addGuru(GuruRegisterRequest $request): GuruRegisterResponse
    {
        $this->validateAddGuruRequest($request);
    
        try {
            Database::beginTransaction();
    
            // Simpan data Guru
            $guru = new Guru();
            $guru->user_id = uniqid();
            $guru->nama = $request->nama;
            $guru->nik = $request->nik;
            $guru->email = $request->email;
            $guru->kontak = $request->kontak;
            $guru->created_at = date("Y-m-d H:i:s");
            $guru->updated_at = date("Y-m-d H:i:s");
            $guru->deleted_at = null;
            $this->guruRepository->save($guru);
    
            // Simpan data User
            $userRequest = new UserRegistrationRequest();
            $userRequest->user_id = $guru->user_id;
            $userRequest->username = $request->email;
            $userRequest->password = $request->nik;
            $userRequest->password_konfirmation = $request->nik;
            $userRequest->role = "guru";
    
            $this->userService->createUser($userRequest);
    
            Database::commitTransaction();
    
            $response = new GuruRegisterResponse();
            $response->guru = $guru;
            return $response;
    
        } catch (\Exception $err) {
            Database::rollbackTransaction();
            error_log("Error saat menambah guru: " . $err->getMessage());
            throw $err;
        }
    }
    
    private function validateAddGuruRequest(GuruRegisterRequest $request)
    {
        $nama = trim($request->nama);
        $nik = trim($request->nik);
        $email = trim($request->email);
        $kontak = trim($request->kontak);
    
        if (empty($nama) || empty($nik) || empty($email) || empty($kontak)) {
            throw new ValidationException("Semua bidang wajib diisi!");
        }
    
        // Validasi email unik
        $existingGuru = $this->guruRepository->findByEmail($email);
        if ($existingGuru !== null) {
            throw new ValidationException("Guru dengan email $email sudah terdaftar!");
        }
    
        // Validasi NIK unik
        $existingNIK = $this->guruRepository->findByNik($nik);
        if ($existingNIK !== null) {
            throw new ValidationException("Guru dengan NIK $nik sudah terdaftar!");
        }
    }
    

    public function updateGuru(GuruUpdateRequest $request): GuruUpdateResponse
    {
        $this->validateGuruUpdateRequest($request);

        try {
            Database::beginTransaction();

            $guru = new Guru();
            $guru->user_id = $request->user_id;
            $guru->nama = $request->nama;
            $guru->nik = $request->nik;
            $guru->email = $request->email;
            $guru->kontak = $request->kontak;
            $guru->updated_at = date("Y-m-d H:i:s");
            $this->guruRepository->update($guru);
            
            Database::commitTransaction();
            
            $response = new GuruUpdateResponse();
            $response->guru = $guru;
            return $response;
        }catch(\Exception $err){
            Database::rollbackTransaction();
            throw $err;
        }
    }

    private function validateGuruUpdateRequest(GuruUpdateRequest $request)
    {
        $nama = trim($request->nama);
        $nik = trim($request->nik);
        $email = trim ($request->email);
        $kontak = trim($request->kontak);

        if(empty($nama) || empty($nik) || empty($email) || empty($kontak)){
            throw new ValidationException("Semua bidang wajib diisi!.");
        }

        $guru = $this->guruRepository->findByUserId($request->nik);
        if($guru === null) {
            throw new ValidationException("Guru dengan NIK $nik tidak terdaftar!");
        }
    }
}
