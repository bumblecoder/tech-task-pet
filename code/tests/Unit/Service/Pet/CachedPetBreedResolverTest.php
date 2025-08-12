<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Tests\Unit\Service\Pet;

use App\Entity\PetBreed;
use App\Service\Pet\CachedPetBreedResolver;
use App\Service\Pet\PetBreedResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CachedPetBreedResolverTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     * @throws ORMException
     * @throws Exception
     */
    public function testCachesPositiveExistenceAndReturnsManagedReference(): void
    {
        $id = '11111111-1111-1111-1111-111111111111';

        $inner = $this->createMock(PetBreedResolverInterface::class);
        $inner->expects(self::once())
            ->method('byId')
            ->with($id)
            ->willReturn($this->createStub(PetBreed::class));

        $cache = $this->fakeCache();

        $em = $this->createMock(EntityManagerInterface::class);
        $ref1 = $this->createMock(PetBreed::class);
        $ref2 = $this->createMock(PetBreed::class);

        $em->expects(self::exactly(2))
            ->method('getReference')
            ->with(
                PetBreed::class,
                self::callback(fn($u) => $u instanceof Uuid && (string)$u === $id)
            )
            ->willReturnOnConsecutiveCalls($ref1, $ref2);

        $resolver = new CachedPetBreedResolver($inner, $cache, $em);

        $a = $resolver->byId($id);
        $b = $resolver->byId($id);

        self::assertSame($ref1, $a);
        self::assertSame($ref2, $b);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ORMException
     */
    public function testCachesNegativeExistenceAndAlwaysReturnsNull(): void
    {
        $id = '22222222-2222-2222-2222-222222222222';

        $inner = $this->createMock(PetBreedResolverInterface::class);
        $inner->expects(self::once())
            ->method('byId')
            ->with($id)
            ->willReturn(null);

        $cache = $this->fakeCache();

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('getReference');

        $resolver = new CachedPetBreedResolver($inner, $cache, $em);

        self::assertNull($resolver->byId($id));
        self::assertNull($resolver->byId($id));
    }

    /**
     * @throws Exception
     */
    private function fakeCache(): CacheInterface
    {
        $store = [];

        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')
            ->willReturnCallback(function (string $key, callable $callback) use (&$store) {
                if (\array_key_exists($key, $store)) {
                    return $store[$key];
                }

                $item = $this->createStub(ItemInterface::class);
                $item->method('expiresAfter')->willReturn($item);
                $item->method('expiresAt')->willReturn($item);
                if (method_exists($item, 'tag')) {
                    $item->method('tag')->willReturn($item);
                }

                $value = $callback($item);
                $store[$key] = $value;

                return $value;
            });

        if (method_exists($cache, 'delete')) {
            $cache->method('delete')->willReturn(true);
        }

        return $cache;
    }
}
