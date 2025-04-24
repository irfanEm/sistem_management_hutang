<?php

namespace IRFANM\SIMAHU\Service;

use PHPUnit\Framework\TestCase;
use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Domain\PaymentMethod;
use IRFANM\SIMAHU\Exception\ValidationException;
use IRFANM\SIMAHU\Model\PaymentMethodCreateRequest;
use IRFANM\SIMAHU\Model\PaymentMethodUpdateRequest;
use IRFANM\SIMAHU\Repository\PaymentMethodRepository;

class PaymentMethodServiceTest extends TestCase
{
    private PaymentMethodService $paymentMethodService;
    private PaymentMethodRepository $paymentMethodRepo;

    protected function setUp(): void
    {
        $this->paymentMethodRepo = new PaymentMethodRepository(Database::getConn());
        $this->paymentMethodService = new PaymentMethodService($this->paymentMethodRepo);

        // Bersihkan data sebelum test
        Database::getConn()->exec("DELETE FROM payment_methods");
    }

    public function testCreatePaymentMethodSuccess()
    {
        $request = new PaymentMethodCreateRequest();
        $request->kode_metode = 'PM-001';
        $request->nama_metode = 'Transfer Bank';

        $response = $this->paymentMethodService->createPaymentMethod($request);

        $this->assertEquals('PM-001', $response->paymentMethod->kode_metode);
        $this->assertEquals('Transfer Bank', $response->paymentMethod->nama_metode);
    }

    public function testCreatePaymentMethodValidationException()
    {
        $this->expectException(ValidationException::class);

        $request = new PaymentMethodCreateRequest();
        $request->kode_metode = ''; // invalid
        $request->nama_metode = ''; // invalid

        $this->paymentMethodService->createPaymentMethod($request);
    }

    public function testUpdatePaymentMethodSuccess()
    {
        // Create dulu
        $createRequest = new PaymentMethodCreateRequest();
        $createRequest->kode_metode = 'PM-002';
        $createRequest->nama_metode = 'QRIS';

        $created = $this->paymentMethodService->createPaymentMethod($createRequest);

        // Update
        $updateRequest = new PaymentMethodUpdateRequest();
        $updateRequest->id = $created->paymentMethod->id;
        $updateRequest->kode_metode = 'PM-002';
        $updateRequest->nama_metode = 'QRIS Updated';

        $updated = $this->paymentMethodService->updatePaymentMethod($updateRequest);

        $this->assertEquals('PM-002', $updated->paymentMethod->kode_metode);
        $this->assertEquals('QRIS Updated', $updated->paymentMethod->nama_metode);
    }

    public function testUpdatePaymentMethodValidationException()
    {
        $this->expectException(ValidationException::class);

        $request = new PaymentMethodUpdateRequest();
        $request->id = 99999; // tidak ada
        $request->kode_metode = '';
        $request->nama_metode = '';

        $this->paymentMethodService->updatePaymentMethod($request);
    }

    public function testDeletePaymentMethodSuccess()
    {
        $createRequest = new PaymentMethodCreateRequest();
        $createRequest->kode_metode = 'PM-003';
        $createRequest->nama_metode = 'COD';

        $created = $this->paymentMethodService->createPaymentMethod($createRequest);

        $this->paymentMethodService->deletePaymentMethod($created->paymentMethod->id);

        $this->expectException(ValidationException::class);
        $this->paymentMethodService->getPaymentMethodById($created->paymentMethod->id);
    }

    public function testDeletePaymentMethodNotFound()
    {
        $this->expectException(ValidationException::class);
        $this->paymentMethodService->deletePaymentMethod(99999);
    }

    public function testGetPaymentMethodByIdSuccess()
    {
        $createRequest = new PaymentMethodCreateRequest();
        $createRequest->kode_metode = 'PM-004';
        $createRequest->nama_metode = 'E-wallet';

        $created = $this->paymentMethodService->createPaymentMethod($createRequest);
        $fetched = $this->paymentMethodService->getPaymentMethodById($created->paymentMethod->id);

        $this->assertEquals($created->paymentMethod->kode_metode, $fetched->paymentMethod->kode_metode);
    }

    public function testGetPaymentMethodByIdNotFound()
    {
        $this->expectException(ValidationException::class);
        $this->paymentMethodService->getPaymentMethodById(99999);
    }

    public function testGetAllPaymentMethods()
    {
        $this->createDummyPaymentMethod('PM-101', 'Test A');
        $this->createDummyPaymentMethod('PM-102', 'Test B');
        $this->createDummyPaymentMethod('PM-103', 'Test C');

        $response = $this->paymentMethodService->getAllPaymentMethods('', 1, 10);
        $this->assertGreaterThanOrEqual(3, $response->total);
        $this->assertNotEmpty($response->paymentMethods);
    }

    private function createDummyPaymentMethod(string $kode, string $nama): void
    {
        $request = new PaymentMethodCreateRequest();
        $request->kode_metode = $kode;
        $request->nama_metode = $nama;
        $this->paymentMethodService->createPaymentMethod($request);
    }
}
