<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Tests\Unit\Service\Pet;

use App\Entity\PetType;
use App\Repository\PetTypeRepository;
use App\Service\Pet\PetTypeResolver;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

final class PetTypeResolverTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testByIdReturnsEntityWhenFound(): void
    {
        $id = 'abc-123';
        $repo = $this->createMock(PetTypeRepository::class);

        $entity = new PetType();

        $repo->expects(self::once())
            ->method('find')
            ->with($id)
            ->willReturn($entity);

        $resolver = new PetTypeResolver($repo);

        self::assertSame($entity, $resolver->byId($id));
    }

    /**
     * @throws Exception
     */
    public function testByIdReturnsNullWhenNotFound(): void
    {
        $id = 'missing';
        $repo = $this->createMock(PetTypeRepository::class);

        $repo->expects(self::once())
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $resolver = new PetTypeResolver($repo);

        self::assertNull($resolver->byId($id));
    }
}
