<?php

namespace IRFANM\SIMAHU\Service;

use Exception;
use IRFANM\SIMAHU\Domain\Agen;
use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Model\AgenCreateRequest;
use IRFANM\SIMAHU\Model\AgenUpdateRequest;
use IRFANM\SIMAHU\Model\AgenCreateResponse;
use IRFANM\SIMAHU\Model\AgenUpdateResponse;
use IRFANM\SIMAHU\Repository\AgenRepository;
use IRFANM\SIMAHU\Exception\ValidationException;

class AgenService
{
    private AgenRepository $agenRepo;

    public function __construct(AgenRepository $agenRepo)
    {
        $this->agenRepo = $agenRepo;        
    }

    public function addAgen(AgenCreateRequest $request): AgenCreateResponse
    {
        $this->verifyAgenCreateRequest($request);

        try{
            Database::beginTransaction();

            $agen = new Agen();
            $agen->kode_agen = $request->kode_agen;
            $agen->nama_agen = $request->nama_agen;
            $agen->kontak = $request->kontak ?? 'belum ada';
            $agen->created_at = date('Y-m-d H:i:s');
            $agen->updated_at = date('Y-m-d H:i:s');

            $savedAgen = $this->agenRepo->save($agen);

            $response = new AgenCreateResponse();
            $response->agen = $savedAgen;

            Database::commitTransaction();
            return $response;
        }catch(Exception $err){
            Database::rollbackTransaction();
            throw $err;
        }
        
    }

    private function verifyAgenCreateRequest(AgenCreateRequest $request)
    {
        if($request->kode_agen == null || $request->nama_agen == null || trim($request->kode_agen) == '' || trim($request->nama_agen) == '') {
            throw new ValidationException("Kode agen & nama agen wajib diisi !");
        }

        $existAgen = $this->agenRepo->findById($request->kode_agen);
        if($existAgen != null) {
            throw new ValidationException("Data Agen dengan kode {$request->kode_agen} sudah ada !");
        }

    }

    public function updateAgen(AgenUpdateRequest $request): AgenUpdateResponse
    {
        $agen = $this->verifyAgenUpdateRequest($request);

        try{
            Database::beginTransaction();

            $agen->nama_agen = $request->nama_agen;
            $agen->kontak = $request->kontak;
            $agen->updated_at = Date("Y-m-d H:i:s");

            $updatedAgen = $this->agenRepo->update($agen);

            $response = new AgenUpdateResponse();
            $response->agen = $updatedAgen;

            Database::commitTransaction();

            return $response;
        }catch(Exception $err){
            Database::rollbackTransaction();
            throw $err;
        }
    }

    private function verifyAgenUpdateRequest(AgenUpdateRequest $request)
    {
        if($request->nama_agen == null || trim($request->nama_agen) == '') {
            throw new ValidationException("Nama agen tidak boleh kosong !");
        }

        $agen = $this->agenRepo->findById($request->kode_agen);
        if($agen == null) {
            throw new ValidationException("Data agen tidak ditemukan !");
        }

        return $agen;
    }

    public function getAgen(?string $kode_agen = null): Agen | array | null
    {
        if($kode_agen !== null){
            try{
                return $this->agenRepo->findById($kode_agen);
            }catch(Exception $err){
                throw $err;
            }
        }

        try{
            return $this->agenRepo->getAll();
        }catch(Exception $err){
            throw $err;
        }
    }

    public function deleteAgen(string $kode_agen): bool
    {
        $agen = $this->agenRepo->findById($kode_agen);
        if($agen === null){
            throw new Exception("Agen dengan kode : {$kode_agen} tidak ditemukan !");
        }
        try{
            return $this->agenRepo->deletePermanently($kode_agen);
        }catch(Exception $err){
            throw $err;
            return false;
        }
    }
}
