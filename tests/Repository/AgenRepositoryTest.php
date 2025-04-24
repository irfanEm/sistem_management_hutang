<?php

namespace IRFANM\SIMAHU\Repository;

use IRFANM\SIMAHU\Domain\Agen;
use PHPUnit\Framework\TestCase;
use IRFANM\SIMAHU\Config\Database;

class AgenRepositoryTest extends TestCase
{
    private AgenRepository $agenRepository;

    public function setUp(): void
    {
        $this->agenRepository = new AgenRepository(Database::getConn());
        
        $this->agenRepository->deleteAllPermanently();
    }

    public function testSave()
    {
        $agen = new Agen();
        $agen->kode_agen = uniqid();
        $agen->nama_agen = "Agen Satu";
        $agen->kontak = "085800010003";
        $agen->created_at = date("Y-m-d H:i:s");
        $agen->updated_at = date("Y-m-d H:i:s");

        $this->agenRepository->save($agen);
        $savedAgen = $this->agenRepository->findById($agen->kode_agen);

        self::assertNotNull($savedAgen);
        self::assertEquals($savedAgen->kode_agen, $agen->kode_agen);
        self::assertEquals($savedAgen->nama_agen, $agen->nama_agen);
        self::assertEquals($savedAgen->kontak, $agen->kontak);
        self::assertEquals($savedAgen->created_at, $agen->created_at);
        self::assertEquals($savedAgen->updated_at, $agen->updated_at);
    }

    public function testGetAll()
    {
        $agen = new Agen();
        $agen->kode_agen = uniqid();
        $agen->nama_agen = "Agen Satu";
        $agen->kontak = "085800010003";
        $agen->created_at = date("Y-m-d H:i:s");
        $agen->updated_at = date("Y-m-d H:i:s");

        $this->agenRepository->save($agen);

        $agen2 = new Agen();
        $agen2->kode_agen = uniqid();
        $agen2->nama_agen = "Agen Dua";
        $agen2->kontak = "085800010002";
        $agen2->created_at = date("Y-m-d H:i:s");
        $agen2->updated_at = date("Y-m-d H:i:s");

        $this->agenRepository->save($agen2);

        $agen3 = new Agen();
        $agen3->kode_agen = uniqid();
        $agen3->nama_agen = "Agen Tiga";
        $agen3->kontak = "085800010001";
        $agen3->created_at = date("Y-m-d H:i:s");
        $agen3->updated_at = date("Y-m-d H:i:s");

        $this->agenRepository->save($agen3);

        $agents = $this->agenRepository->getAll();
        $agen1 = $this->agenRepository->findById($agen->kode_agen);
        
        self::assertNotNull($agents);
        self::assertCount(3, $agents);
        self::assertNotNull($agen1);
        self::assertEquals($agen1->kode_agen, $agen->kode_agen);
        self::assertEquals($agen1->nama_agen, $agen->nama_agen);
        self::assertEquals($agen1->kontak, $agen->kontak);
    }

    public function testUpdate()
    {
        $agen = new Agen();
        $agen->kode_agen = uniqid();
        $agen->nama_agen = "Agen Satu";
        $agen->kontak = "085800010003";
        $agen->created_at = date("Y-m-d H:i:s");
        $agen->updated_at = date("Y-m-d H:i:s");

        $this->agenRepository->save($agen);

        $agen->nama_agen = "Agen X";
        $agen->kontak = "085800010000";
        $agen->updated_at = date("Y-m-d H:i:s");

        $this->agenRepository->update($agen);

        $updatedAgen = $this->agenRepository->findById($agen->kode_agen);

        self::assertNotNull($updatedAgen);
        self::assertEquals($updatedAgen->nama_agen, $agen->nama_agen);
        self::assertEquals($updatedAgen->kontak, $agen->kontak);
    }

    public function testDeletePermanentlyById()
    {
        $agen = new Agen();
        $agen->kode_agen = uniqid();
        $agen->nama_agen = "Agen Satu";
        $agen->kontak = "085800010003";
        $agen->created_at = date("Y-m-d H:i:s");
        $agen->updated_at = date("Y-m-d H:i:s");

        $this->agenRepository->save($agen);

        $statusDelete = $this->agenRepository->deletePermanently($agen->kode_agen);

        $deletedAgen = $this->agenRepository->findById($agen->kode_agen);

        self::assertNull($deletedAgen);
        self::assertTrue($statusDelete);
    }
}
