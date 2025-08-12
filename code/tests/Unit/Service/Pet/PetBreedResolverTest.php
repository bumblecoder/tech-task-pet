<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Tests\Unit\Service\Pet;

use App\Entity\PetBreed;
use App\Repository\PetBreedRepository;
use App\Service\Pet\PetBreedResolver;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

final class PetBreedResolverTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testByIdReturnsEntityWhenFound(): void
    {
        $id = 'breed-123';

        $repo = $this->createMock(PetBreedRepository::class);
        $breed = $this->createStub(PetBreed::class);

        $repo->expects(self::once())
            ->method('find')
            ->with($id)
            ->willReturn($breed);

        $resolver = new PetBreedResolver($repo);

        self::assertSame($breed, $resolver->byId($id));
    }

    /**
     * @throws Exception
     */
    public function testByIdReturnsNullWhenNotFound(): void
    {
        $id = 'missing';

        $repo = $this->createMock(PetBreedRepository::class);
        $repo->expects(self::once())
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $resolver = new PetBreedResolver($repo);

        self::assertNull($resolver->byId($id));
    }
}
