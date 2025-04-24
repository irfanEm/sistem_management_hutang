<?php

namespace IRFANM\SIMAHU\Service;

use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Domain\PaymentMethod;
use IRFANM\SIMAHU\Exception\ValidationException;
use IRFANM\SIMAHU\Model\PaymentMethodCreateRequest;
use IRFANM\SIMAHU\Model\PaymentMethodUpdateRequest;
use IRFANM\SIMAHU\Model\PaymentMethodResponse;
use IRFANM\SIMAHU\Model\PaymentMethodListResponse;
use IRFANM\SIMAHU\Repository\PaymentMethodRepository;

class PaymentMethodService
{
    private PaymentMethodRepository $paymentMethodRepository;

    public function __construct(PaymentMethodRepository $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function createPaymentMethod(PaymentMethodCreateRequest $request): PaymentMethodResponse
    {
        $this->validateCreateRequest($request);

        try {
            Database::beginTransaction();

            $paymentMethod = new PaymentMethod();
            $paymentMethod->kode_metode = strtoupper($request->kode_metode);
            $paymentMethod->nama_metode = $request->nama_metode;

            $this->checkDuplicateKode($paymentMethod->kode_metode);

            $savedPaymentMethod = $this->paymentMethodRepository->save($paymentMethod);

            Database::commitTransaction();

            return new PaymentMethodResponse($savedPaymentMethod);
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function updatePaymentMethod(PaymentMethodUpdateRequest $request): PaymentMethodResponse
    {
        $existingMethod = $this->validateUpdateRequest($request);

        try {
            Database::beginTransaction();

            $existingMethod->kode_metode = strtoupper($request->kode_metode);
            $existingMethod->nama_metode = $request->nama_metode;
            $existingMethod->updated_at = date('Y-m-d H:i:s');

            $updatedMethod = $this->paymentMethodRepository->save($existingMethod);

            Database::commitTransaction();

            return new PaymentMethodResponse($updatedMethod);
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function deletePaymentMethod(int $id): void
    {
        try {
            Database::beginTransaction();

            $paymentMethod = $this->paymentMethodRepository->findById($id);
            
            if (!$paymentMethod) {
                throw new ValidationException("Metode pembayaran tidak ditemukan");
            }

            $this->paymentMethodRepository->deleteById($id);

            Database::commitTransaction();
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function getPaymentMethodById(int $id): PaymentMethodResponse
    {
        $paymentMethod = $this->paymentMethodRepository->findById($id);
        
        if (!$paymentMethod) {
            throw new ValidationException("Metode pembayaran tidak ditemukan");
        }
        
        return new PaymentMethodResponse($paymentMethod);
    }

    public function getAllPaymentMethods(
        string $search = '',
        int $page = 1,
        int $perPage = 10
    ): PaymentMethodListResponse {
        // Validasi pagination
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        // Build kondisi pencarian
        $condition = '';
        $params = [];
        
        if (!empty($search)) {
            $condition = "kode_metode LIKE ? OR nama_metode LIKE ?";
            $params = ["%{$search}%", "%{$search}%"];
        }

        $methods = $this->paymentMethodRepository->findAll($condition, $params, $perPage, $offset);
        $total = $this->paymentMethodRepository->count($condition, $params);

        return new PaymentMethodListResponse($methods, $total, $page, $perPage);
    }

    private function validateCreateRequest(PaymentMethodCreateRequest $request): void
    {
        $errors = [];

        // Validasi kode metode
        if (empty(trim($request->kode_metode))) {
            $errors[] = "Kode metode wajib diisi";
        } elseif (!preg_match('/^PM-[A-Z0-9]{3,}$/', $request->kode_metode)) {
            $errors[] = "Format kode metode tidak valid (Contoh: PM-001)";
        }

        // Validasi nama metode
        if (empty(trim($request->nama_metode))) {
            $errors[] = "Nama metode wajib diisi";
        } elseif (strlen($request->nama_metode) > 100) {
            $errors[] = "Nama metode maksimal 100 karakter";
        }

        if (!empty($errors)) {
            throw new ValidationException(implode(", ", $errors));
        }
    }

    private function validateUpdateRequest(PaymentMethodUpdateRequest $request): PaymentMethod
    {
        $errors = [];
        $existingMethod = $this->paymentMethodRepository->findById($request->id);

        // Validasi ID
        if (!$existingMethod) {
            $errors[] = "Metode pembayaran tidak ditemukan";
        }

        // Validasi kode metode
        if (empty(trim($request->kode_metode))) {
            $errors[] = "Kode metode wajib diisi";
        } elseif (!preg_match('/^PM-[A-Z0-9]{3,}$/', $request->kode_metode)) {
            $errors[] = "Format kode metode tidak valid (Contoh: PM-001)";
        }

        // Validasi nama metode
        if (empty(trim($request->nama_metode))) {
            $errors[] = "Nama metode wajib diisi";
        } elseif (strlen($request->nama_metode) > 100) {
            $errors[] = "Nama metode maksimal 100 karakter";
        }

        if (!empty($errors)) {
            throw new ValidationException(implode(", ", $errors));
        }

        return $existingMethod;
    }

    private function checkDuplicateKode(string $kodeMetode): void
    {
        $existing = $this->paymentMethodRepository->findAll("kode_metode = ?", [$kodeMetode]);
        if (!empty($existing)) {
            throw new ValidationException("Kode metode '{$kodeMetode}' sudah terdaftar");
        }
    }
}