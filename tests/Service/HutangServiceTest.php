<?php

namespace IRFANM\SIMAHU\Service;

use IRFANM\SIMAHU\Domain\Agen;
use IRFANM\SIMAHU\Domain\User;
use PHPUnit\Framework\TestCase;
use IRFANM\SIMAHU\Domain\Hutang;
use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Domain\PaymentMethod;
use IRFANM\SIMAHU\Model\HutangCreateRequest;
use IRFANM\SIMAHU\Model\HutangUpdateRequest;
use IRFANM\SIMAHU\Repository\AgenRepository;
use IRFANM\SIMAHU\Repository\UserRepository;
use IRFANM\SIMAHU\Repository\HutangRepository;
use IRFANM\SIMAHU\Exception\ValidationException;
use IRFANM\SIMAHU\Exception\DataNotFoundException;
use IRFANM\SIMAHU\Repository\PaymentMethodRepository;

class HutangServiceTest extends TestCase
{
    private HutangRepository $hutangRepo;
    private UserRepository $userRepo;
    private AgenRepository $agenRepo;
    private PaymentMethodRepository $paymentMethodRepo;
    private HutangService $hutangService;

    private int $testUserId;
    private int $testAgenId;
    private int $testPaymentMethodId;

    protected function setUp(): void
    {
        $conn = Database::getConn();
    
        // Clear all test data first
        $this->hutangRepo = new HutangRepository($conn);
        $this->userRepo = new UserRepository($conn);
        $this->agenRepo = new AgenRepository($conn);
        $this->paymentMethodRepo = new PaymentMethodRepository($conn);
    
        $this->hutangRepo->deleteAllPermanently();
        $this->userRepo->deleteAllPermanently();
        $this->agenRepo->deleteAllPermanently();
        $this->paymentMethodRepo->deleteAllPermanently();
    
        // Create service
        $this->hutangService = new HutangService(
            $this->hutangRepo,
            $this->userRepo,
            $this->agenRepo,
            $this->paymentMethodRepo
        );

        // Baru buat test data setelah semua kosong
        $this->testUserId = $this->createTestUser();
        $this->testAgenId = $this->createTestAgen();
        $this->testPaymentMethodId = $this->createTestPaymentMethod();
    
    }
    

    public function testCreateHutang()
    {
        $request = new HutangCreateRequest();
        $request->debt_id = 'DEBT-001';
        $request->user_id = $this->testUserId;
        $request->agent_id = $this->testAgenId;
        $request->payment_method_id = $this->testPaymentMethodId;
        $request->tanggal_hutang = '2023-01-01';
        $request->tanggal_jatuh_tempo = '2023-02-01';
        $request->total_hutang = 5000000;

        $response = $this->hutangService->createHutang($request);

        // Check response
        self::assertNotNull($response->hutang);
        self::assertEquals('DEBT-001', $response->hutang->debt_id);
        self::assertEquals(5000000, $response->hutang->sisa_hutang);
        self::assertEquals('2023-01-01', $response->hutang->tanggal_hutang);
        
        // Check database
        $dbHutang = $this->hutangRepo->findById('DEBT-001');
        self::assertNotNull($dbHutang);
        self::assertEquals($this->testUserId, $dbHutang->user_id);
    }

    public function testCreateHutangValidationException()
    {
        // Test missing required fields
        $request = new HutangCreateRequest();
        self::expectException(ValidationException::class);
        $this->hutangService->createHutang($request);

        // Test invalid dates
        $request->debt_id = 'DEBT-002';
        $request->user_id = $this->testUserId;
        $request->agent_id = $this->testAgenId;
        $request->payment_method_id = $this->testPaymentMethodId;
        $request->tanggal_hutang = 'invalid-date';
        $request->tanggal_jatuh_tempo = '2023-01-01';
        $request->total_hutang = 1000000;
        self::expectException(ValidationException::class);
        $this->hutangService->createHutang($request);

        // Test duplicate debt_id
        $this->createTestHutang('DEBT-003');
        $request->debt_id = 'DEBT-003';
        self::expectException(ValidationException::class);
        $this->hutangService->createHutang($request);
    }

    public function testUpdateHutang()
    {
        $debtId = 'DEBT-UPDATE-001';
        $this->createTestHutang($debtId);

        $request = new HutangUpdateRequest();
        $request->debt_id = $debtId;
        $request->payment_method_id = $this->testPaymentMethodId;
        $request->tanggal_jatuh_tempo = '2023-03-01';
        $request->sisa_hutang = 3000000;

        $response = $this->hutangService->updateHutang($request);

        self::assertEquals(3000000, $response->hutang->sisa_hutang);
        self::assertEquals('2023-03-01', $response->hutang->tanggal_jatuh_tempo);
    }

    public function testUpdateHutangValidationException()
    {
        $debtId = 'DEBT-UPDATE-002';
        $this->createTestHutang($debtId);

        // Test negative sisa hutang
        $request = new HutangUpdateRequest();
        $request->debt_id = $debtId;
        $request->sisa_hutang = -1000;
        self::expectException(ValidationException::class);
        $this->hutangService->updateHutang($request);

        // Test invalid payment method
        $request->sisa_hutang = 1000;
        $request->payment_method_id = 9999;
        self::expectException(ValidationException::class);
        $this->hutangService->updateHutang($request);
    }

    public function testGetHutang()
    {
        $debtId = 'DEBT-GET-001';
        $this->createTestHutang($debtId);

        $hutang = $this->hutangService->getHutang($debtId);
        
        self::assertInstanceOf(Hutang::class, $hutang);
        self::assertEquals($debtId, $hutang->debt_id);
        self::assertEquals(1000000, $hutang->sisa_hutang);
    }

    public function testGetAllHutang()
    {
        $this->createTestHutang('DEBT-ALL-001');
        $this->createTestHutang('DEBT-ALL-002');

        $result = $this->hutangService->getAllHutang();
        
        self::assertCount(2, $result);
        self::assertContainsOnlyInstancesOf(Hutang::class, $result);
    }

    public function testDeleteHutang()
    {
        $debtId = 'DEBT-DEL-001';
        $this->createTestHutang($debtId);

        $result = $this->hutangService->deleteHutang($debtId);
        self::assertTrue($result);

        self::expectException(DataNotFoundException::class);
        $this->hutangService->getHutang($debtId);
    }

    private function createTestHutang(string $debtId): void
    {
        $request = new HutangCreateRequest();
        $request->debt_id = $debtId;
        $request->user_id = $this->testUserId;
        $request->agent_id = $this->testAgenId;
        $request->payment_method_id = $this->testPaymentMethodId;
        $request->tanggal_hutang = '2023-01-01';
        $request->tanggal_jatuh_tempo = '2023-02-01';
        $request->total_hutang = 1000000;

        $this->hutangService->createHutang($request);
    }

private function createTestUser(): int
{
    // Contoh implementasi UserRepository
    $user = new User();
    $user->username = 'test_user';
    $user->nama = 'Nama User';
    $user->email = 'test@example.com';
    $user->password = password_hash('test123', PASSWORD_BCRYPT);
    $user->role = 'user';
    $user->status = 1;
    $user->created_at = date('Y-m-d H:i:s');
    $user->updated_at = date('Y-m-d H:i:s');
    
    $savedUser = $this->userRepo->save($user);
    return $savedUser->id;
}


private function createTestAgen(): int
{
    $agen = new Agen();
    $agen->kode_agen = 'AG-001';
    $agen->nama_agen = 'Test Agen';
    $agen->created_at = date("Y-m-d H:i:s");
    $agen->updated_at = date("Y-m-d H:i:s");
    
    $savedAgen = $this->agenRepo->save($agen);
    return $savedAgen->id;
}

private function createTestPaymentMethod(): int
{
    // Asumsi PaymentMethod model dan repo sudah ada
    $pm = new PaymentMethod();
    $pm->nama_metode = 'PM-001';
    $pm->kode_metode = 'CASH';
    
    $savedPm = $this->paymentMethodRepo->save($pm);
    return $savedPm->id;
}
}