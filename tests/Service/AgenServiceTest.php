<?php

namespace IRFANM\SIASHAF\Service;

use IRFANM\SIASHAF\Config\Database;
use IRFANM\SIASHAF\Domain\Agen;
use IRFANM\SIASHAF\Exception\ValidationException;
use IRFANM\SIASHAF\Model\AgenCreateRequest;
use IRFANM\SIASHAF\Model\AgenCreateResponse;
use IRFANM\SIASHAF\Model\AgenUpdateRequest;
use IRFANM\SIASHAF\Repository\AgenRepository;
use PHPUnit\Framework\TestCase;

class AgenServiceTest extends TestCase
{
    private AgenRepository $agenRepository;
    private AgenService $agenService;

    public function setUp(): void
    {
        $agenRepo = $this->agenRepository = new AgenRepository(Database::getConn());
        $this->agenService = new AgenService($agenRepo);

        $this->agenRepository->deleteAllPermanently();
    }

    public function testAddAgen()
    {
        $agen = $this->createAgen('AG-003', 'Agen Zero Three');

        $result = $this->agenRepository->findById($agen->agen->kode_agen);

        self::assertNotNull($result);
        self::assertEquals($agen->agen->kode_agen, $result->kode_agen);
        self::assertEquals($agen->agen->nama_agen, $result->nama_agen);
        self::assertEquals('belum ada', $result->kontak);
        self::assertNotNull($result->created_at);
        self::assertNotNull($result->updated_at);

        // Test Validation Exception
        self::expectException(ValidationException::class);
        $this->createAgen();

        self::expectException(ValidationException::class);
        $this->createAgen('AG-001');
    }
    
    public function testUpdateAgen()
    {
        $agen = $this->createAgen('AG-003', 'Agen X Three');
        $request = new AgenUpdateRequest();
        $request->kode_agen = $agen->agen->kode_agen;
        $request->nama_agen = 'Agen Zero Three';
        $request->kontak = '088321003400';

        $updatedAgen = $this->agenService->updateAgen($request);

        self::assertNotNull($updatedAgen);
        self::assertEquals('AG-003', $updatedAgen->agen->kode_agen);
        self::assertEquals($request->nama_agen, $updatedAgen->agen->nama_agen);
        self::assertEquals($request->kontak, $updatedAgen->agen->kontak);
        self::assertNotNull($updatedAgen->agen->created_at);
        self::assertNotNull($updatedAgen->agen->updated_at);
    }

    public function testUpdateAgenValidationException()
    {
        $agen = $this->createAgen('AG-005', 'Agen X Five');

        $request = new AgenUpdateRequest();
        $request->kode_agen = $agen->agen->kode_agen;
        $request->nama_agen = '';

        self::expectException(ValidationException::class);
        $this->agenService->updateAgen($request);
    }

    public function testUpdateAgenVa()
    {
        $this->createAgen('AG-005', 'Agen X Five');

        $request = new AgenUpdateRequest();
        $request->kode_agen = 'not-found';
        $request->nama_agen = 'Agen Not Found';
        $request->kontak = '088208820884';

        self::expectException(ValidationException::class);
        $this->agenService->updateAgen($request);
    }

    private function createAgen(string $kode_agen = '', string $nama_agen = ''): AgenCreateResponse
    {
        $agenCreateReq = new AgenCreateRequest();
        $agenCreateReq->kode_agen = $kode_agen;
        $agenCreateReq->nama_agen = $nama_agen;

        return $this->agenService->addAgen($agenCreateReq);
    }
}
