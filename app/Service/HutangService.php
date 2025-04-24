<?php

namespace IRFANM\SIMAHU\Service;

use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Domain\Hutang;
use IRFANM\SIMAHU\Exception\ValidationException;
use IRFANM\SIMAHU\Exception\DataNotFoundException;
use IRFANM\SIMAHU\Model\HutangCreateRequest;
use IRFANM\SIMAHU\Model\HutangUpdateRequest;
use IRFANM\SIMAHU\Model\HutangCreateResponse;
use IRFANM\SIMAHU\Model\HutangUpdateResponse;
use IRFANM\SIMAHU\Repository\HutangRepository;
use IRFANM\SIMAHU\Repository\UserRepository;
use IRFANM\SIMAHU\Repository\AgenRepository;
use IRFANM\SIMAHU\Repository\PaymentMethodRepository;

class HutangService
{
    private HutangRepository $hutangRepo;
    private UserRepository $userRepo;
    private AgenRepository $agenRepo;
    private PaymentMethodRepository $paymentMethodRepo;

    public function __construct(
        HutangRepository $hutangRepo,
        UserRepository $userRepo,
        AgenRepository $agenRepo,
        PaymentMethodRepository $paymentMethodRepo
    ) {
        $this->hutangRepo = $hutangRepo;
        $this->userRepo = $userRepo;
        $this->agenRepo = $agenRepo;
        $this->paymentMethodRepo = $paymentMethodRepo;
    }

    public function createHutang(HutangCreateRequest $request): HutangCreateResponse
    {
        $this->validateCreateRequest($request);

        try {
            Database::beginTransaction();

            $hutang = new Hutang();
            $hutang->debt_id = $request->debt_id;
            $hutang->user_id = $request->user_id;
            $hutang->agent_id = $request->agent_id;
            $hutang->payment_method_id = $request->payment_method_id;
            $hutang->tanggal_hutang = $request->tanggal_hutang;
            $hutang->tanggal_jatuh_tempo = $request->tanggal_jatuh_tempo;
            $hutang->sisa_hutang = $request->total_hutang; // Asumsi awal sisa = total
            
            // Auto timestamp
            $currentTime = date('Y-m-d H:i:s');
            $hutang->created_at = $currentTime;
            $hutang->updated_at = $currentTime;

            $savedHutang = $this->hutangRepo->save($hutang);

            Database::commitTransaction();
            
            return new HutangCreateResponse($savedHutang);
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    private function validateCreateRequest(HutangCreateRequest $request): void
    {
        // Validasi field required
        $requiredFields = [
            'debt_id' => 'ID Hutang',
            'user_id' => 'User',
            'agent_id' => 'Agen',
            'payment_method_id' => 'Metode Pembayaran',
            'tanggal_hutang' => 'Tanggal Hutang',
            'tanggal_jatuh_tempo' => 'Tanggal Jatuh Tempo',
            'total_hutang' => 'Total Hutang'
        ];
        
        foreach ($requiredFields as $field => $name) {
            if (empty($request->$field)) {
                throw new ValidationException("$name wajib diisi");
            }
        }

        // Validasi format tanggal
        if (!strtotime($request->tanggal_hutang) || !strtotime($request->tanggal_jatuh_tempo)) {
            throw new ValidationException("Format tanggal tidak valid");
        }

        // Validasi tanggal jatuh tempo > tanggal hutang
        if (strtotime($request->tanggal_jatuh_tempo) <= strtotime($request->tanggal_hutang)) {
            throw new ValidationException("Tanggal jatuh tempo harus setelah tanggal hutang");
        }

        // Validasi referensi data
        // $this->validateEntityExists($this->userRepo, $request->user_id, 'User');
        // $this->validateEntityExists($this->agenRepo, $request->agent_id, 'Agen');
        // $this->validateEntityExists($this->paymentMethodRepo, $request->payment_method_id, 'Metode Pembayaran');

        // Validasi duplikasi debt_id
        if ($this->hutangRepo->findById($request->debt_id) !== null) {
            throw new ValidationException("ID Hutang {$request->debt_id} sudah terdaftar");
        }
    }

    public function updateHutang(HutangUpdateRequest $request): HutangUpdateResponse
    {
        $existingHutang = $this->validateUpdateRequest($request);

        try {
            Database::beginTransaction();

            // Update field yang diizinkan
            $existingHutang->payment_method_id = $request->payment_method_id;
            $existingHutang->tanggal_jatuh_tempo = $request->tanggal_jatuh_tempo;
            $existingHutang->sisa_hutang = $request->sisa_hutang;
            $existingHutang->updated_at = date('Y-m-d H:i:s');

            $updatedHutang = $this->hutangRepo->update($existingHutang);

            Database::commitTransaction();
            
            return new HutangUpdateResponse($updatedHutang);
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    private function validateUpdateRequest(HutangUpdateRequest $request): Hutang
    {
        // Validasi data exists
        $hutang = $this->hutangRepo->findById($request->debt_id);
        if (!$hutang) {
            throw new DataNotFoundException("Hutang dengan ID {$request->debt_id} tidak ditemukan");
        }

        // Validasi sisa hutang tidak negatif
        if ($request->sisa_hutang < 0) {
            throw new ValidationException("Sisa hutang tidak boleh negatif");
        }

        // Validasi payment method
        $this->validateEntityExists($this->paymentMethodRepo, $request->payment_method_id, 'Metode Pembayaran');

        return $hutang;
    }

    public function getHutang(string $debt_id): Hutang
    {
        $hutang = $this->hutangRepo->findById($debt_id);
        if (!$hutang) {
            throw new DataNotFoundException("Hutang dengan ID {$debt_id} tidak ditemukan");
        }
        return $hutang;
    }

    public function getAllHutang(?int $agent_id = null): array
    {
        $condition = "";
        $params = [];
        
        if ($agent_id !== null) {
            $condition = "WHERE agent_id = :agent_id";
            $params = ['agent_id' => $agent_id];
        }
        
        return $this->hutangRepo->findAll($condition, $params);
    }

    public function deleteHutang(string $debt_id): bool
    {
        $hutang = $this->hutangRepo->findById($debt_id);
        if (!$hutang) {
            throw new DataNotFoundException("Hutang dengan ID {$debt_id} tidak ditemukan");
        }

        try {
            Database::beginTransaction();
            $result = $this->hutangRepo->deleteById($debt_id);
            Database::commitTransaction();
            return $result;
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    private function validateEntityExists($repository, $id, $entityName): void
    {
        if (!$repository->findById($id)) {
            throw new ValidationException("$entityName dengan ID {$id} tidak valid");
        }
    }
    
}