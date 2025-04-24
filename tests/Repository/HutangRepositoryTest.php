<?php

namespace IRFANM\SIMAHU\Repository;

use IRFANM\SIMAHU\Domain\Agen;
use IRFANM\SIMAHU\Domain\User;
use PHPUnit\Framework\TestCase;
use IRFANM\SIMAHU\Domain\Hutang;
use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Domain\PaymentMethod;

class HutangRepositoryTest extends TestCase
{
    private HutangRepository $hutangRepository;
    private UserRepository $userRepository;
    private PaymentMethodRepository $paymentMethodRepository;
    private AgenRepository $agenRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConn());
        $this->agenRepository = new AgenRepository(Database::getConn());
        $this->paymentMethodRepository = new PaymentMethodRepository(Database::getConn());
        $this->hutangRepository = new HutangRepository(Database::getConn());
        
        $this->hutangRepository->deleteAllPermanently();
        $this->paymentMethodRepository->deleteAllPermanently();
        $this->agenRepository->deleteAllPermanently();
        $this->userRepository->deleteAllPermanently();
    }

    public function testSaveSuccess()
    {
        $user = new User();
        $user->username = uniqid();
        $user->nama = 'Test User';
        $user->email = 'test1@session.com';
        $user->password = 'password';
        $user->role = 'user';
        $user->status = 1;
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');

        $savedUser = $this->userRepository->save($user);

        $savedUser = $this->createDummyUser();
        $paymentMethod = new PaymentMethod();
        $paymentMethod->kode_metode = 'PM-001';
        $paymentMethod->nama_metode = "Transfer Bank";
        
        $savedPaymentMethod = $this->paymentMethodRepository->save($paymentMethod);

        $agen = new Agen();
        $agen->kode_agen = uniqid();
        $agen->nama_agen = "Agen Satu";
        $agen->kontak = "085800010003";
        $agen->created_at = date("Y-m-d H:i:s");
        $agen->updated_at = date("Y-m-d H:i:s");

        $savedAgen = $this->agenRepository->save($agen);
        
        $hutang = new Hutang();
        $hutang->debt_id = uniqid();
        $hutang->user_id = $savedUser->id;
        $hutang->agent_id = $savedAgen->id; // Asumsi agent sudah ada
        $hutang->payment_method_id = $savedPaymentMethod->id; // Asumsi payment method sudah ada
        $hutang->tanggal_hutang = '2024-01-01';
        $hutang->tanggal_jatuh_tempo = '2024-02-15';
        $hutang->sisa_hutang = 5000000;
        $hutang->created_at = date('Y-m-d H:i:s');
        $hutang->updated_at = date('Y-m-d H:i:s');
        
        $this->hutangRepository->save($hutang);

        $result = $this->hutangRepository->findById($hutang->debt_id);
        
        self::assertNotNull($result);
        self::assertEquals(5000000, $result->sisa_hutang);
    }

    public function testUpdateHutang()
    {
        $user = new User();
        $user->username = uniqid();
        $user->nama = 'Test User';
        $user->email = 'test1@session.com';
        $user->password = 'password';
        $user->role = 'user';
        $user->status = 1;
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');

        $savedUser = $this->userRepository->save($user);

        $savedUser = $this->createDummyUser();
        $paymentMethod = new PaymentMethod();
        $paymentMethod->kode_metode = 'PM-001';
        $paymentMethod->nama_metode = "Transfer Bank";
        
        $savedPaymentMethod = $this->paymentMethodRepository->save($paymentMethod);

        $agen = new Agen();
        $agen->kode_agen = uniqid();
        $agen->nama_agen = "Agen Satu";
        $agen->kontak = "085800010003";
        $agen->created_at = date("Y-m-d H:i:s");
        $agen->updated_at = date("Y-m-d H:i:s");

        $savedAgen = $this->agenRepository->save($agen);
        
        $hutang = new Hutang();
        $hutang->debt_id = uniqid();
        $hutang->user_id = $savedUser->id;
        $hutang->agent_id = $savedAgen->id; // Asumsi agent sudah ada
        $hutang->payment_method_id = $savedPaymentMethod->id; // Asumsi payment method sudah ada
        $hutang->tanggal_hutang = '2024-01-01';
        $hutang->tanggal_jatuh_tempo = '2024-02-15';
        $hutang->sisa_hutang = 5000000;
        $hutang->created_at = date('Y-m-d H:i:s');
        $hutang->updated_at = date('Y-m-d H:i:s');
        
        $this->hutangRepository->save($hutang);

        $hutang->sisa_hutang = 3000000;
        $hutang->tanggal_jatuh_tempo = '2024-03-01';
        
        $this->hutangRepository->update($hutang);
        $updatedHutang = $this->hutangRepository->findById($hutang->debt_id);
        
        self::assertEquals(3000000, $updatedHutang->sisa_hutang);
        self::assertEquals('2024-03-01', $updatedHutang->tanggal_jatuh_tempo);
    }

    public function testDeleteById()
    {
        $user = new User();
        $user->username = uniqid();
        $user->nama = 'Test User';
        $user->email = 'test1@session.com';
        $user->password = 'password';
        $user->role = 'user';
        $user->status = 1;
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');

        $savedUser = $this->userRepository->save($user);

        $savedUser = $this->createDummyUser();
        $paymentMethod = new PaymentMethod();
        $paymentMethod->kode_metode = 'PM-001';
        $paymentMethod->nama_metode = "Transfer Bank";
        
        $savedPaymentMethod = $this->paymentMethodRepository->save($paymentMethod);

        $agen = new Agen();
        $agen->kode_agen = uniqid();
        $agen->nama_agen = "Agen Satu";
        $agen->kontak = "085800010003";
        $agen->created_at = date("Y-m-d H:i:s");
        $agen->updated_at = date("Y-m-d H:i:s");

        $savedAgen = $this->agenRepository->save($agen);
        
        $hutang = new Hutang();
        $hutang->debt_id = uniqid();
        $hutang->user_id = $savedUser->id;
        $hutang->agent_id = $savedAgen->id; // Asumsi agent sudah ada
        $hutang->payment_method_id = $savedPaymentMethod->id; // Asumsi payment method sudah ada
        $hutang->tanggal_hutang = '2024-01-01';
        $hutang->tanggal_jatuh_tempo = '2024-02-15';
        $hutang->sisa_hutang = 5000000;
        $hutang->created_at = date('Y-m-d H:i:s');
        $hutang->updated_at = date('Y-m-d H:i:s');
        
        $this->hutangRepository->save($hutang);
        
        $this->hutangRepository->deleteById($hutang->id);
        $result = $this->hutangRepository->findById($hutang->id);
        
        self::assertNull($result);
    }

    public function testFindAllWithCondition()
    {
        // Create test data
        // $this->createTestHutang('2024-01-01');
        // $this->createTestHutang('2024-02-01', 'PM-002');
        // $this->createTestHutang('2023-12-01', 'PM-003');

        $user = new User();
        $user->username = uniqid();
        $user->nama = 'Test User';
        $user->email = 'test1@session.com';
        $user->password = 'password';
        $user->role = 'user';
        $user->status = 1;
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');

        $savedUser = $this->userRepository->save($user);

        $savedUser = $this->createDummyUser();
        $paymentMethod = new PaymentMethod();
        $paymentMethod->kode_metode = 'PM-001';
        $paymentMethod->nama_metode = "Transfer Bank";
        
        $savedPaymentMethod = $this->paymentMethodRepository->save($paymentMethod);

        $agen = new Agen();
        $agen->kode_agen = uniqid();
        $agen->nama_agen = "Agen Satu";
        $agen->kontak = "085800010003";
        $agen->created_at = date("Y-m-d H:i:s");
        $agen->updated_at = date("Y-m-d H:i:s");

        $savedAgen = $this->agenRepository->save($agen);
        
        $hutang = new Hutang();
        $hutang->debt_id = uniqid();
        $hutang->user_id = $savedUser->id;
        $hutang->agent_id = $savedAgen->id; // Asumsi agent sudah ada
        $hutang->payment_method_id = $savedPaymentMethod->id; // Asumsi payment method sudah ada
        $hutang->tanggal_hutang = '2024-01-01';
        $hutang->tanggal_jatuh_tempo = '2024-02-15';
        $hutang->sisa_hutang = 5000000;
        $hutang->created_at = date('Y-m-d H:i:s');
        $hutang->updated_at = date('Y-m-d H:i:s');
        
        $this->hutangRepository->save($hutang);

        // 2
        $user2 = new User();
        $user2->username = uniqid();
        $user2->nama = 'Test User';
        $user2->email = 'test2@session.com';
        $user2->password = 'password';
        $user2->role = 'user';
        $user2->status = 1;
        $user2->created_at = date('Y-m-d H:i:s');
        $user2->updated_at = date('Y-m-d H:i:s');

        $savedUser2 = $this->userRepository->save($user2);

        $paymentMethod2 = new PaymentMethod();
        $paymentMethod2->kode_metode = 'PM-002';
        $paymentMethod2->nama_metode = "Transfer Bank";
        
        $savedPaymentMethod2 = $this->paymentMethodRepository->save($paymentMethod2);

        $agen2 = new Agen();
        $agen2->kode_agen = uniqid();
        $agen2->nama_agen = "Agen Satu";
        $agen2->kontak = "085800010003";
        $agen2->created_at = date("Y-m-d H:i:s");
        $agen2->updated_at = date("Y-m-d H:i:s");

        $savedAgen2 = $this->agenRepository->save($agen2);
        
        $hutang2 = new Hutang();
        $hutang2->debt_id = uniqid();
        $hutang2->user_id = $savedUser2->id;
        $hutang2->agent_id = $savedAgen2->id; // Asumsi agent sudah ada
        $hutang2->payment_method_id = $savedPaymentMethod2->id; // Asumsi payment method sudah ada
        $hutang2->tanggal_hutang = '2024-02-01';
        $hutang2->tanggal_jatuh_tempo = '2024-02-15';
        $hutang2->sisa_hutang = 5000000;
        $hutang2->created_at = date('Y-m-d H:i:s');
        $hutang2->updated_at = date('Y-m-d H:i:s');
        
        $this->hutangRepository->save($hutang2);

        // 3
        $user3 = new User();
        $user3->username = uniqid();
        $user3->nama = 'Test User';
        $user3->email = 'test3@session.com';
        $user3->password = 'password';
        $user3->role = 'user';
        $user3->status = 1;
        $user3->created_at = date('Y-m-d H:i:s');
        $user3->updated_at = date('Y-m-d H:i:s');

        $savedUser3 = $this->userRepository->save($user3);

        $paymentMethod3 = new PaymentMethod();
        $paymentMethod3->kode_metode = 'PM-003';
        $paymentMethod3->nama_metode = "DANA";
        
        $savedPaymentMethod3 = $this->paymentMethodRepository->save($paymentMethod3);

        $agen3 = new Agen();
        $agen3->kode_agen = uniqid();
        $agen3->nama_agen = "Agen Satu";
        $agen3->kontak = "085800010003";
        $agen3->created_at = date("Y-m-d H:i:s");
        $agen3->updated_at = date("Y-m-d H:i:s");

        $savedAgen3 = $this->agenRepository->save($agen3);
        
        $hutang3 = new Hutang();
        $hutang3->debt_id = uniqid();
        $hutang3->user_id = $savedUser3->id;
        $hutang3->agent_id = $savedAgen3->id; // Asumsi agent sudah ada
        $hutang3->payment_method_id = $savedPaymentMethod3->id; // Asumsi payment method sudah ada
        $hutang3->tanggal_hutang = '2024-12-01';
        $hutang3->tanggal_jatuh_tempo = '2024-12-15';
        $hutang3->sisa_hutang = 5000000;
        $hutang3->created_at = date('Y-m-d H:i:s');
        $hutang3->updated_at = date('Y-m-d H:i:s');
        
        $this->hutangRepository->save($hutang3);
        
        // Test filter
        $results = $this->hutangRepository->findAll(
            "tanggal_hutang > ?", 
            ['2024-01-01']
        );
        
        self::assertCount(2, $results);
    }

    private function createTestHutang(string $tanggal = '2024-01-15', string $kode_metode = 'PM-001', $email = 'test@session.com'): Hutang
    {
        $user = new User();
        $user->username = uniqid();
        $user->nama = 'Test User';
        $user->email = $email;
        $user->password = 'password';
        $user->role = 'user';
        $user->status = 1;
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');

        $savedUser = $this->userRepository->save($user);

        $savedUser = $this->createDummyUser();
        $paymentMethod = new PaymentMethod();
        $paymentMethod->kode_metode = $kode_metode;
        $paymentMethod->nama_metode = "Transfer Bank";
        
        $savedPaymentMethod = $this->paymentMethodRepository->save($paymentMethod);

        $agen = new Agen();
        $agen->kode_agen = uniqid();
        $agen->nama_agen = "Agen Satu";
        $agen->kontak = "085800010003";
        $agen->created_at = date("Y-m-d H:i:s");
        $agen->updated_at = date("Y-m-d H:i:s");

        $savedAgen = $this->agenRepository->save($agen);
        
        $hutang = new Hutang();
        $hutang->debt_id = uniqid();
        $hutang->user_id = $savedUser->id;
        $hutang->agent_id = $savedAgen->id; // Asumsi agent sudah ada
        $hutang->payment_method_id = $savedPaymentMethod->id; // Asumsi payment method sudah ada
        $hutang->tanggal_hutang = $tanggal;
        $hutang->tanggal_jatuh_tempo = '2024-02-15';
        $hutang->sisa_hutang = 5000000;
        $hutang->created_at = date('Y-m-d H:i:s');
        $hutang->updated_at = date('Y-m-d H:i:s');
        
        return $this->hutangRepository->save($hutang);
    }

    private function createDummyUser(): User
    {
        $user = new User();
        $user->username = uniqid();
        $user->nama = 'Test User';
        $user->email = 'test@session.com';
        $user->password = 'password';
        $user->role = 'user';
        $user->status = 1;
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');

        return $this->userRepository->save($user);
    }
}