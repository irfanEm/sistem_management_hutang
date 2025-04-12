<?php

namespace IRFANM\SIASHAF\Service;

use IRFANM\SIASHAF\Config\Database;
use IRFANM\SIASHAF\Domain\Guru;
use IRFANM\SIASHAF\Model\GuruRegisterRequest;
use IRFANM\SIASHAF\Repository\GuruRepository;
use IRFANM\SIASHAF\Repository\UserRepository;
use IRFANM\SIASHAF\Service\GuruService;
use IRFANM\SIASHAF\Service\UserService;
use PHPUnit\Framework\TestCase;

class GuruServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private UserService $userService;
    private GuruRepository $guruRepository;
    private GuruService $guruService;

    public function setUp(): void
    {
        $conn = Database::getConn();
        $this->userRepository = new UserRepository($conn);
        $this->userService = new UserService($this->userRepository);
        $this->guruRepository = new GuruRepository($conn);
        $this->guruService = new GuruService($this->guruRepository,$this->userService);

        $this->userRepository->deleteAllPermanently();
        $this->guruRepository->deleteAllPermanently();
    }

    public function testGellAllGuru()
    {
        $guru0 = new Guru();
        $guru0->user_id = uniqid();
        $guru0->nama = "Puri S.Pd";
        $guru0->nik = "010287211197";
        $guru0->email = "puri00@mail.com";
        $guru0->kontak = "088976657332";
        $guru0->created_at = date("Y-m-d H:i:s");
        $guru0->updated_at = date("Y-m-d H:i:s");
        $guru0->deleted_at = null;
        $this->guruRepository->save($guru0);

        $guru1 = new Guru();
        $guru1->user_id = uniqid();
        $guru1->nama = "Mujahidin S.Pd";
        $guru1->nik = "020388211198";
        $guru1->email = "mujahidin01@mail.com";
        $guru1->kontak = "088678893221";
        $guru1->created_at = date("Y-m-d H:i:s");
        $guru1->updated_at = date("Y-m-d H:i:s");
        $guru1->deleted_at = null;
        $this->guruRepository->save($guru1);
        
        $guru2 = new Guru();
        $guru2->user_id = uniqid();
        $guru2->nama = "Wafa S.Pd";
        $guru2->nik = "030499232296";
        $guru2->email = "wafa02@mail.com";
        $guru2->kontak = "088432215443";
        $guru2->created_at = date("Y-m-d H:i:s");
        $guru2->updated_at = date("Y-m-d H:i:s");
        $guru2->deleted_at = null;
        $this->guruRepository->save($guru2);

        $results = $this->guruService->getAllGuru();
        self::assertNotNull($results);
        self::assertCount(3, $results);
        self::assertIsArray($results);
    }

    public function testAddGuru()
    {
        $guruRegisterRequest = new GuruRegisterRequest();
        $guruRegisterRequest->nama = "Faqih";
        $guruRegisterRequest->nik = "0022448822";
        $guruRegisterRequest->email = "faqih00@mail.com";
        $guruRegisterRequest->kontak = "088432215773";
        $result = $this->guruService->addGuru($guruRegisterRequest);

        self::assertNotNull($result);
        self::assertEquals($guruRegisterRequest->nama, $result->guru->nama);
        self::assertEquals($guruRegisterRequest->nik, $result->guru->nik);
        self::assertEquals($guruRegisterRequest->email, $result->guru->email);
        self::assertEquals($guruRegisterRequest->kontak, $result->guru->kontak);
        self::assertNotNull($result->guru->created_at);
        self::assertNotNull($result->guru->updated_at);

        $user = $this->userRepository->findByUsername($guruRegisterRequest->email);
        
        self::assertNotNull($user);
        self::assertEquals($guruRegisterRequest->email, $user->username);
        self::assertTrue(password_verify($guruRegisterRequest->nik, $user->password));
        self::assertNotNull($user->created_at);
        self::assertNotNull($user->updated_at);
    }
}
