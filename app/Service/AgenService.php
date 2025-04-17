<?php

namespace IRFANM\SIASHAF\Service;

use Exception;
use IRFANM\SIASHAF\Domain\Agen;
use IRFANM\SIASHAF\Config\Database;
use IRFANM\SIASHAF\Model\AgenCreateRequest;
use IRFANM\SIASHAF\Model\AgenUpdateRequest;
use IRFANM\SIASHAF\Model\AgenCreateResponse;
use IRFANM\SIASHAF\Model\AgenUpdateResponse;
use IRFANM\SIASHAF\Repository\AgenRepository;
use IRFANM\SIASHAF\Exception\ValidationException;

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
}
