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
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CachedPetBreedResolverTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testCachesEntityAndAvoidsSecondInnerCall(): void
    {
        $id = '42';
        $breed = $this->createMock(PetBreed::class);
        $inner = $this->createMock(PetBreedResolverInterface::class);
        $inner->expects(self::once())
            ->method('byId')
            ->with($id)
            ->willReturn($breed);

        $cache = $this->fakeCache();

        $resolver = new CachedPetBreedResolver($inner, $cache);

        $first = $resolver->byId($id);
        $second = $resolver->byId($id);

        self::assertSame($breed, $first);
        self::assertSame($breed, $second);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testCachesNullResult(): void
    {
        $id = '404';

        $inner = $this->createMock(PetBreedResolverInterface::class);
        $inner->expects(self::once())
            ->method('byId')
            ->with($id)
            ->willReturn(null);

        $cache = $this->fakeCache();

        $resolver = new CachedPetBreedResolver($inner, $cache);

        $first = $resolver->byId($id);
        $second = $resolver->byId($id);

        self::assertNull($first);
        self::assertNull($second);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testDifferentKeysAreCachedSeparately(): void
    {
        $id1 = 'a';
        $id2 = 'b';

        $breed1 = $this->createMock(PetBreed::class);
        $breed2 = $this->createMock(PetBreed::class);

        $inner = $this->createMock(PetBreedResolverInterface::class);
        $inner->expects(self::exactly(2))
            ->method('byId')
            ->willReturnMap([
                [$id1, $breed1],
                [$id2, $breed2],
            ]);

        $cache = $this->fakeCache();

        $resolver = new CachedPetBreedResolver($inner, $cache);

        self::assertSame($breed1, $resolver->byId($id1));
        self::assertSame($breed2, $resolver->byId($id2));

        self::assertSame($breed1, $resolver->byId($id1));
        self::assertSame($breed2, $resolver->byId($id2));
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
                $item->method('tag')->willReturn($item);

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
