<?php

namespace IRFANM\SIMAHU\Repository;

use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Domain\PaymentMethod;
use PHPUnit\Framework\TestCase;

class PaymentMethodRepositoryTest extends TestCase
{
    private PaymentMethodRepository $paymentMethodRepository;

    protected function setUp(): void
    {
        $this->paymentMethodRepository = new PaymentMethodRepository(Database::getConn());
        $this->paymentMethodRepository->deleteAllPermanently();
    }

    public function testSaveSuccess()
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethod->kode_metode = "PM-001";
        $paymentMethod->nama_metode = "Transfer Bank";
        
        $savedPaymentMethod = $this->paymentMethodRepository->save($paymentMethod);
        
        self::assertNotNull($savedPaymentMethod->id);
        self::assertEquals("PM-001", $savedPaymentMethod->kode_metode);
        self::assertEquals("Transfer Bank", $savedPaymentMethod->nama_metode);
    }

    public function testFindById()
    {
        $paymentMethod = $this->createTestPaymentMethod();
        
        $result = $this->paymentMethodRepository->findById($paymentMethod->id);
        
        self::assertNotNull($result);
        self::assertEquals($paymentMethod->id, $result->id);
        self::assertEquals("PM-001", $result->kode_metode);
    }

    public function testUpdate()
    {
        $paymentMethod = $this->createTestPaymentMethod();
        
        // Update data
        $paymentMethod->nama_metode = "Transfer Bank Syariah";
        $paymentMethod->kode_metode = "PM-001-UPD";
        
        $updatedPaymentMethod = $this->paymentMethodRepository->save($paymentMethod);
        
        // Check updated data
        $result = $this->paymentMethodRepository->findById($paymentMethod->id);
        
        self::assertEquals("Transfer Bank Syariah", $result->nama_metode);
        self::assertEquals("PM-001-UPD", $result->kode_metode);
    }

    public function testDeleteById()
    {
        $paymentMethod = $this->createTestPaymentMethod();
        
        $this->paymentMethodRepository->deleteById($paymentMethod->id);
        $result = $this->paymentMethodRepository->findById($paymentMethod->id);
        
        self::assertNull($result);
    }

    public function testFindAll()
    {
        $this->createTestPaymentMethod("PM-001", "Transfer Bank");
        $this->createTestPaymentMethod("PM-002", "Cash");
        $this->createTestPaymentMethod("PM-003", "E-Wallet");
        
        $result = $this->paymentMethodRepository->findAll();
        
        self::assertCount(3, $result);
    }

    public function testFindAllWithCondition()
    {
        $this->createTestPaymentMethod("PM-001", "Transfer Bank");
        $this->createTestPaymentMethod("PM-002", "Cash");
        
        // Test filter
        $result = $this->paymentMethodRepository->findAll(
            "kode_metode LIKE ?", 
            ['PM-00%']
        );
        
        self::assertCount(2, $result);
    }

    private function createTestPaymentMethod(
        string $kode = "PM-001", 
        string $nama = "Transfer Bank"
    ): PaymentMethod {
        $paymentMethod = new PaymentMethod();
        $paymentMethod->kode_metode = $kode;
        $paymentMethod->nama_metode = $nama;
        return $this->paymentMethodRepository->save($paymentMethod);
    }
}