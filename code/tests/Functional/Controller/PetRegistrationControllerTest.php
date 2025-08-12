<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Tests\Functional\Controller;

use Symfony\Bridge\Twig\DataCollector\TwigDataCollector;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class PetRegistrationControllerTest extends WebTestCase
{
    public function testRegisterPageLoads(): void
    {
        $client = static::createClient();

        $client->request('GET', '/register');

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertStringStartsWith('text/html', $client->getResponse()->headers->get('Content-Type'));
    }

    public function testRouteNameGeneratesExpectedPathAndIsReachable(): void
    {
        $client = static::createClient();
        /** @var RouterInterface $router */
        $router = static::getContainer()->get(RouterInterface::class);

        $path = $router->generate('app_pet_register');
        self::assertSame('/register', $path);

        $client->request('GET', $path);
        self::assertResponseIsSuccessful();
    }

    public function testExpectedTwigTemplateIsRendered(): void
    {
        $client = static::createClient();
        $client->enableProfiler();

        $client->request('GET', '/register');
        self::assertResponseIsSuccessful();

        $profile = $client->getProfile();
        self::assertNotNull($profile, 'test');

        /** @var TwigDataCollector $twigCollector */
        $twigCollector = $profile->getCollector('twig');

        $renderedTemplates = array_keys($twigCollector->getTemplates());
        self::assertContains(
            'pet/register.html.twig',
            $renderedTemplates,
            'pet/register.html.twig'
        );
    }
}
