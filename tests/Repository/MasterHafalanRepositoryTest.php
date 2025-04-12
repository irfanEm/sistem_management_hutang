<?php

namespace IRFANM\SIASHAF\Repository;

use IRFANM\SIASHAF\Config\Database;
use IRFANM\SIASHAF\Factory\HafalanMasterFactory;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertNotNull;

class MasterHafalanRepositoryTest extends TestCase
{
    private MasterHafalanRepository $masterHafalanRepository;

    public function setUp(): void
    {
        $this->masterHafalanRepository = new MasterHafalanRepository(Database::getConn());

        $this->masterHafalanRepository->deleteAllPermanently();
    }

    public function testGetAllHafalan()
    {
        $an_naba = HafalanMasterFactory::createHafalanMaster("m_haf001", "Surat An Naba", 40, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naba);

        $an_naziat = HafalanMasterFactory::createHafalanMaster("m_haf002", "Surat An Naziat", 46, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naziat);

        $abasa = HafalanMasterFactory::createHafalanMaster("m_haf003", "Surat Abasa", 42, "Surat Makiyah");
        $this->masterHafalanRepository->save($abasa);

        $getAllHafalan = $this->masterHafalanRepository->getAll();
        self::assertNotNull($getAllHafalan);
        self::assertCount(3, $getAllHafalan);

        // memastikan master_hafalan tidak terhapus softly
        $existsHafalan = $this->masterHafalanRepository->getAllActive();
        self::assertNotNull($existsHafalan);
        self::assertCount(3, $existsHafalan);
        foreach($existsHafalan as $hafalan) {
            self::assertNull($hafalan['deleted_at']);
        }

        // test mendapatkan semua master hafalan yang dihapus secara softly
        $deleteHafalan = $this->masterHafalanRepository->deleteAllSoftly();
        self::assertTrue($deleteHafalan);

        $deletedHafalanSoftly = $this->masterHafalanRepository->getAllDeleted();
        self::assertNotNull($deletedHafalanSoftly);
        self::assertCount(3, $deletedHafalanSoftly);
        foreach($deletedHafalanSoftly as $deletedHaf) {
            self::assertNotNull($deletedHaf['deleted_at']);
        }
    }

    public function testUpdateMasterHafalan()
    {
        $an_naba = HafalanMasterFactory::createHafalanMaster("m_haf001", "Surat An Naba", 40, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naba);

        $an_naba->title = "Surat Ngamma";
        $an_naba->ayat = 43;
        $an_naba->description = "Surat Madaniyah";
        $updatedHafMas = $this->masterHafalanRepository->update($an_naba);

        self::assertNotNull($updatedHafMas);
        self::assertEquals("m_haf001", $updatedHafMas->memory_id);
        self::assertEquals("Surat Ngamma", $updatedHafMas->title);
        self::assertEquals(43, $updatedHafMas->ayat);
        self::assertEquals("Surat Madaniyah", $updatedHafMas->description);
    }

    public function testFindHafalanByMemoryId()
    {
        $an_naba = HafalanMasterFactory::createHafalanMaster("m_haf001", "Surat An Naba", 40, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naba);

        $an_naziat = HafalanMasterFactory::createHafalanMaster("m_haf002", "Surat An Naziat", 46, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naziat);

        $abasa = HafalanMasterFactory::createHafalanMaster("m_haf003", "Surat Abasa", 42, "Surat Makiyah");
        $this->masterHafalanRepository->save($abasa);

        $findHafalan1 = $this->masterHafalanRepository->findByMemoryId('m_haf001');
        self::assertNotNull($findHafalan1);
        self::assertEquals("m_haf001", $findHafalan1->memory_id);
        self::assertEquals("Surat An Naba", $findHafalan1->title);
        self::assertEquals(40, $findHafalan1->ayat);
        self::assertEquals("Surat Makiyah", $findHafalan1->description);

        $findHafalan2 = $this->masterHafalanRepository->findByMemoryId('m_haf002');
        self::assertNotNull($findHafalan2);
        self::assertEquals("m_haf002", $findHafalan2->memory_id);
        self::assertEquals("Surat An Naziat", $findHafalan2->title);
        self::assertEquals(46, $findHafalan2->ayat);
        self::assertEquals("Surat Makiyah", $findHafalan2->description);

        $findHafalan3 = $this->masterHafalanRepository->findByMemoryId('m_haf003');
        self::assertNotNull($findHafalan3);
        self::assertEquals("m_haf003", $findHafalan3->memory_id);
        self::assertEquals("Surat Abasa", $findHafalan3->title);
        self::assertEquals(42, $findHafalan3->ayat);
        self::assertEquals("Surat Makiyah", $findHafalan3->description);
    }

    public function testDeleteHafalanPermanently()
    {
        $an_naba = HafalanMasterFactory::createHafalanMaster("m_haf001", "Surat An Naba", 40, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naba);

        $an_naziat = HafalanMasterFactory::createHafalanMaster("m_haf002", "Surat An Naziat", 46, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naziat);

        $abasa = HafalanMasterFactory::createHafalanMaster("m_haf003", "Surat Abasa", 42, "Surat Makiyah");
        $this->masterHafalanRepository->save($abasa);

        $deleteHafalanPermanent1 = $this->masterHafalanRepository->deletePermanently("m_haf001");
        self::assertTrue($deleteHafalanPermanent1);
        $allMasterHaf1 = $this->masterHafalanRepository->getAll();
        self::assertCount(2, $allMasterHaf1);

        $deleteHafalanPermanent2 = $this->masterHafalanRepository->deletePermanently("m_haf002");
        self::assertTrue($deleteHafalanPermanent2);
        $allMasterHaf2 = $this->masterHafalanRepository->getAll();
        self::assertCount(1, $allMasterHaf2);

        $deleteHafalanPermanent3 = $this->masterHafalanRepository->deletePermanently("m_haf003");
        self::assertTrue($deleteHafalanPermanent3);
        $allMasterHaf3 = $this->masterHafalanRepository->getAll();
        self::assertCount(0, $allMasterHaf3);
    }

    public function testDeleteAllHafalanPermanently()
    {
        $an_naba = HafalanMasterFactory::createHafalanMaster("m_haf001", "Surat An Naba", 40, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naba);

        $an_naziat = HafalanMasterFactory::createHafalanMaster("m_haf002", "Surat An Naziat", 46, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naziat);

        $abasa = HafalanMasterFactory::createHafalanMaster("m_haf003", "Surat Abasa", 42, "Surat Makiyah");
        $this->masterHafalanRepository->save($abasa);

        $allMasterHafalanDeletedPermanent = $this->masterHafalanRepository->deleteAllPermanently();
        self::assertTrue($allMasterHafalanDeletedPermanent);

        $allMasterHafalan = $this->masterHafalanRepository->getAll();
        self::assertCount(0, $allMasterHafalan);
    }

    public function testDeleteHafalanSoftly()
    {
        $an_naba = HafalanMasterFactory::createHafalanMaster("m_haf001", "Surat An Naba", 40, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naba);

        $an_naziat = HafalanMasterFactory::createHafalanMaster("m_haf002", "Surat An Naziat", 46, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naziat);

        $abasa = HafalanMasterFactory::createHafalanMaster("m_haf003", "Surat Abasa", 42, "Surat Makiyah");
        $this->masterHafalanRepository->save($abasa);

        $allMasterHafalanDeletedSoft = $this->masterHafalanRepository->deleteAllSoftly();
        self::assertTrue($allMasterHafalanDeletedSoft);

        $allMasterHafalanExists = $this->masterHafalanRepository->getAllActive();
        self::assertCount(0, $allMasterHafalanExists);

        $allMasterHafalan = $this->masterHafalanRepository->getAll();
        self::assertCount(3, $allMasterHafalan);
        foreach($allMasterHafalan as $allHaf){
            self::assertNotNull($allHaf['deleted_at']);
        }
    }

    public function testFindDeletedHafalanSoftly()
    {
        $an_naba = HafalanMasterFactory::createHafalanMaster("m_haf001", "Surat An Naba", 40, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naba);

        $an_naziat = HafalanMasterFactory::createHafalanMaster("m_haf002", "Surat An Naziat", 46, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naziat);

        $abasa = HafalanMasterFactory::createHafalanMaster("m_haf003", "Surat Abasa", 42, "Surat Makiyah");
        $this->masterHafalanRepository->save($abasa);

        $this->masterHafalanRepository->deleteSoftly($an_naba->memory_id);
        $this->masterHafalanRepository->deleteSoftly($abasa->memory_id);

        $deletedAn_naba = $this->masterHafalanRepository->findSoftDeleted($an_naba->memory_id);
        $deletedAbasa = $this->masterHafalanRepository->findSoftDeleted($abasa->memory_id);

        self::assertNotNull($deletedAn_naba);
        self::assertNotNull($deletedAn_naba->deleted_at);
        self::assertEquals($an_naba->memory_id, $deletedAn_naba->memory_id);
        self::assertEquals($an_naba->title, $deletedAn_naba->title);
        self::assertEquals($an_naba->ayat, $deletedAn_naba->ayat);
        self::assertEquals($an_naba->description, $deletedAn_naba->description);

        self::assertNotNull($deletedAbasa);
        self::assertNotNull($deletedAbasa->deleted_at);
        self::assertEquals($abasa->memory_id, $deletedAbasa->memory_id);
        self::assertEquals($abasa->title, $deletedAbasa->title);
        self::assertEquals($abasa->ayat, $deletedAbasa->ayat);
        self::assertEquals($abasa->description, $deletedAbasa->description);
    }

    public function testRestoreDeletedHafalanSoftly()
    {
        $an_naba = HafalanMasterFactory::createHafalanMaster("m_haf001", "Surat An Naba", 40, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naba);

        $an_naziat = HafalanMasterFactory::createHafalanMaster("m_haf002", "Surat An Naziat", 46, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naziat);

        $abasa = HafalanMasterFactory::createHafalanMaster("m_haf003", "Surat Abasa", 42, "Surat Makiyah");
        $this->masterHafalanRepository->save($abasa);

        $this->masterHafalanRepository->deleteSoftly($an_naba->memory_id);
        $this->masterHafalanRepository->deleteSoftly($an_naziat->memory_id);
        $this->masterHafalanRepository->deleteSoftly($abasa->memory_id);

        $restoredAnNaba = $this->masterHafalanRepository->restoreSoftDeleted($an_naba->memory_id);
        self::assertTrue($restoredAnNaba);
        $restoredAnNaziat = $this->masterHafalanRepository->restoreSoftDeleted($an_naziat->memory_id);
        self::assertTrue($restoredAnNaziat);
        $restoredAbasa = $this->masterHafalanRepository->restoreSoftDeleted($abasa->memory_id);
        self::assertTrue($restoredAbasa);
    }

    public function testDeleteAllHafalanSoftly()
    {
        $an_naba = HafalanMasterFactory::createHafalanMaster("m_haf001", "Surat An Naba", 40, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naba);

        $an_naziat = HafalanMasterFactory::createHafalanMaster("m_haf002", "Surat An Naziat", 46, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naziat);

        $abasa = HafalanMasterFactory::createHafalanMaster("m_haf003", "Surat Abasa", 42, "Surat Makiyah");
        $this->masterHafalanRepository->save($abasa);

        $deleteSoftly = $this->masterHafalanRepository->deleteAllSoftly();
        self::assertTrue($deleteSoftly);

        $deletedMasHaf = $this->masterHafalanRepository->getAllDeleted();
        self::assertNotNull($deletedMasHaf);
        self::assertCount(3, $deletedMasHaf);

        foreach($deletedMasHaf as $masHaf) {
            assertNotNull($masHaf['deleted_at']);
        }
    }

    public function testRestoreAllDeletedHafalanSoftly()
    {
        $an_naba = HafalanMasterFactory::createHafalanMaster("m_haf001", "Surat An Naba", 40, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naba);

        $an_naziat = HafalanMasterFactory::createHafalanMaster("m_haf002", "Surat An Naziat", 46, "Surat Makiyah");
        $this->masterHafalanRepository->save($an_naziat);

        $abasa = HafalanMasterFactory::createHafalanMaster("m_haf003", "Surat Abasa", 42, "Surat Makiyah");
        $this->masterHafalanRepository->save($abasa);

        $deleteSoftly = $this->masterHafalanRepository->deleteAllSoftly();
        self::assertTrue($deleteSoftly);

        $restoreDeletedMaster = $this->masterHafalanRepository->restoreAllSoftDeleted();
        self::assertTrue($restoreDeletedMaster);
    }
}