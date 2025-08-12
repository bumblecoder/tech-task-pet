<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Tests\Unit\Service\Pet;

use App\Entity\PetBreed;
use App\Entity\PetType;
use App\Repository\PetBreedRepository;
use App\Service\Pet\BreedSearchService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

/** @covers \App\Service\Pet\BreedSearchService */
final class BreedSearchServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSearchDelegatesToRepositoryAndNormalizes(): void
    {
        $type = $this->createStub(PetType::class);
        $term = 'hus';

        $uuid1 = Uuid::fromString('11111111-1111-1111-1111-111111111111');
        $uuid2 = Uuid::fromString('22222222-2222-2222-2222-222222222222');

        $breed1 = $this->mockBreed($uuid1, 'Siberian Husky');
        $breed2 = $this->mockBreed($uuid2, 'Alaskan Husky');

        $repo = $this->createMock(PetBreedRepository::class);
        $repo->expects(self::once())
            ->method('findBySearch')
            ->with($type, $term)
            ->willReturn([$breed1, $breed2]);

        $svc = new BreedSearchService($repo);

        $result = $svc->search($type, $term);

        self::assertSame(
            [
                ['id' => (string) $uuid1, 'name' => 'Siberian Husky'],
                ['id' => (string) $uuid2, 'name' => 'Alaskan Husky'],
            ],
            $result
        );
    }

    /**
     * @throws Exception
     */
    public function testSearchReturnsEmptyArrayWhenRepositoryReturnsEmptyList(): void
    {
        $type = $this->createStub(PetType::class);

        $repo = $this->createMock(PetBreedRepository::class);
        $repo->expects(self::once())
            ->method('findBySearch')
            ->with($type, 'x')
            ->willReturn([]);

        $svc = new BreedSearchService($repo);

        self::assertSame([], $svc->search($type, 'x'));
    }

    /**
     * @throws Exception
     */
    public function testSearchFiltersOutNonPetBreedEntries(): void
    {
        $type = $this->createStub(PetType::class);

        $validUuid = Uuid::fromString('42424242-4242-4242-4242-424242424242');
        $valid = $this->mockBreed($validUuid, 'Test Breed');

        $invalid = new \stdClass();

        $repo = $this->createMock(PetBreedRepository::class);
        $repo->expects(self::once())
            ->method('findBySearch')
            ->with($type, 't')
            ->willReturn([$valid, $invalid]);

        $svc = new BreedSearchService($repo);

        self::assertSame([['id' => (string) $validUuid, 'name' => 'Test Breed']], $svc->search($type, 't'));
    }

    /**
     * @throws Exception
     */
    private function mockBreed(?Uuid $id, string $name): PetBreed
    {
        $m = $this->createMock(PetBreed::class);
        $m->method('getId')->willReturn($id);
        $m->method('getName')->willReturn($name);

        return $m;
    }
}
