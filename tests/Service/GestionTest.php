<?php

namespace App\Tests\Service;

use App\Service\AllRepositories;
use App\Service\Gestion;
use PHPUnit\Framework\TestCase;

class GestionTest extends TestCase
{
    public function testGenerateCodeForAdulte()
    {
        $mockRepository = $this->createMock(AllRepositories::class);
        $mockRepository->method('getOneScout')->willReturn(null);

        $gestion = new Gestion($mockRepository); // Creation du service gestion en passant le faux repository
        $code = $gestion->generateCode('ADULTE'); // Appelle notre methode de generation de code
        $this->assertStringStartsWith('CF', $code);
        $this->assertEquals(12, strlen($code));
    }

    public function testGenerateCodeForJeune()
    {
        $mockRepository = $this->createMock(AllRepositories::class);
        $mockRepository->method('getOneScout')->willReturn(null);

        $gestion = new Gestion($mockRepository);
        $code = $gestion->generateCode('JEUNE');
        $this->assertStringStartsWith('SC', $code);
        $this->assertEquals(12, strlen($code));
    }

    public function testGenerateCodeWhenCodeAlreadyExist()
    {
        $mockRepository = $this->createMock(AllRepositories::class);
        $mockRepository->expects($this->exactly(2))
            ->method('getOneScout')
            ->willReturnOnConsecutiveCalls(true, null);

        $gestion = new Gestion($mockRepository);
        $code = $gestion->generateCode('ADULTE');
        $this->assertStringStartsWith('CF', $code);
        $this->assertEquals(12, strlen($code));
    }
}